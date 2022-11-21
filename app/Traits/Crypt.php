<?php

namespace App\Traits;

/**
 * This trait contains functions for encryption and decryption of data and password compare
 */
trait Crypt
{
	private string $encryption_key = '0e9e4a2347b948a25661f1f778a31a5b';
	private string $ciphering = "AES-128-CTR";
	private string $encryption_iv = '3456789347894789';
	private int $options = 0;

	/**
	 * Encrypt a string a way that it's decryptable
	 *
	 * @param $string
	 *
	 * @return string
	 */
	private function encrypt($string): string
	{
		openssl_cipher_iv_length($this->ciphering);

		return openssl_encrypt($string, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
	}

	/**
	 * Decrypt a string crypted by this trait's @reversible_encrypt function
	 *
	 * @param $string
	 *
	 * @return string
	 */
	private function decrypt($string): string
	{
		return openssl_decrypt($string, $this->ciphering, $this->encryption_key, $this->options, $this->encryption_iv);
	}

	/**
	 * Encrypt a string in a way that it will be impossible to decrypt
	 *
	 * @param $string
	 *
	 * @return string
	 */
	private function passwordEncrypt($string): string
	{
		return password_hash($string, PASSWORD_DEFAULT);
	}

	/**
	 * Check if the provided password matches the saved hash crypted by @password_encrypt
	 *
	 * @param $password
	 * @param $hash
	 *
	 * @return bool
	 */
	private function verifyPassword($password, $hash): bool
	{
		return password_verify($password, $hash);
	}
}