<?php

require( 'vendor/autoload.php' );
require( 'src/OmdbConfig.php' );
require( 'src/OmdbCall.php' );

use OmdbApp\OmdbConfig;
use OmdbApp\OmdbCall;

/**
 * ElementaryFramework
 * @source https://dev.to/na2axl/how-to-create-a-restful-api-with-php-and-the-elementary-framework-30ij
 */
use ElementaryFramework\WaterPipe\WaterPipe;
use ElementaryFramework\WaterPipe\HTTP\Request\Request;
use ElementaryFramework\WaterPipe\HTTP\Response\Response;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseHeader;
use ElementaryFramework\WaterPipe\HTTP\Response\ResponseStatus;

$basePipe = new WaterPipe;

$basePipe->request( '/api/getMovies', function ( Request $req, Response $res ) {

	$call = new OmdbCall;
	$json = $call->CallOmdb( OmdbConfig::OmdbUrl . '?apiKey=' . OmdbConfig::OmdbApiKey . '&t=a' );

	$header = new ResponseHeader();
	$header->setContentType( "application/json" );
	$status = new ResponseStatus( ResponseStatus::OkCode );

	$res
		->setHeader( $header )
		->setStatus( $status )
		->setBody( $json )
		->send();

} );

$basePipe->run();

?>