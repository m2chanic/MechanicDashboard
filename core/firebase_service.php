<?php

class FirebaseService
{
    private $apiKey; // Replace with your Firebase API key

    public function __construct($apiKey)
    {
        session_start(); 
        $this->apiKey = $apiKey;
    }

    public function loginUser($email, $password)
    {
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . $this->apiKey;

        $data = array(
            'email' => $email,
            'password' => $password,
            'returnSecureToken' => true
        );

        $dataString = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($dataString)
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        $responseData = json_decode($response, true);

        if (isset($responseData['idToken']) && isset($responseData['localId'])) {
            $_SESSION['firebase_token'] = $responseData['idToken'];
            $_SESSION['user_id'] = $responseData['localId'];
            return true ;
        } else {
            // Handle login error
            return false;
        }
    }


    public function registerUser($email, $password)
    {
        // Implement the user registration logic using the Firebase Authentication REST API
        // Sample implementation:
        // $url = "https://identitytoolkit.googleapis.com/v1/accounts:signUp?key=" . $this->apiKey;
        // ... (add the logic for user registration)
    }

    public function pushToFirestore($collectionName, $documentData)
    {
        if (isset($_SESSION['idToken'])) {
            $accessToken = $_SESSION['idToken'];
             $url = "https://firestore.googleapis.com/v1/projects/m2chanic/databases/(default)/documents/{$collectionName}?access_token=" . $accessToken;

            $data = array(
                'fields' => $documentData
            );

            $dataString = json_encode($data);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString)
            ));

            $response = curl_exec($ch);
            echo $response; 
            curl_close($ch);

            if ($response) {
                return true; // Push successful
            } else {
                return false; // Push error
            }
        } else {
            return false; // Access token not found
        }
    }

    public function getFromFirestore($path)
    {
        session_start();

        if (isset($_SESSION['idToken'])) {
            $accessToken = $_SESSION['idToken'];
            $url = "https://firestore.googleapis.com/v1/projects/m2chanic/databases/(default)/documents/{$path}?access_token=" . $accessToken;

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            curl_close($ch);

            $responseData = json_decode($response, true);

            if (isset($responseData['fields'])) {
                return $responseData['fields']; // Data retrieved successfully
            } else {
                return false; // Data retrieval error
            }
        } else {
            return false; // Access token not found
        }
    }

    public function pushToStorage($localImagePath, $storagePath)
    {
 
        if (isset($_SESSION['firebase_token'])) {
            $accessToken = $_SESSION['idToken'];
            echo $accessToken ; 
            $bucketName = "m2chanic.appspot.com"; // Replace with your bucket name
            $url = "https://storage.googleapis.com/upload/storage/v1/b/{$bucketName}/o?uploadType=media&name={$storagePath}&access_token=" . $accessToken;

            $contentType = mime_content_type($localImagePath);
            $fileData = file_get_contents($localImagePath);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: ' . $contentType,
                'Content-Length: ' . strlen($fileData)
            ));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            echo $response ; 
            curl_close($ch);

            $responseData = json_decode($response, true);

            if (isset($responseData['mediaLink'])) {
                return $responseData['mediaLink']; // Image URL
            } else {
                return false; // Push error
            }
        } else {
            echo 'no token' ; 
            return false; // Access token not found

        }
    }



    public function getFromStorage($storagePath)
    {
        // Implement the logic to get data from Firebase Cloud Storage using the Cloud Storage REST API
        // Sample implementation:
        // $url = "https://storage.googleapis.com/storage/v1/b/[BUCKET_NAME]/o/" . $storagePath . "?key=" . $this->apiKey;
        // ... (add the logic for getting data from Cloud Storage)
    }
}

?>
