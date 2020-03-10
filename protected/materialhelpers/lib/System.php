<?php

namespace materialhelpers;

use Yii;
use app\modules\admins\models\GAdmin;
use app\models\GSidebarsTable;
use app\models\GSeoTable;

class System
{
    public static $provinces = [
        'Dolnośląskie' => 'Dolnośląskie',
        'Kujawsko-Pomorskie' => 'Kujawsko-Pomorskie',
        'Lubelskie' => 'Lubelskie',
        'Lubuskie' => 'Lubuskie',
        'Łódzkie' => 'Łódzkie',
        'Małopolskie' => 'Małopolskie',
        'Mazowieckie' => 'Mazowieckie',
        'Opolskie' => 'Opolskie',
        'Podkarpackie' => 'Podkarpackie',
        'Podlaskie' => 'Podlaskie',
        'Pomorskie' => 'Pomorskie',
        'Śląskie' => 'Śląskie',
        'Świętokrzyskie' => 'Świętokrzyskie',
        'Warmińsko-Mazurskie' => 'Warmińsko-Mazurskie',
        'Wielkopolskie' => 'Wielkopolskie',
        'Zachodniopomorskie' => 'Zachodniopomorskie',
    ];

    public static function getMessages()
    {
        if (!Yii::$app->session->hasFlash('shmessages')) {
            return false;
        }

        $flash = Yii::$app->session->getFlash('shmessages', false, true);

        $html = '';

        $possible = array(
            'success' => array('icon' => 'fa-success', 'heading' => 'Sukces'),
            'warning' => array('icon' => 'fa-warn', 'heading' => 'Uwaga'),
            'information' => array('icon' => 'fa-info', 'heading' => 'Informacja'),
            'error' => array('icon' => 'fa-error', 'heading' => 'Błąd')
        );

        foreach ($flash as $sm) {
            $html .= '<div class="' . $sm[0] . ' form">' .
                        '<h4><i class="fa ' . $possible[$sm[0]]['icon'] . '"></i> ' . $possible[$sm[0]]['heading'] . '</h4>' .
                        '<p>' . $sm[1] . '</p>' .
                    '</div>';
        }

        return $html;
    }

    public static function getFrontMessages()
    {
        if (!Yii::$app->session->hasFlash('shmessages')) {
            return false;
        }

        $flash = Yii::$app->session->getFlash('shmessages', false, true);

        $html = '';

        $possible = array(
            'success' => array('icon' => 'fa-success', 'heading' => 'Sukces'),
            'warning' => array('icon' => 'fa-warn', 'heading' => 'Uwaga'),
            'information' => array('icon' => 'fa-info', 'heading' => 'Informacja'),
            'error' => array('icon' => 'fa-error', 'heading' => 'Błąd')
        );

        foreach ($flash as $sm) {
            $html .= '<div class="' . $sm[0] . ' form">' .
                '<h4><i class="fa ' . $possible[$sm[0]]['icon'] . '"></i> ' . $possible[$sm[0]]['heading'] . '</h4>' .
                '<p>' . $sm[1] . '</p>' .
                '</div>';
        }

        return $html;
    }

    public static function url($name, $params = [], $absolute = false)
    {
        $route_params = array_merge([ $name ], $params);
        return (!$absolute) ? Yii::$app->urlManager->createUrl($route_params) : Yii::$app->urlManager->createAbsoluteUrl($route_params);
    }

    public static function currPageUrl()
    {
        return Yii::$app->urlManager->createAbsoluteUrl(Yii::$app->request->url);
    }

    public static function adminName()
    {
        $auth = Yii::$app->user;
        $identity = $auth->identity;

        if ($auth->isGuest){
            return 'Gość';
        } else {
            return $identity->realname;
        }
    }

    public static function adminPhoto()
    {
        $auth = Yii::$app->user;

        if ($auth->isGuest){
            return '/a/imgs/avatar.jpg';
        } else {
            $aid = self::userId();

            $aDB = new GAdmin();
            $cdata = $aDB->fetchRowByPrimary($aid, 'avatar');
            if (isset($cdata['avatar']) && !empty($cdata['avatar'])){
                return Convert::imageCache2('/uploads/photos/', $cdata['avatar'], '150x150');
            } else {
                return '/a/imgs/avatar.jpg';
            }
        }
    }

