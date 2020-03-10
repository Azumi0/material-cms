<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GAccessTokensTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'access_tokens';
    public $primary = 'id';

    public function getValidByToken($token) {
        $date = new \DateTime();
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('token = :token AND expires_at > :now', array(':token' => $token, ':now' => $date->format('Y-m-d H:i:s')));
        return $query->one();
    }

    public function cleanExpired()
    {
        $date = new \DateTime();
        $cmd = $this->db->createCommand();
        $cmd->delete($this->table, 'expires_at <= :now', array(':now' => $date->format('Y-m-d H:i:s')))->execute();

        $this->db->createCommand('ALTER TABLE ' . $this->table . ' AUTO_INCREMENT = 1')->execute();
    }
}