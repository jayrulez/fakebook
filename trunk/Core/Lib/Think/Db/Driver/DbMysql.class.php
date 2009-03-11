<?php

define('CLIENT_MULTI_RESULTS', 131072);

class DbMysql extends Db
{
    public function __construct($config=''){
        if ( !extension_loaded('mysql') ) {
            throw_exception(L('_NOT_SUPPERT_').':mysql');
        }
        if(!empty($config)) {
            $this->config   =   $config;
        }
    }

    public function connect($config='',$linkNum=0) {
        if ( !isset($this->linkID[$linkNum]) ) {
            if(empty($config))  $config =   $this->config;
            $host = $config['hostname'].($config['hostport']?":{$config['hostport']}":'');
            if($this->pconnect) {
                $this->linkID[$linkNum] = mysql_pconnect( $host, $config['username'], $config['password'],CLIENT_MULTI_RESULTS);
            }else{
                $this->linkID[$linkNum] = mysql_connect( $host, $config['username'], $config['password'],true,CLIENT_MULTI_RESULTS);
            }
            if ( !$this->linkID[$linkNum]) {
                throw_exception(mysql_error());
            }
            if (!empty($config['database']) && !mysql_select_db($config['database'], $this->linkID[$linkNum]) ) {
                throw_exception(mysql_error());
            }
            $this->dbVersion = mysql_get_server_info($this->linkID[$linkNum]);
            if ($this->dbVersion >= "4.1") {
                mysql_query("SET NAMES '".C('DB_CHARSET')."'", $this->linkID[$linkNum]);
            }
            if($this->dbVersion >'5.0.1'){
                mysql_query("SET sql_mode=''",$this->linkID[$linkNum]);
            }
            $this->connected    =   true;
            if(1 != C('DB_DEPLOY_TYPE')) unset($this->config);
        }
        return $this->linkID[$linkNum];
    }

    public function free() {
        @mysql_free_result($this->queryID);
        $this->queryID = 0;
    }

    protected function _query($str='') {
        $this->initConnect(false);
        if ( !$this->_linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            $this->startTrans();
        }else {
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->Q(1);
        $this->queryID = mysql_query($this->queryStr, $this->_linkID);
        $this->debug();
        if ( !$this->queryID ) {
            if ( $this->debug || C('DEBUG_MODE'))
                throw_exception($this->error());
            else
                return false;
        } else {
            $this->numRows = mysql_num_rows($this->queryID);
            //$this->numCols = mysql_num_fields($this->queryID);
            $this->resultSet = $this->getAll();
            return $this->resultSet;
        }
    }

    protected function _execute($str='') {
        $this->initConnect(true);
        if ( !$this->_linkID ) return false;
        if ( $str != '' ) $this->queryStr = $str;
        if (!$this->autoCommit && $this->isMainIps($this->queryStr)) {
            $this->startTrans();
        }else {
            if ( $this->queryID ) {    $this->free();    }
        }
        $this->W(1);
        $result =   mysql_query($this->queryStr, $this->_linkID) ;
        $this->debug();
        if ( false === $result) {
            if ( $this->debug || C('DEBUG_MODE'))
                throw_exception($this->error());
            else
                return false;
        } else {
            $this->numRows = mysql_affected_rows($this->_linkID);
            $this->lastInsID = mysql_insert_id($this->_linkID);
            return $this->numRows;
        }
    }

    public function startTrans() {
        $this->initConnect(true);
        if ( !$this->_linkID ) return false;
        if ($this->transTimes == 0) {
            mysql_query('START TRANSACTION', $this->_linkID);
        }
        $this->transTimes++;
        return ;
    }

    public function commit()
    {
        if ($this->transTimes > 0) {
            $result = mysql_query('COMMIT', $this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return false;
            }
        }
        return true;
    }

    public function rollback()
    {
        if ($this->transTimes > 0) {
            $result = mysql_query('ROLLBACK', $this->_linkID);
            $this->transTimes = 0;
            if(!$result){
                throw_exception($this->error());
                return false;
            }
        }
        return true;
    }

    public function next() {
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        if($this->resultType== DATA_TYPE_OBJ){
            $this->result = @mysql_fetch_object($this->queryID);
            $stat = is_object($this->result);
        }else{
            $this->result = @mysql_fetch_assoc($this->queryID);
            $stat = is_array($this->result);
        }
        return $stat;
    }

    public function getRow($sql = null,$seek=0) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        if($this->numRows >0) {
            if(mysql_data_seek($this->queryID,$seek)){
                if($this->resultType== DATA_TYPE_OBJ){
                    $result = mysql_fetch_object($this->queryID);
                }else{
                    $result = mysql_fetch_assoc($this->queryID);
                }
            }
            return $result;
        }else {
            return false;
        }
    }

    public function getAll($sql = null,$resultType=null) {
        if (!empty($sql)) $this->_query($sql);
        if ( !$this->queryID ) {
            throw_exception($this->error());
            return false;
        }
        $result = array();
        if($this->numRows >0) {
            if(is_null($resultType)){ $resultType   =  $this->resultType ; }
            $fun    =   $resultType== DATA_TYPE_OBJ?'mysql_fetch_object':'mysql_fetch_assoc';
            while($row = $fun($this->queryID)){
                $result[]   =   $row;
            }
            mysql_data_seek($this->queryID,0);
        }
        return $result;
    }

    public function getFields($tableName) {
        $result =   $this->_query('SHOW COLUMNS FROM '.$tableName);
        $info   =   array();
        foreach ($result as $key => $val) {
            if(is_object($val)) {
                $val    =   get_object_vars($val);
            }
            $info[$val['Field']] = array(
                'name'    => $val['Field'],
                'type'    => $val['Type'],
                'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                'default' => $val['Default'],
                'primary' => (strtolower($val['Key']) == 'pri'),
                'autoInc' => (strtolower($val['Extra']) == 'auto_increment'),
            );
        }
        return $info;
    }

    public function getTables($dbName='') {
        if(!empty($dbName)) {
           $sql    = 'SHOW TABLES FROM '.$dbName;
        }else{
           $sql    = 'SHOW TABLES ';
        }
        $result =   $this->_query($sql);
        $info   =   array();
        foreach ($result as $key => $val) {
            $info[$key] = current($val);
        }
        return $info;
    }

    public function close() {
        if (!empty($this->queryID))
            mysql_free_result($this->queryID);
        if (!mysql_close($this->_linkID)){
            throw_exception($this->error());
        }
        $this->_linkID = 0;
    }

    public function error() {
        $this->error = mysql_error($this->_linkID);
        if($this->queryStr!=''){
            $this->error .= "\n ".L('_SQL_STATEMENT_')." : ".$this->queryStr;
        }
        return $this->error;
    }

    public function escape_string($str) {
        return mysql_escape_string($str);
    }

	public function limit($limit) {
        $limitStr    = '';
        if(!empty($limit)) {
            $limitStr .= ' LIMIT '.$limit.' ';
        }
        return $limitStr;
    }
}

?>