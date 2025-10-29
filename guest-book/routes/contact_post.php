<?php
//CSRF
$name =$_POST["name"] ?? "";
$email =$_POST["email"] ?? "";
$message =$_POST["message"] ?? "";



if(empty($name) || empty($email) || empty($message)){

    badRequest("All filed asr required");

}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

    badRequest("Email Field is required");
}


connectDb();
var_dump($name, $email, $message);