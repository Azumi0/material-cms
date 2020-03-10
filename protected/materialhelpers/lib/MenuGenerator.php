<?php

namespace materialhelpers;

use Yii;

class MenuGenerator
{
    public static $module = null;
    public static $controller = null;
    public static $action = null;
    public static $params = null;
    public static $role = null;
    protected static $menu = array();
    protected static $items = array();

    public static function get($currbyperm = '') {
        self::init();
        return self::prepareItems($currbyperm);
    }

    protected static function init() {
        $menu = $items = array();

        if (YII_DEBUG || !is_file(PROTECTED_PATH . '/runtime/materialhelpers/menu.php')) {
            $fh = fopen(PROTECTED_PATH . '/runtime/materialhelpers/menu.php', 'w');
            fwrite($fh, "<?php\n\n");

            $dh = opendir(PROTECTED_PATH . '/modules/');
            while ($dir = readdir($dh)) {
                if ($dir != '.' && $dir != '..') {
                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/menu.php')) { fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/menu.php')) . "\n"); }
                    if (is_dir(PROTECTED_PATH . '/modules/' . $dir . '/modules/')) {
                        $dhs = opendir(PROTECTED_PATH . '/modules/' . $dir . '/modules/');
                        while ($dirs = readdir($dhs)) {
                            if ($dirs != '.' && $dirs != '..') {
                                if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/menu.php')) { fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/menu.php')) . "\n"); }
                            }
                        }
                        closedir($dhs);
                    }
                }
            }
            closedir($dh);

            fwrite($fh, "\n?>");
            fclose($fh);

            include PROTECTED_PATH . '/runtime/materialhelpers/menu.php';
        } else {
            include PROTECTED_PATH . '/runtime/materialhelpers/menu.php';
        }

        self::$menu = $menu;
        self::$items = $items;
    }

    protected static function prepareItems($currbyperm)
    {
        $items = array();
        $auth = Yii::$app->user;

        foreach (self::$items as $menu => $priorieties) {
            ksort($priorieties);

            foreach ($priorieties as $sitems) {
                foreach ($sitems as $item) {
                    $items[$menu][] = $item;
                }
            }
        }

        self::$items = $items;

        ksort(self::$menu);

        $items = array();
        foreach (self::$menu as $m) {
            ksort($m);

            foreach ($m as $name => $item) {
                if (sizeof(self::$items[$name]) > 1) {
                    $status = 'submenu-trigger';
                    $avaible = array();
                    foreach (self::$items[$name] as $k => $sitem) {
                        if (isset($sitem['perms']) && !empty($sitem['perms'])) {
                            if ($auth->can($sitem['perms']) || self::$role == 'su') {
                                $action = self::$action;

                                if (preg_match('#admin/' . self::$module . '/' . $action . '$#', $sitem['url'][0]) || (strstr(self::$module, '/') && preg_match('#admin/sub/' . self::$module . '/' . self::$action . '#', $sitem['url'][0])) || $currbyperm == $sitem['perms']) {
                                    $status .= ' active';
                                    $sitem['linkOptions']['class'] = 'sactive';
                                }
                                $avaible[$k] = $sitem;
                            }
                        }
                    }
                    if (sizeof($avaible) > 1) {
                        $item['linkOptions']['class'] = $status;
                        $item['items'] = $avaible;
                    } else {
                        $item['linkOptions']['class'] = (preg_match('#admin/' . self::$module . '/#', $item['url']) || preg_match('#admin/sub/' . self::$module . '/#', $item['url']) || $currbyperm == $item['perms']) ? 'active' : 'inactive';
                    }
                } else {
                    $tic = '';
                    if ($currbyperm == $item['perms']){
                        $tic = 'active';
                    } else {
                        $tic = 'inactive';
                    }
                    $item['linkOptions']['class'] = $tic;
                }


                if (isset($item['perms']) && !empty($item['perms'])) {
                    if ($auth->can($item['perms']) || self::$role == 'su') {
                        $items[] = $item;
                    }
                }
            }
        }

        return $items;
    }
}