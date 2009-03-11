<?php

class Db extends Base
{
    protected $dbType           = null;

    protected $dbVersion        = null;

    protected $autoFree         = false;

    protected $autoCommit     = true;

    protected $debug             = false;

    protected $pconnect         = false;

    protected $queryStr          = '';

    protected $result              = null;

    protected $resultSet         = null;

    public $resultType            = DATA_TYPE_ARRAY;

    protected $fields              = null;

    protected $lastInsID         = null;

    protected $numRows        = 0;

    protected $numCols          = 0;

    protected $transTimes      = 0;

    protected $error              = '';

    protected $linkID              = array();

    protected $_linkID            =   null;

    protected $queryID          = null;

    protected $connected       = false;

    protected $config             = '';

    protected $comparison      = array('eq'=>'=','neq'=>'!=','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE','between'=>'BETWEEN','notnull'=>'IS NOT NULL','null'=>'IS NULL');

    protected $beginTime;

	public $tableName = NULL;

    function __construct($config=''){
        $this->resultType = C('DATA_RESULT_TYPE');
        return $this->factory($config);
    }

    public static function getInstance()
    {
        $args = func_get_args();
        return get_instance_of(__CLASS__,'factory',$args);
    }

    public function &factory($db_config='')
    {
        $db_config = $this->parseConfig($db_config);
        if(empty($db_config['dbms'])) {
            throw_exception(L('_NO_DB_CONFIG_'));
        }
        $this->dbType = ucwords(strtolower($db_config['dbms']));
        $dbClass = 'Db'. $this->dbType;
        $dbDriverPath = dirname(__FILE__).'/Driver/';
        require_cache( $dbDriverPath . $dbClass . '.class.php');

        if(class_exists($dbClass)) {
            $db = new $dbClass($db_config);
            if( 'pdo' != strtolower($db_config['dbms']) ) {
                $db->dbType = strtoupper($this->dbType);
            }else{
                $db->dbType = $this->_getDsnType($db_config['dsn']);
            }
        }else {
            throw_exception(L('_NOT_SUPPORT_DB_').': ' . $db_config['dbms']);
        }
        return $db;
    }

    protected function _getDsnType($dsn) {
        $match  =  explode(':',$dsn);
        $dbType = strtoupper(trim($match[0]));
        return $dbType;
    }

    private function parseConfig($db_config='') {
        if ( !empty($db_config) && is_string($db_config)) {
            $db_config = $this->parseDSN($db_config);
        }else if(empty($db_config)){
            $db_config = array (
                'dbms'        =>   C('DB_TYPE'),
                'username'  =>   C('DB_USER'),
                'password'   =>   C('DB_PWD'),
                'hostname'  =>   C('DB_HOST'),
                'hostport'    =>   C('DB_PORT'),
                'database'   =>   C('DB_NAME'),
                'dsn'          =>   C('DB_DSN'),
                'params'     =>   C('DB_PARAMS'),
            );
        }
        return $db_config;
    }

    public function addConnect($config,$linkNum=null) {
        $db_config  =   $this->parseConfig($config);
        if(empty($linkNum)) {
            $linkNum     =   count($this->linkID);
        }
        if(isset($this->linkID[$linkNum])) {
            return false;
        }
        return $this->connect($db_config,$linkNum);
    }

    public function switchConnect($linkNum) {
        if(isset($this->linkID[$linkNum])) {
            $this->_linkID  =   $this->linkID[$linkNum];
            return true;
        }else{
            return false;
        }
    }

    protected function initConnect($master=true) {
        if(1 == C('DB_DEPLOY_TYPE')) {
            $this->_linkID = $this->multiConnect($master);
        }else{
            if ( !$this->connected ) $this->_linkID = $this->connect();
        }
    }

    protected function multiConnect($master=false) {
        static $_config = array();
        if(empty($_config)) {
            foreach ($this->config as $key=>$val){
                $_config[$key]      =   explode(',',$val);
            }
        }
        if(C('DB_RW_SEPARATE')){
            if($master) {
                $r  =   0;
            }else{
                $r = floor(mt_rand(1,count($_config['hostname'])-1));  
            }
        }else{
            $r = floor(mt_rand(0,count($_config['hostname'])-1));  
        }
        $db_config = array(
            'username'  =>   isset($_config['username'][$r])?$_config['username'][$r]:$_config['username'][0],
            'password'   =>   isset($_config['password'][$r])?$_config['password'][$r]:$_config['password'][0],
            'hostname'  =>   isset($_config['hostname'][$r])?$_config['hostname'][$r]:$_config['hostname'][0],
            'hostport'    =>   isset($_config['hostport'][$r])?$_config['hostport'][$r]:$_config['hostport'][0],
            'database'   =>   isset($_config['database'][$r])?$_config['database'][$r]:$_config['database'][0],
            'dsn'          =>   isset($_config['dsn'][$r])?$_config['dsn'][$r]:$_config['dsn'][0],
            'params'     =>   isset($_config['params'][$r])?$_config['params'][$r]:$_config['params'][0],
        );
        return $this->connect($db_config,$r);
    }

