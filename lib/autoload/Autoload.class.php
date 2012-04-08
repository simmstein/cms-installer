<?php

class Autoload {
	public static function register() {
		set_include_path(get_include_path().PATH_SEPARATOR.dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR);
		spl_autoload_register('Autoload::load');
	}

	public static function load($class) {
		foreach(glob(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR) as $dir) {
			if(file_exists($dir.DIRECTORY_SEPARATOR.$class.'.class.php')) {
				require_once $dir.DIRECTORY_SEPARATOR.$class.'.class.php';
			}
		}
	}
}

