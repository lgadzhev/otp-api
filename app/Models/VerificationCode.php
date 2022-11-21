<?php

namespace App\Models;

use App\Contracts\Model;
use App\Mocks\SMSMock;
use DB;
use Exception;

/**
 * This model is responsible for phone verification code generation, verification
 * and sending it to the user
 */
class VerificationCode implements Model
{

	private string $code;

	private User $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Saves the code in the database
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save(): bool
	{
		/*It's more secure to keep the code in the DB, instead of session */
		$result = DB::insert('VerificationCodes', [
			'user_id' => $this->user->getUserId(),
			'code'    => $this->code,
		]);

		if ( ! $result) {
			throw new Exception('Code saving failed!');
		}

		return true;
	}

	/**
	 * Deletes the old generated codes
	 *
	 * @throws Exception
	 */
	public function delete(): bool
	{
		$result = DB::delete('VerificationCodes', 'user_id=%d',
			$this->user->getUserId());
		if ( ! $result) {
			throw new Exception('Code deletion failed!');
		}

		return true;
	}

	/**
	 * Generates and saves a code in the DB that is going to be used to verify the user's phone number
	 */
	public function generateCode(): static
	{
		$this->code = rand(10000, 99999);

		//Delete the old code for that user
		$this->delete();
		//Save the new code
		$this->save();

		return $this;
	}

	/**
	 * Checks if the verification code with the combination of user id exists
	 *
	 * @return bool
	 */
	public function verifyCode(): bool
	{
		//Log the verification attempt
		$verificationLog = new VerificationLog($this->user);
		$verificationLog->setAttemptCode($this->code);
		$verificationLog->save();

		//Check if the provided by the user code is matching
		$result = DB::queryFirstRow(
			"SELECT id FROM VerificationCodes WHERE user_id=%d AND code=%s LIMIT 1",
			$this->user->getUserId(),
			$this->code
		);

		if (empty($result)) {
			return false;
		}
		
		try {
			DB::startTransaction();
			$this->user->verifyUser();
			$this->delete();

			$mockMessage = new SMSMock();
			$mockMessage->sendSuccess($this->user->getPhone());

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();

			return false;
		}

		return true;
	}

	/**
	 * Checks if 1 minute has passed since the last code generation
	 *
	 * @return bool
	 */
	public function shouldSendNewCode(): bool
	{
		$result = DB::queryFirstField(
			"SELECT COUNT(id) FROM VerificationCodes 
			WHERE user_id=%d AND created_on > date_sub(now(), interval 1 minute) LIMIT 1",
			$this->user->getUserId()
		);

		return intval($result) === 0;
	}

	/**
	 * Sends a mock sms to the user with the phone validation code
	 */
	public function sendCode(): void
	{
		$mockMessage = new SMSMock();
		$mockMessage->sendCode($this->user->getPhone(), $this->code);
	}

	/**
	 * @return string
	 */
	public function getCode(): string
	{
		return $this->code;
	}

	/**
	 * @param  string  $code
	 *
	 * @return void
	 */
	public function setCode(string $code): void
	{
		$this->code = $code;
	}
}
