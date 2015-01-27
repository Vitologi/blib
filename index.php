<?php
	error_reporting(-1);
	if(!array_key_exists('blib', $_GET)){$_GET['blib']='bIndex';}
 
	require_once("b/bBlib/bBlib.php");
	bBlib::init($_GET['blib']);