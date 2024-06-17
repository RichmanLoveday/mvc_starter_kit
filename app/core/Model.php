<?php
/*
** Master Model
*
*/

declare(strict_types=1);

namespace app\core;

use app\core\Database;

class Model extends Database
{

    public array $errors = [];

    function __construct()
    {
        // Adding property exist to check for table propery of any model instantiated

        //echo get_class($this);
        //var_dump(property_exists($this, 'table'));

        if (!property_exists($this, 'table')) {
            $this->table = strtolower(get_class($this));
        }
    }

    public function where(string $column, string $value, string $order_by = 'DESC', $limit = 100, $offset = 0)
    {
        // check if class name is corect else get default table      

        $column = addslashes($column);
        $this->query("SELECT * FROM $this->table WHERE $column = :value ORDER BY id $order_by limit $limit offset $offset");

        $this->bind(':value', $value);
        $data = $this->resultSet();

        // Run function after finding where
        if (is_array($data)) {

            foreach ($data as $value) {
                if (property_exists($this, 'afterSelect')) {
                    foreach ($this->afterSelect as $func) {
                        $this->$func($value);
                    }
                }
            }
            return $data;
        }
        return false;
    }



    public function findAll(string $order_by = 'DESC', $limit = 100, $offset = 0)
    {
        // check if class name is corect else get default table
        $this->query("SELECT * FROM $this->table ORDER BY id $order_by limit $limit offset $offset");
        $data = $this->resultSet();  // Array of objects

        // Run function after Select
        if (is_array($data)) {
            foreach ($data as $value) {
                if (property_exists($this, 'afterSelect')) {
                    foreach ($this->afterSelect as $func) {
                        $this->$func($value);
                    }
                }
            }
            return $data;
        }

        return false;
    }


    public function insert(array $data, $table = null)
    {
        //show($data); echo $table; die;
        // Remove unwanted columns
        if (property_exists($this, 'allowedColumns')) {
            foreach ($data as $key => $column) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }


        // Run function before insert
        if (property_exists($this, 'beforeInsert')) {
            foreach ($this->beforeInsert as $func) {
                $data = $this->$func($data);
            }
        }


        $keys = array_keys($data);
        $columns = implode(',', $keys);
        $values = implode(',:', $keys);

        // check if class name is corect else get default table
        $db_table = $this->table;
        if (!$table == null) {
            $db_table = $table;
        }

        // show($columns); die;
        $this->query("INSERT INTO $db_table ($columns) VALUES(:$values)");
        return $this->execute($data);
    }


    public function update($id, array $data, $table = NULL)
    {
        // Remove unwanted columns
        if (property_exists($this, 'allowedColumns')) {
            foreach ($data as $key => $column) {
                if (!in_array($key, $this->allowedColumns)) {
                    unset($data[$key]);
                }
            }
        }


        // Run function before insert
        if (property_exists($this, 'beforeUpdate')) {
            foreach ($this->beforeUpdate as $func) {
                $data = $this->$func($data);
            }
        }

        //show($data); die;


        $data['id'] = $id;
        $str = '';

        foreach ($data as $key => $value) {
            $str .= $key . "=:" . $key . ",";
        }

        $str = trim($str, ',');


        // check if class name is corect else get default table
        $db_table = is_null($table) ? $this->table : $table;

        // show($data); die;

        // show($data);
        // echo $str; echo $db_table; die;

        $this->query("UPDATE $this->table SET $str WHERE id = :id");
        return $this->execute($data);
    }


    public function delete($id)
    {

        $this->query("DELETE FROM $this->table WHERE id = :id");
        $data['id'] = $id;
        return $this->execute($data);
    }


    // checking if row exist
    public function row_exist(array $data, $limit = null, $offset = null, string $table = NULL, string $result = null)
    {
        $str = ' ';
        foreach ($data as $key => $value) {
            $str .= $key . '=:' . $key . '&&';
        }

        $str = trim($str, '&&');

        // check if class name is corect else get default table
        $db_table = is_null($table) ? $this->table : $table;

        if (is_null($limit)) {
            $this->query("SELECT * FROM $db_table WHERE $str ORDER by id DESC");
        } else {
            $this->query("SELECT * FROM $db_table WHERE $str ORDER by id DESC limit $limit offset $offset");
        }


        $this->execute($data);

        if ($this->rowCount() > 0) {
            $result = is_null($result) ? $this->resultSet() : $this->$result();

            // Run function after Select
            if (is_array($result)) {
                foreach ($result as $value) {
                    if (property_exists($this, 'afterSelect')) {
                        foreach ($this->afterSelect as $func) {
                            $this->$func($value);
                        }
                    }
                }
                return $result;
            }
            return $result;
        }

        return false;
    }
}
