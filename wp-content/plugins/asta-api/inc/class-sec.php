<?php

defined( 'ABSPATH' ) || exit;

class SEC {


	private $pwd;


	public function __construct() {

		$sec       = get_option( 'wpr-asta-sec-val' );
		$this->pwd = (
			! empty( $sec ) ?
				$this->decrypt_by_pwd(
					base64_decode( $sec ),
					'm6G1Q5fTXsnOEWjEgX92idflamakUOpmlzJZQk6ggqEahQZNOF'
				) :
				''
		);

		if ( empty( $this->pwd ) ) {

			$this->pwd = $this->str_random( 64 );

			update_option(
				'wpr-asta-sec-val',
				base64_encode(
					$this->encrypt_by_pwd(
						$this->pwd,
						'm6G1Q5fTXsnOEWjEgX92idflamakUOpmlzJZQk6ggqEahQZNOF'
					)
				)
			);
		}
	}


	/**
	 * It generates a random string of characters.
	 *
	 * @param int length The length of the string to be generated.
	 *
	 * @return string random string of length 16.
	 */
	private function str_random( int $length = 16 ) {

		$pool = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@-#$';

		return substr( str_shuffle( str_repeat( $pool, $length ) ), 0, $length );
	}


	/**
	 * It encrypts a string using a password.
	 *
	 * @param string plaintext The text to be encrypted.
	 * @param string password The password used to encrypt the data.
	 *
	 * @return string IV, the hash, and the ciphertext.
	 */
	private function encrypt_by_pwd( string $plaintext, string $password ) {

		$method = 'AES-256-CBC';
		$key    = hash( 'sha256', $password, true );
		$iv     = openssl_random_pseudo_bytes( 16 );

		$ciphertext = openssl_encrypt( $plaintext, $method, $key, OPENSSL_RAW_DATA, $iv );
		$hash       = hash_hmac( 'sha256', $ciphertext . $iv, $key, true );

		return $iv . $hash . $ciphertext;
	}


	/**
	 * It decrypts a string using a password.
	 *
	 * @param string iv_hash_ciphertext The encrypted data.
	 * @param string password The password used to encrypt the data.
	 *
	 * @return string decrypted data.
	 */
	private function decrypt_by_pwd( string $iv_hash_ciphertext, string $password ) {

		$method     = 'AES-256-CBC';
		$iv         = substr( $iv_hash_ciphertext, 0, 16 );
		$hash       = substr( $iv_hash_ciphertext, 16, 32 );
		$ciphertext = substr( $iv_hash_ciphertext, 48 );
		$key        = hash( 'sha256', $password, true );

		if ( ! hash_equals( hash_hmac( 'sha256', $ciphertext . $iv, $key, true ), $hash ) ) {
			return null;
		}

		return openssl_decrypt( $ciphertext, $method, $key, OPENSSL_RAW_DATA, $iv );
	}


	/**
	 * It takes a regex string and an array, and returns the array with all the values cleaned by the
	 * regex
	 *
	 * @param string regex_val The regex to use to clean the array.
	 * @param array array The array to be cleaned.
	 *
	 * @return array of cleaned data.
	 */
	private function regex_applied_array( string $regex_val, array $array ) {

		$cleaned = array();

		foreach ( $array as $key => $value ) {

			$clean_key = preg_replace( '/[^0-9a-zA-Z\-\_]/i', '', $key );

			if ( ! is_array( $value ) ) {

				$clean_val = preg_replace( $regex_val, '', $value );

				$cleaned[ $clean_key ] = $clean_val;

			} else {
				$cleaned[ $clean_key ] = $this->regex_applied_array( $regex_val, $value );
			}
		}

		return $cleaned;
	}


	/**
	 * It takes a string, and returns a encripted string
	 *
	 * @param string plaintext The text to be encrypted.
	 *
	 * @return string encrypted.
	 */
	public function encrypt( string $plaintext ) {
		return $this->encrypt_by_pwd( $plaintext, $this->pwd );
	}


	/**
	 * It decrypts the ciphertext using the password.
	 *
	 * @param string iv_hash_ciphertext The string that was returned by the encrypt() function.
	 *
	 * @return string decrypted.
	 */
	public function decrypt( string $iv_hash_ciphertext ) {
		return $this->decrypt_by_pwd( $iv_hash_ciphertext, $this->pwd );
	}


	/**
	 * It takes an array and returns a base64 encoded string.
	 *
	 * @param array array The array you want to encrypt.
	 *
	 * @return string base64 encoded cripted array.
	 */
	public function array_option_enc( array $array ) {
		return base64_encode( $this->encrypt( json_encode( $array ) ) );
	}


	/**
	 * It decrypts the base64 encoded string and then decodes the JSON string.
	 *
	 * @param string opt The option to be decrypted.
	 *
	 * @return array of options.
	 */
	public function array_option_dec( string $opt ) {
		return json_decode( $this->decrypt( base64_decode( $opt ) ), true );
	}


	/**
	 * It takes an array, and returns an array with all the values cleaned of any characters that are not
	 * alphanumeric, hyphens, underscores, periods, slashes, colons, or percent signs
	 *
	 * @param array The array to be cleaned.
	 *
	 * @return array cleared.
	 */
	public function clean_array_key_val( array $array ) {

		return $this->regex_applied_array(
			'/[^0-9a-zA-Z\-\_\.\/\:\%]/i',
			$array
		);
	}


	/**
	 * This PHP function cleans an array by removing any non-alphanumeric characters and special
	 * characters specific to certain languages.
	 *
	 * @param array array The input parameter is an array named .
	 *
	 * @return array function `clean_array_key_val_soft` returns an array with all non-alphanumeric
	 * characters and some special characters removed from the keys and values of the input array. The
	 * regular expression used in the function removes all characters except alphanumeric characters,
	 * whitespace, and some special characters including some non-ASCII characters.
	 */
	public function clean_array_key_val_soft( array $array ) {

		// RU special chars
		// А Б В Г Д Е Ё Ж З И Й К М Л Н О П Р С Т У Ф Х Ч Ц Ш Щ Ъ Ы Ь Э Ю Я
		// а б в г д е ё ж з и й к м л н о п р с т у ф х ч ц ш щ ъ ы ь э ю я

		return $this->regex_applied_array(
			'/[^0-9a-zA-Z\s\Ă\ă\Â\â\Î\î\Ș\ș\Ț\А\Б\В\Г\Д\Е\Ё\Ж\З\И\Й\К\М\Л\Н\О\П\Р\С\Т\У\Ф\Х\Ч\Ц\Ш\Щ\Ъ\Ы\Ь\Э\Ю\Я\а\б\в\г\д\е\ё\ж\з\и\й\к\м\л\н\о\п\р\с\т\у\ф\х\ч\ц\ш\щ\ъ\ы\ь\э\ю\я\?\!\'\"\$\€\%\&\(\)\=\[\]\\\@\#\*\-\_\;\:\.\,]/i',
			$array
		);
	}
}
