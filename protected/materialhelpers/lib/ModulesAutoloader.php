<?php

class ModulesAutoloader
{
    public static function get()
    {
        if (!file_exists(PROTECTED_PATH . '/runtime/materialhelpers')) {
            mkdir(PROTECTED_PATH . '/runtime/materialhelpers');
        }
        
        if (YII_DEBUG || !is_file(PROTECTED_PATH . '/runtime/materialhelpers/modules.php')) {
            $fh = fopen(PROTECTED_PATH . '/runtime/materialhelpers/modules.php', 'w');

            if ($fh !== false) {
                fwrite($fh, "<?php\n\n");

                $dh = opendir(PROTECTED_PATH . '/modules/');
                $x = 0;
                while ($dir = readdir($dh)) {
                    if ($dir != '.' && $dir != '..') {
                        //fwrite($fh, '$modules[] = \'' . $dir . "';\n");
                        if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/module.php')) {
                            fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/module.php')) . "\n");
                        }
                        if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/routes.php')) {
                            fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/routes.php')) . "\n");
                        }
                        if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/backend_routes.php')) {
                            fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/backend_routes.php')) . "\n");
                        }
                        if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/imports.php')) {
                            fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/imports.php')) . "\n");
                        }
                        if (is_dir(PROTECTED_PATH . '/modules/' . $dir . '/modules/')) {
                            $dhs = opendir(PROTECTED_PATH . '/modules/' . $dir . '/modules/');
                            while ($dirs = readdir($dhs)) {
                                if ($dirs != '.' && $dirs != '..') {
                                    //fwrite($fh, '$modules[\'' . $dir . '\'][\'modules\'][] = \'' . $dirs . "';\n");
                                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/module.php')) {
                                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/module.php')) . "\n");
                                    }
                                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/routes.php')) {
                                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/routes.php')) . "\n");
                                    }
                                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/backend_routes.php')) {
                                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/backend_routes.php')) . "\n");
                                    }
                                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/imports.php')) {
                                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/imports.php')) . "\n");
                                    }
                                }
                            }
                            closedir($dhs);
                        }
                        $x++;
                    }
                }

                fwrite($fh, "\n?>");
                fclose($fh);
            }
        }

        $module = array();
        $routes = array();
        $backend_routes = array();
        $imports = array();

        include PROTECTED_PATH . '/runtime/materialhelpers/modules.php';

        return array('module' => $module, 'routes' => $routes, 'backend_routes' => $backend_routes, 'imports' => $imports);
    }

    public static function getSubmodules($module)
    {
        $modules = array();

        include PROTECTED_PATH . '/runtime/materialhelpers/modules.php';

        return (isset($modules[$module])) ? $modules[$module]['modules'] : false;
    }

    public static function getFrontendACL()
    {
        $acl = array();

        if (YII_DEBUG || !is_file(PROTECTED_PATH . '/runtime/materialhelpers/frontacl.php')) {
            $fh = fopen(PROTECTED_PATH . '/runtime/materialhelpers/frontacl.php', 'w');
            fwrite($fh, "<?php\n\n");

            $dh = opendir(PROTECTED_PATH . '/modules/');
            $x = 0;
            while ($dir = readdir($dh)) {
                if ($dir != '.' && $dir != '..') {
                    fwrite($fh, '$modules[] = \'' . $dir . "';\n");
                    if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/params/frontacl.php')) {
                        fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/params/frontacl.php')) . "\n");
                    }
                    if (is_dir(PROTECTED_PATH . '/modules/' . $dir . '/modules/')) {
                        $dhs = opendir(PROTECTED_PATH . '/modules/' . $dir . '/modules/');
                        while ($dirs = readdir($dhs)) {
                            if ($dirs != '.' && $dirs != '..') {
                                fwrite($fh, '$modules[\'' . $dir . '\'][\'modules\'][] = \'' . $dirs . "';\n");
                                if (is_file(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/frontacl.php')) {
                                    fwrite($fh, str_replace(array("<?php", "?>", "\r", "\n"), "", file_get_contents(PROTECTED_PATH . '/modules/' . $dir . '/modules/' . $dirs . '/params/frontacl.php')) . "\n");
                                }
                            }
                        }
                        closedir($dhs);
                    }
                    $x++;
                }
            }

            fwrite($fh, "\n?>");
            fclose($fh);
        }

        include PROTECTED_PATH . '/runtime/materialhelpers/frontacl.php';

        return $acl;
    }
}