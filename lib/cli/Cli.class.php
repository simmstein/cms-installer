<?php
	
class Cli {
	private static $width = 20;

	public static function printMessage($title, $message, $foreground, $background, $out=STDOUT, $eol=true) {
		$cliColors = CliColors::getInstance();
		$output = '';

		if(!empty($title)) {
			$output.= $cliColors->getColoredString(' '.str_pad($title, self::$width, ' ', STR_PAD_RIGHT), $foreground, $background).' ';
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

	public static function printPrompt($title) {
		self::printMessage($title, '', 'cyan', null, STDOUT, false);
	}

	public static function printBlankLine() {
		echo PHP_EOL;
	}

	public static function prompt($title, $required=true, $default=null, $authorized_values=array()) {
		$ask = true;
		$promptValue = $default;
		if(!empty($authorized_values)) {
			$title.= ' ('.implode(', ', $authorized_values).')';
		}

		if($default !== null) {
			$title.= ' ['.$default.']';
		}

		do {
			self::printPrompt($title);
			$promptValue = trim(fgets(STDIN));

			if(!$required && empty($promptValue)) {
				if(!empty($default)) {
					$promptValue = $default;
				}
				$ask = false;
			}
			elseif($required && empty($promptValue)) {
				if(!empty($default)) {
					$promptValue = $default;
					$ask = false;
				}
				else {
					self::printNotice('> Required.', '');
				}
			}
			elseif(!empty($authorized_values) && !in_array($promptValue, $authorized_values)) {
				self::printNotice('Oops', 'Forbidden value, please try again.');
			}
			else {
				$ask = false;
			}
		} while($ask);

		return $promptValue;
	}	
}
