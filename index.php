<?php
	$vremiya_starta = microtime(true);
 
	require_once("b/bBlib/bBlib.php");
	bBlib::gate();
	
	
	require_once("b/bIndex/bIndex.html");
	$vremya_okonchaniya = microtime(true);
	$vremya = $vremya_okonchaniya - $vremiya_starta;
	//$vremya = round($vremya, 2);
	 
	print "Время выполнения скрипта $vremya секунд(ы)...";
