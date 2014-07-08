<?php

class MainController extends BaseController {

	public function __construct() {
		if (!Session::has('userid')) {
			Session::put('userid', $this->generate_guid());
		}
	}

	public function home() {
		$PageData = array();
		$PageData['title'] = "Upload Files";
		$PageData['userid'] = Session::get('userid');
		return View::make('pages.upload', $PageData);
	}

	public function resume($userid) {
		Session::put('userid', $userid);
		$PageData = array();
		$PageData['title'] = "Resume Session";
		$PageData['userid'] = Session::get('userid');
		return View::make('pages.resume', $PageData);
	}

	public function generate_guid() {
		$hashstr = uniqid() . '|' . Request::getClientIp() . '|' . date(str_shuffle('dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU')) . '|' . str_shuffle(date(str_shuffle('dDjlNSwzWFmMntLoYyaABgGhHisueIOPTZcrU')));
		$firsthalf = md5(substr($hashstr, 0, (strlen($hashstr)/2)));
		$secondhalf = md5(substr($hashstr, (strlen($hashstr)/2)));	
		$middlehalf = md5(substr($hashstr, (strlen($hashstr)/4), (strlen($hashstr)/2))); 
		return strtoupper(substr($firsthalf, 0, 15) . substr($secondhalf, 8, 15) . substr($middlehalf, 17, 15));
	}

}
