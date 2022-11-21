<?php

namespace Models;

use App\Models\User;
use App\Models\VerificationLog;
use DB;
use PHPUnit\Framework\TestCase;

final class VerificationLogTest extends TestCase
{
	public VerificationLog $verificationLog;
	public User $user;

	public function setUp(): void
	{
		require __DIR__ . '/../../app/bootstrap.php';
		DB::$nested_transactions = true;
		DB::startTransaction();

		$this->user = new User();
		$this->user->setEmail('example@domain.com');
		$this->user->setPassword('password');
		$this->user->setPhone('+359899999900');
		$this->user->save();

		$this->verificationLog = new VerificationLog($this->user);
	}

	public function testSave()
	{
		$this->verificationLog->setAttemptCode('random-code');
		$result = $this->verificationLog->save();
		$this->assertTrue($result);
	}

	public function testCoolDownCheck()
	{
		$result = $this->verificationLog->coolDownCheck();
		$this->assertFalse($result);
	}

	public function testCoolDownCheckFail()
	{
		$this->verificationLog->setAttemptCode('random-code');
		$this->verificationLog->save();
		$this->verificationLog->save();
		$this->verificationLog->save();

		$result = $this->verificationLog->coolDownCheck();
		$this->assertTrue($result);
	}

	public function tearDown(): void
	{
		DB::rollback();
	}
}