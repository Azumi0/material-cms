<?php

namespace app\modules\users\models;

use app\models\GUsersTable;
use materialhelpers\Validators;
use Yii;
use yii\base\Model;


class UsersForm extends Model
{
    public $available_roles_validation = [];
    public $name;
    public $surname;
    public $mail;
    public $phone;
    public $password;
    public $rpassword;
    public $role;
    public $base_address;
    public $nip;
    public $company_name;
    public $company_province;
    public $company_city;
    public $company_address;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['mail', 'phone', 'name', 'surname', 'rpassword', 'role', 'nip', 'base_address'], 'trim'],
            [['mail', 'phone', 'name', 'surname', 'rpassword', 'role', 'nip', 'base_address'], 'default'],
            [['mail', 'phone', 'name', 'surname'], 'required', 'on' => ['add', 'edit', 'frontendEdit'], 'message' => 'Pole nie może być puste'],
            ['phone', 'string', 'length' => [9, 9], 'on' => ['add', 'edit', 'frontendEdit'], 'tooLong' => 'Prosimy podać prawidłowy numer telefonu (9 cyfr)', 'tooShort' => 'Prosimy podać prawidłowy numer telefonu (9 cyfr)'],
            ['phone', 'number', 'on' => ['add', 'edit', 'frontendEdit'], 'message' => 'Prosimy podać prawidłowy numer telefonu (9 cyfr)'],
            [['role'], 'required', 'on' => ['add', 'edit'], 'message' => 'Pole nie może być puste'],
            [['company_name', 'company_province', 'company_city', 'company_address'], 'required', 'on' => ['edit'], 'when' => function($model) {
                return $model->role === 'executor';
            }, 'message' => 'Pole nie może być puste'],
            ['password', 'required', 'on' => 'add', 'message' => 'Pole nie może być puste'],
            ['rpassword', 'required', 'on' => 'add', 'message' => 'Pole nie może być puste'],
            ['password', 'compare', 'on' => ['add', 'edit', 'frontendEdit'], 'compareAttribute' => 'rpassword', 'skipOnEmpty' => true, 'message' => 'Hasło i powtórzone hasło nie są zgodne.'],
            ['nip', 'required', 'on' => ['add', 'edit'], 'when' => function($model) {
                return $model->role === 'executor';
            }, 'message' => 'Pole nie może być puste'],
            ['nip', 'checknip', 'on' => ['add', 'edit']],
            ['role', 'in', 'range' => $this->available_roles_validation, 'on' => ['add', 'edit'], 'message' => 'Niedozwolona wartość'],
            ['mail', 'shmail', 'on' => ['add', 'edit', 'frontendEdit']],
            ['mail', 'shexist', 'on' => 'add'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name' => 'Imię',
            'surname' => 'Nazwisko',
            'mail' => 'Adres e-mail',
            'phone' => 'Numer telefonu',
            'password' => 'Hasło',
            'rpassword' => 'Powtórz hasło',
            'role' => 'Grupa uprawnień',
            'base_address' => 'Adres siedziby',
            'nip' => 'Numer NIP',
            'company_name' => 'Nazwa firmy',
            'company_province' => 'Województwo firmy',
            'company_city' => 'Miasto firmy',
            'company_address' => 'Adres firmy',
        ];
    }

    public function checknip($attribute, $params) {
        if ($this->role === 'executor') {
            if (!Validators::checkNIP($this->$attribute)) {
                $this->addError($attribute, 'Prosimy podać prawidłowy numer nip');
            }
        }
    }

    public function shexist($attribute, $params) {
        if (!$this->hasErrors()) {
            $aDB = new GUsersTable();
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
