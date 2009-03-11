<?php 

class FilterDbSession extends Base
{
   protected $lifeTime=''; 

   protected $sessionTable='';

   protected $dbHandle; 

    public function open($savePath, $sessName) { 
       // get session-lifetime 
       $this->lifeTime = C('SESSION_EXPIRE'); 
	   $this->sessionTable	 =	 C('DB_PREFIX').C('SESSION_TABLE');
       $dbHandle = mysql_connect(C('DB_HOST'),C('DB_USER'),C('DB_PWD')); 
       $dbSel = mysql_select_db(C('DB_NAME'),$dbHandle); 
       // return success 
       if(!$dbHandle || !$dbSel) 
           return false; 
       $this->dbHandle = $dbHandle; 
       return true; 
    } 

   public function close() { 
       $this->gc(ini_get('session.gc_maxlifetime')); 
       // close database-connection 
       return mysql_close($this->dbHandle); 
   } 

   public function read($sessID) { 
       // fetch session-data 
       $res = mysql_query("SELECT session_data AS d FROM ".$this->sessionTable." WHERE session_id = '$sessID'   AND session_expires >".time(),$this->dbHandle); 
       // return data or an empty string at failure 
       if($res) {
           $row = mysql_fetch_assoc($res);
           $data = $row['d'];
            if( function_exists('gzcompress')) {
                //$data   =   gzuncompress($data);
            }
           return $data; 
       }
       return ""; 
   } 

   public function write($sessID,$sessData) { 
       // new session-expire-time 
       $newExp = time() + $this->lifeTime; 
        if( function_exists('gzcompress')) {
            //$sessData   =   gzcompress($sessData,3);
        }
       // is a session with this id in the database? 
       $res = mysql_query("SELECT * FROM ".$this->sessionTable." WHERE session_id = '$sessID'",$this->dbHandle); 
       // if yes, 
       if(mysql_num_rows($res)) { 
           // ...update session-data 
           mysql_query("UPDATE ".$this->sessionTable."  SET session_expires = '$newExp', session_data = '$sessData' WHERE session_id = '$sessID'",$this->dbHandle); 
           // if something happened, return true 
           if(mysql_affected_rows($this->dbHandle)) 
               return true; 
       } 
       // if no session-data was found, 
       else { 
           // create a new row 
           mysql_query("INSERT INTO ".$this->sessionTable." (  session_id, session_expires, session_data)  VALUES( '$sessID', '$newExp',  '$sessData')",$this->dbHandle); 
           // if row was created, return true 
           if(mysql_affected_rows($this->dbHandle)) 
               return true; 
       } 
       // an unknown error occured 
       return false; 
   } 

   public function destroy($sessID) { 
       // delete session-data 
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_id = '$sessID'",$this->dbHandle); 
       // if session was deleted, return true, 
       if(mysql_affected_rows($this->dbHandle)) 
           return true; 
       // ...else return false 
       return false; 
   } 

   public function gc($sessMaxLifeTime) { 
       // delete old sessions 
       mysql_query("DELETE FROM ".$this->sessionTable." WHERE session_expires < ".time(),$this->dbHandle); 
       // return affected rows 
       return mysql_affected_rows($this->dbHandle); 
   } 

    public function execute() 
    {
    	session_set_save_handler(array(&$this,"open"), 
                         array(&$this,"close"), 
                         array(&$this,"read"), 
                         array(&$this,"write"), 
                         array(&$this,"destroy"), 
                         array(&$this,"gc")); 

    }
}

?>