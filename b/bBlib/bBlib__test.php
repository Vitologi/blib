<?php
	header('Content-type: application/json');
	
	
	
	$answer = array("status"=>false, "message"=>$_REQUEST, "data"=> json_decode(file_get_contents("php://input"),true));

	//отправляем клиенту статус подключения
	echo json_encode($answer);
?>