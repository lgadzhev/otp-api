<?php

namespace App\Http\Validation;

use App\Traits\Crypt;
use DB;
use Rakit\Validation\Rule;

class UniqueRule extends Rule
{

	use Crypt;

	protected $message = ":attribute :value has been used";

	protected $fillableParams = ['table', 'column'];

	public function check($value): bool
	{
		// Make sure required parameters exists
		$this->requireParameters(['table', 'column']);

		// Getting parameters
		$column = $this->parameter('column');
		$table  = $this->parameter('table');

		$result = DB::queryFirstField(
			"SELECT COUNT(*) FROM {$table} WHERE {$column}=%s",
			$this->encrypt($value));

		// True for valid, false for not
		return intval($result) === 0;
	}
}