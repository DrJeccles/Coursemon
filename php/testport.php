<?php

if(!function_exists('fsockopen')) {
	echo '<span style="color:red">fsockopen is not enabled</span>';
	return;
}

$tests = array(25 => 'smtp.sendgrid.com',2525 => 'smtp.sendgrid.com',587 => 'smtp.sendgrid.com',465 => 'ssl://smtp.sendgrid.com');

foreach($tests as $port => $server){
	$fp = @fsockopen($server,$port,$errno,$errstr,5);
	if($fp){
		echo '<br/><span style="color:green" >Port '.$port.' opened on your server</span>';
		fclose($fp);
	}else{
		echo '<br/><span style="color:red" >Port '.$port.' NOT opened on your server</span>';
		echo " errornum: ".$errno.' : '.$errstr;
	}
}