<?php

namespace Mink67\KafkaConnect;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Mink67\KafkaConnect\DependencyInjection\KafkaConnectExtension;

/**
 * Perment de créer un un kafka connect
 */
class KafkaConnectBundle extends Bundle
{

    /**
     * 
     */
    public function __construct() 
    {
        
    }

    /**
     * 
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

    }
}