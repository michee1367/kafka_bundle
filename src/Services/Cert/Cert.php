<?php

namespace Mink67\KafkaConnect\Services\Cert;


/**
 * Perment de créer un un kafka connect
 */
class Cert {
    /**
     * @var string
     */
    private $channelName;
    /**
     * @var string
     */
    private $typeInst;
    /**
     * @var string
     */
    private $pkey;
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var string
     */
    private $brute;

    /**
     * 
     */
    public function __construct(
        string $channelName,
        string $typeInst,
        string $pkey,
        string $uuid,
        string $brute=null,
    ) {
        $this->channelName = $channelName;
        $this->typeInst = $typeInst;
        $this->pkey = $pkey;
        $this->uuid = $uuid;
        $this->brute = $brute;
    }

    /**
     * Get the value of channelName
     *
     * @return  string
     */ 
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * Set the value of channelName
     *
     * @param  string  $channelName
     *
     * @return  self
     */ 
    public function setChannelName(string $channelName)
    {
        $this->channelName = $channelName;

        return $this;
    }

    /**
     * Get the value of typeInst
     *
     * @return  string
     */ 
    public function getTypeInst()
    {
        return $this->typeInst;
    }

    /**
     * Set the value of typeInst
     *
     * @param  string  $typeInst
     *
     * @return  self
     */ 
    public function setTypeInst(string $typeInst)
    {
        $this->typeInst = $typeInst;

        return $this;
    }

    /**
     * Get the value of pkey
     *
     * @return  string
     */ 
    public function getPkey()
    {
        return $this->pkey;
    }

    /**
     * Set the value of pkey
     *
     * @param  string  $pkey
     *
     * @return  self
     */ 
    public function setPkey(string $pkey)
    {
        $this->pkey = $pkey;

        return $this;
    }

    /**
     * Get the value of uuid
     *
     * @return  string
     */ 
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set the value of uuid
     *
     * @param  string  $uuid
     *
     * @return  self
     */ 
    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get the value of brute
     *
     * @return  string
     */ 
    public function getBrute()
    {
        return $this->brute;
    }

    /**
     * Set the value of brute
     *
     * @param  string  $brute
     *
     * @return  self
     */ 
    public function setBrute(string $brute=null)
    {
        $this->brute = $brute;

        return $this;
    }
}