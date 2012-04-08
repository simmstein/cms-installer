<?php

class mySfYaml extends sfYaml {
	private static $file_loaded = false;
	private static $file_content = null;

	public static function load($input) {
		self::$file_content = parent::load($input);
		self::$file_loaded = true;
		return self::$file_content;
	}

	public static function get($var) {
		if(!self::$file_loaded) {
			throw new Exception('Yaml file is not loaded yet, please see sfYaml::load method');			
		}

		$road = self::$file_content;

		$parts = explode('_', $var);

		while(true) {
			$shift = array_shift($parts);		

			if(isset($road[$shift])) {
				if(empty($parts)) {
					return $road[$shift];
				}
				else {
					$road = $road[$shift];
				}
			}
			else {
				return null;
			}
		}
	}
}
