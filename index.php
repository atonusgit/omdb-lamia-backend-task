<?php

require( 'vendor/autoload.php' );
require( 'src/searchMBConfig.php' );
require( 'src/searchMBCall.php' );
require( 'src/searchMBSecurity.php' );

use searchMBApp\searchMBConfig;
use searchMBApp\searchMBCall;
use searchMBApp\searchMBSecurity;

/**
 * ElementaryFramework
 * @source https://github.com/ElementaryFramework/WaterPipe
 */
use ElementaryFramework\WaterPipe\WaterPipe;
use ElementaryFramework\WaterPipe\HTTP\Request\Request;
use ElementaryFramework\WaterPipe\HTTP\Response\Response;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseHeader;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseStatus;

/**
 * PHP-JWT
 * @source https://github.com/firebase/php-jwt
 */
use \Firebase\JWT\JWT;

$basePipe = new WaterPipe;

$basePipe->get( "/login", function ( Request $req, Response $res ) {

	$res->sendText( "Send a POST request to this endpoint. Attach username and password in order to get your access token." );

} );

$basePipe->post( "/login", function ( Request $req, Response $res ) {

	$body = $req->getBody();
	$username = isset( $body["username"] ) ? $body["username"] : null;
	$password = isset( $body["password"] ) ? $body["password"] : null;

	if ( searchMBSecurity::verify_password( $username, $password ) ) {

		$jwt = searchMBSecurity::generate_jwt( $username );
		$res->sendJson( array( 'accessToken' => $jwt ) );

	} else {

		$res->sendText( "Incorrect username or password." );

	}

} );

$basePipe->request( '/getMovie', function ( Request $req, Response $res ) {

	$jwt = isset( $req->getHeader()['authorization'] ) ? explode( ' ', $req->getHeader()['authorization'] )[1] : null;
	$username = searchMBSecurity::verify_jwt( $jwt );

	if ( searchMBSecurity::verify_user( $username ) === true ) {

		$params = $req->getParams();

		if ( empty( $params['title'] )
			&& empty( $params['year'] )
			&& empty( $params['plot'] ) ) {

			$res->sendText( 'Hi ' . $username . '! Welcome to use OMDB movie search. Use parameters ?title=, ?year= and/or ?plot= for searching.' );

		} else {

			$call = new searchMBCall;
			$json = $call->call( searchMBConfig::OmdbUrl . '?apiKey=' . searchMBConfig::OmdbApiKey . '&t=' . $params["title"] . '&y=' . $params["year"] . '&plot=' . $params["plot"] );
			$header = new ResponseHeader();
			$header->setContentType( "application/json" );
			$status = new ResponseStatus( ResponseStatus::OkCode );
			$res
				->setHeader( $header )
				->setStatus( $status )
				->setBody( $json )
				->send();

		}

	} else {

		$errorMessage = $username ? $username : 'none';
		$res->sendJson( array(
			'error' => 'Verifying access token failed.',
			'error message' => $errorMessage
		) );

	}

} );

$basePipe->request( '/getBook', function ( Request $req, Response $res ) {

	$jwt = isset( $req->getHeader()['authorization'] ) ? explode( ' ', $req->getHeader()['authorization'] )[1] : null;
	$username = searchMBSecurity::verify_jwt( $jwt );

	if ( searchMBSecurity::verify_user( $username ) === true ) {

		$params = $req->getParams();

		if ( empty( $params['isbn'] ) ) {

			$res->sendText( 'Hi ' . $username . '! Welcome to use OpenLibrary search. Use parameter ?isbn= for searching.' );

		} else {
				
			$call = new searchMBCall;
			$json = $call->call( searchMBConfig::OpenLibraryUrl . 'api/books?bibkeys=ISBN:' . $params["isbn"] . '&format=json&jscmd=data');

			$header = new ResponseHeader();
			$header->setContentType( "application/json" );
			$status = new ResponseStatus( ResponseStatus::OkCode );

			$res
				->setHeader( $header )
				->setStatus( $status )
				->setBody( $json )
				->send();

		}

	} else {

		$errorMessage = $username ? $username : 'none';
		$res->sendJson( array(
			'error' => 'Verifying access token failed.',
			'error message' => $errorMessage
		) );

	}

} );

$basePipe->run();

?>