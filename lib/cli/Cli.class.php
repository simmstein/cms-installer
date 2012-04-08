<?php
	
class Cli {
	public static function printMessage($title, $message, $foreground, $background, $out=STDOUT, $eol=true) {
		$cliColors = CliColors::getInstance();
		$output = '';

		if(!empty($title)) {
			$output.= $cliColors->getColoredString(' '.str_pad($title, 20, ' ', STR_PAD_RIGHT), $foreground, $background).' ';
		}

		$output.= $message.($eol ? PHP_EOL : '');

		fwrite($out, $output);
	}

	public static function printError($title, $message) {
		self::printMessage($title, $message, 'red', 'white', STDERR);
	}

	public static function  printNotice($title, $message) {
		self::printMessage($title, $message, 'light_red', 'white');
	}

	public static function printInfo($title, $message) {
		self::printMessage($title, $message, 'green', null);
	}

	public static function printInfoNoEOL($title, $message) {
		self::printMessage($title, $message, 'green', null, STDOUT, false);
	}

	public static function printBlankLine() {
		echo PHP_EOL;
	}
}
