<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GPhotosTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'photos';
    public $primary = 'id';


    public function getByName($name) {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('name = :name', array(':name' => $name));
        return $query->one();
    }

    public function getOnePhoto($name, $code)
    {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('name = :name and code = :code', array(':name' => $name, ':code' => $code));

        return $query->one();
    }
}