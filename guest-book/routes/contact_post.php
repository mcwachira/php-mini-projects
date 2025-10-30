<?php


//CSRF protection
if(!validateCsrfToken($_POST['csrfToken'] ?? null)){
    addFlashMessage('error', 'Sorry , please send the  form again,');
    redirect('/guest-book/public/contact');
}



$name =$_POST["name"] ?? "";
$email =$_POST["email"] ?? "";
$message =$_POST["message"] ?? "";



if(empty($name) || empty($email) || empty($message)){

    badRequest("All filed asr required");

}

if(!filter_var($email, FILTER_VALIDATE_EMAIL)){

    badRequest("Email Field is required");
}


$inserted = insertMessage(connectDb(),
    name:$name,
email:$email,
message:$message
);

if($inserted){
    $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

    addFlashMessage("success","Thank you , $safeName , for your message. It was stored." );
    redirect("/guestbook/public/guestbook");
}

addFlashMessage('error', 'Could not store the message. sorry');

redirect('/guest-book/public/contact');
//var_dump($name, $email, $message);