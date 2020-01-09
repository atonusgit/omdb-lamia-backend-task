<?php

namespace OmdbApp;

class OmdbCall {

	public function CallOmdb( $url ) {

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $curl );
		curl_close( $curl );
		return $result;

	}

}