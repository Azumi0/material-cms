<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GDonationsTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'donations';
    public $primary = 'id';


}