    public function parseDSN($dsnStr)
    {
        if( empty($dsnStr) ){return false;}
        $info = parse_url($dsnStr);
        if($info['scheme']){
            $dsn = array(
            'dbms'        => $info['scheme'],
            'username'  => isset($info['user']) ? $info['user'] : '',
            'password'   => isset($info['pass']) ? $info['pass'] : '',
            'hostname'  => isset($info['host']) ? $info['host'] : '',
            'hostport'    => isset($info['port']) ? $info['port'] : '',
            'database'   => isset($info['path']) ? substr($info['path'],1) : ''
            );
        }else {
            preg_match('/^(.*?)\:\/\/(.*?)\:(.*?)\@(.*?)\:([0-9]{1, 6})\/(.*?)$/',trim($dsnStr),$matches);
            $dsn = array (
            'dbms'        => $matches[1],
            'username'  => $matches[2],
            'password'   => $matches[3],
            'hostname'  => $matches[4],
            'hostport'    => $matches[5],
            'database'   => $matches[6]
            );
        }
        return $dsn;
     }

    protected function debug() {
        if ( $this->debug || C('SQL_DEBUG_LOG'))    {
            $runtime    =   number_format(microtime(TRUE) - $this->beginTime, 6);
            Log::record(" RunTime:".$runtime."s SQL = ".$this->queryStr,Log::SQL);
        }
    }

    protected function parseTables($tables)
    {
        if(is_array($tables)) {
            array_walk($tables, array($this, 'addSpecialChar'));
            $tablesStr = implode(',', $tables);
        }
        else if(is_string($tables)) {
            if(0 === strpos($this->dbType,'MYSQL') && false === strpos($tables,'`')) {
                $tablesStr =  '`'.trim($tables).'`';
            }else{
                $tablesStr = $tables;
            }
        }
        return $tablesStr;
    }

