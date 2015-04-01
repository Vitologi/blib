<?php
	require_once("vendor/autoload.php");

	error_reporting(-1);
    $request = (array)json_decode(file_get_contents("php://input"),true)+(array)$_POST +(array)$_GET;
	if(!isset($request['blib'])){ $request['blib']='bIndex';}

	require_once("b/bBlib/bBlib.php");
	bBlib::init($request['blib']);