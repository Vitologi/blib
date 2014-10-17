<?php
	error_reporting(-1);
	if(!array_key_exists('blib', $_GET)){$_GET['blib']='bIndex';}
	//$vremiya_starta = microtime(true);
 
	require_once("b/bBlib/bBlib.php");
	bBlib::gate();
	

	//$vremya_okonchaniya = microtime(true);
	//$vremya = $vremya_okonchaniya - $vremiya_starta;
	//$vremya = round($vremya, 2);
	 
	//print "Время выполнения скрипта $vremya секунд(ы)...";
