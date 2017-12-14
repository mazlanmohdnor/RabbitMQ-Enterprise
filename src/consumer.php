<?php
require dirname(__DIR__) . '/vendor/autoload.php';
include_once '../dashboard/pages/conn/conn.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;



$host = 'mosquito.rmq.cloudamqp.com';
$port = 5672;
$user = 'bcoocfcv';
$pass = '4s4ROw5Zeqy_QhtNKQ5uXtDz2SVj3DBG';
$vhost = 'bcoocfcv';

$exchange = 'subscriber';
$queue = 'enterprise_subscriber';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);

$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);


//process the message from MQ
function process_message($message)
{
 
     $messageBody = json_decode($message->body);
     $name = $messageBody->name;
     $email = $messageBody->email;
     $city = $messageBody->city;
     $website = $messageBody->website;
     $avatar = $messageBody->avatar;
 
     $sql = "INSERT INTO employee (name,email,city,website,avatar)
     VALUES ('$name','$email','$city','$website','$avatar')";
 
 
     if ($con->query($sql) === TRUE) {
         echo "New record created successfully". PHP_EOL;
     } else {
         echo "Error: " . $sql . "<br>" . $con->error. PHP_EOL;
     }
     mysqli_close($con);

    echo 'consumed data: ' . $name . PHP_EOL;
 
     if ($message->body === 'quit') {
         $ $message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
     }
    
}
/*
    queue: Queue from where to get the messages
    consumer_tag: Consumer identifier
    no_local: Don't receive messages published by this consumer.
    no_ack: Tells the server if the consumer will acknowledge the messages.
    exclusive: Request exclusive consumer access, meaning only this consumer can access the queue
    nowait:
    callback: A PHP Callback
 */

$consumerTag = 'enterprise_consumer';
$channel->basic_consume($queue, $consumerTag, false, false, false, false, 'process_message');

function shutdown($channel, $connection)
{
    $channel->close();
    $connection->close();
}
register_shutdown_function('shutdown', $channel, $connection);
// Loop as long as the channel has callbacks registered
while (count($channel->callbacks)) {
    $channel->wait();
}