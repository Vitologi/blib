<?php
	if(!$_GET['blib']){$_GET['blib']='bIndex';}
	$vremiya_starta = microtime(true);
 
	require_once("b/bBlib/bBlib.php");
	bBlib::gate();
	

	$vremya_okonchaniya = microtime(true);
	$vremya = $vremya_okonchaniya - $vremiya_starta;
	//$vremya = round($vremya, 2);
	 
	print "Время выполнения скрипта $vremya секунд(ы)...";
