<?php

namespace App\Contracts;

interface Controller
{
	/**
	 * Handles the response that is sent to the client
	 *
	 * @param int $status - the status code that the client will receive
	 * @param string $status_message - the message sent to the client
	 * @param array $fields - the data fields parsed to the client
	 *
	 * @return void
	 */
	public function response(int $status = 200, string $status_message = '', array $fields = []): void;
}