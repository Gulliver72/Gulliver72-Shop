<?php
/*
###################################################################################
  Bigware Shop 2.3
  Release Datum: 23.08.2015
  
  Bigware Shop
  http://www.bigware.de

  Copyright (c) 2015 Bigware LTD
  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
  (c) 2015	Bigware LTD

  Copyrightvermerke duerfen nicht entfernt werden.
  ------------------------------------------------------------------------
  Dieses Programm ist freie Software. Sie koennen es unter den Bedingungen
  der GNU General Public License, wie von der Free Software Foundation
  veroeffentlicht, weitergeben und/oder modifizieren, entweder gemaess Version 2 
  der Lizenz oder (nach Ihrer Option) jeder spaeteren Version.
  Die Veroeffentlichung dieses Programms erfolgt in der Hoffnung, dass es Ihnen
  von Nutzen sein wird, aber OHNE IRGENDEINE GARANTIE, sogar ohne die
  implizite Garantie der MARKTREIFE oder der VERWENDBARKEIT FUER EINEN
  BESTIMMTEN ZWECK. Details finden Sie in der GNU General Public License.
  
  Sie sollten eine Kopie der GNU General Public License zusammen mit diesem
  Programm erhalten haben. Falls nicht, schreiben Sie an die Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.

  Infos:
  ------------------------------------------------------------------------
  Der Bigware Shop wurde vor vielen Jahren bereits aus dem bekannten Shopsystem osCommerce
  weiter- und neuentwickelt.
  Der Bigware Shop legt im hohen Masse Wert auf Bedienerfreundlichkeit, beinhaltet eine leichte
  Installation, viele neue professionelle Werkzeuge und zeichnet sich aus durch eine grosse 
  Community, die bei Problemen weiterhelfen kann.
  
  Der Bigware Shop ist auf jedem System lauffaehig, welches eine PHP Umgebung
  (ab PHP 4.1.3) und mySQL zur Verfuegung stellt und auf Linux basiert.
 
  Hilfe erhalten Sie im Forum auf www.bigware.de 
  
  -----------------------------------------------------------------------
  
 ##################################################################################

Captcha f�r Bigware 2.1 by Ede & Tigerstyle


*/
?>
<?php   
function go_validate_email($email) {
	$valid_address = true;
	$mail_pat = '^(.+)@(.+)$';
	$valid_chars = "[^] \(\)<>@,;:\.\\\"\[]";
	$atom = "$valid_chars+";
	$quoted_user='(\"[^\"]*\")';
	$word = "($atom|$quoted_user)";
	$user_pat = "^$word(\.$word)*$";
	$ip_domain_pat='^\[([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\]$';
	$domain_pat = "^$atom(\.$atom)*$";
	if (preg_match("/$mail_pat/i", $email, $components)) {
		$user = $components[1];
		$domain = $components[2]; 
		if (preg_match("/$user_pat/i", $user)) { 
			if (preg_match("/$ip_domain_pat/i", $domain, $ip_components)) { 
				for ($i=1;$i<=4;$i++) {
					if ($ip_components[$i] > 255) {
						$valid_address = false;
						break;
					}
				}
			}
			else { 
				if (preg_match("/$domain_pat/i", $domain)) {
					$domain_components = explode(".", $domain); 
					if (sizeof($domain_components) < 2) {
						$valid_address = false;
					} else {
						$top_level_domain = strtolower($domain_components[sizeof($domain_components)-1]); 
						if (preg_match('/^[a-z][a-z]$/i', $top_level_domain) != 1) {
							$tld_pattern = ''; 
							$tlds = file(FOLDER_RELATIV_INCLUDES . 'tld.txt');
							while (list(,$line) = each($tlds)) { 
								$words = explode('#', $line);
								$tld = trim($words[0]); 
								if (preg_match('/^[a-z]{3,}$/i', $tld) == 1) {
									$tld_pattern .= '^' . $tld . '$|';
								}
							} 
							$tld_pattern = substr($tld_pattern, 0, -1);
							if (preg_match("/$tld_pattern/i", $top_level_domain) == 0) {
								$valid_address = false;
							}
						}
					}
				}
				else {
					$valid_address = false;
				}
			}
		}
		else {
			$valid_address = false;
		}
	}
	else {
		$valid_address = false;
	}
	if ($valid_address && TYPE_IN_EMAIL_ADDRESS_CHECK == 'true') {
		if (!checkdnsrr($domain, "MX") && !checkdnsrr($domain, "A")) {
			$valid_address = false;
		}
	}
	return $valid_address;
}

