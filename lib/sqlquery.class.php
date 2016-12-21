<?php

class SQLQuery
{

    protected $_dbHandle;

    protected $_result;

    function connect($address, $user, $password, $name)
    {
        $this->_dbHandle = @mysql_connect($address, $user, $password);
        if ($this->_dbHandle != 0)
            return (mysql_select_db($name, $this->_dbHandle)) ? 1 : 0;
        else
            return 0;
    }

    function disconnect()
    {
        return (@mysql_close($this->_dbHandle) != 0) ? 1 : 0;
    }

    function select($id = NULL)
    {
        $query = 'select * from `' . $this->_table . '`';
        if (isset($id))
            $query .= ' where `id` = \'' . mysql_real_escape_string($id) . '\'';
        return $this->query($query, 1);
    }

    function query($query, $singleResult = 0)
    {
        $this->_result = mysql_query($query, $this->_dbHandle);
        
        if (preg_match('/select/i', $query)) {
            $result = [];
            $table = [];
            $field = [];
            $temp = [];
            $fieldcount = mysql_num_fields($this->_result);
            
            for ($i = 0; $i < $fieldcount; ++ $i) {
                array_push($table, mysql_field_table($this->_result, $i));
                array_push($table, mysql_field_name($this->_result, $i));
            }
            
            while ($row = mysql_fetch_row($this->_result)) {
                for ($i = 0; $i < $fieldcount; ++ $i) {
                    $table[$i] = trim(ucfirst($table[$i]), 's');
                    $temp[$table[$i]][$field[$i]] = $row[$i];
                }
                
                if ($singleResult == 1) {
                    mysql_free_result($this->_result);
                    return $temp;
                }
                
                array_push($result, $temp);
            }
            
            mysql_free_result($this->_result);
            return ($result);
        }
    }

    function getRowCount()
    {
        return mysql_num_rows($this->_result);
    }

    function freeResult()
    {
        mysql_free_result($this->_result);
    }

    function getError()
    {
        return mysql_error($this->_dbHandle);
    }
}