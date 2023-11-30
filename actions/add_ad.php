<?php
/*require_once 'config.php';
require_once '../core/firebase_service.php';

$file = $_FILES["file"];
$target_dir = "../assets/uploads/";
$target_file = $target_dir . basename($file["name"]);

if (move_uploaded_file($file["tmp_name"], $target_file)) {
    echo "The file " . basename($file["name"]) . " has been uploaded.";

    // Call the pushToStorage method with the uploaded image file
    $imageUrl = $firebase->pushToStorage($target_file, "images/" . basename($file["name"]));
    if ($imageUrl) {
        $now = (new DateTime())->format('Y-m-d H:i:s');
        $response = $firebase->pushToFirestore('ads', [
            'url' => $imageUrl,
            'date' => $now
        ]);

        if ($response) {
            echo "Image uploaded and Firestore updated successfully.";
        } else {
            echo "Failed to update Firestore.";
            // Log the error for debugging
            error_log("Failed to update Firestore: " . date("Y-m-d H:i:s"));
        }
    } else {
        echo "<br>Failed to upload image to storage.";
        // Log the error for debugging
        error_log("Failed to upload image to storage: " . date("Y-m-d H:i:s"));
    }
} else {
    echo "Sorry, there was an error uploading your file.";
    // Log the error for debugging
    error_log("Error uploading file: " . date("Y-m-d H:i:s"));
}
*/
/*
require 'config.php';
require '../vendor/autoload.php';
 
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
 
    try {
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket('m2chanic.appspot.com');  

        $object = $bucket->upload(
            file_get_contents($file['tmp_name']),
            [
                'name' => 'images/' . $file['name']
            ]
        );

        //$objectUrl = $object->signedUrl(new \DateTime('tomorrow'));
        $now = (new DateTime())->format('Y-m-d\TH:i:s\Z');
        $data = [
            'fields' => [
                'url' => ['stringValue' => 'mahmoud'],
             ]
        ];
        $result = $fireStore->pushToFirestore('ads', $data);
        
        echo "Image uploaded and data saved in Firestore.".$result;
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "No file uploaded.";
}*/
?>