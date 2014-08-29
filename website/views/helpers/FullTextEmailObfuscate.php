<?php
/**
 * View helper FullTextEmailObfuscate Encodeert een emailadres via javascript voor de weergave in HTML tegen spambots
 * 
 * @category Studioemma
 * @package SE_View
 * @subpackage Helper
 */
require_once('EmailObfuscate.php');
class Website_Helper_FullTextEmailObfuscate {
	
	/**
	 * Email obfuscate a text
	 *
	 * @param string $text
	 * @return string
	 */
	public function fullTextEmailObfuscate($text) {
		$link_email_regex = "/<a(?:(?!<\/a>).)*?>([\w\d._-]+@[\w\d-]+\.[\w.]*[\w]{2,5})<\/a>/ms";
		$text = preg_replace_callback($link_email_regex, "Website_Helper_FullTextEmailObfuscate::obfuscationLinkCallBack", $text);
		
		$email_regex = "/([\w\d._-]+@[\w\d-]+\.[\w.]*[\w]{2,5})/ms";
		return preg_replace_callback($email_regex, "Website_Helper_FullTextEmailObfuscate::obfuscationCallBack", $text);
	}
	
	/**
	 * Call back function to obfuscate a email in a link
	 *
	 * @param array $matches
	 * @return string
	 */
	public static function obfuscationLinkCallBack($matches) {
		return Website_Helper_EmailObfuscate::emailObfuscate($matches[1]);
	}
	
	/**
	 * Call back function to obfuscate a email in a text
	 *
	 * @param array $matches
	 * @return string
	 */
	public static function obfuscationCallBack($matches) {
		return Website_Helper_EmailObfuscate::emailObfuscate($matches[0]);
	}
}