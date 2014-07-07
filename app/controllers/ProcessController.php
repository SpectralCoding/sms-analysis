<?php

class ProcessController extends BaseController {

	public function process($chunkSize) {
		$start = microtime(true);
		$smsFile = new SMSFile();
		$smsFile->getSMSObjs('/content/www/trips.spectralcoding.com/files/');
		echo count($smsFile->SMSMessages) . "\n";
		$tempArr = $smsFile->SMSMessages;
		usort($tempArr, function($a, $b) {
			if ($a['timestamp_ms'] == $b['timestamp_ms']) { return 0; }
			return ($a['timestamp_ms'] < $b['timestamp_ms']) ? -1 : 1;
		}); 
		$smsFile->SMSMessages = $tempArr;
		$chunkSeconds = $chunkSize * 3600;
		$chunkSplit = array();
		$tempChunkName = -1;
		foreach ($tempArr as $curSMS) {
			$chunkTS = strtotime(date('Y-m-d', $curSMS['timestamp_s']) . ' 00:00:00');
			if (($chunkSize % 168) == 0) { $chunkTS -= date('w', $chunkTS) * 86400; }
			$chunkSplit[$chunkTS][] = $curSMS;
		}
		unset($chunkSplit[0]);				// Remove first because it will have incomplete data.
		unset($chunkSplit[$chunkTS]);		// Remove last because it will have incomplete data.
		foreach ($chunkSplit as $chunkName => $chunkSMSs) {
			echo $chunkName . " - " . count($chunkSMSs) . "\n";
		}
		//d($smsFile->SMSMessages);
		echo (microtime(true) - $start);
	}

}
