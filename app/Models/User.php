<?php

namespace App\Models;

use App\Contracts\Model;
use App\Traits\Crypt;
use DB;
use Exception;

/**
 * The user model responsible for the user related logic and database operations
 */
class User implements Model
{
	use Crypt;

	private string $email;
	private string $password;
	private string $phone;
	private int $userId;

	/**
	 * @param bool|int $userId - pass user id to build the user model for already existing user
	 */
	public function __construct(bool|int $userId = false)
	{
		if (!$userId) {
			return;
		}

		$this->userId = $userId;
		$this->buildUser();
	}

	/**
	 * Saves the user in the database and returns the ID
	 *
	 * @return bool|int
	 */
	public function save(): bool|int
	{
		try {
			DB::startTransaction();

			//*The DB library that is being used is taking care of preparing the values to protect against sql injections */
			$result = DB::insert('Users', [
				'email' => $this->encrypt($this->email),
				'password' => $this->passwordEncrypt($this->password),
				'phone' => $this->encrypt($this->phone),
				'status' => 'pending',
			]);

			if (!$result) {
				throw new Exception('User saving failed!');
			}

			$this->userId = DB::insertId();

			//Generate and send code for the current user
			$verificationCode = new VerificationCode($this);
			$verificationCode->generateCode()->sendCode();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return false;
		}

		return true;
	}

	/**
	 * Changes the status of the user to verified
	 *
	 * @return bool
	 */
	public function verifyUser(): bool
	{
		$result = DB::update(
			'Users',
			['status' => 'verified'],
			"id=%d",
			$this->userId
		);

		return !empty($result);
	}

	/**
	 * Fetches the user from the database and sets it's data
	 *
	 * @return void
	 */
	private function buildUser(): void
	{
		$row
			= DB::queryFirstRow(
			"SELECT id, email, phone FROM Users WHERE id=%d LIMIT 1",
			$this->userId
		);

		$this->email = $this->decrypt($row['email']);
		$this->phone = $this->decrypt($row['phone']);
		$this->userId = $row['id'];
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * @return string
	 */
	public function getPhone(): string
	{
		return $this->phone;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone(string $phone): void
	{
		$this->phone = $phone;
	}

	/**
	 * @return int
	 */
	public function getUserId(): int
	{
		return $this->userId;
	}
}
