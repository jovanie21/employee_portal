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
                $_SESSION['user_id'] = $row['user_id'];;
                $role_id = $row['role_id'];
                if ($role_id == '0'){
                  header('Location: ../admin/index.php');
                }else if ($role_id == '1'){
                  echo'role 1';
                }else{
                  echo'role 2';
                }
              } 
              // header('Location: ../admin/index.php');
              exit;
          }
          else{
              // if user not exists we will insert the user
              $insert = mysqli_query($db, "INSERT INTO tbl_user(google_id,fname,email,profile_image) VALUES('$google_id','$full_name','$email','$profile_pic')");
              if($insert){
                  // $_SESSION['login_id'] = $id;
                  echo "Insert";
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

<body class="hold-transition login-page">
<div class="login-box">
  <!-- /.login-logo -->
  <div class="card card-outline card-primary">
    <div class="card-header text-center">
      <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a>
    </div>
    <div class="card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <form action="" method="post">
        <div class="input-group mb-3">
          <input name="username" class="form-control" placeholder="username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-8">
            <div class="icheck-primary">
              <input type="checkbox" id="remember">
              <label for="remember">
                Remember Me
              </label>
            </div>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div>
      <!-- /.social-auth-links -->

      <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p>
      <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p>
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>

<?php
include '../partials/footer.php';
?>