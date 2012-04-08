<?php

class CmsInstallerApp {
	const   VERSION = 0.1;
	private static $root   = ''; 
	private static $rules  = array();
	private $opts;

	public function __construct() {
		require_once('Zend'.DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR.'Getopt.php');
		self::$root = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
		mySfYaml::load(self::$root.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'app.yml');
		Cli::printNotice('Cms Installer', 'Welcome');
	}

	public function init() {
		$this->opts = new Zend_Console_Getopt(mySfYaml::get('app_rules'));
		Cli::printInfo('Initialization', 'done.');
	}

	public function get($param) {
		try {
			return $this->opts->getOption($param);
		}
		catch(Zend_Console_Getopt_Exception $e) {
			CmsInstallerApp::showError('Invalid argument', '"'.$param.'" is not a valid argument. Show help by using --help argument.');	
		}
	}

	public static function showError($title, $message) {
		Cli::printError($title, $message);
		exit(1);
	}
}
