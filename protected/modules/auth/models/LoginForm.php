<?php

namespace app\modules\auth\models;

use Yii;
use yii\base\Model;


class LoginForm extends Model
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
            ['mail', 'email', 'message' => 'Nieprawidłowy format adresu e-mail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'mail' => 'E-MAIL',
            'password' => 'HASŁO',
        ];
    }
}
