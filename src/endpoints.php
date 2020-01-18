<?php

/**
 *	Endpoints
 *
 *	@author Anton Valle
 */
require( 'vendor/autoload.php' );
require( 'src/config.php' );
require( 'src/requests.php' );
require( 'src/security.php' );

use searchMBApp\Config;
use searchMBApp\Requests;
use searchMBApp\Security;

/**
 * ElementaryFramework
 * @source https://github.com/ElementaryFramework/WaterPipe
 */
use ElementaryFramework\WaterPipe\WaterPipe;
use ElementaryFramework\WaterPipe\HTTP\Request\Request;
use ElementaryFramework\WaterPipe\HTTP\Response\Response;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseHeader;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseStatus;

$basePipe = new WaterPipe;

$basePipe->get( "/login", function ( Request $req, Response $res ) {

	$res->sendText( "Send a POST request to this endpoint.\n\nAdd username and password to your request body in order to receive your access token.\n\nTry localhost:PORT/searchMB/help endpoint to get help." );

} );

$basePipe->post( "/login", function ( Request $req, Response $res ) {

	$body = $req->getBody();
	$username = isset( $body["username"] ) ? $body["username"] : null;
	$password = isset( $body["password"] ) ? $body["password"] : null;

	if ( Security::verify_password( $username, $password ) ) {

		$jwt = Security::generate_jwt( $username );
		$res->sendJson( array( 'accessToken' => $jwt ) );

	} else {

		$res->sendText( "Incorrect username or password." );

	}

} );

$basePipe->request( '/getMovie', function ( Request $req, Response $res ) {

	$jwt = isset( $req->getHeader()['authorization'] ) ? explode( ' ', $req->getHeader()['authorization'] )[1] : null;
	$username = Security::verify_jwt( $jwt );

	if ( Security::verify_access( $username ) === true ) {

		$params = $req->getParams();

		if ( empty( $params['title'] )
			&& empty( $params['year'] )
			&& empty( $params['plot'] ) ) {

			$res->sendText( "Hi " . $username . "!\n\nWelcome to use OMDb movie search. Use parameters ?title=, ?year= and/or ?plot= for searching.\n\nExample: localhost:PORT/searchMB/getMovie?title=Superman\n\nAdditionally, you can add your own OMDb API key with ?apikey=, if author's 1000 requests is depleted :)" );

		} else {

			$apiKey = isset( $params['apikey'] ) ? $params['apikey'] : Config::OmdbApiKey;
			$call = new Requests;
			$json = $call->curl( Config::OmdbUrl . '?apiKey=' . $apiKey . '&t=' . $params["title"] . '&y=' . $params["year"] . '&plot=' . $params["plot"] );
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
			'jwt error message' => $errorMessage
		) );

	}

} );

$basePipe->request( '/getBook', function ( Request $req, Response $res ) {

	$jwt = isset( $req->getHeader()['authorization'] ) ? explode( ' ', $req->getHeader()['authorization'] )[1] : null;
	$username = Security::verify_jwt( $jwt );

	if ( Security::verify_access( $username ) === true ) {

		$params = $req->getParams();

		if ( empty( $params['isbn'] ) ) {

			$res->sendText( "Hi " . $username . "!\n\nWelcome to use OpenLibrary search. Use parameter ?isbn= for searching.\n\nExample: localhost:PORT/searchMB/getBook?isbn=9510082449" );

		} else {
				
			$call = new Requests;
			$json = $call->curl( Config::OpenLibraryUrl . 'api/books?bibkeys=ISBN:' . $params["isbn"] . '&format=json&jscmd=data');

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
			'jwt error message' => $errorMessage
		) );

	}

} );

$basePipe->request( "/help", function ( Request $req, Response $res ) {

	$res->sendText(
		"Simple instructions" .
		"\n\n\tFirst, get a unique access token" .
		"\n\t1. Get your username and password from the author" .
		"\n\t2. Send POST request to /login, with username and password attached to the request body" .
		"\n\t3. Save your unique access token" .
		"\n\n\tMake requests to OMDb" .
		"\n\t1. Get your OMDb API key from http://www.omdbapi.com/" .
		"\n\t2. Attach your unique access token to the GET/POST request header (key: Authorization, value: Bearer <access token>)" .
		"\n\t3. Make GET/POST request to /getMovie" .
		"\n\t4. Follow instructions" .
		"\n\n\tMake requests to OpenLibrary" .
		"\n\t1. Attach your unique access token to the request header (key: Authorization, value: Bearer <access token>)" .
		"\n\t2. Make GET/POST request to /getBook" .
		"\n\t3. Follow instructions"
	);

} );

$basePipe->request( "/", function ( Request $req, Response $res ) {

	$res->sendText( "Try localhost:PORT/searchMB/help endpoint to get help." );

} );