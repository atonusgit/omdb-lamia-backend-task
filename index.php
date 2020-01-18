<?php

/**
 *	searchMB
 *
 *	searchMB (search Movies and Books) is a RESTful API to fetch information 
 *	about movies from OMDb (omdbapi.com) and books from OpenLibrary (openlibrary.org).
 *
 *	Try localhost:PORT/searchMB/help endpoint to get help.
 *
 *	@author Anton Valle
 */
include( 'src/endpoints.php' );

$basePipe->run();

?>