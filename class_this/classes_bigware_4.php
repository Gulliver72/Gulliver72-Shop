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




*/
?>
<?php
class cc_validation {
	var $cc_type, $cc_number, $cc_expiry_month, $cc_expiry_year;
	function validate($number, $expiry_m, $expiry_y) {
		$this->cc_number = preg_replace('/[^0-9]/', '', $number);
		if (preg_match('/^4[0-9]{12}([0-9]{3})?$/', $this->cc_number)) {
			$this->cc_type = 'Visa';
		} elseif (preg_match('/^5[1-5][0-9]{14}$/', $this->cc_number)) {
			$this->cc_type = 'Master Card';
		} elseif (preg_match('/^3[47][0-9]{13}$/', $this->cc_number)) {
			$this->cc_type = 'American Express';
		} elseif (preg_match('/^3(0[0-5]|[68][0-9])[0-9]{11}$/', $this->cc_number)) {
			$this->cc_type = 'Diners Club';
		} elseif (preg_match('/^6011[0-9]{12}$/', $this->cc_number)) {
			$this->cc_type = 'Discover';
		} elseif (preg_match('/^(3[0-9]{4}|2131|1800)[0-9]{11}$/', $this->cc_number)) {
			$this->cc_type = 'JCB';
		} elseif (preg_match('/^5610[0-9]{12}$/', $this->cc_number)) { 
			$this->cc_type = 'Australian BankCard';
		} else {
			return -1;
		}
		if (is_numeric($expiry_m) && ($expiry_m > 0) && ($expiry_m < 13)) {
			$this->cc_expiry_month = $expiry_m;
		} else {
			return -2;
		}
		$current_year = date('Y');
		$expiry_y = substr($current_year, 0, 2) . $expiry_y;
		if (is_numeric($expiry_y) && ($expiry_y >= $current_year) && ($expiry_y <= ($current_year + 10))) {
			$this->cc_expiry_year = $expiry_y;
		} else {
			return -3;
		}
		if ($expiry_y == $current_year) {
			if ($expiry_m < date('n')) {
				return -4;
			}
		}
		return $this->is_valid();
	}
	function is_valid() {
		$cardNumber = strrev($this->cc_number);
		$numSum = 0;
		for ($i=0; $i<strlen($cardNumber); $i++) {
			$currentNum = substr($cardNumber, $i, 1); 
			if ($i % 2 == 1) {
				$currentNum *= 2;
			} 
			if ($currentNum > 9) {
				$firstNum = $currentNum % 10;
				$secondNum = ($currentNum - $firstNum) / 10;
				$currentNum = $firstNum + $secondNum;
			}
			$numSum += $currentNum;
		} 
		return ($numSum % 10 == 0);
	}
}
?>
