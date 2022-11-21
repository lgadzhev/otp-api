<?php

namespace App\Mocks;

use DB;

/**
 * Mock for sending SMS messages
 */
class SMSMock
{

	protected string $code_message = 'Your verification code is: ';
	protected string $success_message = 'Your registration was successful!!';

	/**
	 * Mock that saves the code message inside the database, instead of actually sending it
	 *
	 * @param $phone
	 * @param $code
	 *
	 * @return mixed
	 */
	public function sendCode($phone, $code): mixed
	{
		return DB::insert('MockMessages', [
			'phone' => $phone,
			'message' => $this->code_message . $code,
		]);
	}

	/**
	 * Mock that saves the success message inside the database, instead of actually sending it
	 *
	 * @param $phone
	 *
	 * @return mixed
	 */
	public function sendSuccess($phone): mixed
	{
		return DB::insert('MockMessages', [
			'phone' => $phone,
			'message' => $this->success_message,
		]);
	}
}