<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
  header("location: index.php");
  exit;
}
 
// Include config file
require_once "./bdd/config.php";
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese su usuario.";
    } else{
        $username = trim($_POST["username"]);
    }
    
    // Check if password is empty
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingrese su contraseña.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = $username;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Store result
                mysqli_stmt_store_result($stmt);
                
                // Check if username exists, if yes then verify password
                if(mysqli_stmt_num_rows($stmt) == 1){                    
                    // Bind result variables
                    mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);
                    if(mysqli_stmt_fetch($stmt)){
                        if(password_verify($password, $hashed_password)){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: index.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "La contraseña que has ingresado no es válida.";
                        }
                    }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No existe cuenta registrada con ese nombre de usuario.";
                }
            } else{
                echo "Algo salió mal, por favor vuelve a intentarlo.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
}
?>


<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Affan - PWA Mobile HTML Template">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#0134d4">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">

    <!-- Title -->
    <title>Login || IlernaDaw</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
    <!-- Favicon -->
    <link rel="icon" href="favicon.ico">
    <!-- Core Stylesheet -->
    <link rel="stylesheet" href="assets/css/userlogin.css">
    <link rel="stylesheet" href="assets/loginAssets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/loginAssets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/loginAssets/css/tiny-slider.css">
    <link rel="stylesheet" href="assets/loginAssets/css/baguetteBox.min.css">
    <link rel="stylesheet" href="assets/loginAssets/css/rangeslider.css">
    <link rel="stylesheet" href="assets/loginAssets/css/vanilla-dataTables.min.css">
    <link rel="stylesheet" href="assets/loginAssets/css/apexcharts.css">
   
  </head>                                                                                                                 
  <body>
    <!-- Preloader -->
    <div id="preloader">        
      <div class="spinner-grow text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
    </div>
    <!-- Internet Connection Status -->
    <!-- # This code for showing internet connection status -->
    <div class="internet-connection-status" id="internetStatus"></div>
    <!-- Back Button -->
    <div class="login-back-button"><a href="index.html">
        <svg class="bi bi-arrow-left-short" width="32" height="32" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"></path>
        </svg></a></div>
    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
      <div class="custom-container">
        <div class="text-center px-4"><img class="login-intro-img" src="img/bg-img/36.png" alt=""></div>
        <!-- Register Form -->
        <div class="register-form mt-4">
          <h6 class="mb-3 text-center">Welcome Back!</h6>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
              <input class="form-control" type="text" name="username" placeholder="Name" value="<?php echo $username; ?>">
              <span class="help-block"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group position-relative <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
              <input class="form-control" id="psw-input" name="password" type="password" placeholder="Password">
              <div class="position-absolute" id="password-visibility"><i class="bi bi-eye"></i><i class="bi bi-eye-slash"></i></div>
              <span class="help-block"><?php echo $password_err; ?></span>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>
        </div>
        <!-- Login Meta -->
        <div class="login-meta-data text-center"><a class="stretched-link forgot-password d-block mt-3 mb-1" href="#"></a>
          <p class="mb-0">¿Aun no estas Registrado? <a class="stretched-link" href="register.php">Registrese Ahora</a></a></p>
        </div>
      </div>
    </div>
  </body>
  <!-- All JavaScript Files -->
    <script src="assets/js/userlogin.js"></script>
    <script src="assets/loginAssets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/loginAssets/js/slideToggle.min.js"></script>
    <script src="assets/loginAssets/js/internet-status.js"></script>
    <script src="assets/loginAssets/js/tiny-slider.js"></script>
    <script src="assets/loginAssets/js/baguetteBox.min.js"></script>
    <script src="assets/loginAssets/js/countdown.js"></script>
    <script src="assets/loginAssets/js/rangeslider.min.js"></script>
    <script src="assets/loginAssets/js/vanilla-dataTables.min.js"></script>
    <script src="assets/loginAssets/js/index.js"></script>
    <script src="assets/loginAssets/js/magic-grid.min.js"></script>
    <script src="assets/loginAssets/js/dark-rtl.js"></script>
    <script src="assets/loginAssets/js/active.js"></script>



</html>