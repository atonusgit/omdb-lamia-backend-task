<?php

/**
 *	Security
 *
 *	@author Anton Valle
 */
namespace searchMB;

/**
 *	PHP-JWT
 *
 *	@source https://github.com/firebase/php-jwt
 */
use \Firebase\JWT\JWT;

class Security {

	public static function verify_password( $username, $password ) {

		return hash( 'sha256', $password ) == Config::users[$username]['password'] ? true : false;

	}

	private static function add_user_to_payload( $username ) {

		$payload = array(
			"user" => $username,
		);

		return $payload;

	}

	public static function generate_jwt( $username ) {

		return JWT::encode( Security::add_user_to_payload( $username ), Config::AccessTokenSecret );

	}

	private static function verify_user( $username ) {

		return isset( Config::users[$username] ) ? true : false;
			
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

		return isset( $username ) ? Security::verify_user( $username ) : false;

	}

	public static function validate_url( $url ) {

		return filter_var( $url, FILTER_VALIDATE_URL ) ? true : false;

	}

}