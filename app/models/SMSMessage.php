<?php

class SMSMessage {

	var $uniqueid;
	var $body;
	var $timestamp;
	var $address;
	var $contact;

	public function __construct($xmlObj) {
		$this->body = (string)$xmlObj['body'];
		$this->timestamp = new DateTime("@" . floor((string)$xmlObj['date'] / 1000));
		$this->address = (string)$xmlObj['address'];
		$this->contact = (string)$xmlObj['contact_name'];
		$this->uniqueid = md5($this->timestamp->getTimestamp() . '|' . $this->address . '|' . $this->contact . '|' . $this->body);
	}

}
