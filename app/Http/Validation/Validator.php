<?php

namespace App\Http\Validation;

use Rakit\Validation\Validator as RakitValidator;

/**
 * Wrapper of the RakitValidator. This is adding custom validation rules
 */
class Validator extends RakitValidator
{
	public function __construct()
	{
		parent::__construct();

		$this->addValidator('phone', new PhoneRule());
		$this->addValidator('unique', new UniqueRule());
	}
}