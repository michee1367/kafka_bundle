# Documentation 

## Add the config file

- Create file `kafka_connect.yaml` in `config/packages` directory

- Add the following content in the file :

    ```
        kafka_connect:
          producer:
            bootstrap_servers: "localhost:9093/"
            socket_timeout_ms: 50
            queue_buffering_max_messages: 1000
            max_in_flight_requests_per_connection: 1
            topic:
              message_timeout_ms: 30000
              request_required_acks: -1
              request_timeout_ms: 5000
          consumer:
            bootstrap_servers: "localhost:9093/"
            group_id: "consumer-highlevel"
            enable_partition_eof: "true"
            auto_offset_reset: "earliest"
          db:
            table_name: 'kafka_settings'

        mink67_encrypt:
            ciphering: 'AES-128-CTR'
            options: 0
            encryption_iv: '1234567891011121'
            encryption_key: '%env(resolve:DATABASE_URL)%'
            decryption_iv: '1234567891011121'
            decryption_key: '%env(resolve:DATABASE_URL)%'
            
    ```

## Add package 

```    
    $ composer require mink67/kafka_connect_bundle
```

    
