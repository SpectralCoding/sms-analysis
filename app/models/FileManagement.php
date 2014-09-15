<?php

class FileManagement {
	
	public function __construct() {

	}

	public function list_user_files() {
		// Fix the path and list all files in the directory so we can give it to jQuery-File-Upload
		$filelist = array_values(array_diff(scandir(realpath(Config::get('smsanalyzer.store') . '/' . Session::get('userid'))), array('..', '.')));
		$returnArr = array();
		foreach ($filelist as $curFile) {
			$curFileObj = array(
				'name' => $curFile,
				'size' => filesize(realpath(Config::get('smsanalyzer.store') . '/' . Session::get('userid') . '/' . $curFile)),
				'deleteUrl' => '/upload/' . $curFile,
				'deleteType' => 'DELETE'
			);
			$returnArr[] = $curFileObj;
		}
		return $returnArr;
	}

	public function delete_user_file($filename) {
		// Delete the file and double check for the return value.
		$returnArr = array();
		$fullpath = realpath(Config::get('smsanalyzer.store') . '/' . Session::get('userid') . '/' . $filename);
		if (file_exists($fullpath)) {
			unlink($fullpath);
			if (file_exists($fullpath)) {
				$returnArr[$filename] = false;
			} else {
				$returnArr[$filename] = true;
			}
		}
		return $returnArr;
	}

	public function save_file($fileObj) {
		// Move the file to permenant storage and return data on the new file location to jQuery-File-Upload
		$fileObj->move(realpath(Config::get('smsanalyzer.store') . '/' . Session::get('userid') . '/'), $fileObj->getClientOriginalName());
		return array(
			'name' => $fileObj->getClientOriginalName(),
			'size' => filesize(realpath(Config::get('smsanalyzer.store') . '/' . Session::get('userid') . '/' . $fileObj->getClientOriginalName())),
			'deleteUrl' => '/upload/' . $fileObj->getClientOriginalName(),
			'deleteType' => 'DELETE'
		);
	}


}
