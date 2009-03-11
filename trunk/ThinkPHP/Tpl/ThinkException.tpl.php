<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Error</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8"/>
<meta name="Generator" content="EditPlus"/>

</head>
<body>
<div class="notice">
<h2>System Error </h2>
<?php if(isset($e['file'])) {?>
<p><strong>LOCATION:</strong>　FILE: <span class="red"><?php echo $e['file'] ;?></span>　LINE: <span class="red"><?php echo $e['line'];?></span></p>
<?php }?>
<p class="title">[ DETAIL ]</p>
<p class="message"><?php echo $e['message'];?></p>
<?php if(isset($e['trace'])) {?>
<p class="title">[ TRACE ]</p>
<p id="trace">
<?php echo nl2br($e['trace']);?>
</p>	
<?php }?>
</div>
<div> ThinkPHP <sup><?php echo THINK_VERSION;?></sup></div>
</body>
</html>
