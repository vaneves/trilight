<?php

class Request
{
	private static $instance = null;
	
	private function __construct() {}
	
	public static function getInstance()
	{
		if(self::$instance === null)
			self::$instance = new self();
		return self::$instance;
	}
	
	public function isJson()
	{
		return $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' && ((isset($_SERVER['CONTENT_TYPE']) && preg_match('@^application/json@', $_SERVER['CONTENT_TYPE'])) || (isset($_SERVER['HTTP_ACCEPT']) && preg_match('@^application/json@', $_SERVER['HTTP_ACCEPT'])));
	}

	public function isPost()
	{
		return $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	public function getParams()
	{
		return $this->getJson();
	}

	private function getJson()
	{
		$json = file_get_contents('php://input');
		return json_decode($json);
	}
	
	public function getCharset()
	{
		$charset = '';
		if(isset($_SERVER['CONTENT_TYPE']))
		{
			$matches = array();
			if(preg_match('/charset=(.*)/', $_SERVER['CONTENT_TYPE'], $matches))	
				$charset = 'charset=' . $matches[1];
		}
		return $charset;
	}
}