<?php
namespace eBizIndia;

class Settings {
    private $rec_id;

    public function __construct(?int $rec_id=null) {
        $this->rec_id = $rec_id;
    }

    public function getDetails() {
        if(empty($this->rec_id))
            return false;
        $options = [];
        $options['filters'] = [
            [ 'field' => 'id', 'type' => 'EQUAL', 'value' => $this->rec_id ]
        ];
        return self::getList($options);
    }

    public static function getList($options = []) {
        $data = [];
        $fields_mapper = [];

        $fields_mapper['*'] = "s.*";
        $fields_mapper['recordcount'] = 'count(1)';
        $fields_mapper['setting_id'] = "s.setting_id";
        $fields_mapper['setting_name'] = "s.setting_name";
        $fields_mapper['setting_value'] = "s.setting_value";
        $fields_mapper['setting_description'] = "s.setting_description";
        
        $where_clause = [];
        $str_params_to_bind = [];
        $int_params_to_bind = [];

        if (array_key_exists('filters', $options) && is_array($options['filters'])) {
            $field_counter = 0;
            foreach ($options['filters'] as $filter) {
                ++$field_counter;
                switch ($filter['field']) {
                    case 'id':
                        $id = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                        $where_clause[] = $fields_mapper[$filter['field']] . ' = :whr' . $field_counter . '_id';
                        $int_params_to_bind[':whr' . $field_counter . '_id'] = $id;
                        break;
                    
                    case 'setting_name':
                        $value = (is_array($filter['value'])) ? $filter['value'][0] : $filter['value'];
                        $where_clause[] = $fields_mapper[$filter['field']] . " = :whr" . $field_counter . "_value";
                        $str_params_to_bind[':whr' . $field_counter . '_value'] = $value;
                        break;
                }
            }
        }

        $select_string = $fields_mapper['*'];

        if (array_key_exists('fieldstofetch', $options) && is_array($options['fieldstofetch'])) {
            $fields_to_fetch = [];
            foreach ($options['fieldstofetch'] as $field) {
                if (array_key_exists($field, $fields_mapper)) {
                    $fields_to_fetch[] = $fields_mapper[$field] . (($field != '*') ? ' AS ' . $field : '');
                }
            }
            if (!empty($fields_to_fetch)) {
                $select_string = implode(', ', $fields_to_fetch);
            }
        }

        $where_clause_string = '';
        if (!empty($where_clause)) {
            $where_clause_string = ' WHERE ' . implode(' AND ', $where_clause);
        }

        // Updated SQL query with correct table name format
        $sql = "SELECT $select_string FROM " . CONST_TBL_PREFIX . "settings s $where_clause_string";

        

        try {
            $pdo_stmt_obj = PDOConn::query($sql, $str_params_to_bind, $int_params_to_bind);

            if (array_key_exists('resourceonly', $options) && $options['resourceonly']) {
                return $pdo_stmt_obj;
            }

            while ($row = $pdo_stmt_obj->fetch(\PDO::FETCH_ASSOC)) {
                $data[] = $row;
            }
            return $data;

        } catch (\Exception $e) {
            ErrorHandler::logError(['function' => __METHOD__, 'sql' => $sql, 'params' => ['str' => $str_params_to_bind, 'int' => $int_params_to_bind]], $e);
            return false;
        }
    }

    public function saveDetails($data, $id = '') {
        $str_data = $int_data = [];
        $table = CONST_TBL_PREFIX . 'settings'; // Removed backticks
        
        if (is_array($id) && !empty($id)) {
            $type = 'update';
            $sql = "UPDATE $table SET ";
            $place_holders = [];
            $id_count = count($id);
            for ($i = 0; $i < $id_count; $i++) {
                $key = ":id_{$i}_";
                $place_holders[] = $key;
                $int_data[$key] = $id[$i];
            }
            $whereclause = " WHERE id IN (" . implode(",", $place_holders) . ")";
        } else if ($id != '') {
            $type = 'update';
            $sql = "UPDATE $table SET ";
            $int_data[':id'] = $id;
            $whereclause = " WHERE id=:id";
        } else {
            $type = 'insert';
            $sql = "INSERT INTO $table SET ";
            $whereclause = '';
        }

        $values = array();
        foreach ($data as $field => $value) {
            $key = ":$field";
            if ($value === '')
                $values[] = "$field=NULL";
            else {
                $values[] = "$field=$key";
                $str_data[$key] = $value;
            }
        }

        $sql .= implode(',', $values);
        $sql .= $whereclause;
        
        $error_details_to_log = [];
        $error_details_to_log['at'] = date('Y-m-d H:i:s');
        $error_details_to_log['function'] = __METHOD__;
        $error_details_to_log['type'] = $type;
        $error_details_to_log['data'] = $data;
        $error_details_to_log['id'] = $id;
        $error_details_to_log['sql'] = $sql;

        try {
            $stmt_obj = PDOConn::query($sql, $str_data, $int_data);
            $affetcedrows = $stmt_obj->rowCount();
            if ($type == 'insert')
                return PDOConn::lastInsertId();
            return true;
        } catch (\Exception $e) {
            if (!is_a($e, '\PDOStatement'))
                ErrorHandler::logError($error_details_to_log, $e);
            else
                ErrorHandler::logError($error_details_to_log);
            return false;
        }
    }

    public function updateDetails($data) {
        if ($this->rec_id == '')
            return false;
        return $this->saveDetails($data, $this->rec_id);
    }
}