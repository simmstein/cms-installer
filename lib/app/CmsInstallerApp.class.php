<?php

class CmsInstallerApp {
	private static $rules  = array();
	private $opts;
	private $argv;

	public function __construct($argv) {
		require_once('Zend'.DIRECTORY_SEPARATOR.'Console'.DIRECTORY_SEPARATOR.'Getopt.php');
		$this->argv = $argv;

		$files = sfFinder::type('file')->name('*.yml')->in(ROOT.DIRECTORY_SEPARATOR.'config');

		foreach($files as $file) {
			mySfYaml::merge($file);
		}
	}

	public function init() {
		$this->opts = new Console_Getopt(mySfYaml::get('app_rules'));

		if(count($this->argv) > 1) {
			try {
				$this->opts->parse();
				foreach($this->opts->getOptions() as $arg) {
					$opt_config = mySfYaml::get('app_configuration_'.$arg);
					
					if(!isset($opt_config['callback'])) {
						throw new CmsInstallerException($arg.' has not a callback configuration (callback parameter not found).');
					}

					if(!isset($opt_config['callback']['class'])) {
						throw new CmsInstallerException($arg.' has not a callback configuration (class parameter not found).');
					}

					if(!isset($opt_config['callback']['method'])) {
						throw new CmsInstallerException($arg.' has not a callback configuration (class method not found).');
					}		

					$this->callback($opt_config['callback']['class'], $opt_config['callback']['method'], $this->get($arg));
				}
			}
			catch(Zend_Console_Getopt_Exception $e) {
				CmsInstallerApp::showError('Invalid argument', 'Show help by using --help argument.');
			}
			catch(CmsInstallerException $e) {
				CmsInstallerApp::showError('Configuration error', $e->getMessage());
			}
		}
	}

	private function callback($class, $method, $value) {
		$class.= 'Task';
		$method = 'execute'.ucfirst($method);

		if(!class_exists($class)) {
			throw new CmsInstallerException($class.' class does not exist');
		}
	
		if(!method_exists($class, $method)) {
			throw new CmsInstallerException($class.'::'.$method.' does not exist');
		}

		$callback = new $class($value, $this);
		$callback->$method();
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

	public function getOpts() {
		return $this->opts;
	}
}
