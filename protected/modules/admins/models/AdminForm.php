<?php

namespace app\modules\admins\models;

use Yii;
use yii\base\Model;
use app\modules\admins\models\GAdmin;


class AdminForm extends Model
{
    public $realname;
    public $avatar;
    public $mail;
    public $password;
    public $rpassword;
    public $role;
    public $banner;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['mail', 'realname', 'avatar'], 'trim'],
            [['banner', 'mail', 'realname', 'avatar'], 'default'],
            [['mail', 'realname', 'role'], 'required', 'on' => ['add', 'edit'], 'message' => 'Pole nie może być puste'],
            ['password', 'required', 'on' => 'add', 'message' => 'Pole nie może być puste'],
            ['rpassword', 'required', 'on' => 'add', 'message' => 'Pole nie może być puste'],
            ['password', 'compare', 'on' => ['add', 'edit'], 'compareAttribute' => 'rpassword', 'skipOnEmpty' => true, 'message' => 'Hasło i powtórzone hasło nie są zgodne.'],
            ['mail', 'shmail', 'on' => ['add', 'edit']],
            ['mail', 'gwexist', 'on' => 'add'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'realname' => 'Nazwa wyświetlana',
            'avatar' => 'Zdjęcie',
            'mail' => 'Adres e-mail',
            'password' => 'Hasło',
            'rpassword' => 'Powtórz hasło',
            'role' => 'Grupa uprawnień',
            'banner' => 'Ukryj banner'
        ];
    }

    public function gwexist($attribute, $params) {
        if (!$this->hasErrors()) {
            $aDB = new GAdmin();
            if ($aDB->checkExist($this->$attribute)) {
                $this->addError($attribute, 'Użytkownik o takim adresie e-mail istnieje już w systemie');
            }
        }
    }

    public function shmail($attribute, $params) {
        if (!$this->hasErrors()) {
            $pattern = "/^([a-zA-Z0-9._-]+)@([a-zA-Z0-9.-]+)\.([a-zA-Z]{2,6})$/";
            if (!preg_match($pattern, $this->$attribute)) {
                $this->addError($attribute, 'Niepoprawny format adresu e-mail');
            }
        }
    }
}