    protected function parseWhere($where)
    {
        $whereStr = '';
        if(is_string($where) || is_null($where)) {
            $whereStr = $where;
        }else{
            if(is_instance_of($where,'HashMap')){
                $where  =   $where->toArray();
            }elseif(is_object($where)){
                $where = get_object_vars($where);
            }
            if(array_key_exists('_logic',$where)) {
                $operate    =   ' '.strtoupper($where['_logic']).' ';
                unset($where['_logic']);
            }else{
                $operate    =   ' AND ';
            }
            foreach ($where as $key=>$val){
                if(strpos($key,C('FIELDS_DEPR'))) {
                    $key    =   explode(C('FIELDS_DEPR'),$key);
                    array_walk($key, array($this, 'addSpecialChar'));
                }else{
                    $key = $this->addSpecialChar($key);
                }
                $whereStr .= "( ";
                if(is_array($val)) {
                        if(is_array($key)) {
                            $num    =   count($key);
                            if(empty($val[$num])) $val[$num]    =   'AND'; 
                            for ($i=0;$i<$num;$i++){
                                if(is_array($val[$i])) {
                                    if(is_string($val[$i][0]) && preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE)F$/i',$val[$i][0])) {
                                        $op =   $this->comparison[strtolower(substr($val[$i][0],0,-1))];
                                        $str[] = ' ('.$key[$i].' '.$op.' '.$val[$i][1].') ';
                                    }elseif(is_string($val[$i][0]) && preg_match('/IN/i',$val[$i][0])){
                                        $zone   =   is_array($val[$i][1])? implode(',',$val[$i][1]):$val[$i][1];
                                        $str[] =  ' ('.$key[$i].' '.strtoupper($val[$i][0]).' ('.$zone.') )';
                                    }elseif(is_string($val[$i][0]) && preg_match('/BETWEEN/i',$val[$i][0])){
                                        if(is_string($val[$i][1])) {
                                            $data  =  explode(',',$val[$i][1]);
                                        }else{
                                            $data = $val[$i][1];
                                        }
                                        $str[] =  ' ('.$key[$i].' '.strtoupper($val[$i][0]).' '.$data[0].' AND '.$data[1].' )';
                                    }elseif(is_string($val[$i][0]) && preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE|NULL|NOTNULL)$/i',$val[$i][0])){
                                        $op =   $this->comparison[strtolower($val[$i][0])];
                                        $str[] = ' ('.$key[$i].' '.$op.' '.$this->fieldFormat($val[$i][1]).') ';
                                    }else{
                                        $str[] = ' ('.$key[$i].' '.$val[$i][0].' '.$this->fieldFormat($val[$i][1]).') ';
                                    }
                                }else{
                                    $str[] = ' ('.$key[$i].' = '.$this->fieldFormat($val[$i]).') ';
                                }
                            }
                            $whereStr .= implode(strtoupper($val[$num]),$str);
                        }else{
                            if(is_string($val[0]) && preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE|NULL|NOTNULL)$/i',$val[0])) {
                                $whereStr .= $key.' '.$this->comparison[strtolower($val[0])].' '.$this->fieldFormat($val[1]);
                            }elseif(is_string($val[0]) && preg_match('/IN/i',$val[0])){
                                $zone   =   is_array($val[1])? implode(',',$val[1]):$val[1];
                                $whereStr .= $key.' '.strtoupper($val[0]).' ('.$zone.')';
                            }elseif(is_string($val[0]) && preg_match('/BETWEEN/i',$val[0])){
                                if(is_string($val[1])) {
                                    $data  =  explode(',',$val[1]);
                                }else{
                                    $data = $val[1];
                                }
                                $whereStr .=  ' ('.$key.' '.strtoupper($val[0]).' '.$data[0].' AND '.$data[1].' )';
                            }elseif(is_string($val[0]) && preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE)F$/i',$val[0])){
                                $whereStr .= $key.' '.$this->comparison[strtolower(substr($val[0],0,-1))].' '.$val[1];
                            }else {
                                if(($count = count($val))>2) {
                                    if(in_array(strtoupper(trim($val[$count-1])),array('AND','OR','XOR'))) {
                                        $rule = strtoupper(trim($val[$count-1]));
                                        $count   =  $count -1;
                                    }else{
                                        $rule = 'AND';
                                    }
                                    for($i=0;$i<$count;$i++) {
                                        $op = is_array($val[$i])?$this->comparison[strtolower($val[$i][0])]:'=';
                                        $data = is_array($val[$i])?$val[$i][1]:$val[$i];
                                        $whereStr .= '('.$key.' '.$op.' '.$this->fieldFormat($data).') '.$rule.' ';
                                    }
                                    $whereStr = substr($whereStr,0,-4);
                                }else{
                                    if(is_array($val[0])) {
                                        $operate1   =   $this->comparison[strtolower($val[0][0])];
                                        $data1  =   $val[0][1];
                                    }else{
                                        $operate1   =   '>=';
                                        $data1  =   $val[0];
                                    }
                                    if(is_array($val[1])) {
                                        $operate2   =   $this->comparison[strtolower($val[1][0])];
                                        $data2  =   $val[1][1];
                                    }else{
                                        $operate2   =   '<=';
                                        $data2  =   $val[1];
                                    }
                                    if(empty($val[2])) $val[2]  =   'AND';
                                    if(in_array(strtoupper(trim($val[2])),array('AND','OR','XOR'))) {
                                        $whereStr .= $key.' '.$operate1.' '.$this->fieldFormat($data1).' '.$val[2].' '.$key.' '.$operate2.' '.$this->fieldFormat($data2);
                                    }
                                }
                            }
                        }
                }else {
                    if(C('LIKE_MATCH_FIELDS') && preg_match('/('.C('LIKE_MATCH_FIELDS').')/i',$key)) {
                        $val = '%'.$val.'%';
                        $whereStr .= $key." LIKE ".$this->fieldFormat($val);
                    }else {
                        $whereStr .= $key." = ".$this->fieldFormat($val);
                    }
                }
                $whereStr .= ' )'.$operate;
            }
            $whereStr = substr($whereStr,0,-strlen($operate));
        }
        return empty($whereStr)?'':' WHERE '.$whereStr;
    }

    protected function parseOrder($order)
    {
        $orderStr = '';
        if(is_array($order))
            $orderStr .= ' ORDER BY '.implode(',', $order);
        else if(is_string($order) && !empty($order))
            $orderStr .= ' ORDER BY '.$order;
        return $orderStr;
    }

    protected function parseJoin($join)
    {
        $joinStr = '';
        if(!empty($join)) {
            if(is_array($join)) {
                foreach ($join as $key=>$_join){
                    if(false !== stripos($_join,'JOIN')) {
                        $joinStr .= ' '.$_join;
                    }else{
                        $joinStr .= ' LEFT JOIN ' .$_join;
                    }
                }
            }else{
                $joinStr .= ' LEFT JOIN ' .$join;
            }
        }
        return $joinStr;
    }

    protected function parseLimit($limit)
    {
        return !empty($limit)?$this->limit($limit):'';
    }

    protected function parseGroup($group)
    {
        $groupStr = '';
        if(is_array($group))
            $groupStr .= ' GROUP BY '.implode(',', $group);
        else if(is_string($group) && !empty($group))
            $groupStr .= ' GROUP BY '.$group;
        return empty($groupStr)?'':$groupStr;
    }

    protected function parseHaving($having)
    {
        $havingStr = '';
        if(is_string($having) && !empty($having))
            $havingStr .= ' HAVING '.$having;
        return $havingStr;
    }

    protected function parseFields($fields)
    {
        if(is_array($fields)) {
            $array   =  array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key)) {
                    $field =  $this->addSpecialChar($key).' AS '.$this->addSpecialChar($field);
                }else{
                    $field =  $this->addSpecialChar($field);
                }
                $array[] =  $field;
            }
            $fieldsStr = implode(',', $array);
        }else if(is_string($fields) && !empty($fields)) {
            if( false === strpos($fields,'`') ) {
                $fields = explode(',',$fields);
                array_walk($fields, array($this, 'addSpecialChar'));
                $fieldsStr = implode(',', $fields);
            }else {
                $fieldsStr = $fields;
            }
        }else{
            $fieldsStr = '*';
        }
        return $fieldsStr;
    }

    protected function parseValues($values)
    {
        if(is_array($values)) {
            array_walk($values, array($this, 'fieldFormat'));
            $valuesStr = implode(',', $values);
        }
        else if(is_string($values))
            $valuesStr = $values;
        return $valuesStr;
    }

    protected function parseSets($sets)
    {
        $setsStr  = '';
        if(is_object($sets) && !empty($sets)){
            if(is_instance_of($sets,'HashMap')){
                $sets = $sets->toArray();
            }
        }
        $sets    = auto_charset($sets,C('OUTPUT_CHARSET'),C('DB_CHARSET'));
        if(is_array($sets)){
            foreach ($sets as $key=>$val){
                $key    =   $this->addSpecialChar($key);
                if(is_array($val) && strtolower($val[0]) == 'exp') {
                    $val    =   $val[1];                    
                }elseif(is_null($val) || is_scalar($val)){
                    $val    =   $this->fieldFormat($val);
                }else{
                    continue;
                }
                $setsStr .= "$key = ".$val.",";
            }
            $setsStr = substr($setsStr,0,-1);
        }else if(is_string($sets)) {
            $setsStr = $sets;
        }
        return ' SET '.$setsStr;
    }

    protected function parseTable($tables) {
        $parseStr   =   '';
        if(is_string($tables)) {
            $tables  =  explode(',',$tables);
        }
        if( 0 === strpos($this->dbType,'MYSQL')) {
            array_walk($tables, array($this, 'addSpecialChar'));
        }
        $parseStr   =  implode(',',$tables);
        return $parseStr;
    }

    protected function setLockMode() {
        if('ORACLE' == $this->dbType || 'OCI'==$this->dbType ) {
            return ' FOR UPDATE NOWAIT ';
        }
        return ' FOR UPDATE ';
    }

    protected function fieldFormat(&$value,$asString=true,$multi=false)
    {
        if ($multi == true) {
            $asString = true;
        }
        if(is_int($value)) {
            $value = intval($value);
        }elseif(is_float($value)) {
            $value = floatval($value);
        }elseif(!$asString){
            $value = $this->escape_string($value);
        }elseif(is_string($value)) {
            $value = '\''.$this->escape_string($value).'\'';
        }elseif(is_null($value)){
            $value   =  'null';
        }
        return $value;
    }

    protected function addSpecialChar(&$value)
    {
        if(0 === strpos($this->dbType,'MYSQL')) {
            $value   =  trim($value);
            if( false !== strpos($value,' ') || false !== strpos($value,'*') ||  false !== strpos($value,'(') || false !== strpos($value,'.') || false !== strpos($value,'`')) {
            }else{
                $value = '`'.$value.'`';
            }
        }
        return $value;
    }

    public function isMainIps($query)
    {
        $queryIps = 'INSERT|UPDATE|DELETE|REPLACE|'
                . 'CREATE|DROP|'
                . 'LOAD DATA|SELECT .* INTO|COPY|'
                . 'ALTER|GRANT|REVOKE|'
                . 'LOCK|UNLOCK';
        if (preg_match('/^\s*"?(' . $queryIps . ')\s+/i', $query)) {
            return true;
        }
        return false;
    }

    public function query($sql='',$cache=false,$lazy=false,$lock=false,$fetchSql=false)
    {
        if(empty($sql)) {
            $sql   = $this->queryStr;
        }
        if($lock) {
            $sql .= $this->setLockMode();
        }
        if($fetchSql) {
            return $sql;
        }
        if($lazy) {
            return $this->lazyQuery($sql);
        }
        if($cache) {
            $guid   =   md5($sql);
            $data = S($guid);
            if(!empty($data)){
                return $data;
            }
        }
        $data = $this->_query($sql);
        if($cache){
             S($guid,$data);
        }
        return $data;
    }

    public function lazyQuery($sql='') {
        import("Think.Db.ResultIterator");
        return new ResultIterator($sql);
    }

    public function execute($sql='',$lock=false,$fetchSql=false)
    {
        if(empty($sql)) {
            $sql  = $this->queryStr;
        }
        if($lock) {
            $sql .= $this->setLockMode();
        }
        if($fetchSql) {
            return $sql;
        }
        return $this->_execute($sql);
    }

    public function autoExec($sql='',$lazy=false,$lock=false,$cache=false,$fetchSql=false)
    {
        if(empty($sql)) {
            $sql  = $this->queryStr;
        }
        if($this->isMainIps($sql)) {
            $this->execute($sql,$lock,$fetchSql);
        }else {
            $this->query($sql,$cache,$lazy,$lock,$fetchSql);
        }
    }

    public function find($where,$tables,$fields='*',$order=null,$limit=null,$group=null,$having=null,$join=null,$cache=false,$lazy=false,$lock=false,$fetchSql=false)
    {
        switch($this->dbType)
		{
			case 'IBASE':
            case 'FIREBIRD':
            case 'INTERBASE':
            	$this->queryStr = 'SELECT '.$this->parseLimit($limit)
					.$this->parseFields($fields)
					.' FROM '.$this->parseTable($tables)
					.$this->parseJoin($join)
					.$this->parseWhere($where)
					.$this->parseGroup($group)
					.$this->parseHaving($having)
					.$this->parseOrder($order);

				break;
			case 'ORACLE':
            case 'OCI':
				if($limit)
				{
					$this->queryStr = "SELECT *	FROM (SELECT rownum AS numrow, thinkphp.* FROM (SELECT "
						.$this->parseFields($fields)
						." FROM ".$this->parseTable($tables)
						.$this->parseJoin($join)
						.$this->parseWhere($where)
						.$this->parseGroup($group)
						.$this->parseHaving($having)
						.$this->parseOrder($order)
						.") thinkphp) WHERE "
						.$this->parseLimit($limit);
				}
				else
				{
					$this->queryStr = 'SELECT '.$this->parseFields($fields)
						.' FROM '.$this->parseTable($tables)
						.$this->parseJoin($join)
						.$this->parseWhere($where)
						.$this->parseGroup($group)
						.$this->parseHaving($having)
						.$this->parseOrder($order);

				}
				break;
			default:
            	$this->queryStr = 'SELECT '.$this->parseFields($fields)
					.' FROM '.$this->parseTable($tables)
					.$this->parseJoin($join)
					.$this->parseWhere($where)
					.$this->parseGroup($group)
					.$this->parseHaving($having)
					.$this->parseOrder($order)
					.$this->parseLimit($limit);
		}
        return $this->query('',$cache,$lazy,$lock,$fetchSql);
    }

    public function add($map,$table,$multi=false,$lock=false,$fetchSql=false)
    {
        if($multi) {
            return $this->addAll($map,$table);
        }
        if(!is_array($map)) {
            if(!is_instance_of($map,'HashMap')){
                throw_exception(L('_DATA_TYPE_INVALID_'));
            }
        }
        foreach ($map as $key=>$val){
            if(is_array($val) && strtolower($val[0]) == 'exp') {
                $val    =   $val[1];                      
            }elseif (is_scalar($val)){
                $val    =   $this->fieldFormat($val);
            }else{
                continue;
            }
            $data[$key] =   $val;
        }
        $data    = auto_charset($data,C('OUTPUT_CHARSET'),C('DB_CHARSET'));
        $fields = array_keys($data);
        array_walk($fields, array($this, 'addSpecialChar'));
        $fieldsStr = implode(',', $fields);
        $values = array_values($data);
        //array_walk($values, array($this, 'fieldFormat'));

        $valuesStr = implode(',', $values);
        $this->queryStr =    'INSERT INTO '.$this->parseTable($table).' ('.$fieldsStr.') VALUES ('.$valuesStr.')';
        return $this->execute($this->queryStr,$lock,$fetchSql);
    }

    public function addAll($map,$table,$lock,$fetchSql=false)
    {
        if(0 === strpos($this->dbType,'MYSQL')) {
            $fields = array_keys((array)$map[0]);
            array_walk($fields, array($this, 'addSpecialChar'));
            $fieldsStr = implode(',', $fields);
            $values = array();
            foreach ($map as $data){
                foreach ($data as $key=>$val){
                    if(is_scalar($val)) {
                        $_data[$key]    =   $val;
                    }
                }
                $_data    = auto_charset($_data,C('OUTPUT_CHARSET'),C('DB_CHARSET'));
                $_values = array_values($_data);
                array_walk($_values, array($this, 'fieldFormat'),true);
                $values[] = '( '.implode(',', $_values).' )';
            }
            $valuesStr = implode(',',$values);
            $this->queryStr =    'INSERT INTO '.$this->parseTable($table).' ('.$fieldsStr.') VALUES '.$valuesStr;
            return $this->execute($this->queryStr,$lock,$fetchSql);
        }else{
            //$this->startTrans();
            foreach ($map as $data){
                $this->add($data,$table);
            }
            //$this->commit();
        }
    }

    public function remove($where,$table,$limit='',$order='',$lock=false,$fetchSql=false)
    {
        $this->queryStr = 'DELETE FROM '
                .$this->parseTable($table)
                .$this->parseWhere($where)
                .$this->parseOrder($order)
                .$this->parseLimit($limit);
        return $this->execute($this->queryStr,$lock,$fetchSql);
    }

    public function save($sets,$table,$where,$limit=0,$order='',$lock=false,$fetchSql=false)
    {
        $this->queryStr = 'UPDATE '
            .$this->parseTable($table)
            .$this->parseSets($sets)
            .$this->parseWhere($where)
            .$this->parseOrder($order)
            .$this->parseLimit($limit);
        return $this->execute($this->queryStr,$lock,$fetchSql);
    }
	
    public function setField($field,$value,$table,$condition,$asString=true,$lock=false,$fetchSql=false) {
        $this->queryStr =   'UPDATE '.$this->parseTable($table).' SET ';
        if(strpos($field,',')) {
            $field =  explode(',',$field);
        }
        if(is_array($field)) {
            $count   =  count($field);
            for($i=0;$i<$count;$i++) {
                $this->queryStr .= $this->addSpecialChar($field[$i]).'='.$this->fieldFormat($value[$i]).',';
            }
        }else{
            $this->queryStr .= $this->addSpecialChar($field).'='.$this->fieldFormat($value,$asString).',';
        }
        $this->queryStr =   substr($this->queryStr,0,-1).$this->parseWhere($condition);
        return $this->execute($this->queryStr,$lock,$fetchSql);
    }

    public function setInc($field,$table,$condition,$step=1) {
        return $this->setField($field,'('.$field.'+'.$step.')',$table,$condition,false);
    }

    public function setDec($field,$table,$condition,$step=1) {
        return $this->setField($field,'('.$field.'-'.$step.')',$table,$condition,false);
    }

    public function Q($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            $this->beginTime = microtime(TRUE);
        }
    }

    public function W($times='') {
        static $_times = 0;
        if(empty($times)) {
            return $_times;
        }else{
            $_times++;
            $this->beginTime = microtime(TRUE);
        }
    }
	
    public function getLastSql() {
        return $this->queryStr;
    }
}

?>