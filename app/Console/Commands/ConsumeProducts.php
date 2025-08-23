<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class ConsumeProducts extends Command
{
    protected $signature = 'rabbitmq:consume-products';
    protected $description = 'Consume product events from RabbitMQ';

    public function handle()
    {
        $connection = new AMQPStreamConnection(
            env('RABBITMQ_HOST', 'localhost'),
            env('RABBITMQ_PORT', 5672),
            env('RABBITMQ_USER', 'guest'),
            env('RABBITMQ_PASSWORD', 'guest')
        );

        $channel = $connection->channel();
        $channel->queue_declare('products', false, true, false, false);

        $this->info(" [*] Waiting for product messages. To exit press CTRL+C");

        $callback = function ($msg) {
            $this->info(" [x] Received: " . $msg->body);
            // TODO: ici tu mets la logique pour mettre à jour la DB Orders
        };

        $channel->basic_consume('products', '', false, true, false, false, $callback);

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();
    }
}
