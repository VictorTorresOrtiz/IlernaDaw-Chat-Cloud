<?php
// Include config file
require_once "./bdd/config.php";
 
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate username
    if(empty(trim($_POST["username"]))){
        $username_err = "Por favor ingrese un usuario.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";
        
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            
            // Set parameters
            $param_username = trim($_POST["username"]);
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                /* store result */
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "Este usuario ya fue tomado.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Al parecer algo salió mal.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Por favor ingresa una contraseña.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "La contraseña al menos debe tener 6 caracteres.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Confirma tu contraseña.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "No coincide la contraseña.";
        }
    }
    
    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        
        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);
            
            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Algo salió mal, por favor inténtalo de nuevo.";
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
    <title>Register || IlernaDaw</title>
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
    <div class="login-back-button"><a href="./index.html">
        <svg class="bi bi-arrow-left-short" width="32" height="32" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"></path>
        </svg></a></div>
    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
      <div class="custom-container">
        <div class="text-center px-4"><img class="login-intro-img" src="img/bg-img/36.png" alt=""></div>
        <!-- Register Form -->
        <div class="register-form mt-4">
          <h6 class="mb-3 text-center">Crear una Cuenta IlernaDaw.</h6>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
              <input class="form-control" type="text" name="username" placeholder="Nombre" value="<?php echo $username; ?>">
              <span class="help-block"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group position-relative <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
              <input class="form-control" id="psw-input" name="password" type="password" placeholder="Contraseña" value="<?php echo $password; ?>">
              <div class="position-absolute" id="password-visibility"><i class="bi bi-eye"></i><i class="bi bi-eye-slash"></i></div>
              <span class="help-block"><?php echo $password_err; ?></span>
            </div>

            <div class="form-group position-relative <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
              <input class="form-control" id="psw-input" name="confirm_password" type="password" placeholder="Repita Contraseña" value="<?php echo $confirm_password; ?>">
              <div class="position-absolute" id="password-visibility"><i class="bi bi-eye"></i><i class="bi bi-eye-slash"></i></div>
              <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>

            <button class="btn btn-primary w-100" type="submit">Crear Cuenta</button>
          </form>
        </div>
        <!-- Login Meta -->
        <div class="login-meta-data text-center"><a class="stretched-link forgot-password d-block mt-3 mb-1" href="#"></a>
          <p class="mb-0">¿Ya Tienes una Cuenta? <a class="stretched-link" href="login.php">Acceder Ahora</a></a></p>
        </div>
      </div>
    </div>
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
  </body>
</html>