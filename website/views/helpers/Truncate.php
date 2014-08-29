<?php
/**
 * View helper Truncate
 * 
 * @category Studioemma
 * @package SE_View
 * @subpackage Helper
 */
class Website_Helper_Truncate extends Zend_View_Helper_Abstract{
	/**
	 * Truncate a text
	 *
	 * @param string $variable
	 * @param int $maxLength
	 * @param string $replace
	 * @param bool $stripTags
	 * @param bool $inputEnitiesDecode
	 * @param bool $outputEnitiesEncode
	 * @return string
	 */
	function truncate($variable, $maxLength, $replace = "", $stripTags = true, $inputEnitiesDecode = true, $outputEnitiesEncode = true){
		$encoding = $this->view->getEncoding();
		$variable = trim($variable);
		
		if ($inputEnitiesDecode){
			$variable = html_entity_decode($variable, ENT_COMPAT, $encoding);
		}
		
		$replaceLength = mb_strlen($replace, $encoding);
		
		if ($stripTags){
			$variable = strip_tags($variable);
		
			if (mb_strlen($variable, $encoding) > $maxLength){
				$variable = mb_substr($variable, 0, ($maxLength - $replaceLength), $encoding).$replace;
			}
		}
		else {
			if (mb_strlen($variable, $encoding) > $maxLength){
				preg_match_all("/(.*?)(<(\/?)(\w*)(?:(?!<\/\w>).)*?>)([^<]*)/ms", $variable, $matches);
				$length = 0;
				$openTags = array();
				$text = "";
				
				$maxLength -= $replaceLength;
				$singleTags = array("br", "img", "input", "hr", "meta", "param");
				foreach ($matches[1] as $key => $value) {
					$substr = mb_substr($value, 0, $maxLength - $length, $encoding); 
					$text .= $substr;
					$substrLength = mb_strlen(trim($substr), $encoding);
					if (!empty($substrLength)){
						$length += mb_strlen($substr, $encoding);
					}
					
					if ($length < $maxLength){
						$tag = $matches[4][$key];
						$isClosing = !empty($matches[3][$key]);
						if (!$isClosing){
							if (!in_array(mb_strtolower($tag, $encoding), $singleTags)){
								$openTags[] = $tag;
							}
						}
						else {
							//Reverse openTags array
							$openTags = array_reverse($openTags);
							
							//Search tag in the list
							$pos = array_search($tag, $openTags);
							if ($pos !== false){
								unset($openTags[$pos]);
							}
							
							//Put the tags back in the right order
							$openTags = array_reverse($openTags);
						}
						//Add tag to text
						$text .= $matches[2][$key];
						
						//Add text that was after the tag
						$substr = mb_substr($matches[5][$key], 0, $maxLength - $length, $encoding); 
						$text .= $substr;
						$substrLength = mb_strlen(trim($substr), $encoding);
						if (!empty($substrLength)){
							$length += mb_strlen($substr, $encoding);
						}
					}
					if ($length >= $maxLength){
						break;
					}
				}
				if (!empty($openTags)){
					$text .= $replace;
					//Reverse openTags array
					$openTags = array_reverse($openTags);
					foreach ($openTags as $tag) {
						$text .= sprintf("</%s>", $tag);
					}
				}
				else {
					$text .= $replace;
				}
				
				$variable = $text;
			}
		}
		
		if ($outputEnitiesEncode){
			$variable = htmlentities($variable, ENT_COMPAT, $encoding);
		}
		
		return $variable;
	}
}