<?php

return array(
    'WEB_LOG_RECORD'=>true, 
    'LOG_RECORD_LEVEL'       =>   array('EMERG','ALERT','CRIT','ERR','WARN','NOTIC','INFO','DEBUG','SQL'), 
    'LOG_FILE_SIZE'=>2097152,  

    'LIMIT_RESFLESH_ON'=>false,
    'LIMIT_REFLESH_TIMES'=>30, 

    'TMPL_CACHE_ON'=>true,    
    'TMPL_CACHE_TIME'=>1,  

    'SQL_DEBUG_LOG'=>true,      
    'DB_FIELDS_CACHE'=>false,    

    'DATA_CACHE_TIME'=>-1,       

    'SHOW_RUN_TIME'=>true,         
    'SHOW_ADV_TIME'=>true,        
    'SHOW_DB_TIMES'=>true,         
    'SHOW_CACHE_TIMES'=>true,       
    'SHOW_USE_MEM'=>true,          
    'SHOW_PAGE_TRACE'=>true,       
    'CHECK_FILE_CASE'  =>   true, 
);
?>