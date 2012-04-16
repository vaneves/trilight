Trilight é um framework PHP para criação de WebMethod, semelhante ao do .NET. A utilização é feita por meio de classes, na qual seus métodos são instanciados via requisições ajax JSON, na qual retorna um resultado no mesmo formato.

## Servidor ##
Basta criar uma classe que herda de `WebMethod` e implementar seus métodos. Por exemplo:

	<?php
	class Example extends WebMethod
	{
		public function hello($name)
		{
			return 'Hi '. $name .', welcome to Trilight!';
		}
	}

## Cliente ##
O cliente deve ser implementado com Javascript, as requisições devem ser feitas utilando AJAX + POST + JSON. Um exemplo utilizando jQuery:

	$.ajax({
		url: 'http://seusite.com/Example.php/hello',
		data: JSON.stringify({name: 'Van'}),
		type: 'POST',
		contentType: 'json',
		success: function(data) {
			console.log(data);
		}
	});

Se o cliente não obedecer os critérios de requisições POST ou JSON ou não informar os parâmetros necessários, o servidor irá retorna uma mensagem de erro 400 (Bad Request).