<?php

namespace app\models;

use materialhelpers\DatabaseManager;
use yii\db\Query;

class GCalendarTable extends DatabaseManager {
    public $lang = 'g_';
    public $table = 'calendar';
    public $primary = 'id';


    public function getCalendar($uid, $start, $end) {
        $query = new Query();
        $query->select('*');
        $query->from($this->table);
        $query->where('user_id = :uid AND ((start_date BETWEEN :start AND :end) OR (end_date BETWEEN :start AND :end))', array(':uid' => $uid, ':start' => $start, ':end' => $end));
        $res = $query->all();

        $date = new \DateTime($start);
        $tmpstart = $date->format('Y-m-d');

        $fid = new \DateTime($end);
        $start_day = $fid->format('j');
        $fid->modify('+1 day');
        $end_day = $fid->format('j');

        if ($start_day != $end_day)
            $fid->modify('last day of last month');
        $finstop = $fid->format('Y-m-d');

        $final = array();
        while ($tmpstart != $finstop){
            $tmparr = array();

            foreach ($res as $single) {
                $singlestartDate = date('Y-m-d', strtotime($single['start_date']));
                $singleendDate = date('Y-m-d', strtotime($single['end_date']));

                if (($tmpstart >= $singlestartDate) && ($tmpstart <= $singleendDate)){
                    $tmparr[] = $single;
                }
            }

            $final[$tmpstart] = $tmparr;

            $date->modify('+1 day');
            $tmpstart = $date->format('Y-m-d');
        }

        return $final;
    }
}