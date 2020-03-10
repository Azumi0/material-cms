<?php

namespace app\modules\admins\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GAdmin extends DatabaseManager
{
    public $lang = 'g_';
    public $table = 'admin';
    public $primary = 'id';

    public $fields = array('realname' => 'LIKE', 'mail' => 'LIKE');
    public $crm_fields = array('ga.realname' => 'LIKE', 'ga.mail' => 'LIKE');

    public function getListing()
    {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('visible = 1');
        return $query->all();
    }

    public function getListingAjax($search = false, $limit = 10, $offset = 0, $order = 'realname asc')
    {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('visible = 1');
        $query->orderBy($order);

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

    public function getListingAjaxCount($search = false)
    {
        $query = new Query();
        $query->select('count(id) as count');
        $query->from($this->table);
        $query->where('visible = 1');

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

    public function getCrmListingAjax($search = false, $limit = 10, $offset = 0, $order = 'date desc')
    {
        $query = new Query();
        $query->select('ga.id, ga.mail, ga.realname, vcutc.count');
        $query->from($this->table . ' ga');
        $query->leftJoin('v_crm_users_tasks_count AS vcutc', 'ga.id = vcutc.user_id');
        $query->where('ga.visible = 1');
        $query->orderBy($order);

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

    public function getCrmListingAjaxCount($search = false)
    {
        $query = new Query();
        $query->select('count(id) as count');
        $query->from($this->table);
        $query->where('visible = 1');

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

    public function getAdministratorsList() {
        $query = new Query();
        $query->select('id, mail AS username, realname');
        $query->from($this->table);
        $query->where('visible = 1 AND active = 1');
        $res =  $query->all();

        $final = array();
        foreach ($res as $r) {
            $final[$r['id']] = $r;
        }

        return $final;
    }

    public function findIdentityByAccessToken($token)
    {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('access_token = :token', array(':token' => $token));

        return $query->one();
    }

    public function checkExist($username) {
        $query = new Query();
        $query->select('id');
        $query->from($this->table);
        $query->where('mail = :mail', array(':mail' => $username));
        return $query->one();
    }
}