    public static function adminData()
    {
        $auth = Yii::$app->user;

        if ($auth->isGuest){
            return [
                'name' => 'Gość',
                'photo' => '/a/imgs/avatar.jpg',
                'banner' => false
            ];
        } else {
            $identity = $auth->identity;
            $ret = ['name' => $identity->realname];

            $aid = self::userId();

            $aDB = new GAdmin();
            $cdata = $aDB->fetchRowByPrimary($aid, 'avatar, banner');

            $ret['banner'] = ($cdata['banner'] == 1) ? true : false;

            if (isset($cdata['avatar']) && !empty($cdata['avatar'])){
                $ret['photo'] = Convert::imageCache2('/uploads/photos/', $cdata['avatar'], '150x150');
            } else {
                $ret['photo'] = '/a/imgs/avatar.jpg';
            }

            return $ret;
        }
    }

    public static function userId()
    {
        $auth = Yii::$app->user;
        $identity = $auth->identity;

        if ($auth->isGuest){
            return 0;
        } else {
            return $identity->id;
        }
    }

    public static function userName()
    {
        $auth = Yii::$app->user;

        if ($auth->isGuest){
            return 'Gość';
        } else {
            return (isset($auth->identity->firstname) && !empty($auth->identity->firstname)) ? $auth->identity->firstname : 'Użytkownik';
        }
    }

    public static function userSidebars()
    {
        $auth = Yii::$app->user;

        if ($auth->isGuest){
            return false;
        } else {
            $aid = self::userId();
            $gsbDB = new GSidebarsTable();

            return $gsbDB->getSettings($aid);
        }
    }

    public static function getCsrfToken()
    {
        return Yii::$app->request->getCsrfToken();
    }

    public static function saveSEO($name, $params)
    {
        $post = Yii::$app->request->post('seo', false);

        if (!empty($post)) {
            $data = array();
            $data['name'] = $name;
            $data['params'] = json_encode($params);
            $data['title'] = $post['title'];
            $data['description'] = $post['description'];
            $data['keywords'] = $post['keywords'];

            $gsDB = new GSeoTable();
            $current = $gsDB->getByParams($data['name'], $data['params']);

            if ($current) {
                $gsDB->save($data, array($data['name'], $data['params']));
            } else {
                $gsDB->save($data);
            }
        }
    }

    public static function getSEO($name, $params)
    {
        $gsDB = new GSeoTable();
        $current = $gsDB->getByParams($name, json_encode($params));

        $data = array();

        if ($current) {
            $data = $current;
        }

        return $data;
    }

    // Returns a file size limit in bytes based on the PHP upload_max_filesize
    // and post_max_size
    public static function file_upload_max_size() {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $max_size = self::parse_size(ini_get('post_max_size'));

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = self::parse_size(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }
        return $max_size;
    }

