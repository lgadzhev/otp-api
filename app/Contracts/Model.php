<?php

namespace App\Contracts;

/**
 * The interface that each model should implement
 */
interface Model
{
	/**
	 * Saves the values of the model inside the database
	 *
	 * @return bool|int
	 */
	public function save(): bool|int;
}