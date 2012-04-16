<?php
/**
 * Classe principal do Framework 
 */
class Trilight
{
	/**
	 * Nome do arquivo que está sendo acessado
	 * @var	string 
	 */
	private $file;
	
	/**
	 * Nome da classe dentro do arquivo
	 * @var	string 
	 */
	private $class;
	
	/**
	 * Nome do método da classe
	 * @var	string
	 */
	private $method;
	
	/**
	 * Parâmetros enviados via POST
	 * @var	array
	 */
	private $parameters = array();
	
	/**
	 * Instância da classe ReflectionClass referente a classe que está sendo acessada
	 * @var	ReflectionClass
	 */
	private $reflection;
	
	/**
	 * Instância da classe Request
	 * @var	Request
	 */
	private $request;
	
	/**
	 * Instância da classe Response
	 * @var	Response
	 */
	private $response;
	
	/**
	 * Construtor da classe, é privado para classe não ser instanciada
	 */
	private function __construct()
	{
		$this->request = Request::getInstance();
		$this->response = Response::getInstance();
	}

	/**
	 * Método main, é executado no lugar no construtor e inicializa a classe
	 * @param	string	$url	
	 */
	public static function main($url)
	{
		$self = new self();
		try
		{
			$self->request();
			$self->route($url);
			$self->instance();
			$self->parameters();
			$self->invoke();
		}
		catch (Exception $e)
		{
			$self->response->error($e);
		}
	}

	private function request()
	{
		if (!$this->request->isPost() || !$this->request->isJson())
			throw new Exception("Request format is invalid", 400);
	}

	private function route($url)
	{
		$urls = explode('/', trim($url, '/'));
		$this->file = $urls[0];
		$this->method = $urls[1];
		$this->class = str_replace('.php', '', $this->file);
	}

	private function instance()
	{
		if (!file_exists($this->file))
			throw new Exception("The file '" . $this->file . "' not found", 404);

		require_once $this->file;

		if (!class_exists($this->class, false))
			throw new Exception("The class '" . $this->class . "' not found in '" . $this->file . "'", 404);

		if (!method_exists($this->class, $this->method))
			throw new Exception("The method '" . $this->class . "->" . $this->method . "' not found", 404);

		$this->reflection = new ReflectionClass($this->class);
		
		if(!is_subclass_of($this->class, 'WebMethod'))
			throw new Exception("The class '". $this->class ."' not extends WebMethod", 500);
		
		$method = $this->reflection->getMethod($this->method);

		if (!$method->isPublic())
			throw new Exception("The method '" . $this->class . "->" . $this->method . "()' is not public", 500);

		if ($method->isStatic())
			throw new Exception("The method '" . $this->class . "::" . $this->method . "()' is static", 500);
	}

	private function parameters()
	{
		$json = $this->request->getParams();
		$params = $this->reflection->getMethod($this->method)->getParameters();

		if (count((array) $json) != count($params))
			throw new Exception('Invalid arguments', 400);

		foreach ($params as $p)
		{
			$property = $p->getName();
			if (property_exists($json, $property))
				$this->parameters[] = $json->{$property};
			else
				throw new Exception('Invalid arguments', 400);
		}
	}

	private function invoke()
	{
		$instance = new $this->class();
		$return = call_user_func_array(array($instance, $this->method), $this->parameters);
		$this->response->json($return);
	}

}