<?php

define('HAS_ONE',1);
define('BELONGS_TO',2);
define('HAS_MANY',3);
define('MANY_TO_MANY',4);

define('MUST_TO_VALIDATE',1);   
define('EXISTS_TO_VAILIDATE',0);       
define('VALUE_TO_VAILIDATE',2);  

class Model extends Base  implements IteratorAggregate
{
    protected $_db = array();
    protected $db = null;
    protected $tablePrefix  =   '';
    protected $tableSuffix = '';
    protected $name = '';
    protected $dbName  = '';
    protected $tableName = '';
    protected $trueTableName ='';
    protected $fields = array();
    protected $type  =   array();
    protected $data =   array();
    protected $options  =   array();
    protected $dataList =   array();
    protected $error = '';
    protected $validateError    =   array();
    protected $aggregation = array();
    protected $composite = false;
    protected $viewModel = false;
    protected $optimLock = 'lock_version';
    protected $pessimisticLock = false;
    protected $autoSaveRelations      = false;      
    protected $autoDelRelations        = false;     
    protected $autoAddRelations       = false;      
    protected $autoReadRelations      = false;     
    protected $lazyQuery                =   false;             
    protected $autoCreateTimestamps = array('create_at','create_on','cTime');
    protected $autoUpdateTimestamps = array('update_at','update_on','mTime');
    protected $autoTimeFormat = '';
    protected $blobFields     =   null;
    protected $blobValues    = null;

    public function __construct($data='')
    {
        $this->_initialize();
        $this->name =   $this->getModelName();
        if(!$this->composite) {
            import("Think.Db.Db");
            if(!empty($this->connection)) {
                $this->db = Db::getInstance($this->connection);
            }else{
                $this->db = Db::getInstance();
            }
            $this->db->resultType   =   C('DATA_RESULT_TYPE');
            $this->db->tableName = $this->parseName($this->name);
            $this->_db[0]   =   $this->db;
            $this->tablePrefix = $this->tablePrefix?$this->tablePrefix:C('DB_PREFIX');
            $this->tableSuffix = $this->tableSuffix?$this->tableSuffix:C('DB_SUFFIX');
            $this->_checkTableInfo();
        }
        if(!empty($data)) {
            $this->create($data);
        }
    }

    public static function getInstance()
    {
        return get_instance_of(__CLASS__);
    }

