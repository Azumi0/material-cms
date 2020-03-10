<?php

namespace app\modules\users\models;

use Yii;
use yii\base\Model;


class UserLoginForm extends Model
{
    public $mail;
    public $password;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['mail', 'trim'],
            [['mail', 'password'], 'default'],
            ['mail', 'required', 'message' => 'Podaj adres e-mail'],
            ['password', 'required', 'message' => 'Podaj hasło'],
            ['mail', 'shmail', 'message' => 'Nieprawidłowy format adresu e-mail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mail' => 'E-MAIL',
            'password' => 'HASŁO',
        ];
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
