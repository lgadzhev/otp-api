<?php

namespace App\Models;

use App\Contracts\Model;
use DB;
use Exception;

/**
 * This models handles the logging of the code verification attempts
 */
class VerificationLog implements Model
{
	private string $attemptCode;
	private User $user;


	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Saves the verification attempt in the database
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save(): bool
	{
		$result = DB::insert('VerificationLogs', [
			'user_id' => $this->user->getUserId(),
			'attempt_code' => $this->attemptCode,
		]);

		if (!$result) {
			throw new Exception('Code saving failed!');
		}

		return true;
	}

	/**
	 * Check if the user has done more than 3 attempts in the last minute
	 *
	 * @return bool
	 */
	public function coolDownCheck(): bool
	{
		$attemptsCount = DB::queryFirstField(
			"SELECT COUNT(id) FROM VerificationLogs 
				WHERE user_id=%d AND created_on > date_sub(now(), interval 1 minute) LIMIT 3",
			$this->user->getUserId());

		return intval($attemptsCount) >= 3;
	}

	/**
	 * @param string $attemptCode
	 */
	public function setAttemptCode(string $attemptCode): void
	{
		$this->attemptCode = $attemptCode;
	}
}