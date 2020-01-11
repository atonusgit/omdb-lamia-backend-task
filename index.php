<?php

require( 'vendor/autoload.php' );
require( 'src/searchMBConfig.php' );
require( 'src/searchMBCall.php' );

use searchMBApp\searchMBConfig;
use searchMBApp\searchMBCall;

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

$basePipe->request( '/api/getMovie', function ( Request $req, Response $res ) {

	$params = $req->getParams();

	if ( empty( $params['title'] )
		&& empty( $params['year'] )
		&& empty( $params['plot'] ) ) {

		$res->sendText( "Welcome to use OMDB movie search. Use parameters ?title=, ?year= and/or ?plot= for searching." );

	} else {

		$call = new searchMBCall;
		$json = $call->searchMBCall( searchMBConfig::OmdbUrl . '?apiKey=' . searchMBConfig::OmdbApiKey . '&t=' . $params["title"] . '&y=' . $params["year"] . '&plot=' . $params["plot"] );

		$header = new ResponseHeader();
		$header->setContentType( "application/json" );
		$status = new ResponseStatus( ResponseStatus::OkCode );

		$res
			->setHeader( $header )
			->setStatus( $status )
			->setBody( $json )
			->send();

	}

} );

$basePipe->request( '/api/getBook', function ( Request $req, Response $res ) {

	$params = $req->getParams();

	if ( empty( $params['isbn'] ) ) {

		$res->sendText( "Welcome to use OpenLibrary search. Use parameter ?isbn= for searching." );

	} else {
			
		$call = new searchMBCall;
		$json = $call->searchMBCall( searchMBConfig::OpenLibraryUrl . 'api/books?bibkeys=ISBN:' . $params["isbn"] . '&format=json&jscmd=data');

		$header = new ResponseHeader();
		$header->setContentType( "application/json" );
		$status = new ResponseStatus( ResponseStatus::OkCode );

		$res
			->setHeader( $header )
			->setStatus( $status )
			->setBody( $json )
			->send();

	}

} );

$basePipe->run();

?>