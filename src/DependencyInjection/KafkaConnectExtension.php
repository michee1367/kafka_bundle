<?php

namespace Mink67\KafkaConnect\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use function Symfony\Component\String\u;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * 
 */
class KafkaConnectExtension extends Extension {


    /**
     * Loads a specific configuration.
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container){

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__. "/../Resources/config")
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        // producer
        $container->setParameter("mink67.kafka_connect.producer.bootstrap_servers", $config["producer"]["bootstrap_servers"]);
        $container->setParameter("mink67.kafka_connect.producer.socket_timeout_ms", $config["producer"]["socket_timeout_ms"]);
        $container->setParameter("mink67.kafka_connect.producer.queue_buffering_max_messages", $config["producer"]["queue_buffering_max_messages"]);
        $container->setParameter("mink67.kafka_connect.producer.max_in_flight_requests_per_connection", $config["producer"]["max_in_flight_requests_per_connection"]);
        //producer config
        $container->setParameter("mink67.kafka_connect.producer.topic.message_timeout_ms", $config["producer"]["topic"]["message_timeout_ms"]);
        $container->setParameter("mink67.kafka_connect.producer.topic.request_required_acks", $config["producer"]["topic"]["request_required_acks"]);
        $container->setParameter("mink67.kafka_connect.producer.topic.request_timeout_ms", $config["producer"]["topic"]["request_timeout_ms"]);
        // consumer
        $container->setParameter("mink67.kafka_connect.consumer.bootstrap_servers", $config["consumer"]["bootstrap_servers"]);
        $container->setParameter("mink67.kafka_connect.consumer.group_id", $config["consumer"]["group_id"]);
        $container->setParameter("mink67.kafka_connect.consumer.enable_partition_eof", $config["consumer"]["enable_partition_eof"]);
        $container->setParameter("mink67.kafka_connect.consumer.auto_offset_reset", $config["consumer"]["auto_offset_reset"]);

        $container->setParameter("mink67.kafka_connect.db.table_name", $config["db"]["table_name"]);

        
        //dd((string) u("For-oo_iir")->camel());
        

        //dd($this->data_convert);
        

    }

}
