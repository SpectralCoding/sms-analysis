<?php

class SMSFile {

	public $SMSMessages = array();

	public function __construct() {
		$this->SMSMessages = array();
	}

	public function get_sms_objects($filename, $phone) {
		if (is_dir($filename)) {
			$filelist = scandir($filename);
			foreach ($filelist as $curFile) { 
				if (substr($curFile, 0, 1) != '.') {
					$this->get_sms_objects($filename . $curFile, $phone);
				}
			}
		} else {
			try { $xmldoc = simplexml_load_file($filename); } catch (Exception $e) { return; }
			foreach ($xmldoc->sms as $xmlObj) {
				$temp = array();
				$temp['address'] = (string)$xmlObj['address'];
				if (strtoupper($phone) != "ALL") {
					if (preg_replace("/[^0-9]/", "", $temp['address']) != $phone) {
						continue;
					}
				}
				$temp['timestamp_ms'] = (string)$xmlObj['date'];
				$temp['timestamp_s'] = floor($temp['timestamp_ms'] / 1000);
				$temp['timestamp_dt'] = new DateTime("@" . $temp['timestamp_s']);
				$temp['body'] = (string)$xmlObj['body'];
				$temp['contact'] = (string)$xmlObj['contact_name'];
				$temp['uniqueid'] = md5($temp['timestamp_ms'] . '|' . $temp['address'] . '|' . $temp['contact'] . '|' . $temp['body']);
				$temp['duplicates'] = 0;
				if (array_key_exists($temp['uniqueid'], $this->SMSMessages)) {
					$this->SMSMessages[$temp['uniqueid']]['duplicates']++;
				} else {
					$this->SMSMessages[$temp['uniqueid']] = $temp;
				}
			}
		}
	}

}
