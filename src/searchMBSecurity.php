<?php

namespace searchMBApp;

use \Firebase\JWT\JWT;

class searchMBSecurity {

	public static function verify_password( $username, $password ) {

		if ( isset ( searchMBConfig::users[$username] ) ) {

			return hash( 'sha256', $password ) == searchMBConfig::users[$username]['password'] ? true : false;

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

		return JWT::encode( searchMBSecurity::add_user_to_payload( $username ), searchMBConfig::AccessTokenSecret );

	}

	public static function verify_user( $username ) {

		return searchMBConfig::users[$username] ? true : false;

	}

	public static function verify_jwt( $jwt ) {

		try {

			$payload = JWT::decode( $jwt, searchMBConfig::AccessTokenSecret, array( 'HS256' ) );

		} catch ( \Exception $e ) {

			return $e->getMessage();

		}

		return isset( $payload->user ) ? $payload->user : false;

	}

}