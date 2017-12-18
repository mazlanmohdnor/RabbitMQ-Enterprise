<?php
require dirname(__DIR__) . '/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

$host = 'mosquito.rmq.cloudamqp.com';
$port = 5672;
$user = 'bcoocfcv';
$pass = '4s4ROw5Zeqy_QhtNKQ5uXtDz2SVj3DBG';
$vhost = 'bcoocfcv';

$exchange = 'subscriber';
$queue = 'upm-result';

$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);

$channel->exchange_declare($exchange, 'direct', false, true, false);
$channel->queue_bind($queue, $exchange);

//process the message from MQ
function process_message($message)
{

    $servername = 'localhost';
    $username = 'root';
    $password = '';
    $dbname = 'enterprisemq';

// Create connection
    $con = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    // echo $message->body;
    $messages = json_decode($message->body);
    foreach ($messages as $messageBody) {

        $ic = $messageBody->ic;
        $name = $messageBody->name;
        $phone = $messageBody->phone;
        $address = str_replace(",", " ", (str_replace("\n", " ", $messageBody->address)));
        $result = $messageBody->result;
        $status = $messageBody->status;

       $sql = "INSERT INTO studentsuccess (ic,name,phone,address,result,status)
    VALUES ('$ic','$name','$phone','$address','$result','$status')";

if ($con->query($sql) === true) {
    echo "New record created successfully" . PHP_EOL;
} else {
    echo "Error: " . $sql . "<br>" . $con->error . PHP_EOL;
}


        // echo 'consumed data: ' . $messageBody . PHP_EOL;

    }
   
    mysqli_close($con);

    if ($message->body === 'quit') {
        $$message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
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
