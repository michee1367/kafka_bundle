<?php
namespace Mink67\KafkaConnect\Services\Utils;

use Mink67\Encrypt\Crypt as ImplCrypt;
use Mink67\KafkaConnect\Contracts\Crypt as ContractsCrypt;


/**
 * 
 */
class Crypt implements ContractsCrypt {
    /**
     * @var ImplCrypt
     */
    private $crypt;
    /**
     * 
     */
    public function __construct(ImplCrypt $crypt) {
        $this->crypt = $crypt;
    }

    /**
     * 
     */
    public function encrypt(string $text) : string
    {
        return $this->crypt->encrypt($text);
    }

    public function decrypt(string $text) : string
    {
        return $this->crypt->decrypt($text);
    }

}