<?php

namespace app\modules\acl\models;

use Yii;
use yii\base\Model;


class AclForm extends Model
{
    public $description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['description', 'required', 'message' => 'Podaj nazwę grupy']
        ];
    }
    public function attributeLabels()
    {
        return [
            'description' => 'Nazwa grupy',
        ];
    }
}