    public static function parse_size($size) {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }
        else {
            return round($size);
        }
    }

    public static function getYiiParam($name) {
        return (isset(Yii::$app->params[$name])) ? Yii::$app->params[$name] : '';
    }

    public static function createDirectoryStructure($dir) {
        $arr = explode('/', $dir);
        $toCreate = '';

        foreach ($arr as $item) {
            $toCreate .= $item . '/';

            if (!is_dir(MAIN_PATH . '/' . $toCreate)) {
                mkdir(MAIN_PATH . '/' . $toCreate);
            }
        }

        return MAIN_PATH . '/' . $toCreate;
    }

    public static function photoUploadHandler($keyFilesArr) {
        $ret = [];

        if (!isset($_FILES[$keyFilesArr])) {
            return $ret;
        }

        $output_dir = System::createDirectoryStructure('uploads/photos');

        if (!is_array($_FILES[$keyFilesArr]["name"])) //single file
        {
            $fileName = $_FILES[$keyFilesArr]["name"];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $key = Stat::keyGen(20);
            $newfilename = $key . '.' . $ext;
            move_uploaded_file($_FILES[$keyFilesArr]["tmp_name"], $output_dir . $newfilename);
            if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                $ret[] = array('name' => $newfilename, 'urld' => urlencode($newfilename));
            }
        } else  //Multiple files, file[]
        {
            $fileCount = count($_FILES[$keyFilesArr]["name"]);
            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = $_FILES[$keyFilesArr]["name"][$i];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $key = Stat::keyGen(20);
                $newfilename = $key . '.' . $ext;
                move_uploaded_file($_FILES[$keyFilesArr]["tmp_name"][$i], $output_dir . $newfilename);

                if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                    $ret[] = array('name' => $newfilename, 'urld' => urlencode($newfilename));
                }
            }

        }

        return $ret;
    }

    public static function filesUploadHandler($keyFilesArr) {
        $ret = [];

        if (!isset($_FILES[$keyFilesArr])) {
            return $ret;
        }

        $output_dir = System::createDirectoryStructure('uploads/files');

        if (!is_array($_FILES[$keyFilesArr]["name"])) //single file
        {
            $fileName = $_FILES[$keyFilesArr]["name"];
            $ext = pathinfo($fileName, PATHINFO_EXTENSION);
            $key = Stat::keyGen(20);
            $newfilename = $key . '.' . $ext;
            move_uploaded_file($_FILES[$keyFilesArr]["tmp_name"], $output_dir . $newfilename);
            if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                $ret[] = [
                    'oryginal' => $fileName,
                    'new' => $newfilename
                ];
            }
        } else  //Multiple files, file[]
        {
            $fileCount = count($_FILES[$keyFilesArr]["name"]);
            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = $_FILES[$keyFilesArr]["name"][$i];
                $ext = pathinfo($fileName, PATHINFO_EXTENSION);
                $key = Stat::keyGen(20);
                $newfilename = $key . '.' . $ext;
                move_uploaded_file($_FILES[$keyFilesArr]["tmp_name"][$i], $output_dir . $newfilename);

                if (file_exists($output_dir . $newfilename) && is_readable($output_dir . $newfilename)) {
                    $ret[] = [
                        'oryginal' => $fileName,
                        'new' => $newfilename
                    ];
                }
            }

        }

        return $ret;
    }

    public static function parseUserExtraData($jsonDecoded) {
        $province = [
            'dolnoslaskie' => 'Dolnośląskie',
            'kujawsko_pomorskie' => 'Kujawsko-Pomorskie',
            'lubelskie' => 'Lubelskie',
            'lubuskie' => 'Lubuskie',
            'lodzkie' => 'Łódzkie',
            'malopolskie' => 'Małopolskie',
            'mazowieckie' => 'Mazowieckie',
            'opolskie' => 'Opolskie',
            'podkarpackie' => 'Podkarpackie',
            'podlaskie' => 'Podlaskie',
            'pomorskie' => 'Pomorskie',
            'slaskie' => 'Śląskie',
            'swietokrzyskie' => 'Świętokrzyskie',
            'warminsko_mazurskie' => 'Warmińsko-Mazurskie',
            'wielkopolskie' => 'Wielkopolskie',
            'zachodniopomorskie' => 'Zachodniopomorskie',
        ];
        $executorSkills = [
            'cutting_trees' => 'Wycinka drzew/ek',
            'removing_bushes' => 'Powierzchniowe usuwanie krzaków (mulczowanie)',
            'removing_selfsows' => 'Powierzchniowe usuwanie samosiewów (mulczowanie)',
            'removing_branches' => 'Usuwanie korzeni (rekultywacja lub punktowe frezowanie pni)',
            'removing_orchard' => 'Likwidacja sadu (mulczowanie + rekultywacja)',
            'removing_plantation' => 'Likwidacja plantacji. (mulczowanie + rekultywacja)',
            'removing_sticks' => 'Likwidacja stosów gałęzi (mulczowanie)',
            'mineralization' => 'Mineralizacja pasów ppoż.',
            'creating_belts' => 'Tworzenie pasów ppoż. (rekultywacja / frezowanie pni)',
            'other_work' => 'Inne',
            'max_trunk_diameter_mulcz' => 'Maksymalna średnica pnia do mulczowania – w cm.',
            'max_trunk_diameter_frez' => 'Maksymalna średnica pnia do frezowania (rekultywacji) – w cm.',
            'max_frez_depth' => 'Maksymalna głębokość frezowania / rekultywacji.',
        ];
        $provinces = [];

        if (!empty($jsonDecoded['area_province'])) {
            foreach ($jsonDecoded['area_province'] as $k => $v) {
                if ($v) {
                    $provinces[] = $province[$k];
                }
            }
        }

        $executorSkillsUsers = [
            'selected' => [],
            'txt' => []
        ];

        if (!empty($jsonDecoded['executor_skills'])) {
            foreach ($jsonDecoded['executor_skills'] as $k => $v) {
                if (gettype($v) === 'boolean') {
                    if ($v) {
                        $executorSkillsUsers['selected'][] = $executorSkills[$k];
                        continue;
                    }
                }

                $executorSkillsUsers['txt'][$executorSkills[$k]] = $v;
            }
        }

        return [
            'provinces' => $provinces,
            'executorSkillsUsers' => $executorSkillsUsers,
        ];
    }
}