    public function __set($name,$value) {
        $this->data[$name]  =   $value;
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }elseif(property_exists($this,$name)){
            return $this->$name;
        }else{
            return null;
        }
    }

    public function __call($method,$args) {
        if(strtolower(substr($method,0,5))=='getby') {

            $field   =   $this->parseName(substr($method,5));
            if(in_array($field,$this->fields,true)) {
                array_unshift($args,$field);
                return call_user_func_array(array(&$this, 'getBy'), $args);
            }
        }elseif(strtolower(substr($method,0,6))=='getsby') {

            $field   =   $this->parseName(substr($method,6));
            if(in_array($field,$this->fields,true)) {
                array_unshift($args,$field);
                return call_user_func_array(array(&$this, 'getByAll'), $args);
            }
        }elseif(strtolower(substr($method,0,3))=='get'){

            $field   =   $this->parseName(substr($method,3));
            return $this->__get($field);
        }elseif(strtolower(substr($method,0,3))=='top'){

            $count = substr($method,3);
            array_unshift($args,$count);
            return call_user_func_array(array(&$this, 'topN'), $args);
        }elseif(strtolower(substr($method,0,5))=='setby'){

            $field   =   $this->parseName(substr($method,5));
            if(in_array($field,$this->fields,true)) {
                array_unshift($args,$field);
                return call_user_func_array(array(&$this, 'setField'), $args);
            }
        }elseif(strtolower(substr($method,0,3))=='set'){

            $field   =   $this->parseName(substr($method,3));
            array_unshift($args,$field);
            return call_user_func_array(array(&$this, '__set'), $args);
        }elseif(strtolower(substr($method,0,5))=='delby'){

            $field   =   $this->parseName(substr($method,5));
            if(in_array($field,$this->fields,true)) {
                array_unshift($args,$field);
                return call_user_func_array(array(&$this, 'deleteBy'), $args);
            }
        }elseif(strtolower(substr($method,0,3))=='del'){

            $field   =   $this->parseName(substr($method,3));
            if(in_array($field,$this->fields,true)) {
                if(isset($this->data[$field])) {
                    unset($this->data[$field]);
                }
            }
        }elseif(strtolower(substr($method,0,8))=='relation'){
            $type    =   strtoupper(substr($method,8));
            if(in_array($type,array('ADD','SAVE','DEL'),true)) {
                array_unshift($args,$type);
                return call_user_func_array(array(&$this, 'opRelation'), $args);
            }
        }else{
            throw_exception(__CLASS__.':'.$method.L('_METHOD_NOT_EXIST_'));
        }
        return;
    }

    protected function _initialize() {}

    private function _create(&$data,$autoLink=false,$multi=false,$lock=false,$fetchSql=false) {
        if(!$this->_before_create($data)) {
            return false;
        }
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            $data      =   isset($this->options['data'])?      $this->options['data']:     $data;
            $lock      =   isset($this->options['lock'])?      $this->options['lock']:     $lock;
            $autoLink=  isset($this->options['link'])?          $this->options['link']:     $autoLink;
            $table     =   isset($this->options['table'])?     $this->options['table']:    $table;
            $fetchSql = isset($this->options['fetch'])?       $this->options['fetch']:    $fetchSql;
            $this->options  =   array();
        }
        if($fetchSql) {
            return $this->db->add($data,$table,$multi,$lock,$fetchSql);
        }

        if(false === $result = $this->db->add($data,$table,$multi)){

            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            $insertId   =   $this->getLastInsID();
            if($insertId && !isset($data[$this->getPk()])) {
                $data[$this->getPk()]   =    $insertId;
            }
            $this->saveBlobFields($data);

            if ($this->autoAddRelations || $autoLink){
                $this->opRelation('ADD',$data);
            }

            $this->_after_create($data);

            return $insertId ?  $insertId   : $result;
        }
    }

    protected function _before_create(&$data) {return true;}
    protected function _after_create(&$data) {}

    private function _update(&$data,$where='',$limit='',$order='',$autoLink=false,$lock=false,$fetchSql=false) {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            $where   =   isset($this->options['where'])?     $this->options['where']:    $where;
            $limit      =   isset($this->options['limit'])?     $this->options['limit']:        $limit;
            $order    =   isset($this->options['order'])?     $this->options['order']:    $order;
            $lock      =   isset($this->options['lock'])?      $this->options['lock']:     $lock;
            $autoLink=  isset($this->options['link'])?          $this->options['link']:     $autoLink;
            $table     =   isset($this->options['table'])?     $this->options['table']:    $table;
            $fetchSql = isset($this->options['fetch'])?       $this->options['fetch']:    $fetchSql;
            $this->options  =   array();
        }

        if(!$this->_before_update($data,$where)) {
            return false;
        }
        $lock    =   ($this->pessimisticLock || $lock);
        if($this->viewModel) {
            $where  =   $this->checkCondition($where);
        }
        if($fetchSql) {

            return $this->db->save($data,$table,$where,$limit,$order,$lock,true);
        }
        if(false ===$this->db->save($data,$table,$where,$limit,$order,$lock) ){
            $this->error = L('_OPERATION_WRONG_');
            return false;
        }else {
            $this->saveBlobFields($data);

            if ($this->autoSaveRelations || $autoLink){
                $this->opRelation('SAVE',$data);
            }

            $this->_after_update($data,$where);
            return true;
        }
    }

    protected function _before_update(&$data,$where) {return true;}
    protected function _after_update(&$data,$where) {}

    private function _read($condition='',$fields='*',$all=false,$order='',$limit='',$group='',$having='',$join='',$cache=false,$relation=false,$lazy=false,$lock=false,$fetchSql=false) {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            $condition  =   isset($this->options['where'])?         $this->options['where']:    $condition;
            $table       =   isset($this->options['table'])?         $this->options['table']:    $table;
            $fields       =   isset($this->options['field'])?         $this->options['field']:    $fields;
            $limit        =   isset($this->options['limit'])?         $this->options['limit']:        $limit;
            $order      =   isset($this->options['order'])?         $this->options['order']:    $order;
            $group      =   isset($this->options['group'])?         $this->options['group']:    $group;
            $having     =   isset($this->options['having'])?        $this->options['having']:   $having;
            $join         =   isset($this->options['join'])?          $this->options['join']:     $join;
            $cache      =   isset($this->options['cache'])?         $this->options['cache']:    $cache;
            $lock         =   isset($this->options['lock'])?          $this->options['lock']:     $lock;
            $lazy        =   isset($this->options['lazy'])?          $this->options['lazy']: $lazy;
            $relation    =   isset($this->options['link'])?              $this->options['link']:     $relation;
            $fetchSql = isset($this->options['fetch'])?       $this->options['fetch']:    $fetchSql;
            $this->options  =   array();
        }

        if(!$this->_before_read($condition)) {
            return false;
        }
        if($cache) {
            if($all) {
                $identify   = $this->name.'List_'.to_guid_string(func_get_args());
            }else{
                $identify   = $this->name.'_'.to_guid_string($condition);
            }
            $result  =  S($identify);
            if(false !== $result) {
                if(!$all) {
                    $this->cacheLockVersion($result);
                }
                $this->_after_read($condition,$result);
                return $result;
            }
        }
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
            $fields =   $this->checkFields($fields);
            $order  =   $this->checkOrder($order);
            $group  =   $this->checkGroup($group);
        }
        $lazy    =   ($this->lazyQuery || $lazy);
        $lock    =   ($this->pessimisticLock || $lock);
        if($fetchSql) {
            return $this->db->find($condition,$table,$fields,$order,$limit,$group,$having,$join,$cache,$lazy,$lock,true);
        }
        $rs = $this->db->find($condition,$table,$fields,$order,$limit,$group,$having,$join,$cache,$lazy,$lock);
        $result =   $this->rsToVo($rs,$all,0,$relation);

        $this->_after_read($condition,$result);
        if($result && $cache) {
            S($identify,$result);
        }
        return $result;
    }

    protected function _before_read(&$condition) {return true;}
    protected function _after_read(&$condition,$result) {}

    private function _delete($data,$where='',$limit=0,$order='',$autoLink=false,$lock=false,$fetchSql=false) {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            $where      =   isset($this->options['where'])?     $this->options['where']:    $where;
            $table          =   isset($this->options['table'])?     $this->options['table']:    $table;
            $limit          =   isset($this->options['limit'])?     $this->options['limit']:        $limit;
            $order      =   isset($this->options['order'])?     $this->options['order']:    $order;
            $lock         =   isset($this->options['lock'])?          $this->options['lock']:     $lock;
            $autoLink   =   isset($this->options['link'])?          $this->options['link']:     $autoLink;
            $fetchSql = isset($this->options['fetch'])?       $this->options['fetch']:    $fetchSql;
            $this->options  =   array();
        }

        if(!$this->_before_delete($where)) {
            return false;
        }
        if($this->viewModel) {
            $where  =   $this->checkCondition($where);
        }
        if($fetchSql) {
            return $this->db->remove($where,$table,$limit,$order,$lock,true);
        }
        $result=    $this->db->remove($where,$table,$limit,$order,$lock);
        if(false === $result ){
            $this->error =  L('_OPERATION_WRONG_');
            return false;
        }else {
            $this->delBlobFields($data);
            if ($this->autoDelRelations || $autoLink){
                $this->opRelation('DEL',$data);
            }
            $this->_after_delete($where);
            return $result;
        }
    }
    protected function _before_delete(&$where) {return true;}
    protected function _after_delete(&$where) {}

    private function _query($sql='',$cache=false,$lazy=false,$lock=false,$fetchSql=false) {
        if(!empty($this->options)) {
            $sql        =   isset($this->options['sql'])?           $this->options['sql']:      $sql;
            $cache  =   isset($this->options['cache'])?     $this->options['cache']:    $cache;
            $lazy       =   isset($this->options['lazy'])?      $this->options['lazy']: $lazy;
            $lock       =   isset($this->options['lock'])?      $this->options['lock']:     $lock;
            $fetchSql = isset($this->options['fetch'])?       $this->options['fetch']:    $fetchSql;
            $this->options  =   array();
        }
        if(!$this->_before_query($sql)) {
            return false;
        }
        if($cache) {
            $identify   = md5($sql);
            $result =   S($identify);
            if(false !== $result) {
                return $result;
            }
        }
        $lazy    =   ($this->lazyQuery || $lazy);
        $lock    =   ($this->pessimisticLock || $lock);
        if($fetchSql) {
            return $this->db->query($sql,$cache,$lazy,$lock,$fetchSql);
        }
        $result =   $this->db->query($sql,$cache,$lazy,$lock);
        if($cache)    S($identify,$result);
        $this->_after_query($result);
        return $result;
    }
    protected function _before_query(&$sql) {return true;}
    protected function _after_query(&$result) {}

    private function _checkTableInfo() {
        if(empty($this->fields) && strtolower(get_class($this))!='model') {
            if(C('DB_FIELDS_CACHE')) {
                $identify   =   $this->name.'_fields';
                $this->fields = F($identify);
                if(!$this->fields) {
                    $this->flush();
                }
            }else{
                $this->flush();
            }
        }
    }

    public function flush() {
        if($this->viewModel) {
            $this->fields = array();
            $this->fields['_autoInc'] = false;
            foreach ($this->viewFields as $name=>$val){
                $k = isset($val['_as'])?$val['_as']:$name;
                foreach ($val as $key=>$field){
                    if(is_numeric($key)) {
                        $this->fields[] =   $k.'.'.$field;
                    }else{
                        $this->fields[] =   $k.'.'.$key;
                    }
                }
            }
        }else{
            $fields =   $this->db->getFields($this->getTableName());
            $this->fields   =   array_keys($fields);
            $this->fields['_autoInc'] = false;
            foreach ($fields as $key=>$val){
                $this->type[$key]    =   $val['type'];
                if($val['primary']) {
                    $this->fields['_pk']    =   $key;
                    if($val['autoInc']) $this->fields['_autoInc']   =   true;
                }
            }
        }
        if(C('DB_FIELDS_CACHE')) {
            $identify   =   $this->name.'_fields';
            F($identify,$this->fields);
        }
    }

    public function filterFields(&$result) {
        if(!empty($this->_filter)) {
            foreach ($this->_filter as $field=>$filter){
                $fun  =  $filter[1];
                if(!empty($fun)) {
                    if(isset($filter[2]) && $filter[2]){
                        if(is_array($result)) {
                            $result[$field]  =  call_user_func($fun,$result);
                        }else{
                            $result->$field =  call_user_func($fun,$result);
                        }
                    }else{
                        if(is_array($result) && isset($result[$field])) {
                            $result[$field]  =  call_user_func($fun,$result[$field]);
                        }elseif(isset($result->$field)){
                            $result->$field =  call_user_func($fun,$result->$field);
                        }
                    }
                }
            }
        }
        return $result;
    }

    public function filterListFields(&$resultSet) {
        if(!empty($this->_filter)) {
            foreach ($resultSet as $key=>$result){
                $resultSet[$key]  =  $this->filterFields($result);
            }
        }
    }

    public function getListBlobFields(&$resultSet,$field='') {
        if(!empty($this->blobFields)) {
            foreach ($resultSet as $key=>$result){
                $result =   $this->getBlobFields($result,$field);
                $resultSet[$key]    =   $result;
            }
        }
    }

    public function getBlobFields(&$data,$field='') {
        if(!empty($this->blobFields)) {
            $pk =   $this->getPk();
            $id =   is_array($data)?$data[$pk]:$data->$pk;
            if(empty($field)) {
                foreach ($this->blobFields as $field){
                    if($this->viewModel) {
                        $identify   =   $this->masterModel.'_'.$id.'_'.$field;
                    }else{
                        $identify   =   $this->name.'_'.$id.'_'.$field;
                    }
                    if(is_array($data)) {
                        $data[$field]   =   F($identify);
                    }else{
                        $data->$field   =   F($identify);
                    }
                }
                return $data;
            }else{
                $identify   =   $this->name.'_'.$id.'_'.$field;
                return F($identify);
            }
        }
    }

    public function saveBlobFields(&$data) {
        if(!empty($this->blobFields)) {
            foreach ($this->blobValues as $key=>$val){
                if(strpos($key,'@@_?id_@@')) {
                    $key    =   str_replace('@@_?id_@@',$data[$this->getPk()],$key);
                }
                F($key,$val);
            }
        }
    }

    public function delBlobFields(&$data,$field='') {
        if(!empty($this->blobFields)) {
            $pk =   $this->getPk();
            $id =   is_array($data)?$data[$pk]:$data->$pk;
            if(empty($field)) {
                foreach ($this->blobFields as $field){
                    $identify   =   $this->name.'_'.$id.'_'.$field;
                    F($identify,null);
                }
            }else{
                $identify   =   $this->name.'_'.$id.'_'.$field;
                F($identify,null);
            }
        }
    }

    public function getIterator()
    {
        if(!empty($this->dataList)) {
            return new ArrayObject($this->dataList);
        }elseif(!empty($this->data)){
            return new ArrayObject($this->data);
        }else{
            $fields = $this->fields;
            unset($fields['_pk'],$fields['_autoInc']);
            return new ArrayObject($fields);
        }
    }

    public function toArray()
    {
        if(!empty($this->dataList)) {
            return $this->dataList;
        }elseif (!empty($this->data)){
            return $this->data;
        }
        return false;
    }

    public function add($data=null,$autoLink=false,$multi=false)
    {
        if(empty($data)) {
            if(!empty($this->options['data'])) {
                $data    =   $this->options['data'];
            }elseif(!empty($this->data)) {
                $data    =   $this->data;
            }elseif(!empty($this->dataList)){
                return $this->addAll($this->dataList);
            }else{
                $this->error = L('_DATA_TYPE_INVALID_');
                return false;
            }
        }
        $data   =   $this->_facade($data);
        if(!$data) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        if($this->optimLock && !isset($data[$this->optimLock]) ) {
            if(in_array($this->optimLock,$this->fields,true)) {
                $data[$this->optimLock]  =   0;
            }
        }
        return $this->_create($data,$autoLink);
    }

    protected function _facade($data) {
        if(is_instance_of($data,'HashMap')){
            $data = $data->toArray();
        }elseif(is_object($data)) {
            $data    =   get_object_vars($data);
        }elseif(is_string($data)){
            parse_str($data,$data);
        }elseif(!is_array($data)){
            return false;
        }
        if(!empty($this->aggregation)) {
            foreach ($this->aggregation as $name){
                if(is_array($name)) {
                    $fields =   $name[1];
                    $name   =   $name[0];
                    if(is_string($fields)) $fields = explode(',',$fields);
                }
                if(!empty($data[$name])) {
                    $combine = (array)$data[$name];
                    if(!empty($fields)) {
                        foreach ($fields as $key=>$field){
                            if(is_int($key)) $key = $field;
                            if(isset($combine[$key])) {
                                $data[$field]   =   $combine[$key];
                            }
                        }
                    }else{
                        $data = $data+$combine;
                    }
                    unset($data[$name]);
                }
            }
        }
        foreach ($data as $key=>$val){
            if(!$this->viewModel && empty($this->_link)) {
                if(!in_array($key,$this->fields,true)) {
                    unset($data[$key]);
                }
            }
        }
        if(!empty($this->blobFields)) {
            foreach ($this->blobFields as $field){
                if(isset($data[$field])) {
                    if(isset($data[$this->getPk()])) {
                        $this->blobValues[$this->name.'_'.$data[$this->getPk()].'_'.$field] =   $data[$field];
                    }else{
                        $this->blobValues[$this->name.'_@@_?id_@@_'.$field] =   $data[$field];
                    }
                    unset($data[$field]);
                }
            }
        }
        if(!empty($this->_filter)) {
            foreach ($this->_filter as $field=>$filter){
                if(isset($data[$field])) {
                    $fun              =  $filter[0];
                    if(!empty($fun)) {
                        if(isset($filter[2]) && $filter[2]) {
                            $data[$field]   =  call_user_func($fun,$data);
                        }else{
                            $data[$field]   =  call_user_func($fun,$data[$field]);
                        }
                    }
                }
            }
        }
        if(isset($this->_map)) {
            foreach ($this->_map as $key=>$val){
                if(isset($data[$key]) && $key != $val ) {
                    $data[$val] =   $data[$key];
                    unset($data[$key]);
                }
            }
        }
        return $data;
    }

    public function checkCondition($data) {
         if((empty($data) || (is_instance_of($data,'HashMap') && $data->isEmpty())) && !empty($this->viewCondition)) {
             $data = $this->viewCondition;
         }elseif(!is_string($data)) {
            $data    =   $this->_facade($data);
            $baseCondition = empty($this->viewCondition)?array():$this->viewCondition;
            $view   =   array();
            foreach ($this->viewFields as $key=>$val){
                $k = isset($val['_as'])?$val['_as']:$key;
                foreach ($data as $name=>$value){
                    if(false !== $field = array_search($name,$val)) {
                        if(is_numeric($field)) {
                            $_key   =   $k.'.'.$name;
                        }else{
                            $_key   =   $k.'.'.$field;
                        }
                        $view[$_key]    =   $value;
                        unset($data[$name]);
                        if(is_array($baseCondition) && isset($baseCondition[$_key])) {
                            $view[$_key.','.$_key]  =   array($value,$baseCondition[$_key]);
                            unset($baseCondition[$_key]);
                            unset($view[$_key]);
                        }
                    }
                }
            }
            //if(!empty($view) && !empty($baseCondition)) {
                $data    =   array_merge($data,$baseCondition,$view);
            //}
         }
        return $data;
    }

    public function checkFields($fields) {
        if(empty($fields) || '*'==$fields ) {
            $fields =   array();
            foreach ($this->viewFields as $name=>$val){
                $k = isset($val['_as'])?$val['_as']:$name;
                foreach ($val as $key=>$field){
                    if(is_numeric($key)) {
                        $fields[]    =   $k.'.'.$field.' AS '.$field;
                    }elseif('_' != substr($key,0,1)) {
                        if( false !== strpos($key,'*') ||  false !== strpos($key,'(') || false !== strpos($key,'.')) {
                            $fields[]    =   $key.' AS '.$field;
                        }else{
                            $fields[]    =   $k.'.'.$key.' AS '.$field;
                        }
                    }
                }
            }
            $fields = implode(',',$fields);
        }else{
            if(!is_array($fields)) {
                $fields =   explode(',',$fields);
            }
            $array   =  array();
            foreach ($this->viewFields as $name=>$val){
                $k = isset($val['_as'])?$val['_as']:$name;
                foreach ($fields as $key=>$field){
                    if(false !== $_field = array_search($field,$val)) {
                        if(is_numeric($_field)) {
                            $array[]    =   $k.'.'.$field.' AS '.$field;
                        }else{
                            if( false !== strpos($_field,'*') ||  false !== strpos($_field,'(') || false !== strpos($_field,'.')) {
                                $array[]    =   $_field.' AS '.$field;
                            }else{
                                $array[]    =   $k.'.'.$_field.' AS '.$field;
                            }
                        }
                    }
                }
            }
            $fields = implode(',',$array);
        }
        return $fields;
    }
	
    public function checkOrder($order) {
         if(!empty($order)) {
            $orders = explode(',',$order);
            $_order = array();
            foreach ($orders as $order){
                $array = explode(' ',$order);
                $field   =   $array[0];
                $sort   =   isset($array[1])?$array[1]:'ASC';
                foreach ($this->viewFields as $name=>$val){
                    $k = isset($val['_as'])?$val['_as']:$name;
                    if(false !== $_field = array_search($field,$val)) {
                        if(is_numeric($_field)) {
                            $field     =  $k.'.'.$field;
                        }else{
                            $field     =  $k.'.'.$_field;
                        }
                        break;
                    }
                }
                $_order[] = $field.' '.$sort;
            }
            $order = implode(',',$_order);
         }
        return $order;
    }

    public function checkGroup($group) {
         if(!empty($group)) {
            //$group = $this->getPk();
            $groups = explode(',',$group);
            $_group = array();
            foreach ($groups as $group){
                $array = explode(' ',$group);
                $field   =   $array[0];
                $sort   =   isset($array[1])?$array[1]:'';
                foreach ($this->viewFields as $name=>$val){
                    $k = isset($val['_as'])?$val['_as']:$name;
                    if(false !== $_field = array_search($field,$val)) {
                        if(is_numeric($_field)) {
                            $field  =  $k.'.'.$field;
                        }else{
                            $field  =  $k.'.'.$_field;
                        }
                        break;
                    }
                }
                $_group[$field] = $field.' '.$sort;
            }
            $group  =   $_group;
         }
        return $group;
    }

    public function addAll($dataList='',$autoLink=false)
    {
        if(empty($dataList)) {
            $dataList   =   $this->dataList;
        }elseif(!is_array($dataList)) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        return $this->_create($dataList,$autoLink,true);
    }

    public function save($data=null,$where='',$autoLink=false,$limit=0,$order='')
    {
        if(empty($data)) {
            if(!empty($this->options['data'])) {
                $data    =   $this->options['data'];
            }elseif(!empty($this->data)) {
                $data    =   $this->data;
            }elseif(!empty($this->dataList)){
                $data    =   $this->dataList;
                $this->startTrans();
                foreach ($data as $val){
                    $result   =  $this->save($val,$where,$autoLink);
                }
                $this->commit();
                return $result;
            }
        }
        $data   =   $this->_facade($data);
        if(!$data) {
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        if(!$this->checkLockVersion($data,$where)) {
            $this->error = L('_RECORD_HAS_UPDATE_');
            return false;
        }
        $pk   =  $this->getPk();
        if(empty($where) && isset($data[$pk]) && !is_array($data[$pk])) {
            $where  = $pk."='".$data[$pk]."'";
            unset($data[$pk]);
        }
        return $this->_update($data,$where,$limit,$order,$autoLink);
    }

    protected function checkLockVersion(&$data,$where='') {
        $pk   =  $this->getPk();
        if(isset($data[$pk])) {
            $where  = $pk."=".$data[$pk];
            $guid =  $data[$pk];
        }else{
            $guid =  to_guid_string($where);
        }
        $identify   = $this->name.'_'.$guid.'_lock_version';
        if($this->optimLock && isset($_SESSION[$identify])) {
            $lock_version = $_SESSION[$identify];
            if(!empty($where)) {
                $vo = $this->find($where,$this->optimLock);
            }else {
                $vo = $this->find($data,$this->optimLock);
            }
            $_SESSION[$identify]     =   $lock_version;
            $curr_version = is_array($vo)?$vo[$this->optimLock]:$vo->{$this->optimLock};
            if(isset($curr_version)) {
                if($curr_version>0 && $lock_version != $curr_version) {
                    return false;
                }else{
                    $save_version = $data[$this->optimLock];
                    if($save_version != $lock_version+1) {
                        $data[$this->optimLock]  =   $lock_version+1;
                    }
                    $_SESSION[$identify]     =   $lock_version+1;
                }
            }
        }
        return true;
    }

    public function getRelation(&$result,$name='',$return=false)
    {
        if(!empty($this->_link)) {
            foreach($this->_link as $key=>$val) {
                    $mappingName =  !empty($val['mapping_name'])?$val['mapping_name']:$key; 
                    if(empty($name) || $mappingName == $name) {
                        $mappingType = !empty($val['mapping_type'])?$val['mapping_type']:$val;  
                        $mappingClass  = !empty($val['class_name'])?$val['class_name']:$key;           
                        $mappingFields = !empty($val['mapping_fields'])?$val['mapping_fields']:'*';   
                        $mappingCondition = !empty($val['condition'])?$val['condition']:'1=1';        
                        if(strtoupper($mappingClass)==strtoupper($this->name)) {
                            $mappingFk   =   !empty($val['parent_key'])? $val['parent_key'] : 'parent_id';
                        }else{
                            $mappingFk   =   !empty($val['foreign_key'])?$val['foreign_key']:strtolower($this->name).'_id';    
                        }
                        $model = D($mappingClass);
                        switch($mappingType) {
                            case HAS_ONE:
                                $pk   =  is_array($result)?$result[$this->getPk()]:$result->{$this->getPk()};
                                $mappingCondition .= " AND {$mappingFk}='{$pk}'";
                                $relationData   =  $model->find($mappingCondition,$mappingFields,false,false);
                                if(isset($val['as_fields'])) {
                                    $fields =   explode(',',$val['as_fields']);
                                    foreach ($fields as $field){
                                        $fieldAs = explode(':',$field);
                                        if(count($fieldAs)>1) {
                                            $fieldFrom = $fieldAs[0];
                                            $fieldTo    =   $fieldAs[1];
                                        }else{
                                            $fieldFrom   =   $field;
                                            $fieldTo      =   $field;
                                        }
                                        $fieldVal    =   is_array($relationData)?$relationData[$fieldFrom]:$relationData->$fieldFrom;
                                        if(isset($fieldVal)) {
                                            if(is_array($result)) {
                                                $result[$fieldTo]   =   $fieldVal;
                                            }else{
                                                $result->$fieldTo  =   $fieldVal;
                                            }
                                        }
                                    }
                                    unset($relationData);
                                }
                                break;
                            case BELONGS_TO:
                                if(strtoupper($mappingClass)==strtoupper($this->name)) {
                                    $mappingFk   =   !empty($val['parent_key'])? $val['parent_key'] : 'parent_id';
                                }else{
                                    $mappingFk   =   !empty($val['foreign_key'])?$val['foreign_key']:strtolower($model->name).'_id';
                                }
                                $fk   =  is_array($result)?$result[$mappingFk]:$result->{$mappingFk};
                                $mappingCondition .= " AND {$model->getPk()}='{$fk}'";
                                $relationData   =  $model->find($mappingCondition,$mappingFields,false,false);
                                if(isset($val['as_fields'])) {
                                    $fields =   explode(',',$val['as_fields']);
                                    foreach ($fields as $field){
                                        $fieldAs = explode(':',$field);
                                        if(count($fieldAs)>1) {
                                            $fieldFrom = $fieldAs[0];
                                            $fieldTo    =   $fieldAs[1];
                                        }else{
                                            $fieldFrom   =   $field;
                                            $fieldTo      =   $field;
                                        }
                                        $fieldVal    =   is_array($relationData)?$relationData[$fieldFrom]:$relationData->$fieldFrom;
                                        if(isset($fieldVal)) {
                                            if(is_array($result)) {
                                                $result[$fieldTo]   =   $fieldVal;
                                            }else{
                                                $result->$fieldTo   =   $fieldVal;
                                            }
                                        }
                                    }
                                    unset($relationData);
                                }
                                break;
                            case HAS_MANY:
                                $pk   =  is_array($result)?$result[$this->getPk()]:$result->{$this->getPk()};
                                $mappingCondition .= " AND {$mappingFk}='{$pk}'";
                                $mappingOrder =  !empty($val['mapping_order'])?$val['mapping_order']:'';
                                $mappingLimit =  !empty($val['mapping_limit'])?$val['mapping_limit']:'';
                                $relationData   =  $model->findAll($mappingCondition,$mappingFields,$mappingOrder,$mappingLimit);
                                break;
                            case MANY_TO_MANY:
                                $pk   =  is_array($result)?$result[$this->getPk()]:$result->{$this->getPk()};
                                $mappingCondition = " {$mappingFk}='{$pk}'";
                                $mappingOrder =  $val['mapping_order'];
                                $mappingLimit =  $val['mapping_limit'];
                                $mappingRelationFk = $val['relation_foreign_key']?$val['relation_foreign_key']:$model->name.'_id';
                                $mappingRelationTable  =  $val['relation_table']?$val['relation_table']:$this->getRelationTableName($model);
                                $sql = "SELECT b.{$mappingFields} FROM {$mappingRelationTable} AS a, ".$model->getTableName()." AS b WHERE a.{$mappingRelationFk} = b.{$model->getPk()} AND a.{$mappingCondition}";
                                if(!empty($val['condition'])) {
                                    $sql   .= ' AND '.$val['condition'];
                                }
                                if(!empty($mappingOrder)) {
                                    $sql .= ' ORDER BY '.$mappingOrder;
                                }
                                if(!empty($mappingLimit)) {
                                    $sql .= ' LIMIT '.$mappingLimit;
                                }
                                $relationData   =   $this->_query($sql);
                                break;
                        }
                        if(!$return){
                            if(!isset($val['as_fields'])) {
                                if(is_array($result)) {
                                    $result[$mappingName] = $relationData;
                                }else{
                                    $result->$mappingName = $relationData;
                                }
                            }
                        }else{
                            return $relationData;
                        }
                    }
            }
        }
        return $result;
    }

    public function getRelations(&$resultSet,$name='') {
        foreach($resultSet as $key=>$val) {
            $val  = $this->getRelation($val,$name);
            $resultSet[$key]    =   $val;
        }
        return $resultSet;
    }

    public function opRelation($opType,$data='',$name='')
    {
        $result =   false;
        if(is_instance_of($data,'HashMap')){
            $data = $data->toArray();
        }elseif(is_object($data)){
            $data    =   get_object_vars($data);
        }elseif(empty($data) && !empty($this->data)){
            $data = $this->data;
        }elseif(!is_array($data)){
            return false;
        }
        if(!empty($this->_link)) {
            foreach($this->_link as $key=>$val) {
                    $mappingName =  $val['mapping_name']?$val['mapping_name']:$key; 
                    if(empty($name) || $mappingName == $name) {
                        $mappingType = !empty($val['mapping_type'])?$val['mapping_type']:$val; 
                        $mappingClass  = !empty($val['class_name'])?$val['class_name']:$key;    
                        $pk =   $data[$this->getPk()];
                        if(strtoupper($mappingClass)==strtoupper($this->name)) {
                            $mappingFk   =   !empty($val['parent_key'])? $val['parent_key'] : 'parent_id';
                        }else{
                            $mappingFk   =   !empty($val['foreign_key'])?$val['foreign_key']:strtolower($this->name).'_id';   
                        }
                        if(empty($val['condition'])) {
                            $mappingCondition = "{$mappingFk}='{$pk}'";
                        }
                        $model = D($mappingClass);
                        $mappingData    =   $data[$mappingName];
                        if(is_object($mappingData)){
                            $mappingData =   get_object_vars($mappingData);
                        }
                        if(!empty($mappingData) || $opType == 'DEL') {
                            switch($mappingType) {
                                case HAS_ONE:
                                    switch (strtoupper($opType)){
                                        case 'ADD': 
                                        $mappingData[$mappingFk]    =   $pk;
                                        $result   =  $model->add($mappingData,false);
                                        break;
                                        case 'SAVE':   
                                        $result   =  $model->save($mappingData,$mappingCondition,false);
                                        break;
                                        case 'DEL': 
                                        $result   =  $model->delete($mappingCondition,'','',false);
                                        break;
                                    }
                                    break;
                                case BELONGS_TO:
                                    break;
                                case HAS_MANY:
                                    switch (strtoupper($opType)){
                                        case 'ADD'   :  
                                        $model->startTrans();
                                        foreach ($mappingData as $val){
                                            $val[$mappingFk]    =   $pk;
                                            $result   =  $model->add($val,false);
                                        }
                                        $model->commit();
                                        break;
                                        case 'SAVE' :   
                                        $model->startTrans();
                                        $pk   =  $model->getPk();
                                        foreach ($mappingData as $vo){
                                            if(isset($vo[$pk])) {
                                                $mappingCondition   =  "$pk ={$vo[$pk]}";
                                                $result   =  $model->save($vo,$mappingCondition,false);
                                            }else{
                                                $vo[$mappingFk] =  $data[$this->getPk()];
                                                $result   =  $model->add($vo,false);
                                            }
                                        }
                                        $model->commit();
                                        break;
                                        case 'DEL' :   
                                        $result   =  $model->delete($mappingCondition,'','',false);
                                        break;
                                    }
                                    break;
                                case MANY_TO_MANY:
                                    $mappingRelationFk = $val['relation_foreign_key']?$val['relation_foreign_key']:$model->name.'_id';
                                    $mappingRelationTable  =  $val['relation_table']?$val['relation_table']:$this->getRelationTableName($model);
                                    foreach ($mappingData as $vo){
                                        $relationId[]   =   $vo[$model->getPk()];
                                    }
                                    $relationId =   implode(',',$relationId);
                                    switch (strtoupper($opType)){
                                        case 'ADD': 
                                        case 'SAVE':   
                                        $this->startTrans();
                                        $this->db->remove($mappingCondition,$mappingRelationTable);
                                        $sql  = 'INSERT INTO '.$mappingRelationTable.' ('.$mappingFk.','.$mappingRelationFk.') SELECT a.'.$this->getPk().',b.'.$model->getPk().' FROM '.$this->getTableName().' AS a ,'.$model->getTableName()." AS b where a.".$this->getPk().' ='. $pk.' AND  b.'.$model->getPk().' IN ('.$relationId.") ";
                                        $result =   $model->execute($sql);
                                        if($result) {
                                            $this->commit();
                                        }else {
                                            $this->rollback();
                                        }
                                        break;
                                        case 'DEL': 
                                        $result =   $this->db->remove($mappingCondition,$mappingRelationTable);
                                        break;
                                    }
                                    break;
                            }
                    }
                }
            }
        }
        return $result;
    }

    public function deleteById($id,$autoLink=false)
    {
        $pk =   $this->getPk();
        return $this->_delete(array($pk=>$id),$pk."='$id'",0,'',$autoLink);
    }

    public function deleteByIds($ids,$limit='',$order='',$autoLink=false)
    {
        if(is_array($ids)) {
            $ids    =    implode(',',$ids);
        }
        return $this->_delete(false,$this->getPk()." IN ($ids)",$limit,$order,$autoLink);
    }

    public function deleteBy($field,$value,$limit='',$order='',$autoLink=false) {
        $condition[$field]  =  $value;
        return $this->_delete(false,$condition,$limit,$order,$autoLink);
    }

    public function delete($data=null,$limit='',$order='',$autoLink=false)
    {
        if(preg_match('/^\d+(\,\d+)*$/',$data)) {
            return $this->deleteByIds($data,$limit,$order,$autoLink);
        }
        if(empty($data)) {
            $data    =   $this->data;
        }
        $pk   =  $this->getPk();
        if(is_array($data) && isset($data[$pk]) && !is_array($data[$pk])) {
            $data   =   $this->_facade($data);
            $where  = $pk."='".$data[$pk]."'";
        }else {
            $where  =   $data;
        }
        return $this->_delete($data,$where,$limit,$order,$autoLink);
    }

    public function deleteAll($condition='',$autoLink=false)
    {
        if(is_instance_of($condition,'HashMap')) {
            $condition    = $condition->toArray();
        }elseif(empty($condition) && !empty($this->dataList)){
            $id = array();
            foreach ($this->dataList as $data){
                $data = (array)$data;
                $id[]    =   $data[$this->getPk()];
            }
            $ids = implode(',',$id);
            $condition = $this->getPk().' IN ('.$ids.')';
        }
        return $this->_delete(false,$condition,0,'',$autoLink);
    }

    public function getById($id,$fields='*',$cache=false,$relation=false,$lazy=false)
    {
        return $this->_read($this->getPk()."='{$id}'",$fields,false,null,null,null,null,null,$cache,$relation,$lazy);
    }

    public function getByIds($ids,$fields='*',$order='',$limit='',$cache=false,$relation=false,$lazy=false)
    {
        if(is_array($ids)) {
            $ids    =   implode(',',$ids);
        }
        return $this->_read($this->getPk()." IN ({$ids})",$fields,true,$order,$limit,null,null,$cache,$relation,$lazy);
    }

    public function getBy($field,$value,$fields='*',$cache=false,$relation=false,$lazy=false)
    {
        $condition[$field]  =  $value;
        return $this->_read($condition,$fields,false,null,null,null,null,null,$cache,$relation,$lazy);
    }

    public function getByAll($field,$value,$fields='*',$cache=false,$relation=false,$lazy=true)
    {
        $condition[$field]  =  $value;
        return $this->_read($condition,$fields,true,null,null,null,null,null,$cache,$relation,$lazy);
    }

    public function find($condition='',$fields='*',$cache=false,$relation=false,$lazy=false)
    {
        if(is_numeric($condition)) {
            return $this->getById($condition,$fields,$cache,$relation,$lazy);
        }
        return $this->_read($condition,$fields,false,null,null,null,null,null,$cache,$relation,$lazy);
    }

    public function xFind($condition='',$fields='*',$cache=false,$lazy=false)
    {
        return $this->find($condition,$fields,$cache,true,$lazy);
    }

    public function findAll($condition='',$fields='*',$order='',$limit='',$group='',$having='',$join='',$cache=false,$relation=false,$lazy=false)
    {
        if(is_string($condition) && preg_match('/^\d+(\,\d+)+$/',$condition)) {
            return $this->getByIds($condition,$fields,$order,$limit,$cache,$relation,$lazy);
        }
        return $this->_read($condition,$fields,true,$order,$limit,$group,$having,$join,$cache,$relation,$lazy);
    }

    public function xFindAll($condition='',$fields='*',$order='',$limit='',$group='',$having='',$join='',$cache=false)
    {
        return $this->findAll($condition,$fields,$order,$limit,$group,$having,$join,$cache,true,false);
    }

    public function topN($count,$condition='',$fields='*',$order='',$group='',$having='',$join='',$cache=false,$relation=false,$lazy=false) {
        return $this->findAll($condition,$fields,$order,$count,$group,$having,$join,$cache,$relation,$lazy);
    }

    public function query($sql,$cache=false,$lazy=false)
    {
        if(empty($sql) && !empty($this->options['sql'])) {
            $sql    =   $this->options['sql'];
        }
        if(is_array($sql)) {
            return $this->patchQuery($sql);
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            return $this->_query($sql,$cache,$lazy);
        }else{
            return false;
        }
    }

    public function execute($sql='')
    {
        if(empty($sql) && !empty($this->options['sql'])) {
            $sql    =   $this->options['sql'];
        }
        if(!empty($sql)) {
            if(strpos($sql,'__TABLE__')) {
                $sql    =   str_replace('__TABLE__',$this->getTableName(),$sql);
            }
            $result =   $this->db->execute($sql);
            return $result;
        }else {
            return false;
        }
    }

    public function patchQuery($sql=array()) {
        if(!is_array($sql)) {
            return false;
        }
        $this->startTrans();
        foreach ($sql as $_sql){
            $result   =  $this->execute($_sql);
            if(false === $result) {
                $this->rollback();
                return false;
            }
        }
        $this->commit();
        return true;
    }

    public function getField($field,$condition='')
    {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        if($this->viewModel) {
            $condition   =   $this->checkCondition($condition);
            $field         =   $this->checkFields($field);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$field);
        return $this->getCol($rs,$field);
    }

    public function getFields($field,$condition='',$sepa=' ')
    {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        if($this->viewModel) {
            $condition   =   $this->checkCondition($condition);
            $field         =   $this->checkFields($field);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$field);
        return $this->getCols($rs,$field,$sepa);
    }

    public function setField($field,$value,$condition='',$asString=true) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        if($this->viewModel) {
            $condition   =   $this->checkCondition($condition);
            $field         =   $this->checkFields($field);
        }
        return $this->db->setField($field,$value,$this->getTableName(),$condition,$asString);
    }

    public function setInc($field,$condition='',$step=1) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        if($this->viewModel) {
            $condition   =   $this->checkCondition($condition);
            $field         =   $this->checkFields($field);
        }
        return $this->db->setInc($field,$this->getTableName(),$condition,$step);
    }

    public function setDec($field,$condition='',$step=1) {
        if(empty($condition) && isset($this->options['where'])) {
            $condition   =  $this->options['where'];
        }
        if($this->viewModel) {
            $condition   =   $this->checkCondition($condition);
            $field         =   $this->checkFields($field);
        }
        return $this->db->setDec($field,$this->getTableName(),$condition,$step);
    }

    public function getCol($rs,$field)
    {
        if(!empty($rs) && count($rs)>0) {
            $result    =   $rs[0];
            $field      =   is_array($result)?$result[$field]:$result->$field;
            return $field;
        }else {
            return null;
        }
    }

    protected function getFirstCol($rs)
    {
        if(!empty($rs) && count($rs)>0) {
            $result    =   $rs[0];
            if(is_object($result)) {
                $result   =  get_object_vars($result);
            }
            return  reset($result);
        }else {
            return null;
        }
    }

    public function getCols($rs,$field,$sepa=' ') {
        if(!empty($rs)) {
            $field  =   explode(',',$field);
            $cols    =   array();
            $length  = count($field);
            foreach ($rs as $result){
                if(is_object($result)) $result  =   get_object_vars($result);
                if($length>1) {
                    $cols[$result[$field[0]]]   =   '';
                    for($i=1; $i<$length; $i++) {
                        if($i+1<$length){
                            $cols[$result[$field[0]]] .= $result[$field[$i]].$sepa;
                        }else{
                            $cols[$result[$field[0]]] .= $result[$field[$i]];
                        }
                    }
                }else{
                    $cols[]  =   $result[$field[0]];
                }
            }
            return $cols;
        }
        return null;
    }

    public function count($condition='',$field='*')
    {
        $fields = 'count('.$field.') as tpcount';
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        return $this->getFirstCol($rs)|0;
    }

    public function max($field,$condition='')
    {
        $fields = 'MAX('.$field.') as tpmax';
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
            return floatval($this->getFirstCol($rs));
        }else{
            return false;
        }
    }

    public function min($field,$condition='')
    {
        $fields = 'MIN('.$field.') as tpmin';
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
            return floatval($this->getFirstCol($rs));
        }else{
            return false;
        }
    }

    public function sum($field,$condition='')
    {
        $fields = 'SUM('.$field.') as tpsum';
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
            return floatval($this->getFirstCol($rs));
        }else{
            return false;
        }
    }

    public function avg($field,$condition='')
    {
        $fields = 'AVG('.$field.') as tpavg';
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
        }
        $rs = $this->db->find($condition,$this->getTableName(),$fields);
        if($rs) {
            return floatval($this->getFirstCol($rs));
        }else{
            return false;
        }
    }

    public function getN($position=0,$condition='',$order='',$fields='*',$relation=false)
    {
        $table      =   $this->getTableName();
        if(!empty($this->options)) {
            $condition  =   $this->options['where']?            $this->options['where']:    $condition;
            $table          =   $this->options['table']?            $this->options['table']:    $this->getTableName();
            $fields     =   $this->options['filed']?            $this->options['field']:    $fields;
            $limit          =   $this->options['limit']?            $this->options['limit']:        $limit;
            $order      =   $this->options['order']?            $this->options['order']:    $order;
            $relation       =   isset($this->options['link'])?      $this->options['link']:     $relation;
            $this->options  =   array();
        }
        if($this->viewModel) {
            $condition  =   $this->checkCondition($condition);
            $field  =   $this->checkFields($field);
        }
        if($position>=0) {
            $rs = $this->db->find($condition,$table,$fields,$order,$position.',1');
            return $this->rsToVo($rs,false,0,$relation);
        }else{
            $rs = $this->db->find($condition,$this->getTableName(),$fields,$order);
            return $this->rsToVo($rs,false,$position,$relation);
        }
    }

    public function first($condition='',$order='',$fields='*',$relation=false) {
        return $this->getN(0,$condition,$order,$fields,$relation);
    }

    public function last($condition='',$order='',$fields='*',$relation=false) {
        return $this->getN(-1,$condition,$order,$fields,$relation);
    }

    protected function cacheLockVersion($data) {
        if($this->optimLock) {
            if(is_object($data))    $data   =   get_object_vars($data);
            if(isset($data[$this->optimLock]) && isset($data[$this->getPk()])) {
                $_SESSION[$this->name.'_'.$data[$this->getPk()].'_lock_version']    =   $data[$this->optimLock];
            }
        }
    }

    public function rsToVo($resultSet,$returnList=false,$position=0,$relation='')
    {
        if($resultSet ) {
            if(!$returnList) {
                if(is_instance_of($resultSet,'ResultIterator')) {
                    $resultSet  =   $resultSet->getIterator();
                }
                if($position<0) {
                    $position = count($resultSet)-abs($position);
                }
                if(count($resultSet)<= $position) {
                    $this->error = L('_SELECT_NOT_EXIST_');
                    return false;
                }
                $result  =  $resultSet[$position];
                $this->cacheLockVersion($result);
                $this->getBlobFields($result);
                $this->filterFields($result);
                if( $this->autoReadRelations || $relation ) {
                    $result  =  $this->getRelation($result,$relation);
                }
                $result  =   auto_charset($result,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
                $this->data  =   (array)$result;
                return $result;
            }else{
                if(is_instance_of($resultSet,'ResultIterator')) {
                    return $resultSet;
                }
                $this->getListBlobFields($resultSet);
                $this->filterListFields($resultSet);
                if( $this->autoReadRelations || $relation ) {
                    $this->getRelations($resultSet,$relation);
                }
                $resultSet  =   auto_charset($resultSet,C('DB_CHARSET'),C('TEMPLATE_CHARSET'));
                $this->dataList =   $resultSet;
                return $resultSet;
            }
        }else {
            return false;
        }
    }

    public function create($data='',$batch=false)
    {
        if(true === $batch) {
            return $this->createAll($data);
        }
        if(empty($data)) {
            $data    =   $_POST;
        }
        elseif(is_instance_of($data,'HashMap')){
            $data = $data->toArray();
        }
        elseif(is_instance_of($data,'Model')){
            $data = $data->getIterator();
        }
        elseif(is_object($data)){
            $data   =   get_object_vars($data);
        }
        elseif(!is_array($data)){
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        $vo =   $this->_createData($data);
        return $vo;
    }

    public function createAll($dataList='')
    {
        if(empty($dataList)) {
            $dataList    =   $_POST;
        }
        elseif(!is_array($dataList)){
            $this->error = L('_DATA_TYPE_INVALID_');
            return false;
        }
        foreach ($dataList as $data){
            $vo =   $this->_createData($data);
            if(false === $vo) {
                return false;
            }else{
                $this->dataList[] = $vo;
            }
        }
        return $this->dataList;
    }

    private function _createData($data) {
        $vo = array();
        $type    =   'add';
        if(!$this->composite && isset($data[$this->getPk()])) {
            $value   = $data[$this->getPk()];
            $rs     = $this->db->find($this->getPk()."='{$value}'",$this->getTableName());
            if($rs && count($rs)>0) {
                $type    =   'edit';
                /*$vo = $rs[0];
                if(DATA_TYPE_OBJ == C('DATA_RESULT_TYPE')) {
                    $vo =   get_object_vars($vo);
                }*/
            }
        }
        if(!$this->_before_validation($data,$type)) {
            return false;
        }
        if(!$this->autoValidation($data,$type)) {
            return false;
        }
        if(!$this->_after_validation($data,$type)) {
            return false;
        }

        if($this->composite) {
            foreach ($data as $key=>$val){
                $vo[$key]   =   $val;
            }
        }else{
            if(isset($this->_map)) {
                foreach ($this->_map as $key=>$val){
                    if(isset($data[$key])) {
                        $data[$val] =   $data[$key];
                        unset($data[$key]);
                    }
                }
            }
            foreach ( $this->fields as $key=>$name){
                if(substr($key,0,1)=='_') continue;
                $val = isset($data[$name])?$data[$name]:null;
                if(!is_null($val) ){
                    $vo[$name] = $val;
                }elseif(    (strtolower($type) == "add" && in_array($name,$this->autoCreateTimestamps,true)) ||
                (strtolower($type) == "edit" && in_array($name,$this->autoUpdateTimestamps,true)) ){
                    if(!empty($this->autoTimeFormat)) {
                        $vo[$name] =    date($this->autoTimeFormat);
                    }else{
                        $vo[$name] = time();
                    }
                }
            }
        }

        $this->_before_operation($vo);
        $this->autoOperation($vo,$type);
        $this->_after_operation($vo);

        $this->data =   $vo;

        if(DATA_TYPE_OBJ == C('DATA_RESULT_TYPE')) {
            $vo =   (object) $vo;
        }
        return $vo;
    }

    private function autoOperation(&$data,$type) {
        if(!empty($this->_auto)) {
            foreach ($this->_auto as $auto){
                if($this->composite || in_array($auto[0],$this->fields,true)) {
                    if(empty($auto[2])) $auto[2] = 'ADD';
                    else $auto[2]   =   strtoupper($auto[2]);
                    if( (strtolower($type) == "add"  && $auto[2] == 'ADD') ||   (strtolower($type) == "edit"  && $auto[2] == 'UPDATE') || $auto[2] == 'ALL')
                    {
                        switch($auto[3]) {
                            case 'function':   
                            case 'callback': 
                                if(isset($auto[4])) {
                                    $args = $auto[4];
                                }else{
                                    $args = array();
                                }
                                array_unshift($args,$data[$auto[0]]);
                                if('function'==$auto[3]) {
                                    $data[$auto[0]]  = call_user_func_array($auto[1], $args);
                                }else{
                                    $data[$auto[0]]  =  call_user_func_array(array(&$this,$auto[1]), $args);
                                }
                                break;
                            case 'field':    
                                $data[$auto[0]] = $data[$auto[1]];
                                break;
                            case 'string':
                            default: 
                                $data[$auto[0]] = $auto[1];
                        }
                        if(false === $data[$auto[0]] ) {
                            unset($data[$auto[0]]);
                        }
                    }
                }
            }
        }
        return $data;
    }

    private function autoValidation($data,$type) {
        if(!empty($this->_validate)) {
            import("ORG.Text.Validation");
            $multiValidate  =   C('MULTI_FIELD_VALIDATE');
            $this->validateError    =   array();
            foreach($this->_validate as $key=>$val) {
                // array(field,rule,message,condition,append,when,params)
				// Field rule message must 
                // Condition to verify the conditions: 0 to verify the existence field one must verify that the value of 2 is not empty when the authentication by default to 0 
                // Append additional rules: function confirm regex equal in unique default regex 
                // When verification time: all add edit the default for all 
                // Params Additional parameters such as the use of an array defined array ( 'var1', 'var2') 
                // Required to determine whether the implementation of authentication
                if(empty($val[5]) || $val[5]=='all' || strtolower($val[5])==strtolower($type) ) {
                    if(0==strpos($val[2],'{%') && strpos($val[2],'}')) {
                        $val[2]  =  L(substr($val[2],2,-1));
                    }
                    switch($val[3]) {
                        case MUST_TO_VALIDATE: 
                            if(!$this->_validationField($data,$val)){
                                if($multiValidate) {
                                    $this->validateError[$val[0]]   =   $val[2];
                                }else{
                                    $this->error    =   $val[2];
                                    return false;
                                }
                            }
                            break;
                        case VALUE_TO_VAILIDATE: 
                            if('' != trim($data[$val[0]])){
                                if(!$this->_validationField($data,$val)){
                                    if($multiValidate) {
                                        $this->validateError[$val[0]]   =   $val[2];
                                    }else{
                                        $this->error    =   $val[2];
                                        return false;
                                    }
                                }
                            }
                            break;
                        default:  
                            if(isset($data[$val[0]])){
                                if(!$this->_validationField($data,$val)){
                                    if($multiValidate) {
                                        $this->validateError[$val[0]]   =   $val[2];
                                    }else{
                                        $this->error    =   $val[2];
                                        return false;
                                    }
                                }
                            }
                    }
                }
            }
        }
        if(!empty($this->validateError)) {
            return false;
        }else{
            // TODO Data type validation 
            // To determine whether the type of data
            return true;
        }
    }

    protected function getValidateError() {
        if(!empty($this->validateError)) {
            return $this->validateError;
        }else{
            return $this->error;
        }
    }

    private function _validationField($data,$val) {
        switch($val[4]) {
            case 'function':
            case 'callback':
                if(isset($val[6])) {
                    $args = $val[6];
                }else{
                    $args = array();
                }
                array_unshift($args,$data[$val[0]]);
                if('function'==$val[4]) {
                    return call_user_func_array($val[1], $args);
                }else{
                    return call_user_func_array(array(&$this, $val[1]), $args);
                }
            case 'confirm': 
                if($data[$val[0]] != $data[$val[1]] ) {
                    return false;
                }
                break;
            case 'in': 
                if(!in_array($data[$val[0]] ,$val[1]) ) {
                    return false;
                }
                break;
            case 'equal': 
                if($data[$val[0]] != $val[1]) {
                    return false;
                }
                break;
            case 'unique': 
                if(is_string($val[0]) && strpos($val[0],',')) {
                    $val[0]  =  explode(',',$val[0]);
                }
                if(is_array($val[0])) {
                    $map = array();
                    foreach ($val[0] as $field){
                        $map[$field]   =  $data[$field];
                    }
                    if($this->find($map)) {
                        return false;
                    }
                }else{
                    if($this->getBy($val[0],$data[$val[0]])) {
                        return false;
                    }
                }
                break;
            case 'regex':
                default: 
                if( !Validation::check($data[$val[0]],$val[1])) {
                    return false;
                }
        }
        return true;
    }

    protected function _before_validation(&$data,$type) {return true;}
    protected function _after_validation(&$data,$type) {return true;}

    protected function _before_operation(&$data) {}
    protected function _after_operation(&$data) {}

    public function getModelName()
    {
        if(empty($this->name)) {
            $prefix =   C('MODEL_CLASS_PREFIX');
            $suffix =   C('MODEL_CLASS_SUFFIX');
            if(strlen($suffix)>0) {
                $this->name =   substr(substr(get_class($this),strlen($prefix)),0,-strlen($suffix));
            }else{
                $this->name =   substr(get_class($this),strlen($prefix));
            }
        }
        return $this->name;
    }

    public function getTableName()
    {
        if(empty($this->trueTableName)) {
            if($this->viewModel) {
                $tableName = '';
                foreach ($this->viewFields as $key=>$view){
                    $Model  =   D($key);
                    if($Model) {
                        $tableName .= $Model->getTableName();
                    }else{
                        $viewTable  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
                        $viewTable .= $key;
                        $viewTable .= !empty($this->tableSuffix) ? $this->tableSuffix : '';
                        $tableName .= strtolower($viewTable);
                    }
                    if(isset($view['_as'])) {
                        $tableName .= ' '.$view['_as'];
                    }else{
                        $tableName .= ' '.$key;
                    }
                    if(isset($view['_on'])) {
                        $tableName .= ' ON '.$view['_on'];
                    }
                    if(!empty($view['_type'])) {
                        $type = $view['_type'];
                    }else{
                        $type = '';
                    }
                    $tableName   .= ' '.strtoupper($type).' JOIN ';
                    $len  =  strlen($type.'_JOIN ');
                }
                $tableName = substr($tableName,0,-$len);
                $this->trueTableName    =   $tableName;
            }else{
                $tableName  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
                if(!empty($this->tableName)) {
                    $tableName .= $this->tableName;
                }elseif(C('AUTO_NAME_IDENTIFY')){
                    $tableName .= $this->parseName($this->name);
                }else{
                    $tableName .= $this->name;
                }
                $tableName .= !empty($this->tableSuffix) ? $this->tableSuffix : '';
                if(!empty($this->dbName)) {
                    $tableName    =  $this->dbName.'.'.$tableName;
                }
                $this->trueTableName    =   strtolower($tableName);
            }
        }
        return $this->trueTableName;
    }

    public function getRelationTableName($relation)
    {
        $relationTable  = !empty($this->tablePrefix) ? $this->tablePrefix : '';
        $relationTable .= $this->tableName?$this->tableName:$this->name;
        $relationTable .= '_'.$relation->getModelName();
        $relationTable .= !empty($this->tableSuffix) ? $this->tableSuffix : '';
        return strtolower($relationTable);
    }

    public function startLazy()
    {
        $this->lazyQuery = true;
        return ;
    }

    public function stopLazy()
    {
        $this->lazyQuery = false;
        return ;
    }

    public function startLock()
    {
        $this->pessimisticLock = true;
        return ;
    }

    public function stopLock()
    {
        $this->pessimisticLock = false;
        return ;
    }

    public function startTrans()
    {
        $this->commit();
        $this->db->startTrans();
        return ;
    }

    public function commit()
    {
        return $this->db->commit();
    }

    public function rollback()
    {
        return $this->db->rollback();
    }

    public function getPk() {
        return isset($this->fields['_pk'])?$this->fields['_pk']:'id';
    }

    public function getError(){
        return $this->error;
    }

    public function getDbFields(){
        return $this->fields;
    }

    public function getLastInsID() {
        return $this->db->lastInsID;
    }

    public function getAffectRows() {
        return $this->db->numRows;
    }

    public function getLastSql() {
        return $this->db->getLastSql();
    }

    public function addConnect($config,$linkNum=NULL) {
        if(isset($this->_db[$linkNum])) {
            return false;
        }
        if(NULL === $linkNum && is_array($config)) {
            foreach ($config as $key=>$val){
                $this->_db[$key]            =    Db::getInstance($val);
            }
            return true;
        }
        $this->_db[$linkNum]            =    Db::getInstance($config);
        return true;
    }

    public function delConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            unset($this->_db[$linkNum]);
            return true;
        }
        return false;
    }

    public function closeConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->_db[$linkNum]->close();
            return true;
        }
        return false;
    }

    public function switchConnect($linkNum) {
        if(isset($this->_db[$linkNum])) {
            $this->db   =   $this->_db[$linkNum];
            return true;
        }else{
            return false;
        }
    }

    public function where($where) {
        $this->options['where'] =   $where;
        return $this;
    }

    public function order($order) {
        $this->options['order'] =   $order;
        return $this;
    }

    public function table($table) {
        $this->options['table'] =   $table;
        return $this;
    }

    public function group($group) {
        $this->options['group'] =   $group;
        return $this;
    }

    public function field($field) {
        $this->options['field'] =   $field;
        return $this;
    }

    public function limit($limit) {
        $this->options['limit'] =   $limit;
        return $this;
    }

    public function join($join) {
        if(is_array($join)) {
            $this->options['join'] =  $join;
        }else{
            $this->options['join'][]  =   $join;
        }
        return $this;
    }

    public function having($having) {
        $this->options['having']    =   $having;
        return $this;
    }

    public function lazy($lazy) {
        $this->options['lazy']  =   $lazy;
        return $this;
    }

    public function lock($lock) {
        $this->options['lock']  =   $lock;
        return $this;
    }

    public function cache($cache) {
        $this->options['cache'] =   $cache;
        return $this;
    }

    public function sql($sql) {
        $this->options['sql']   =   $sql;
        return $this;
    }

    public function data($data) {
        $this->options['data']  =   $data;
        return $this;
    }

    public function relation($name) {
        $this->options['link']  =   $name;
        return $this;
    }

    public function fetchSql($fetch=true) {
        if(in_array(strtolower($fetch),array('find','findall','save','add','delete'))) {
            $this->options['fetch'] =   true;
            return $this->{$fetch}();
        }else{
            $this->options['fetch'] =   $fetch;
        }
        return $this;
    }

    public function relationGet($name) {
        if(empty($this->data)) {
            return false;
        }
        $relation   = $this->getRelation($this->data,$name,true);
        return $relation;
    }

    public function sortBy($field, $sortby='asc', $list='' ) {
       if(empty($list) && !empty($this->dataList)) {
           $list     =   $this->dataList;
       }
       if(is_array($list)){
           $refer = $resultSet = array();
           foreach ($list as $i => $data) {
                if(is_object($data)) {
                    $data    =   get_object_vars($data);
                }
               $refer[$i] = &$data[$field];
           }
           switch ($sortby) {
               case 'asc': 
                    asort($refer);
                    break;
               case 'desc':
                    arsort($refer);
                    break;
               case 'nat':
                    natcasesort($refer);
                    break;
           }
           foreach ( $refer as $key=> $val) {
               $resultSet[] = &$list[$key];
           }
           return $resultSet;
       }
       return false;
    }

    public function toTree($list=null, $pk='id',$pid = 'pid',$child = '_child',$root=0)
    {
        if(null === $list) {
            $list   =   &$this->dataList;
        }
        $tree = array();
        if(is_array($list)) {
            $refer = array();
            foreach ($list as $key => $data) {
                $_key = is_object($data)?$data->$pk:$data[$pk];
                $refer[$_key] =& $list[$key];
            }
            foreach ($list as $key => $data) {
                $parentId = is_object($data)?$data->$pid:$data[$pid];
                if ($root == $parentId) {
                    $tree[] =& $list[$key];
                }else{
                    if (isset($refer[$parentId])) {
                        $parent =& $refer[$parentId];
                        $parent[$child][] =& $list[$key];
                    }
                }
            }
        }
        return $tree;
    }
}

?>