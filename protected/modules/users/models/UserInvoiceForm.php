<?php

namespace app\modules\users\models;

use materialhelpers\Validators;
use Yii;
use yii\base\Model;


class UserInvoiceForm extends Model
{
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
            [['company_name', 'company_province', 'company_city', 'company_address', 'nip'], 'trim', 'on' => 'edit'],
            [['company_name', 'company_province', 'company_city', 'company_address', 'nip'], 'default', 'on' => 'edit'],
            [['company_name', 'company_province', 'company_city', 'company_address', 'nip'], 'required', 'on' => 'edit', 'message' => 'Pole nie może być puste'],
            ['nip', 'checknip', 'on' => 'edit'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'nip' => 'Numer NIP',
            'company_name' => 'Nazwa firmy',
            'company_province' => 'Województwo firmy',
            'company_city' => 'Miasto firmy',
            'company_address' => 'Adres firmy',
        ];
    }

    public function checknip($attribute, $params) {
        if (!Validators::checkNIP($this->$attribute)) {
            $this->addError($attribute, 'Prosimy podać prawidłowy numer nip');
        }
    }
}
