<?php


Route::get('/', function () {
   Mail::raw('Raw string email', function($msg) { $msg->to(['lackmulbe@gmail.com']); $msg->from(['devlackmul@gmail.com']); });
   return "Welcome";
});

$router->post('/adminlogedin','RegistrationController@adminLogin');
$router->post('/registrationrequest','RegistrationController@onInvite');
$router->post('/signup','RegistrationController@onRegister');
$router->post('/searchOtp','RegistrationController@onOtpUpdate');
$router->post('/logedin','RegistrationController@userLogin');
$router->post('/editprofile','RegistrationController@profileUpdate');

