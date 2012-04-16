<?php
	require_once 'core/Request.php';
	require_once 'core/Response.php';
	require_once 'core/Trilight.php';
	require_once 'core/WebMethod.php';

	$url = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	
	Trilight::main($url);