<?php
if (!class_exists('Crypt_Rijndael')) {
    require_once('Rijndael.php');
}
define('CRYPT_AES_MODE_CTR', CRYPT_MODE_CTR);
define('CRYPT_AES_MODE_ECB', CRYPT_MODE_ECB);
define('CRYPT_AES_MODE_CBC', CRYPT_MODE_CBC);
define('CRYPT_AES_MODE_CFB', CRYPT_MODE_CFB);
define('CRYPT_AES_MODE_OFB', CRYPT_MODE_OFB);
define('CRYPT_AES_MODE_INTERNAL', CRYPT_MODE_INTERNAL);
define('CRYPT_AES_MODE_MCRYPT', CRYPT_MODE_MCRYPT);
/**#@-*/


class Crypt_AES extends Crypt_Rijndael {
    var $const_namespace = 'AES';
    function __construct($mode = CRYPT_AES_MODE_CBC)
    {
        parent::Crypt_Rijndael($mode);
    }

    function setBlockLength($length)
    {
        return;
    }
}
