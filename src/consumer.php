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

// $host = 'spider.rmq.cloudamqp.com';
// $port = 5672;
// $user = 'ydjtkfhg';
// $pass = 'SnRM9CiwuIasm1rIDD72gaqsFtsNa5zI';
// $vhost = 'ydjtkfhg';
// $exchange = 'subsriber';
// $queue = 'upm-result';


$connection = new AMQPStreamConnection($host, $port, $user, $pass, $vhost);
$channel = $connection->channel();

$channel->queue_declare($queue, false, true, false, false);

// $channel->exchange_declare($exchange, 'direct', false, true, false);
// $channel->queue_bind($queue, $exchange);



// //process the message from MQ
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


    $array =  $message->body;
    $messages = json_decode($array);
    $i = 0;
    foreach ($messages as $message) {
 
    
        $ic = $message->ic;
        $name = $message->name;
        $phone = $message->phone;
        $address = str_replace(",", " ", (str_replace("\n", " ", $message->address)));
        $result = $message->result;
        $status = $message->status;

        if ( $status=='succeed') {
            $sql = "INSERT INTO studentsuccess (ic,name,phone,address,result,status)
                VALUES ('$ic','$name','$phone','$address','$result','$status')";


            if ($con->query($sql) === true) {
                echo $i++." - New success record created successfully" . PHP_EOL;
            } else {
                echo "Error: " . $sql . "<br>" . $con->error . PHP_EOL;
            }

        } else {
            $sql = "INSERT INTO studentfailed (ic,name,phone,address,result,status)
                VALUES ('$ic','$name','$phone','$address','$result','$status')";


            if ($con->query($sql) === true) {
                echo $i++." - New failed record created successfully" . PHP_EOL;
            } else {
                echo "Error: " . $sql . "<br>" . $con->error . PHP_EOL;
            }
        }
        
      

   

    if ($message === 'quit') {
        $$message->delivery_info['channel']->basic_cancel($message->delivery_info['consumer_tag']);
    }


    }
 

    mysqli_close($con);

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
// $callback = function ($msg) {
//     // echo " [x] Received ", $msg->body, "\n";
//     process_message($msg);
// };


// $consumerTag = 'enterprise_consumer';
$channel->basic_consume($queue, '', false, true, false, false, 'process_message');
// Loop as long as the channel has callbacks registered
while (count($channel->callbacks)) {
    $channel->wait();
}


    $channel->close();
    $connection->close();

