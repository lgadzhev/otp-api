<?php

namespace App\Abstracts;

use App\Contracts\Controller as ControllerInterface;

/**
 * Base controller abstract class that each controller should extend
 */
abstract class Controller implements ControllerInterface
{

	public function response(int $status = 200, string $status_message = '', array $fields = []): void
	{
		header("HTTP/1.1 " . $status);
		header('Access-Control-Allow-Origin: *');

		$response['status'] = $status;
		$response['status_message'] = $status_message;
		$response['fields'] = $fields;

		$json_response = json_encode($response);
		echo $json_response;
		exit();
	}
}