/*
Captcha
*
* Modifiziert f�r Bigware Shop 2.3 by Tigerstyle & Ede
* Feb. 2012, optimiert von Richy f�r Strato & Co. , Mai 2012
*/

$sql=go_db_query('SELECT * FROM captcha_settings');
  $row=go_db_fetch_array($sql);  
  $key = $row['captcha_key'];
  $possible = $row['possible'];
  $cap_width = $row['cap_width'];
  $cap_height = $row['cap_height'];
  $character = $row['characters'];
  $backg_color = $row['background_color'];
  $txt_color = $row['text_color'];
  $nois_color = $row['noise_color'];
  $captcha_font = $row['captcha_font'];
  define('CAPTCHA_FONT', FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_PICTURES . "captcha/capture.ttf");

// BEGIN - CAPTCHA Encryption Functionality
  function go_CAP_RC4($data) {
	global $key;
	$salt = substr(sha1(md5($key)),0,12); // Thanx Richy
	return crypt($data,$salt);
  }

  function go_urlsafe_b64encode($string) {
    $data = base64_encode($string);
    $data = str_replace(array('+','/','='),array('-','_',''),$data);
    return $data;
  }

  function go_urlsafe_b64decode($string) {
    $data = str_replace(array('-','_'),array('+','/'),$string);
    $mod4 = strlen($data) % 4;
    if ($mod4) {
      $data .= substr('====', $mod4);
    }
    return base64_decode($data);
  }

// END - CAPTCHA Encryption Functionality

/*
* CaptchaSecurityImages.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 03/08/06
* Updated: 07/02/07
* Requirements: PHP 4/5 with GD and FreeType libraries
* Link: http://www.white-hat-web-design.co.uk/articles/php-captcha.php
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details:
* http://www.gnu.org/licenses/gpl.html
*
*
* Modifiziert f�r Bigware Shop 2.3 by Tigerstyle & Ede
* Feb. 2012
*/

function allocate_color_from_hex($handle, $hex) {
	if (strlen($hex) == 4) $hex = '#' . $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];

	$red   = hexdec(substr($hex, 1, 2));
	$green = hexdec(substr($hex, 3, 2));
	$blue  = hexdec(substr($hex, 5, 2));
 
	return imagecolorallocate($handle, $red, $green, $blue);
}
//Ede end
	function go_captcha_code($character) {
	   global $character, $possible;
		$code = '';
		$i = 0;
		while ($i < $character) {
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}
        function go_captcha_image($cap_width='',$cap_height='',$character='') {
         global $backg_color, $txt_color, $nois_color, $cap_width, $cap_height, $character;
     
		$code = go_captcha_code($character);
		/* font size will be 65% of the image height */
		$font_size = $cap_height * 0.65;
		$image = @imagecreate($cap_width, $cap_height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = allocate_color_from_hex($image, $backg_color);
		$text_color = allocate_color_from_hex($image, $txt_color);
		$noise_color = allocate_color_from_hex($image, $nois_color);
		/* generate random dots in background */
		for( $i=0; $i<($cap_width*$cap_height)/3; $i++ ) {
			imagefilledellipse($image, mt_rand(0,$cap_width), mt_rand(0,$cap_height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($cap_width*$cap_height)/150; $i++ ) {
			imageline($image, mt_rand(0,$cap_width), mt_rand(0,$cap_height), mt_rand(0,$cap_width), mt_rand(0,$cap_height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, CAPTCHA_FONT , $code) or die('Error in imagettfbbox function');
		$x = ($cap_width - $textbox[4])*0.5;
		$y = ($cap_height - $textbox[5])*0.45; // just a little higher than middle for decenders
		imagettftext($image, $font_size, 0, $x, $y, $text_color, CAPTCHA_FONT , $code) or die('Error in imagettftext function');
                /* output captcha image to file */
                imagejpeg($image, CAPTCHA_IMAGE);
                imagedestroy($image);
                /* return the encoded encrypted code for this image */
                return go_urlsafe_b64encode(go_CAP_RC4($code));
	}

?>
