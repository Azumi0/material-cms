<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GSeoTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'seo';
    public $primary = array('name', 'params');


    public function getListing() {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where("readableName != ''");
        $query->orderBy('readableName ASC');
        return $query->all();
    }

    public function getByParams($name, $params) {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('name = :name AND params = :params', array(':name' => $name, ':params' => $params));
        return $query->one();
    }

    public function getByDT($domain_text) {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('domain_text = :domain_text', array(':domain_text' => $domain_text));
        return $query->one();
    }

    public function fetchRowById($id, $columns = '*')
    {
        $query = new Query();
        $query->select($columns);
        $query->from($this->table);
        $query->where('id = :id', array(':id' => $id));

        return $query->one();
    }
}