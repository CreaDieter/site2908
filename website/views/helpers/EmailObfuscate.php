<?php
/**
 * View helper Email obfuscate Encodeert een emailadres via javascript voor de weergave in HTML tegen spambots
 * 
 * @category Studioemma
 * @package SE_View
 * @subpackage Helper
 */
class Website_Helper_EmailObfuscate {
       
       /**
        * Obfuscate an email address
        *
        * @param string $email
        * @return string
        */
       public function emailObfuscate($email, $ajax = false, $options = array()) {
               if (filter_var($email, FILTER_VALIDATE_EMAIL) !== FALSE){
                      /*** split the email into single chars ***/
                      $charArray = str_split($email);
                      /*** apply a callback funcion to each array member ***/
                      $encodedArray = filter_var($charArray, FILTER_CALLBACK, array ('options' => "Website_Helper_EmailObfuscate::makeASCII" ));
                      /*** put the string back together ***/
                      $encodedString = implode('', $encodedArray);
					  
					  $txt =  $encodedString ;
					  if(isset($options["txt"])) {
							$txt =  $options["txt"];
					  }
                      
                      if ($ajax) {
                              if (!isset($options["id"])) {
                                     $options["id"] = "emailaddress";
                              }

                              $html = '<span id="'.$options["id"].'"></span>';
                              $html .= "
<script type=\"text/javascript\">
	//<![CDATA[ 
		document.getElementById('".$options["id"]."').innerHTML = '<a ' + 'hr' + 'ef=\"ma' + 'i' + 'lt' + 'o:" . $encodedString . "\">" . $txt . "<\/a>'; 
	//]]> 
</script>";
                              return $html;
                      } else {
                              return "
<script type=\"text/javascript\"> 
	//<![CDATA[ 
		document.write('<a ' + 'hr' + 'ef=\"ma' + 'i' + 'lt' + 'o:" . $encodedString . "\">" . $txt . "<\/a>'); 
	//]]> 
</script>";
                      }
               }
               else{
                      return false;
               }
       }
       
       public static function makeASCII($char = 0) {
               return '&#' . ord($char) . ';';
       }
}

