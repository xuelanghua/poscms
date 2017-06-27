<?php

class ucclient_dbi {

    var $querynum = 0;
    var $link;
    var $histories;

    var $dbhost;
    var $dbuser;
    var $dbpw;
    var $dbcharset;
    var $pconnect;
    var $tablepre;
    var $time;

    var $goneaway = 5;

    function connect($dbhost, $dbuser, $dbpw, $dbname = '', $dbcharset = '', $pconnect = 0, $tablepre='', $time = 0) {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpw = $dbpw;
        $this->dbname = $dbname;
        $this->dbcharset = $dbcharset;
        $this->pconnect = $pconnect;
        $this->tablepre = $tablepre;
        $this->time = $time;

        if(!$this->db = new mysqli($dbhost, $dbuser, $dbpw, $dbname)) {
            $this->halt('Can not connect to MySQLi server');
        }

        if($this->version() > '4.1') {
            if($dbcharset) {
                $this->db->set_charset($dbcharset);
            }

            if($this->version() > '5.0.1') {
                $this->db->query("SET sql_mode=''");
            }
        }
    }

    function fetch_array($query, $result_type = MYSQLI_ASSOC) {
        return $query ? $query->fetch_array($result_type) : null;
    }

    function result_first($sql) {
        $query = $this->query($sql);
        return $this->result($query, 0);
    }

    function fetch_first($sql) {
        $query = $this->query($sql);
        return $this->fetch_array($query);
    }

    function fetch_all($sql, $id = '') {
        $arr = array();
        $query = $this->query($sql);
        while($data = $this->fetch_array($query)) {
            $id ? $arr[$data[$id]] = $data : $arr[] = $data;
        }
        return $arr;
    }

    function cache_gc() {
        $this->query("DELETE FROM {$this->tablepre}sqlcaches WHERE expiry<$this->time");
    }

    function query($sql, $type = '', $cachetime = FALSE) {
        $resultmode = $type == 'UNBUFFERED' ? MYSQLI_USE_RESULT : MYSQLI_STORE_RESULT;
        if(!($query = $this->db->query($sql, $resultmode)) && $type != 'SILENT') {
            $this->halt('MySQLi Query Error', $sql);
        }
        $this->querynum++;
        $this->histories[] = $sql;
        return $query;
    }

    function affected_rows() {
        return $this->db->affected_rows;
    }

    function error() {
        return (($this->db) ? $this->db->error : mysqli_error());
    }

    function errno() {
        return intval(($this->db) ? $this->db->errno : mysqli_errno());
    }

    function result($query, $row) {
        if(!$query || $query->num_rows == 0) {
            return null;
        }
        $query->data_seek($row);
        $assocs = $query->fetch_row();
        return $assocs[0];
    }

    function num_rows($query) {
        $query = $query ? $query->num_rows : 0;
        return $query;
    }

    function num_fields($query) {
        return $query ? $query->field_count : 0;
    }

    function free_result($query) {
        return $query ? $query->free() : false;
    }

    function insert_id() {
        return ($id = $this->db->insert_id) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
    }

    function fetch_row($query) {
        $query = $query ? $query->fetch_row() : null;
        return $query;
    }

    function fetch_fields($query) {
        return $query ? $query->fetch_field() : null;
    }

    function version() {
        return $this->db->server_info;
    }

    function escape_string($str) {
        return $this->db->escape_string($str);
    }

    function close() {
        return $this->db->close();
    }

    function halt($message = '', $sql = '') {
        $error = $this->error();
        $errorno = $this->errno();
        if($errorno == 2006 && $this->goneaway-- > 0) {
            $this->connect($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname, $this->dbcharset, $this->pconnect, $this->tablepre, $this->time);
            $this->query($sql);
        } else {
            $s = '';
            if($message) {
                $s = "<b>UCenter info:</b> $message<br />";
            }
            if($sql) {
                $s .= '<b>SQL:</b>'.htmlspecialchars($sql).'<br />';
            }
            $s .= '<b>Error:</b>'.$error.'<br />';
            $s .= '<b>Errno:</b>'.$errorno.'<br />';
            $s = str_replace(UC_DBTABLEPRE, '[Table]', $s);
            exit($s);
        }
    }
}

?>