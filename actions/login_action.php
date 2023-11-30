<?php /*
require '../vendor/autoload.php';
require 'config.php'; 
session_start(); 

$email = $_POST['username'];
$password = $_POST['password'];

try {
    $signInResult = $auth->signInWithEmailAndPassword($email, $password);
    $idTokenString = $signInResult->idToken();
    // Save the ID token in the session
    $_SESSION['idToken'] = $idTokenString;
    $response = array('redirectURL' => 'index.html');
    echo json_encode($response);
} catch (\Kreait\Firebase\Auth\SignIn\FailedToSignIn $e) {
    $response = array('error' => 'Invalid email or password');
    echo json_encode($response);
}*/
/*require 'config.php'; 
$email = $_POST['username'];
$password = $_POST['password'];

$result = $firebase->loginUser($email,$password); 

if($result){
    $response = array('redirectURL' => 'index.html');
    echo json_encode($response);
}else{
    $response = array('error' => 'Invalid email or password');
    echo json_encode($response);
}*/