<?php

class DelectusIndexResponse extends DelectusResponse {
	const ResponseCodeOK = 200;

	public function __construct( $data, $responseCode = self::ResponseCodeOK, $responseMessage = 'OK') {
		parent::__construct($data, $responseCode, $responseMessage);
	}

	public function isOK() {
		return $this->getResponseCode() == static::ResponseCodeOK;
	}
}