<?php

/**
 *	Security
 *
 *	@author Anton Valle
 */
namespace searchMBApp;

/**
 * PHP-JWT
 * @source https://github.com/firebase/php-jwt
 */
use \Firebase\JWT\JWT;

class Security {

	public static function verify_password( $username, $password ) {

		if ( isset ( config::users[$username] ) ) {

			return hash( 'sha256', $password ) == config::users[$username]['password'] ? true : false;

		} else {

			return false;

		}

	}

	private static function add_user_to_payload( $username ) {

		$payload = array(
			"user" => $username,
		);

		return $payload;

	}

	public static function generate_jwt( $username ) {

		return JWT::encode( security::add_user_to_payload( $username ), config::AccessTokenSecret );

	}

	private static function verify_user( $username ) {

		return config::users[$username] ? true : false;

	}

	public static function verify_jwt( $jwt ) {

		try {

			$payload = JWT::decode( $jwt, config::AccessTokenSecret, array( 'HS256' ) );

		} catch ( \Exception $e ) {

			return $e->getMessage();

		}

		return isset( $payload->user ) ? $payload->user : false;

	}

	public static function verify_access( $username ) {

		return security::verify_user( $username );

	}

}