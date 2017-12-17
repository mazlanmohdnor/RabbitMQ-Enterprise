<?php
require dirname(__DIR__) . '/vendor/autoload.php';
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

$host = 'mosquito.rmq.cloudamqp.com';
$port = 5672;
$user = 'bcoocfcv';
$pass = '4s4ROw5Zeqy_QhtNKQ5uXtDz2SVj3DBG';
$vhost = 'bcoocfcv';


$exchange = 'fanout_example_exchange';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();
/*
    name: $exchange
    type: fanout
    passive: false // don't check is an exchange with the same name exists
    durable: false // the exchange won't survive server restarts
    auto_delete: true //the exchange will be deleted once the channel is closed.
*/
$channel->exchange_declare($exchange, 'fanout', false, false, true);

//message start
// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();
//set how many record want 
$limit = 10000;
$itteration = 0;
while ($itteration < $limit) {
    $email = $faker->email;
    // $messageBody = json_encode([
    //     'email' => $faker->email,
    //     'email' => $faker->email,
    // ]);
    $message = new AMQPMessage($email);
    $channel->basic_publish($message, $exchange);
    echo 'Publishing message to exchage: ' . $email . PHP_EOL;
    $itteration++;
}
echo 'Finished publishing to exchage: ' . $queue . PHP_EOL;
//message end

$channel->basic_publish($message, $exchange);
$channel->close();
$connection->close();