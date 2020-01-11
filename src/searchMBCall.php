<?php

namespace searchMBApp;

class searchMBCall {

	public function searchMBCall( $url ) {

		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		$result = curl_exec( $curl );
		curl_close( $curl );
		return $result;

	}

}