<?php

class mySfYaml extends sfYaml {
	private static $file_loaded = false;
	private static $global_config = null;

	public static function load($input) {
		self::$global_config = parent::load($input);
		self::$file_loaded = true;
		return self::$global_config;
	}

	public static function merge($input) {
		if(!self::$file_loaded) {
			return self::load($input);
		}

		$config = sfYaml::load($input);

		if($config) {
			self::recursiveMerge(self::$global_config, $config);
		}
		
		return self::$global_config;
	}

	private static function recursiveMerge(&$global_config, $config) {
		foreach($config as $key => $value) {
			if(!isset($global_config[$key])) {
				$global_config[$key] = $value;
			}
			else {
				if(is_array($value)) {
					foreach($value as $vkey => $vvalue) {
						if(!isset($global_config[$key][$vkey])) {
							$global_config[$key][$vkey] = $vvalue;
						}
						else {
							self::recursiveMerge($global_config[$key], $value);
						}
					}
				}
				else {
					$global_config[$key] = $value;
				}
			}
		}
	}

	public static function getAll() {
		return self::$global_config;
	}

	public static function get($var) {
		if(!self::$file_loaded) {
			throw new Exception('Yaml file is not loaded yet, please see sfYaml::load method');			
		}

		$road = self::$global_config;

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
