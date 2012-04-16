<?php

class Response
{
	private static $instance = null;
	
	private function __construct() {}
	
	public static function getInstance()
	{
		if(self::$instance === null)
			self::$instance = new self();
		return self::$instance;
	}
	
	public function error(Exception $ex)
	{
		$errors = array(
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable'
		);
		
		$code = $ex->getCode() ? $ex->getCode() : 500;

		header('HTTP/1.1 '. $code .' '. $errors[$code]);
		//header('Content-type: application/json; '. Request::getInstance()->getCharset());

		$err = new stdClass;
		$err->message = $ex->getMessage();

		echo json_encode($err);
		exit;
	}

	public function json($json)
	{
		header('Content-type: application/json; '. Request::getInstance()->getCharset());
		if ($json)
			echo json_encode($json);
		exit;
	}
}