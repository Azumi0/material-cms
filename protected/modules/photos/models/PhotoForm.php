<?php

namespace app\modules\photos\models;

use materialhelpers\Stat;
use materialhelpers\System;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;


class PhotoForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $photo;
    /**
     * @var string
     */
    public $photoBaseName;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['photo'], 'file', 'on' => ['add', 'edit'], 'skipOnEmpty' => false, 'extensions' => 'jpeg, jpg, png'],
        ];
    }

    public function upload()
    {
        $validate = $this->validate();

        $dir = System::createDirectoryStructure('uploads/photos');

        if ($validate && !empty($this->photo->size)) {
            $this->photoBaseName = Stat::keyGenSecure();
            $fileName = $this->photoBaseName . '.' . $this->photo->extension;
            $this->photo->saveAs($dir . $fileName);

            return $fileName;
        }

        return false;
    }

    public function attributeLabels()
    {
        return [
            'photo' => 'Zdjęcie',
        ];
    }
}
