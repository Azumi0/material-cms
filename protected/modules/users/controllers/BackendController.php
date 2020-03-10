<?php

namespace app\modules\users\controllers;

use app\models\GUsersTable;
use app\modules\users\models\UsersForm;
use GusApi\Adapter\Soap\Exception\NoDataException;
use GusApi\Adapter\Soap\SoapAdapter;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\RegonConstantsInterface;
use materialhelpers\Convert;
use materialhelpers\Stat;
use materialhelpers\System;
use Yii;
use app\components\AdminController;


class BackendController extends AdminController
{
    protected $roleMapping = [
        'customer' => 'Klient',
        'executor' => 'Wykonawca'
    ];

    public function actionIndex()
    {
        if (isset($_GET['reset']) && !empty($_GET['reset'])) {
            if ($_GET['reset'] == 'true') {
                $this->unsetSession('gw_user_search');
                return $this->redirect($this->url('users/backend/index'));
            }
        }

        $view = array();
        $view['currbyperm'] = 'users|backend|index';

        $gUsrDB = new GUsersTable();
        $view['count'] = $count = $gUsrDB->getCount();
        $view['roleMapping'] = $this->roleMapping;

        $usersearchSession = $this->getSession('gw_user_search', false);

        if ($this->isAjax()) {
            $post = $this->getPost();
            $limit = $post['limit'];

            if (!empty($usersearchSession)) {
                $adv = json_decode($usersearchSession, true);
            } else {
                $adv = false;
            }

            if ($post['search']) {
                $row = $gUsrDB->getListingAjaxCount($post['search'], $adv);
                $count = $row['count'];

                if ($count < $post['offset']) {
                    $post['offset'] = 0;
                }
            }
            $view['offset'] = $post['offset'];

            $currLast = ($post['offset'] + 1) * $limit;
            if ($limit > $count || ($currLast >= $count)) {
                $view['allRec'] = true;
            }

            $view['listing'] = $gUsrDB->getListingAjax($post['search'], $limit, $post['offset'], $post['order'] . ' ' . $post['direction'], $adv);

            $html = $this->renderPartial('_listing', $view);
            echo json_encode(array('html' => $html, 'count' => $count, 'page' => ($post['offset'] / $limit) + 1));
            die;
        }

        $adv = [];
        if ($this->isPost()) {
            $adv = $this->getPost('Usersadvsearch');
            if (count($adv) > 0) {
                $this->setSession('gw_user_search', json_encode($adv));
            }
        } elseif (!empty($usersearchSession)) {
            $adv = json_decode($usersearchSession, true);
        }

        if (Stat::notEmptyArray($adv)) {
            $view['usersadvsearch'] = $adv;
        }

        return $this->render('index', $view);
    }

    public function actionAdd()
    {
        $view = array();
        $view['currbyperm'] = 'users|backend|index';
        $view['roleMapping'] = $this->roleMapping;

        $form = new UsersForm(['scenario' => 'add']);
        $form->available_roles_validation = array_keys($this->roleMapping);

        if ($this->isPost()) {
            $view['data'] = $data = $this->getPost('UsersForm');
            $form->setAttributes($data);
//            $form->attributes = $data;
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

                $this->setFlash('success', 'Nowe konto użytkownika zostało zapisane prawidłowo');
                return $this->redirect($this->url('users/backend/index'));
            } else {
                $this->setFlash(
                    'error',
                    'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.'
                );
                $view['errors'] = $form->getErrors();
            }
        }

        $view['model'] = $form;

        return $this->render('add', $view);
    }

    public function actionEdit()
    {
        $view = array();
        $view['currbyperm'] = 'users|backend|index';
        $view['roleMapping'] = $this->roleMapping;
        $view['provinces'] = System::$provinces;

        $view['id'] = $id = $this->getParam('id');
        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('users/backend/index'));
        }

        $gUsrDB = new GUsersTable();
        $cdata = $gUsrDB->fetchRowByPrimary($id);
        unset($cdata['password']);
        $view['data'] = $view['cdata'] = $cdata;

        $form = new UsersForm(['scenario' => 'edit']);
        $form->available_roles_validation = array_keys($this->roleMapping);
        $form->setAttributes($cdata);

        if ($this->isPost()) {
            $view['data'] = $data = $this->getPost('UsersForm');

            $form->setAttributes($data);
//            $form->rpassword = $data['rpassword'];
            if ($form->validate()) {
                unset($data['rpassword']);

                if ($data['password']) {
                    $data['salt'] = $salt = Stat::keyGen(5);

                    $data['password'] = Yii::$app->getSecurity()->generatePasswordHash($salt . $data['password']);
                } else {
                    unset($data['password']);
                }

                $gUsrDB->save($data, $id);

                $this->setFlash('success', 'Modyfikacja wybranego użytkownika powiodła się');
                return $this->redirect($this->url('users/backend/index'));
            } else {
                $this->setFlash(
                    'error',
                    'Wystąpił błąd zapisu. Prosimy upewnić się że wszystkie pola zostały prawidłowo uzupełnione.'
                );
                $view['errors'] = $form->getErrors();
            }
        }

        $view['model'] = $form;

        return $this->render('edit', $view);
    }

    public function actionDetails()
    {
        $view = array();
        $view['currbyperm'] = 'users|backend|index';

        $id = $this->getParam('id');
        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($this->url('users/backend/index'));
        }

        $gUsrDB = new GUsersTable();
        $cdata = $gUsrDB->fetchRowByPrimary($id, 'extra_data');
        $jsonDecoded = [];

        if (!empty($cdata['extra_data'])) {
            $jsonDecoded = json_decode($cdata['extra_data'], true);
        }

        $view['data'] = $jsonDecoded;
        $view['area_types'] = [
            'whole_country' => 'Cała Polska',
            'province' => 'Województwa',
            'area_type_zone' => 'W promieniu od siedziby',
        ];

        $parsedUserExtraData = System::parseUserExtraData($jsonDecoded);

        $view['provinces'] = $parsedUserExtraData['provinces'];
        $view['executorSkillsUsers'] = $parsedUserExtraData['executorSkillsUsers'];

        return $this->render('details', $view);
    }

    public function actionActive()
    {
        $ref = $this->getReferrer();
        $id = $this->getParam('id');

        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($ref);
        }

        $gUsrDB = new GUsersTable();
        $flag = $gUsrDB->flag($id, 'active');
        $data = $gUsrDB->fetchRowByPrimary($id, 'mail');

        if ($flag) {
            $this->setFlash('success', 'Konto użytkownika zostało aktywowane');

            $body = $this->render('/mails/admin_activation.twig');
            Yii::$app->mailer->compose()
                ->setFrom([MAIL_FROM => MAIL_FROM_NAME])
                ->setTo($data['mail'])
                ->setSubject('Aktywacja konta')
                ->setHtmlBody($body)
                ->send();
        } else {
            $this->setFlash('success', 'Konto użytkownika zostało wyłączona');
        }

        return $this->redirect($ref);
    }

    public function actionDelete()
    {
        $ref = $this->getReferrer();
        $id = $this->getParam('id');

        if (!$id) {
            $this->setFlash('warning', 'Nieprawidłowy parametr wejściowy');
            return $this->redirect($ref);
        }

        $gUsrDB = new GUsersTable();
        $gUsrDB->delete($id);

        $this->setFlash('success', 'Konto użytkownika zostało usunięte');

        return $this->redirect($ref);
    }
}
