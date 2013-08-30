<?php
/**
 * Created by PhpStorm.
 * User: tilastman
 * Date: 25.08.13
 * Time: 0:11
 */
include_once("static/config.php");


function __autoload($name) {
	$dir = '';
	if(strtolower(substr($name, 0, 1)) == 'c') {
		$dir = "controller/";
	} elseif(strtolower(substr($name, 0, 1)) == 'm') {
		$dir = "model/";
	}

	include_once($dir.$name.".php");
}

class Run {
	static function go() {
		$controller = (isset($_GET['controller'])) ? $_GET['controller'] : 'base';

		switch($controller) {
			case 'ajax':
				$issue = new C_Ajax();
			break;
			case 'base':
			default:
			$issue = new C_Base();
		}

		$issue->Request();
	}
} 