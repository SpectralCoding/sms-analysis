<?php

class ProcessController extends BaseController {

	public function process() {
		$smsfile = new SMSFile('/content/www/trips.spectralcoding.com/files/sms-20140706203308.xml');
		$smsobjs = $smsfile->getSMSObjs();
	}

}
