<?php

class Autoload {
	public static function register() {
		set_include_path(get_include_path().PATH_SEPARATOR.ROOT.'lib');
		spl_autoload_register('Autoload::load');
	}

	public static function load($class) {
		require_once ROOT.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'util'.DIRECTORY_SEPARATOR.'sfFinder.class.php';

		$files = sfFinder::type('file')->name('*.class.php')->in(ROOT.DIRECTORY_SEPARATOR.'lib');

		foreach($files as $file) {
			if(basename($file) == $class.'.class.php') {
				require_once $file;
			}
		}
	}
}

