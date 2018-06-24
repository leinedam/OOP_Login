<?php


class Hash{

  public static function make($string, $salt = ''){
    return hash('sha256', $string . $salt);
  }

  public static function salt($length){

     // WARNING
    // deprecated mcrypt_create_iv() function, replace system with random_bytes or openssl_encrypt()  http://php.net/openssl_encrypt
    // return bin2hex(random_bytes($length));

    return random_bytes($length);


  }

  public static function unique(){
    return self::make(uniqid());
  }

}
