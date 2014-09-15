<?php

class MainController extends BaseController {

	public function __construct() {
		if (!Session::has('userid')) {
			// If there is no UserID then generate one.
			Session::put('userid', $this->generate_guid());
		} else {
			if (!preg_match('/^[0-9a-f]{45}$/i', Session::get('userid'))) {
				// Make sure the userid matches a known format.
				// If not, kill the value and generate a new one
				Session::forget('userid');
				Session::put('userid', $this->generate_guid());
			}
		}
	}

	public function home() {
		$PageData = array();
		$PageData['title'] = "Upload Files";
		$PageData['userid'] = Session::get('userid');
		$fileManagement = new FileManagement();	
		$PageData['filelist'] = $fileManagement->list_user_files();
		return View::make('pages.upload', $PageData);
	}

	public function resume($userid) {
		// We're resuming a session from a URL so update the userid
		Session::put('userid', $userid);
		$PageData = array();
		$PageData['title'] = "Resume Session";
		$PageData['userid'] = Session::get('userid');
		return View::make('pages.resume', $PageData);
	}

	public function uploadList() {
		// Return the list of uploaded files for the current user
		$fileManagement = new FileManagement();	
		return Response::json(array('files' => $fileManagement->list_user_files()), 200);
	}

	public function uploadDelete($filename) {
		// Delete the file and return data for success or failure.
		$fileManagement = new FileManagement();	
		$deleteData = $fileManagement->delete_user_file($filename);
		if (count($deleteData) > 0) {
			foreach ($deleteData as $key => $value) {
				if ($value == true) {
					// The file was deleted
					return Response::json(array('files' => array($deleteData)), 200);
				} else {
					// The file was not deleted
					return Response::json(array('files' => array($deleteData)), 404);
				}
			}
		} else {
			// No file found
			return Response::json(array('files' => array($deleteData)), 404);
		}
	}

	public function uploadAction() {
		$file = Input::file('file');
		$filename = $file->getClientOriginalName();
		$filesize = $file->getSize();
		// Verify file validity, size, and mime type.
		if ($file->isValid()) {
			if ($filesize = Config::get('smsanalyzer.maxsize')) {
				if (in_array($file->getMimeType(), Config::get('smsanalyzer.validtypes'))) {
					// Save the file (move it from temp storage to permenant storage).
					$fileManagement = new FileManagement();	
					return Response::json(array('files' => array($fileManagement->save_file($file))), 200);
				} else {
					return Response::json(array('files' => array(array('name' => $filename, 'size' => $filesize, 'error' => 'Invalid MIME Type'))), 200);
				}
			} else {
				return Response::json(array('files' => array(array('name' => $filename, 'size' => $filesize, 'error' => 'File too large (20MB limit)'))), 200);
			}
		} else {
			return Response::json(array('files' => array(array('name' => $filename, 'size' => $filesize, 'error' => 'Something went wrong'))), 200);
		}
	}

	public function generate_guid() {
		// Creates a hopefully random string by combining a GUID, the client's IP, date() function with date elements in a random order, and a new randomly order date function whose result is scrambled.
		$hashstr = uniqid() . '|' . Request::getClientIp() . '|' . date(str_shuffle('dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU')) . '|' . str_shuffle(date(str_shuffle('dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU')));
		$firsthalf = md5(substr($hashstr, 0, (strlen($hashstr)/2)));
		$secondhalf = md5(substr($hashstr, (strlen($hashstr)/2)));	
		$middlehalf = md5(substr($hashstr, (strlen($hashstr)/4), (strlen($hashstr)/2))); 
		return strtoupper(substr($firsthalf, 0, 15) . substr($secondhalf, 8, 15) . substr($middlehalf, 17, 15));
	}

}
