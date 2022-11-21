<?php

namespace Models;

use App\Models\User;
use DB;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{

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
	}

	public function testSave()
	{
		$result = $this->user->save();
		$this->assertTrue($result);
	}

	public function testFailedSaveUponDublicateEmail()
	{
		$this->user->save();
		$result = $this->user->save();
		$this->assertFalse($result);
	}

	public function testVerifyUser()
	{
		$this->user->save();
		$result = $this->user->verifyUser();
		$this->assertTrue($result);
	}

	public function tearDown(): void
	{
		DB::rollback();
	}
}