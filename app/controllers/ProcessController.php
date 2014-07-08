<?php

class ProcessController extends BaseController {

	public function process($chunkSize, $phone) {
		$start = microtime(true);
		$smsFile = new SMSFile();
		$smsFile->getSMSObjs('/content/www/trips.spectralcoding.com/files/', $phone);
		$tempArr = $smsFile->SMSMessages;

		usort($tempArr, function($a, $b) {
			if ($a['timestamp_ms'] == $b['timestamp_ms']) { return 0; }
			return ($a['timestamp_ms'] < $b['timestamp_ms']) ? -1 : 1;
		}); 
		//$contacts = $this->getcontactsplit($tempArr);
		//print_r($contacts);
		$smsFile->SMSMessages = $tempArr;
		$splitdata = $this->splitdata($tempArr, $chunkSize);
		print_r($splitdata);
		//d($smsFile->SMSMessages);
		echo (microtime(true) - $start);
	}

	function getcontactsplit($MsgArr) {
		$returnArr = array();
		foreach ($MsgArr as $curMsg) {
			$address = preg_replace("/[^0-9]/", "", $curMsg['address']);
			if (!array_key_exists($address, $returnArr)) { $returnArr[$address] = array(); }
			$returnArr[$address][] = $curMsg;
		}
		return $returnArr;
	}

	function splitdata($MsgArr, $ChunkSize) {
		$chunkSeconds = $ChunkSize * 3600;
		$chunkSplit = array();
		$tempChunkName = -1;
		foreach ($MsgArr as $curSMS) {
			$chunkTS = strtotime(date('Y-m-d', $curSMS['timestamp_s']) . ' 00:00:00');
			if (($ChunkSize % 168) == 0) { $chunkTS -= date('w', $chunkTS) * 86400; }
			$chunkSplit[$chunkTS][] = $curSMS;
		}
		unset($chunkSplit[0]);				// Remove first because it will have incomplete data.
		unset($chunkSplit[$chunkTS]);		// Remove last because it will have incomplete data.
		$chunks = array();
		foreach ($chunkSplit as $chunkName => $chunkSMSs) {
			$chars = 0;
			$words = 0;
			foreach ($chunkSMSs as $curSMS) {
				$chars += strlen($curSMS['body']);
				$words += str_word_count($curSMS['body']);
			}
			$chunks[$chunkName]['messages'] = count($chunkSMSs);
			$chunks[$chunkName]['chars'] = $chars;
			$chunks[$chunkName]['cpm'] = round($chars / $chunks[$chunkName]['messages'], 2);
			$chunks[$chunkName]['words'] = $words;
			$chunks[$chunkName]['wpm'] = round($words / $chunks[$chunkName]['messages'], 2);
		}
		return $chunks;
	}

}
