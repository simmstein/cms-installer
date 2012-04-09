<?php

class StringUtil {
	static public function genPassword($length=8, $chars=null) {
		if(!$chars) {
			$chars = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
		}

		$password = array();
		$length_chars = strlen($chars)-1;

		for($u=0; $u<$length; $u++) {
			$rand = mt_rand(0, $length_chars);
			$password[] = $chars[$rand];
		}

		return implode('', $password);
	}	
}
