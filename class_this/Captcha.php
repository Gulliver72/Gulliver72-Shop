<?php
 /*
 * Captcha
 * 
 * Modifiziert für Bigware Shop 2.3 by Tigerstyle & Ede
 * Feb. 2012, optimiert von Richy für Strato & Co. , Mai 2012
 * 2017 modifiziert von Gulliver72 für Bigware Shop 3.0
 * 
 */

  class Captcha {
  
    /**
    * instance
    *
    * Statische Variable, um die aktuelle (einzige!) Instanz dieser Klasse zu halten
    *
    * @var Singleton
    */
    protected static $_instance = null;
    /**
    * data
    *
    * Variable, um die Daten der Datenbank dieser Klasse zu halten
    *
    * @var Array
    */
    protected $data;
    
    protected $key;
    protected $possible;
    protected $cap_width;
    protected $cap_height;
    protected $character;
    protected $backg_color;
    protected $txt_color;
    protected $nois_color;
    protected $captcha_font;
    
    protected $captcha_enable_8;
    protected $captcha_enable_10;
    protected $captcha_enable_11;
    protected $captcha_enable_37;
    protected $captcha_enable_49;
    protected $captcha_enable_53;
    /**
    * CAPTCHA_FONT
    *
    * statische Variable, um die Schriftart dieser Klasse zu halten
    *
    * @var Array
    */
    protected $CAPTCHA_FONT = FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_PICTURES . "captcha/capture.ttf";
    /**
    * get instance
    *
    * Falls die einzige Instanz noch nicht existiert, erstelle sie
    * Gebe die einzige Instanz dann zurück
    *
    * @return   Singleton
    */
    public static function getInstance()
    {
      if (null === self::$_instance)
      {
        self::$_instance = new self;
      }
      return self::$_instance;
    }
    /**
    * constructor
    *
    * externe Instanzierung verbieten
    */
    protected function __construct()
    {
      $this->data = captcha_settings::ArrayBuilder()->getOne();
      
      $this->key = $this->data['captcha_key'];
      $this->possible = $this->data['possible'];
      $this->cap_width = $this->data['cap_width'];
      $this->cap_height = $this->data['cap_height'];
      $this->character = $this->data['characters'];
      $this->backg_color = $this->data['background_color'];
      $this->txt_color = $this->data['text_color'];
      $this->nois_color = $this->data['noise_color'];
      $this->captcha_font = $this->data['captcha_font'];
      
      $this->captcha_enable_8 = $this->data['captcha_enable_8']; 
      $this->captcha_enable_10 = $this->data['captcha_enable_10'];
      $this->captcha_enable_11 = $this->data['captcha_enable_11'];
      $this->captcha_enable_37 = $this->data['captcha_enable_37'];
      $this->captcha_enable_49 = $this->data['captcha_enable_49'];
      $this->captcha_enable_53 = $this->data['captcha_enable_53'];
    }
    
    // BEGIN - CAPTCHA Encryption Functionality
    public function get_CAP_RC4($data) {
    
      return password_hash ($data, PASSWORD_DEFAULT);
    }
    
    public function get_urlsafe_b64encode($string) {
    
      $data = base64_encode($string);
      $data = str_replace(array('+','/','='),array('-','_',''),$data);
      
      return $data;
    }
    
    public function get_urlsafe_b64decode($string) {
    
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
    */
    
    protected function allocate_color_from_hex($handle, $hex) {
    
      if (strlen($hex) == 4) $hex = '#' . $hex[1] . $hex[1] . $hex[2] . $hex[2] . $hex[3] . $hex[3];
      
      $red   = hexdec(substr($hex, 1, 2));
      $green = hexdec(substr($hex, 3, 2));
      $blue  = hexdec(substr($hex, 5, 2));
      
      return imagecolorallocate($handle, $red, $green, $blue);
    }
    //Ede end
    
    protected function get_captcha_code() {
    
      $code = '';
      $i = 0;
      while ($i < $this->character) {
        $code .= substr($this->possible, mt_rand(0, strlen($this->possible)-1), 1);
        $i++;
      }
      
      return $code;
    }
    
    protected function get_captcha_image() {
    
      $code = $this->get_captcha_code();
      
      /* font size will be 65% of the image height */
      $font_size = $this->cap_height * 0.65;
      
      try {
        $image = imagecreatetruecolor($this->cap_width, $this->cap_height);
        
        /* set the colours */
        $background_color = $this->allocate_color_from_hex($image, $this->backg_color);
        $text_color = $this->allocate_color_from_hex($image, $this->txt_color);
        $noise_color = $this->allocate_color_from_hex($image, $this->nois_color);
        
        /* generate random dots in background */
        for( $i = 0; $i < ($this->cap_width*$this->cap_height)/3; $i++ ) {
          imagefilledellipse($image, mt_rand(0, $this->cap_width), mt_rand(0, $this->cap_height), 1, 1, $noise_color);
        }
        
        /* generate random lines in background */
        for( $i = 0; $i < ($this->cap_width*$this->cap_height)/150; $i++ ) {
          imageline($image, mt_rand(0, $this->cap_width), mt_rand(0, $this->cap_height), mt_rand(0, $this->cap_width), mt_rand(0, $this->cap_height), $noise_color);
        }
        
        /* create textbox and add text */
        try {
          $textbox = imagettfbbox($font_size, 0, $this->CAPTCHA_FONT , $code);
          $x = ($this->cap_width - $textbox[4])*0.5;
          $y = ($this->cap_height - $textbox[5])*0.45; // just a little higher than middle for decenders
          
          try {
            imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->CAPTCHA_FONT , $code);
            
            /* output captcha image to file */
            imagejpeg($image, CAPTCHA_IMAGE);
            imagedestroy($image);
            
            /* return the encoded encrypted code for this image */
            return $this->get_CAP_RC4($code);
          }
          catch (Exception $e) {
            SimpleLogger::warn('Error in imagettftext function');
            
            return false;
          }
        }
        catch (Exception $e) {
          SimpleLogger::warn('Error in imagettfbbox function');
          
          return false;
        }
      }
      catch (Exception $e) {
        SimpleLogger::warn('Cannot initialize new GD image stream');
        
        return false;
      }
    }
    /**
    * function wrapper for protected function get_captcha_image()
    * 
    */
    public function get_captcha() {
    
      return $this->get_captcha_image();
    }
    
    public function set_captcha_enable_8($set) {
    
      $this->captcha_enable_8 = $set;
    }
    
    public function get_captcha_enable_8() {
    
      return $this->captcha_enable_8;
    }
    
    public function set_captcha_enable_10($set) {
    
      $this->captcha_enable_10 = $set;
    }
    
    public function get_captcha_enable_10() {
    
      return $this->captcha_enable_10;
    }
    
    public function set_captcha_enable_11($set) {
    
      $this->captcha_enable_11 = $set;
    }
    
    public function get_captcha_enable_11() {
    
      return $this->captcha_enable_11;
    }
    
    public function set_captcha_enable_37($set) {
    
      $this->captcha_enable_37 = $set;
    }
    
    public function get_captcha_enable_37() {
    
      return $this->captcha_enable_37;
    }
    
    public function set_captcha_enable_49($set) {
    
      $this->captcha_enable_49 = $set;
    }
    
    public function get_captcha_enable_49() {
    
      return $this->captcha_enable_49;
    }
    
    public function set_captcha_enable_53($set) {
    
      $this->captcha_enable_53 = $set;
    }
    
    public function get_captcha_enable_53() {
    
      return $this->captcha_enable_53;
    }
    /**
    * clone
    *
    * Kopieren der Instanz von aussen ebenfalls verbieten
    */
     protected function __clone() {}
  }
?>