<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$host = 'mosquito.rmq.cloudamqp.com';
$port = 5672;
$user = 'bcoocfcv';
$pass = '4s4ROw5Zeqy_QhtNKQ5uXtDz2SVj3DBG';
$vhost = 'bcoocfcv';

$exchange = 'subscriber';
$queue = 'enterprise_subscriber';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();
/*
    The following code is the same both in the consumer and the producer.
    In this way we are sure we always have a queue to consume from and an
        exchange where to publish messages.
 */
/*
    name: $queue
    passive: false
    durable: true // the queue will survive server restarts
    exclusive: false // the queue can be accessed in other channels
    auto_delete: false //the queue won't be deleted once the channel is closed.
 */
$channel->queue_declare($queue, false, true, false, false);
/*
    name: $exchange
    type: direct
    passive: false
    durable: true // the exchange will survive server restarts
    auto_delete: false //the exchange won't be deleted once the channel is closed.
 */
$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();


//set how many record want 
$limit = 1000;
$itteration = 0;

while ($itteration < $limit) {

    $name = $faker->name;

    // $messageBody = json_encode([
    //     'name' => $faker->name,
    //     'email' => $faker->email,
    // ]);
    
    $message = new AMQPMessage($name);
    $channel->basic_publish($message, $exchange);

    echo 'Publishing message to queue: ' . $name . PHP_EOL;
    
    $itteration++;
}

echo 'Finished publishing to queue: ' . $queue . PHP_EOL;

$channel->close();
$connection->close();