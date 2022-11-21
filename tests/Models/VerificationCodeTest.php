<?php

namespace Models;

use App\Models\User;
use App\Models\VerificationCode;
use DB;
use PHPUnit\Framework\TestCase;

final class VerificationCodeTest extends TestCase
{
	public VerificationCode $verificationCode;
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

		$this->verificationCode = new VerificationCode($this->user);
	}

	public function testGenerateCode()
	{
		$result = $this->verificationCode->generateCode();
		$this->assertInstanceOf(VerificationCode::class, $result);
	}

	public function testVerifyCode()
	{
		//Fetch the code from the database
		$code = DB::queryFirstField('SELECT code FROM VerificationCodes WHERE user_id=%d', $this->user->getUserId());

		$this->verificationCode->setCode($code);
		$result = $this->verificationCode->verifyCode();
		$this->assertTrue($result);
	}

	public function testVerifyCodeFail()
	{
		$this->verificationCode->setCode('invalidCode');
		$result = $this->verificationCode->verifyCode();
		$this->assertFalse($result);
	}

	public function testShouldSendNewCodeFail()
	{
		$result = $this->verificationCode->shouldSendNewCode();
		$this->assertFalse($result);
	}

	public function tearDown(): void
	{
		DB::rollback();
	}
}