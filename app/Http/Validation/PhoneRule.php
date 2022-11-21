<?php

namespace App\Http\Validation;

use Rakit\Validation\Rule;

/**
 * Registers a new validation rule for validating universal phone numbers
 */
class PhoneRule extends Rule
{
	protected $message = "Required a valid phone number, example: +359899999900";

	/**
	 * The phone number validation logic
	 *
	 * @param $value
	 *
	 * @return bool
	 */
	public function check($value): bool
	{
		//Checks if the phone number starts with a + and it's followed by digits
		if (!preg_match('/^[+][0-9]/', $value)) {
			return false;
		}

		//Remove +
		$value = str_replace(['+'], '', $value);

		//Checks if the rest is digit only and if it's between the 9 and 14 symbols
		return preg_match('/^[0-9]{9,14}\z/', $value);
	}
}