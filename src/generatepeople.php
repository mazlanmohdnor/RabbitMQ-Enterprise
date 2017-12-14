<?php
require dirname(__DIR__) . '/vendor/autoload.php';
include_once '../dashboard/pages/conn/conn.php';

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

//set how many record want
$limit = 20;
$itteration = 0;

while ($itteration < $limit) {
    $name = $faker->lastname;
    $email = $faker->email;
    $city = $faker->city;
    $website = $faker->domainName;
    $avatar = $faker->imageUrl($width = 50, $height = 50, 'people');

    $sql = "INSERT INTO employee (name,email,city,website,avatar) VALUES ('$name','$email','$city','$website','$avatar')";

    if ($con->query($sql) === true) {
        echo "New record created successfully" . PHP_EOL;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error . PHP_EOL;
    }

    $itteration++;

}

mysqli_close($con);
echo 'Finished adding data to DB ' . PHP_EOL;
