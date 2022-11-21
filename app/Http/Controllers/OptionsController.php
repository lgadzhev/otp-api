<?php

namespace App\Http\Controllers;

use App\Abstracts\Controller;

/**
 * This controller handles the preflight request made to the application
 */
class OptionsController extends Controller
{
	/**
	 * Sets the cross-origin headers
	 *
	 * @return void
	 */
	public function crossOrigin(): void
	{
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type');
		header('Access-Control-Max-Age: 1728000');
		header('Content-Length: 0');
		header('Content-Type: text/plain');
		die();
	}
}