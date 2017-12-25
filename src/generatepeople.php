<?php
require dirname(__DIR__) . '/vendor/autoload.php';
include_once '../dashboard/pages/conn/conn.php';

// use the factory to create a Faker\Generator instance
$faker = Faker\Factory::create();

//set how many record want
$limit = 1000;
$itteration = 0;
//ic, name, phone, address, result
while ($itteration < $limit) {
    // $name = $faker->lastname;
    // $email = $faker->email;
    // $city = $faker->city;
    // $website = $faker->domainName;
    // $avatar = $faker->imageUrl($width = 50, $height = 50, 'people');
    $ic = $faker->ean8;
    $name = $faker->lastname;
    $phone = $faker->e164PhoneNumber;
    $address = $faker->address;
    $result = (1 + mt_rand() / mt_getrandmax() * (4 - 1));

    // $sql = "INSERT INTO employee (name,email,city,website,avatar) VALUES ('$name','$email','$city','$website','$avatar')";
    $sql = "INSERT INTO students (ic,name,phone,address,result) VALUES ('$ic','$name','$phone','$address','$result')";

    if ($con->query($sql) === true) {
        echo "New record created successfully" . PHP_EOL;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error . PHP_EOL;
    }

    $itteration++;

}

mysqli_close($con);
echo 'Finished adding data to DB ' . PHP_EOL;
