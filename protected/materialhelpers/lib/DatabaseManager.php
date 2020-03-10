<?php

namespace materialhelpers;

use yii\db\Query;

class DatabaseManager
{
    public $db = null;
    public $orgtable = null;
    public $lang = false;
    public $primary = null;

    public function __construct()
    {
        $this->db = \Yii::$app->db;

        if (!$this->lang) { $this->lang = \Yii::$app->language . '_'; }
        $this->orgtable = $this->table;
        $this->table = $this->lang . $this->table;
    }

    public function setLang($lang)
    {
        $this->table = $lang . $this->orgtable;
    }

    public function setPrimary($attr)
    {
        $this->primary = $attr;
    }

    public function dump($var)
    {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
    }

    public function fetchRowByPrimary($id, $columns = '*')
    {
        $query = new Query();
        $query->select($columns);
        $query->from($this->table);

        if (!is_array($id)) {
            $query->where($this->primary . '= :id', array(':id' => $id));
        } else {
            $cond = array();
            $ids = array();

            $x = 1;
            foreach ($this->primary as $value) {
                $cond[] = $value . ' = :id' . $x;
                $ids[':id' . $x] = $id[$x - 1];
                $x++;
            }

            $query->where(implode(' and ', $cond), $ids);
        }

        return $query->one();
    }

    public function fetchAll($columns = '*')
    {
        $query = new Query();
        $query->select($columns);
        $query->from($this->table);
        return $query->all();
    }

    public function fetchRow($columns = '*')
    {
        $query = new Query();
        $query->select($columns);
        $query->from($this->table);
        return $query->one();
    }

    public function flag($id, $name)
    {
        $query = new Query();
        $query->select($name);
        $query->from($this->table);
        if (!is_array($id)) {
            $query->where($this->primary . '= :id', array(':id' => $id));
        } else {
            $cond = array();
            $ids = array();

            $x = 1;
            foreach ($this->primary as $value) {
                $cond[] = $value . ' = :id' . $x;
                $ids[':id' . $x] = $id[$x - 1];
                $x++;
            }

            $query->where(implode(' and ', $cond), $ids);
        }
//        $query->where('id = :id', array(':id' => $id));
        $current = $query->one();

        if ($current[$name]) {
            $flag = 0;
        } else {
            $flag = 1;
        }

        $this->save(array($name => $flag), $id);

        return $flag;
    }

    public function delete($id, $named = array(), $where = false)
    {
        $cmd = $this->db->createCommand();

        if ($id) {
            if (!is_array($id)) {
                $cmd->delete($this->table, $this->primary . ' = :id', array(':id' => $id))->execute();
            } else {
                $cond = array();
                $ids = array();

                $x = 1;
                foreach ($this->primary as $value) {
                    $cond[] = $value . ' = :id' . $x;
                    $ids[':id' . $x] = $id[$x - 1];
                    $x++;
                }

                $cmd->delete($this->table, implode(' and ', $cond), $ids)->execute();
            }
        } elseif ($named) {
            $cond = array();
            $params = array();

            foreach ($named as $attr => $param) {
                $cond[] = $attr . ' = :' . $attr;
                $params[':' . $attr] = $param;
            }

            $cmd->delete($this->table, implode(' and ', $cond), $params)->execute();
        } elseif ($where) {
            $cmd->delete($this->table, $where)->execute();
        }

        $this->db->createCommand('ALTER TABLE ' . $this->table . ' AUTO_INCREMENT = 1')->execute(); // if delete release id no
    }

    public function truncate()
    {
        $this->db->createCommand('TRUNCATE  ' . $this->table)->execute(); // if delete release id no
    }

    public function save($data, $id = false, $named = array())
    {
        $cmd = $this->db->createCommand();

        if (!$id) {
            $cmd->insert($this->table, $data)->execute();
            return $this->db->lastInsertID;
        } else {
            if ($id) {
                if (!is_array($id)) {
                    $cmd->update($this->table, $data, $this->primary . ' = :id', array(':id' => $id))->execute();

                    return $id;
                } else {
                    $cond = array();
                    $ids = array();

                    $x = 1;
                    foreach ($this->primary as $value) {
                        $cond[] = $value . ' = :id' . $x;
                        $ids[':id' . $x] = $id[$x - 1];
                        $x++;
                    }

                    $cmd->update($this->table, $data, implode(' and ', $cond), $ids)->execute();

                    return $ids;
                }
            } elseif ($named) {
                $cond = array();
                $params = array();

                foreach ($named as $attr => $param) {
                    $cond[] = $attr . ' = :' . $attr;
                    $params[':' . $attr] = $param;
                }

                $cmd->update($this->table, $data, implode(' and ', $cond), $params)->execute();
            }
        }
    }

    public function updateAll($data, $named = array())
    {
        $cmd = $this->db->createCommand();
        if ($named) {
            $cond = array();
            $params = array();

            foreach ($named as $attr => $param) {
                $cond[] = $attr . ' ' . $param[1] . ' :' . $attr;
                $params[':' . $attr] = $param[0];
            }

            $cmd->update($this->table, $data, implode(' and ', $cond), $params)->execute();
        } else {
            $cmd->update($this->table, $data)->execute();
        }
    }

    public function prepareSearch($fields, $search)
    {
        $components = array();
        $params = array();

        foreach ($fields as $column => $operation) {
            $char = '_';
            $unicolname = preg_replace("/[^a-zA-Z0-9_]/", " ", $column);
            $unicolname = trim($unicolname);
            $unicolname = str_replace(" ", $char, $unicolname);
            while (preg_match("/$char$char/", $unicolname)) {
                $unicolname = str_replace("$char$char", "$char", $unicolname);
            }
            
            $components[] = $column . ' ' . $operation . ' :' . $unicolname;

            switch ($operation) {
                case 'LIKE':
                    $params[':' . $unicolname] = '%' . strip_tags(trim($search)) . '%';
                    break;
                default:
                    $params[':' . $unicolname] = strip_tags(trim($search));
                    break;
            }
        }

        $condition = '(' . implode(' OR ', $components) . ')';

        return array('condition' => $condition, 'params' => $params);
    }

    public function applyAliasToFields($fields, $alias)
    {
        $aliased = [];

        foreach ($fields as $field => $value) {
            $aliased["$alias.$field"] = $value;
        }

        return $aliased;
    }
}