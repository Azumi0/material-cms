<?php

namespace app\modules\users\controllers;

use app\components\FrontendIdentity;
use app\models\GAccessTokensTable;
use app\models\GUsersTable;
use app\modules\users\models\UserInvoiceForm;
use app\modules\users\models\UserLoginForm;
use app\modules\users\models\UsersForm;
use GusApi\Adapter\Soap\Exception\NoDataException;
use GusApi\Adapter\Soap\SoapAdapter;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\RegonConstantsInterface;
use materialhelpers\Convert;
use materialhelpers\CorsCustom;
use materialhelpers\Stat;
use materialhelpers\System;
use Ramsey\Uuid\Uuid;
use yii;
use app\components\Controller;

class FrontendController extends Controller
{
    public $enableCsrfValidation = false;

    protected $roleMapping = [
        'customer' => 'Klient',
        'executor' => 'Wykonawca'
    ];

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => CorsCustom::className(),
            ],
            [
                'class' => yii\filters\ContentNegotiator::className(),
                'formats' => [
                    'application/json' => yii\web\Response::FORMAT_JSON,
                ],
            ],
        ];
    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    public function actionOptions()
    {
        return [
            'success' => true,
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionLogin()
    {
        $response = [
            'success' => false
        ];

        $form = new UserLoginForm();

        if (!$this->isPost()) {
            throw new \Exception('Invalid request', 400);
        }

        $data = $this->getPost();

        $form->attributes = $data;

        if ($form->validate()){
            $identity = FrontendIdentity::authenticate($data['mail'], $data['password']);

            if (!empty($identity)){
                Yii::$app->user->login($identity);

                $expireCalc = new \DateTime();
                $expireCalc->add(new \DateInterval(Yii::$app->params['userTokenExpire']));

                $gAcTkDB = new GAccessTokensTable();
                $newToken = [
                    'token' => Uuid::uuid4()->toString(),
                    'expires_at' => $expireCalc->format('Y-m-d H:i:s'),
                    'user_id' => $identity->id,
                ];
                $gAcTkDB->save($newToken);

                $response['success'] = true;
                $response['token'] = $newToken['token'];
                $response['expires_at'] = $newToken['expires_at'];

                return $response;
            }

            $response['msg'] = 'Nieprawidłowy adres e-mail lub hasło.';

            return $response;
        }

        $response['msg'] = 'Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.';
        $response['errors'] = $form->getErrors();


        return $response;
    }

    /**
     * @return array
     */
    public function actionLogout()
    {
        $response = [
            'success' => true
        ];

        $gAcTkDB = new GAccessTokensTable();
        $gAcTkDB->delete(false, ['token' => $this->userUsedToken]);
        Yii::$app->user->logout();

        return $response;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function actionRefresh()
    {
        $response = [
            'success' => true
        ];

        $expireCalc = new \DateTime();
        $expireCalc->add(new \DateInterval(Yii::$app->params['userTokenExpire']));

        $gAcTkDB = new GAccessTokensTable();
        $currentToken = $gAcTkDB->getValidByToken($this->userUsedToken);
        if (!$currentToken) {
            return [
                'success' => false,
                'used_token' => $this->userUsedToken
            ];
        }
        $currentToken['expires_at'] = $expireCalc->format('Y-m-d H:i:s');

        $gAcTkDB->save($currentToken, $currentToken['id']);

        $response['token'] = $currentToken['token'];
        $response['expires_at'] = $currentToken['expires_at'];

        return $response;
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function actionGetbasicuser()
    {
        $identity = Yii::$app->user->getIdentity(false);

        $response = [
            'success' => true,
            'user' => [
                'name' => $identity->name,
                'surname' => $identity->surname,
                'phone' => $identity->phone,
                'mail' => $identity->mail,
                'active' => $identity->active,
                'role' => $identity->role,
                'nip' => $identity->nip,
                'company_name' => $identity->company_name,
                'company_province' => $identity->company_province,
                'company_city' => $identity->company_city,
                'company_address' => $identity->company_address,
                'base_address' => $identity->base_address,
            ]
        ];

        if (is_array($identity->extra_data)) {
            $response['user']['extra_data'] = $identity->extra_data;
        } else {
            $response['user']['extra_data'] = json_decode($identity->extra_data, true);
        }

        return $response;
    }

    /**
     * @return array
     * @throws yii\base\Exception
     */
    public function actionRegister()
    {
        $form = new UsersForm(['scenario' => 'add']);
        $form->available_roles_validation = array_keys($this->roleMapping);
        $data = $this->getPost();

        $form->setAttributes($data);

        if ($form->validate()) {
            unset($data['rpassword']);
            $data['salt'] = $salt = Stat::keyGen(5);
            $data['password'] = Yii::$app->getSecurity()->generatePasswordHash($salt . $data['password']);

            if ($data['role'] === 'executor') {
                $gus = (Yii::$app->params['gus_db'] === 'test') ?
                    new GusApi('abcde12345abcde12345') :
                    new GusApi(Yii::$app->params['gus_key'], new SoapAdapter(RegonConstantsInterface::BASE_WSDL_URL, RegonConstantsInterface::BASE_WSDL_ADDRESS));
                try {
                    $sid = $gus->login();

                    $gusReports = $gus->getByNip($sid, $data['nip']);
                    if (!empty($gusReports[0])) {
                        $provinces = System::$provinces;
                        $gusResult = $gusReports[0]->jsonSerialize();
                        $data['company_name'] = $gusResult['name'];
                        $gusProvince = Convert::toLower($gusResult['province']);
                        $data['company_province'] = array_reduce($provinces, function ($carry, $value) use ($gusProvince) {
                            if (!empty($carry)) {
                                return $carry;
                            }

                            return (Convert::toLower($value) === $gusProvince) ? $value : null;
                        }, null);
                        $data['company_city'] = $gusResult['city'];
                        $data['company_address'] = $gusResult['street'];
                    }
                } catch (InvalidUserKeyException $e) {
                    Yii::error('Can\'t login to GUS database' . '; Message: ' . $e->getMessage());
                } catch (NotFoundException $e) {
                    Yii::error('GUS data not found for nip' . $data['nip'] . '; Message: ' . $gus->getResultSearchMessage($sid));
                } catch (NoDataException $e) {
                    Yii::error('No GUS data for nip' . $data['nip'] . '; Message: ' . $e->getMessage());
                } catch (\Exception $e) {
                    Yii::error('Unknown exception while getting GUS data for nip' . $data['nip'] . '; Message: ' . $e->getMessage());
                }
            }

            $gUsrDB = new GUsersTable();
            $gUsrDB->save($data);

            return [
                'success' => true,
                'errors' => []
            ];
        }

        return [
            'success' => false,
            'errors' => $form->getErrors()
        ];
    }

    /**
     * @return array
     * @throws \Throwable
     * @throws yii\base\Exception
     */
    public function actionEdit()
    {
        $form = new UsersForm(['scenario' => 'frontendEdit']);
        $data = $this->getPost();
        $currIdentity = Yii::$app->user->getIdentity(false);
        $form->setAttributes($data);

        if (!empty($data['rpassword'])) {
            $identity = FrontendIdentity::authenticate($currIdentity->mail, $data['old_password']);
            if (empty($identity)){
                $form->validate();
                $err = $form->getErrors();
                $err['old_password'] = "Nieprawidłowe hasło";

                return [
                    'success' => false,
                    'errors' => $err
                ];
            }
        }

        if ($form->validate()) {
            unset($data['old_password']);
            unset($data['rpassword']);
            if ($data['password']) {
                $data['salt'] = $salt = Stat::keyGen(5);

                $data['password'] = Yii::$app->getSecurity()->generatePasswordHash($salt . $data['password']);
            } else {
                unset($data['password']);
            }

            $gUsrDB = new GUsersTable();
            $gUsrDB->save($data, $currIdentity->id);

            return [
                'success' => true,
                'errors' => []
            ];
        }

        return [
            'success' => false,
            'errors' => $form->getErrors()
        ];
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function actionInvoice()
    {
        $form = new UserInvoiceForm(['scenario' => 'edit']);
        $data = $this->getPost();

        $form->setAttributes($data);

        if ($form->validate()) {
            $currIdentity = Yii::$app->user->getIdentity(false);

            $gUsrDB = new GUsersTable();
            $gUsrDB->save($data, $currIdentity->id);

            return [
                'success' => true,
                'errors' => []
            ];
        }

        return [
            'success' => false,
            'errors' => $form->getErrors()
        ];
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function actionExtra()
    {
        $data = $this->getPost();

        if (isset($data['base_address']) && isset($data['extra_data'])) {
            $currIdentity = Yii::$app->user->getIdentity(false);
            $save = [
                'base_address' => $data['base_address'],
                'extra_data' => $data['extra_data']
            ];

            $gUsrDB = new GUsersTable();
            $gUsrDB->save($save, $currIdentity->id);

            return [
                'success' => true,
                'errors' => []
            ];
        }

        return [
            'success' => false,
            'errors' => 'Brak danych do zapisu'
        ];
    }
}
