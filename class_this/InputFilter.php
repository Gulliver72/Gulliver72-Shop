<?php
/*
 * This simple class for input security
 *
 * @author Mohammad Reza Safari <mrdev1020@gmail.com>
 * @copyright 2016 Web artisans
 *
 * This is the long description for this class,
 * which can prevention as many attack
 *
 */


//namespace MohammadReza\SecurityBundle;



class InputFilter
{
   
    /*
     * The integer instance.
     *
     * @var integer
     */
    protected $int;
   
    /*
     * The string instance.
     *
     * @var string
     */
    protected $string;
   
    /*
     * The utf8_persian_ci string instance.
     *
     * @var string
     */
    protected $pc;
   
    /*
     * The destructive code instance.
     *
     * @var string
     */
    protected $special;
   
    /*
     * The decimal instance.
     *
     * @var decimal
     */
    protected $decimal;
   
    /*
     * The email instance.
     *
     * @var email
     */
    protected $email;


    /** The date instance.
     *
     *
     * @var date
     */
    protected $date;


    /*
     * Sets $regex to a remove addition characters in class instantiation
     *
     * @param string $regex a value required for the class
     *
     * @return pure string after remove addition characters
     */
    protected function regex($regex)
    {
        $regex = preg_replace("[\"]", "", $regex);
        $regex = preg_replace("[']", "", $regex);
        $regex = preg_replace("[/]", "", $regex);

        return $regex;
    }


    /*
     * Sets $tag to a remove html tags in class instantiation
     *
     * @param string $tag a value required for the class
     *
     * @return pure string Without html tags
     */
    protected function Tag($tag)
    {
        $tag = preg_replace("/<.*?>/", "", $tag);

        return $tag;
    }


    /*
     * Sets $slashes to a remove the backslash in front of and the backslash string in class instantiation
     *
     * @param string $slashes a value required for the class
     *
     * @return pure string Without backslash(s)
     */
    protected function slashes($slashes)
    {
        $slashes = stripslashes($slashes);
        $slashes = stripcslashes($slashes);

        return $slashes;
    }

   
    /*
     * Sets $pr to a remove the string except fa_IR in class instantiation
     *
     * @param string $pr a value required for the class
     *
     * @return pure fa_IR string
     */
    protected function PersianRegex($pr)
    {
        $pr = preg_replace('/[a-zA-Z!@#$%^&*()_+=<;>;?]/', "", $pr);

        return $pr;
    }


    /**
     * @param $dateRegex
     * @return mixed
     */
    protected function DateRegex($dateRegex)
    {
        return preg_replace('/\d{2}\/\d{2}\/\d{4}/', "", $dateRegex);
    }


    /*
     * A private variable (NumberInt - set)
     * Sets $int to a remove the string in class instantiation
     *
     * @param int $int
     *
     * @return void
     */
    private function setNumberInt($int)
    {
        $int = $this->regex($int);
        $int = filter_var($int, FILTER_SANITIZE_NUMBER_INT);
        $this->int = $int;
    }

   
    /*
     * A public variable (NumberInt - get)
     * Send $int to setNumberInt for validate integer
     *
     * @param int $int
     *
     * @return int
     */
    public function getNumberInt($int)
    {
        $this->setNumberInt($int);

        return $this->int;
    }
   
   
    /*
     * A private variable (String - set)
     * Sets $string to a removes the html tags in class instantiation
     *
     * @param string $string
     *
     * @return void
     */
    private function setString($string)
    {
        $string = $this->regex($string);
        $string = $this->Tag($string);
        $string = $this->slashes($string);
        $string = filter_var($string, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
        $string = htmlspecialchars($string);
        $this->string = $string;
    }

   
    /*
     * A public variable (String - get)
     * Send $string to setString for validate string
     *
     * @param string $string
     *
     * @return string
     */
    public function getString($string)
    {
        $this->setString($string);

        return $this->string;
    }
   
   
     /*
     * A private variable (Special Characters - set)
     * Sets $special to a removes the html tags && special characters in class instantiation
     *
     * @param string $special
     *
     * @return void
     */
    private function setSpecialChars($special)
    {
        $special = $this->regex($special);
        $special = $this->Tag($special);
        $special = $this->slashes($special);
        $special = filter_var($special, FILTER_SANITIZE_STRING);
        $special = filter_var($special, FILTER_SANITIZE_SPECIAL_CHARS);
        $special = htmlspecialchars($special);
        $this->special = $special;
    }


    /*
     * A public variable (Special Characters - get)
     * Send $special to setSpecialChars for validate string
     *
     * @param string $special
     *
     * @return string
     */
    public function getSpecialChars($special)
    {
        $this->setSpecialChars($special);

        return $this->special;
    }
   
   
    /*
     * A private variable (Persian Characters - set)
     * Sets $pc to a removes the html tags && special characters in class instantiation
     *
     * @param string $pc
     *
     * @return void
     */
    private function setPersianCharacters($pc)
    {
        $pc = $this->regex($pc);
        $pc = $this->Tag($pc);
        $pc = $this->PersianRegex($pc);
        $pc = $this->slashes($pc);
        $pc = filter_var($pc, FILTER_SANITIZE_STRING);
        $pc = filter_var($pc, FILTER_SANITIZE_SPECIAL_CHARS);
        $pc = htmlspecialchars($pc);
        $this->pc = $pc;
    }


    /*
     * A public variable (Persian Characters - get)
     * Send $pc to setPersianCharacters for validate utf8_persian_ci
     *
     * @param string $pc
     *
     * @return string
     */
    public function getPersianCharacters($pc)
    {
        $this->setPersianCharacters($pc);

        return $this->pc;
    }



    /*
     * A private variable (Email - set)
     * Check correct email && remove addition characters
     *
     * @param string $email
     *
     * @return void
     */
    private function setEmail($email)
    {
        $email = $this->regex($email);
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        $this->email = $email;
    }


    /*
     * A public variable (Email - get)
     * Send $email to setEmail for validate email
     *
     * @param string $email
     *
     * @return string
     */
    public function getEmail($email)
    {
        $this->setEmail($email);

        return $this->email;
    }


    /*
     * A private variable (Decimal - set)
     * Check correct decimal
     *
     * @param decimal $decimal
     *
     * @return void
     */
    private function setDecimal($decimal)
    {
        $decimal = preg_replace('/,/', '.', $decimal);
        $decimal = preg_replace('/[^0-9\.]/', '', $decimal);
        $decimal = filter_var($decimal, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $this->decimal = $decimal;
    }


     /*
     * A public variable (Decimal - get)
     * Send $decimal to setPersianCharacters for validate decimal
     *
     * @param decimal $decimal
     *
     * @return decimal
     */
    public function getDecimal($decimal)
    {
        $this->setDecimal($decimal);

        return $this->decimal;
    }


    /**
     * @param $date
     */
    private function setDate($date)
    {
        $this->date = $this->DateRegex($date);
    }


    /**
     * @param $date
     * @return date
     */
    public function getDate($date)
    {
        $this->setDate($date);
        return $this->date;
    }

} 