<?php

namespace App\Http\Controllers;

use App\Abstracts\Controller;
use App\Http\Validation\Validator;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\VerificationLog;

/**
 * This controller handles the user registration requests
 */
class RegistrationController extends Controller
{
	/**
	 * Handles the new user registration
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function store(): void
	{
		$validator = new Validator();
		$validation = $validator->make($_POST, [
			'email' => 'required|email|unique:Users,email',
			'password' => 'required|between:6,12',
			'phone' => 'required|phone',
		]);

		$validation->validate();

		if ($validation->fails()) {
			$errors = $validation->errors();
			$this->response(400, implode('. ', $errors->all()));
		}

		$data = $validation->getValidData();

		$user = new User();
		$user->setEmail($data['email']);
		$user->setPassword($data['password']);
		$user->setPhone($data['phone']);

		$result = $user->save();
		if (!$result) {
			$this->response(400, 'Failed adding user');
		}

		$this->response(200, 'User added! Pending phone verification', ['userId' => $user->getUserId()]);
	}

	/**
	 * Handles the phone verification
	 *
	 * @return void
	 */
	public function verifyPhone(): void
	{
		$validator = new Validator;
		$validation = $validator->make($_POST, [
			'code' => 'required|alpha_num|min:5|max:5',
			'user_id' => 'required|integer',
		]);
		$validation->validate();

		if ($validation->fails()) {
			$errors = $validation->errors();
			$this->response(400, implode('. ', $errors->all()));
		}

		$data = $validation->getValidData();

		$user = new User($data['user_id']);

		//Validate that there is no cooldown before the next verification
		$verificationLog = new VerificationLog($user);
		if ($verificationLog->coolDownCheck()) {
			$this->response(400, 'Wait 1 minute before trying again', ['status' => 'pending', 'cooldown' => true]);
		}

		//Check if the code is valid
		$verificationCode = new VerificationCode($user);
		$verificationCode->setCode($data['code']);
		$result = $verificationCode->verifyCode();

		if (!$result) {
			$this->response(400, 'Invalid Code', ['status' => 'pending', 'cooldown' => false]);
		}

		$this->response(200, 'Phone verified!', ['status' => 'verified']);
	}


	/**
	 * Handles the new code request
	 *
	 * @return void
	 */
	public function newCode(): void
	{
		$validator = new Validator;
		$validation = $validator->make($_POST, [
			'user_id' => 'required|integer',
		]);

		$validation->validate();

		if ($validation->fails()) {
			$errors = $validation->errors();
			$this->response(400, implode('. ', $errors->all()));
		}
		$data = $validation->getValidData();

		$user = new User($data['user_id']);

		$verificationCode = new VerificationCode($user);

		if (!$verificationCode->shouldSendNewCode()) {
			$this->response(400, 'Wait 1 minute before requesting a new code', []);
		}

		$verificationCode->generateCode()->sendCode();

		$this->response(200, 'New Code Sent', []);
	}
}