<?php
require dirname(__DIR__) . '/vendor/autoload.php';
include_once '../dashboard/pages/conn/conn.php';

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

$sql = "SELECT * FROM employee";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        $messageBody = json_encode([
            'name' => $row["name"],
            'email' => $row["email"],
            'city' => $row["city"],
            'website' => $row["website"],
            'avatar' => $row["avatar"],
        ]);
        $message = new AMQPMessage($messageBody, [
            'content_type' => 'application/json',
            'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT,
        ]);
        $channel->basic_publish($message, $exchange);
        $body = json_decode($messageBody);
        echo 'Publishing message to queue: ' . $body->name . PHP_EOL;

    }
} else {
    echo "0 results";
}
$con->close();

//set how many record want
// $limit = 1000;
// $itteration = 0;

// while ($itteration < $limit) {

//     // $name = $faker->name;

//     $messageBody = json_encode([
//         'name' => $faker->lastname,
//         'email' => $faker->email,
//         'city' => $faker->city,
//         'website' => $faker->domainName,
//         'avatar' => $faker->imageUrl($width = 50, $height = 50, 'people')
//     ]);

// $message = new AMQPMessage($messageBody,[
//     'content_type' => 'application/json',
//     'delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT
// ]);
// $channel->basic_publish($message, $exchange);
// $body = json_decode($messageBody);
// echo 'Publishing message to queue: ' . $body->name . PHP_EOL;

// $itteration++;
// }

echo 'Finished publishing to queue: ' . $queue . PHP_EOL;

$channel->close();
$connection->close();
