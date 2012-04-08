<?php
	
class Cli {
	public static function printMessage($title, $message, $foreground, $background, $out=STDOUT) {
		$cliColors = CliColors::getInstance();
		$output = '';

		if(!empty($title)) {
			$output.= $cliColors->getColoredString(' '.str_pad($title, 20, ' ', STR_PAD_RIGHT), $foreground, $background).' ';
		}
		$output.= $message.PHP_EOL;

		fwrite($out, $output);
	}

	public static function printError($title, $message) {
		self::printMessage($title, $message, 'red', 'white', STDERR);
	}

	public static function  printNotice($title, $message) {
		self::printMessage($title, $message, 'white', 'cyan');
	}

	public static function printInfo($title, $message) {
		self::printMessage($title, $message, 'green', null);
	}
}
