<?php
include '../partials/header.php';
require '../styles/plugins/google-api/vendor/autoload.php';
require '../db/db.php';
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
              while($row = $get_user->fetch_assoc()) {
                $_SESSION['user_id'] = $row['user_id'];
                $role_id = $row['role_id'];
                if ($role_id == '1'){
                  header('Location: ../super-admin/index.php');
                }else if ($role_id == '2'){
                  header('Location: ../admin/index.php');
                }else{
                  header('Location: ../employee/index.php');
                }
              } 
            
              exit;
          }
          else{
              // if user not exists we will insert the user
              $insert = mysqli_query($db, "INSERT INTO tbl_user(google_id,fname,email,profile_image) VALUES('$google_id','$full_name','$email','$profile_pic')");
              if($insert){
                 header('Location: register.php');
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
?>
<body>
        <div style="background-image: url(../styles/dist/img/Group\ 2.png); background-size: 100%;">
            <div class="l-header">
                <div class="left-header">
                    <img class="smart-logo" src="../styles/dist/img/regular logo 1.png" alt="">
                </div>
                <div class="right-header">
                    <img class="free-tools-icon" src="../styles/dist/img/Ticket_use_fill@3x.png" alt="">
                    <button class="free-tools-button">Free Tools</button> 
                    <img class="oracle-icon" src="../styles/dist/img/Info_light@3x.png">
                    <button class="oracle-button">Oracle</button>
                </div>
            </div>
                
            <div class="l-row">
                <div class="column" style="background-color: transparent">
                <h2 class="welcome-text" style="font-weight: 500; font-family: Poppins, Arial, Helvetica, sans-serif; line-height: 5px; font-size: 45px;">Welcome to our</h2>
                <h1 class="employee-text" style="font-weight: 700; font-family: Poppins, Arial, Helvetica, sans-serif; line-height: 70px; font-size: 50px;">Employee Portal</h1>
                <button type="button" class="btn get-started" data-toggle="modal" data-target=".bd-example-modal-sm"><img style="width: 16px;" src="../styles/dist/img/Sign_in_squre_light@3x.png" alt=""> &nbsp; &nbsp;Get Started</button>
                </div>
                <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="login-box">
                            <!-- /.login-logo -->
                            <div class="card card-outline card-primary">
                                <div class="card-header text-center">
                                <a href="" class="h1">Sign in with Google</a>
                                </div>
                                <div class="card-body">
                                <!-- <p class="login-box-msg">Sign in to start your session</p> -->
                                <div class="social-auth-links text-center mt-2 mb-3">
                                    <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-block btn-danger">
                                    <i class="fab fa-google-plus mr-2"></i> Google+
                                    </a>
                                </div>
                                <!-- /.social-auth-links -->

                                <p class="mb-1">
                                    <a href="forgot-password.html">I forgot my password</a>
                                </p>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 2nd Column for graphics -->
                <div class="column">
                <img class="boy" src="../styles/dist/img/Group 1.png">
                </div>
            </div>
        </div>
                
        <div class="footer">   
            <div class="footer1">
                        <img style="margin-top: 30px; margin-left: 30px;" src="../styles/dist/img/white 1.png" alt="">
                        <p style="margin-top: 10px; margin-left: 30px; margin-right: 120px; font-size: 14px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor.</p>
                        <img style="height: 25px; padding-left: 30px;" src="../styles/dist/img/facebook@512px.png">
                        <img style="height: 25px;" src="../styles/dist/img/linkedin@512px.png">
                        <img style="height: 25px;" src="../styles/dist/img/instagram@512px.png">
            </div>
                        <div class="footer1">
                        <h2 style="margin-top: 30px;">Quick Links</h2>
                        <div>
                        <p>Store</p>
                        <p>Free Tools</p>
                        <p>Hand Books</p>
                        </div>
                        </div>
                        <div class="footer1">
                        <h2 style="margin-top: 30px;">Our Website</h2>
                        <p>Website #1</p>
                        <p>Website #2</p>
                        </div>
                        <div class="footer1">
                        <h2 style="margin-top: 30px;">Need Help?</h2>
                        <p>Submit Issue</p>
                        <p>Help Center</p>
                        </div>
        </div>
 
<?php
include '../partials/footer.php';
?>