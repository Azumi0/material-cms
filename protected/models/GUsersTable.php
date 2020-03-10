<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\db\Query;

class GUsersTable extends DatabaseManager
{
    public $lang = 'g_';
    public $table = 'users';
    public $primary = 'id';

    public $fields = [
        'name' => 'LIKE',
        'surname' => 'LIKE',
        'mail' => 'LIKE',
    ];

    public function getListing($cols = '*')
    {
        $query = new Query();
        $query->select($cols);
        $query->from($this->table);
        return $query->all();
    }

    public function getListingAjax($search = false, $limit = 10, $offset = 0, $order = 'name ASC', $advSearch = false)
    {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->orderBy($order);

        if ($advSearch !== false) {
            if (!empty($advSearch['role'])) {
                $query->andWhere('role = :userRole', [':userRole' => $advSearch['role']]);
            }
        }

        if ($search) {
            $result = $this->prepareSearch($this->fields, $search);
            if ($query->where) {
                $query->andWhere($result['condition'], $result['params']);
            } else {
                $query->where($result['condition'], $result['params']);
            }
        }

        $query->offset($offset);
        $query->limit($limit);

        return $query->all();
    }

    public function getListingAjaxCount($search = false, $advSearch = false)
    {
        $query = new Query();
        $query->select('count(id) as count');
        $query->from($this->table);

        if ($advSearch !== false) {
            if (!empty($advSearch['role'])) {
                $query->andWhere('role = :userRole', [':userRole' => $advSearch['role']]);
            }
        }

        if ($search) {
            $result = $this->prepareSearch($this->fields, $search);
            if ($query->where) {
                $query->andWhere($result['condition'], $result['params']);
            } else {
                $query->where($result['condition'], $result['params']);
            }
        }

        return $query->one();
    }

    public function getCount()
    {
        $query = new Query();
        $query->select('COUNT(id) AS count');
        $query->from($this->table);
        $row = $query->one();

        return ($row) ? $row['count'] : 0;
    }

    public function checkExist($username) {
        $query = new Query();
        $query->select('id');
        $query->from($this->table);
        $query->where('mail = :mail', array(':mail' => $username));
        return $query->one();
    }
}
