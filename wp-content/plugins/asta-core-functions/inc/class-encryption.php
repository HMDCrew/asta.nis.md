<?php


/**
 * Encryption class for encrypt/decrypt that works between programming languages.
 *
 * @author Vee Winch.
 * @link https://stackoverflow.com/questions/41222162/encrypt-in-php-openssl-and-decrypt-in-javascript-cryptojs Reference.
 */
class Encryption {


	/**
	 * @link http://php.net/manual/en/function.openssl-get-cipher-methods.php Available methods.
	 * @var string Cipher method. Recommended AES-128-CBC, AES-192-CBC, AES-256-CBC
	 */
	protected static $encrypt_method = 'AES-256-CBC';


	/**
	 * Decrypt string.
	 *
	 * @link https://stackoverflow.com/questions/41222162/encrypt-in-php-openssl-and-decrypt-in-javascript-cryptojs Reference.
	 * @param string $encrypted_string The encrypted string that is base64 encode.
	 * @param string $key The key.
	 * @return mixed Return original string value. Return null for failure get salt, iv.
	 */
	public static function decrypt( string $encrypted_string, string $key ) {
		$json = json_decode( base64_decode( $encrypted_string ), true );

		try {
			$salt = hex2bin( $json['salt'] );
			$iv   = hex2bin( $json['iv'] );
		} catch ( Exception $e ) {
			return null;
		}

		$cipher_text = base64_decode( $json['ciphertext'] );

		$iterations = intval( abs( $json['iterations'] ) );
		if ( $iterations <= 0 ) {
			$iterations = 999;
		}
		$hash_key = hash_pbkdf2( 'sha512', $key, $salt, $iterations, ( self::encrypt_method_length() / 4 ) );
		unset( $iterations, $json, $salt );

		$decrypted = openssl_decrypt( $cipher_text, self::$encrypt_method, hex2bin( $hash_key ), OPENSSL_RAW_DATA, $iv );
		unset( $cipher_text, $hash_key, $iv );

		return $decrypted;
	}


	/**
	 * Encrypt string.
	 *
	 * @link https://stackoverflow.com/questions/41222162/encrypt-in-php-openssl-and-decrypt-in-javascript-cryptojs Reference.
	 * @param string $original_string The original string to be encrypt.
	 * @param string $key The key.
	 * @return string Return encrypted string.
	 */
	public static function encrypt( string $original_string, string $key ) {
		$iv_length = openssl_cipher_iv_length( self::$encrypt_method );
		$iv        = openssl_random_pseudo_bytes( $iv_length );

		$salt       = openssl_random_pseudo_bytes( 256 );
		$iterations = 999;
		$hash_key   = hash_pbkdf2( 'sha512', $key, $salt, $iterations, ( self::encrypt_method_length() / 4 ) );

		$encrypted_string = openssl_encrypt( $original_string, self::$encrypt_method, hex2bin( $hash_key ), OPENSSL_RAW_DATA, $iv );

		$encrypted_string = base64_encode( $encrypted_string );
		unset( $hash_key );

		$output = array(
			'ciphertext' => $encrypted_string,
			'iv'         => bin2hex( $iv ),
			'salt'       => bin2hex( $salt ),
			'iterations' => $iterations,
		);
		unset( $encrypted_string, $iterations, $iv, $iv_length, $salt );

		return base64_encode( json_encode( $output ) );
	}


	/**
	 * Get encrypt method length number (128, 192, 256).
	 *
	 * @return integer.
	 */
	protected static function encrypt_method_length() {
		$number = (int) preg_replace( '/[^0-9]/i', '', self::$encrypt_method );

		return intval( abs( $number ) );
	}


	/**
	 * Set encryption method.
	 *
	 * @link http://php.net/manual/en/function.openssl-get-cipher-methods.php Available methods.
	 * @param string $cipher_method
	 */
	public static function set_cipher_method( string $cipher_method ) {
		self::$encrypt_method = $cipher_method;
	}
}
