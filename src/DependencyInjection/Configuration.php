<?php

namespace Mink67\KafkaConnect\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
/**
 * 
 */
class Configuration implements ConfigurationInterface {


    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(){
        $treepBuilder = new TreeBuilder("kafka_connect");

        
        $root = $treepBuilder->getRootNode();

        if($root instanceof ArrayNodeDefinition){
            $root
                ->children()
                    ->arrayNode("producer")
                        ->isRequired()
                        //->cannotBeEmpty()
                        ->children()
                            ->scalarNode("bootstrap_servers")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("socket_timeout_ms")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("queue_buffering_max_messages")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("max_in_flight_requests_per_connection")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->arrayNode("topic")
                                ->isRequired()
                                ->children()
                                    ->scalarNode("message_timeout_ms")
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode("request_required_acks")
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                    ->scalarNode("request_timeout_ms")
                                        ->isRequired()
                                        ->cannotBeEmpty()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode("consumer")
                        ->isRequired()
                        ->children()
                            ->scalarNode("bootstrap_servers")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("group_id")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("enable_partition_eof")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("auto_offset_reset")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode("db")
                        ->isRequired()
                        ->children()
                            ->scalarNode("table_name")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode("crypt")
                        ->isRequired()
                        ->children()
                            ->scalarNode("ciphering")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("options")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("encryption_iv")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("encryption_key")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("decryption_iv")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                            ->scalarNode("decryption_key")
                                ->isRequired()
                                ->cannotBeEmpty()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ;

        }else{

            throw new InvalidConfigurationException("Root must be an instance to ArrayNodeDefinition");
        }

        return $treepBuilder;

    }

}