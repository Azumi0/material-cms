<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GSidebarsTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'admin_sidebars';
    public $primary = 'uid';


    public function getSettings($uid) {
        $query = new Query();
        $query->select('sbLeft, sbRight');
        $query->from($this->table);
        $query->where('uid = :uid', array(':uid' => $uid));
        $row = $query->one();

        if ($row){
            return $row;
        } else {
            $cmd = $this->db->createCommand();
            $cmd->insert($this->table, array('uid' => $uid))->execute();

            return array('sbLeft' => 1, 'sbRight' => 1);
        }
    }
}