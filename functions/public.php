<?php

require '../db/db.php';
$db = new ConnectionController;

class LoginLogoutController{
    public function login(){
            // Creating new google client instance
            $client = new Google_Client();
            // Enter your Client ID
            $client->setClientId('634038212813-7ujphnolgundodkp6pvkrrsrl29v6rgt.apps.googleusercontent.com');
            // Enter your Client Secrect
            $client->setClientSecret('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');
            // Enter the Redirect URL
            $client->setRedirectUri('http://localhost/employeeportal/public/login.php');
            // Adding those scopes which we want to get (email & profile Information)
            $client->addScope("email");
            $client->addScope("profile");
            if(isset($_GET['code'])){
                $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
                if(!isset($token["error"])){
                    $client->setAccessToken($token['access_token']);
                    // getting profile information
                    $google_oauth = new Google_Service_Oauth2($client);
                    $google_account_info = $google_oauth->userinfo->get();
                
                    // Storing data into database
                    $google_id = mysqli_real_escape_string($db, $google_account_info->id);
                    $full_name = mysqli_real_escape_string($db, trim($google_account_info->name));
                    $email = mysqli_real_escape_string($db, $google_account_info->email);
                    $profile_pic = mysqli_real_escape_string($db, $google_account_info->picture);
                    // checking user already exists or not
                    $get_user = mysqli_query($db, "SELECT * FROM tbl_user WHERE google_id='$google_id'");
                    if(mysqli_num_rows($get_user) > 0){
                        // $_SESSION['login_id'] = $id; 
                        header('Location: ../admin/index.php');
                        exit;
                    }
                    else{
                        // if user not exists we will insert the user
                        $insert = mysqli_query($db, "INSERT INTO tbl_user(google_id,fname,email,profile_image) VALUES('$google_id','$full_name','$email','$profile_pic')");
                        if($insert){
                            // $_SESSION['login_id'] = $id;
                
                            exit;
                        }
                        else{
                            echo "Sign up failed!(Something went wrong).";
                        }
                    }
                }
                else{
                    // header('Location: ');
                    exit;
                }
            } 
            else{
                // Google Login Url = $client->createAuthUrl(); 
            }
    }
}

?>