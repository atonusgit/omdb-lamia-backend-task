<?php

require( 'vendor/autoload.php' );
require( 'src/OmdbConfig.php' );
require( 'src/OmdbCall.php' );

use OmdbApp\OmdbConfig;
use OmdbApp\OmdbCall;

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

	if ( !empty( $params ) ) {

		$call = new OmdbCall;
		$json = $call->CallOmdb( OmdbConfig::OmdbUrl . '?apiKey=' . OmdbConfig::OmdbApiKey . '&t=' . $params["title"] . '&y=' . $params["year"] . '&plot=' . $params["plot"] );

		$header = new ResponseHeader();
		$header->setContentType( "application/json" );
		$status = new ResponseStatus( ResponseStatus::OkCode );

		$res
			->setHeader( $header )
			->setStatus( $status )
			->setBody( $json )
			->send();

	} else {

		$res->sendText( "Welcome to use OMDB movie search. Use parameters title, year and plot for searching." );

	}

} );

$basePipe->run();

?>