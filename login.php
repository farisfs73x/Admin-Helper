<?php
session_start();
ob_start();

include('config.php');
include('func.php');

if (isset($_SESSION['uid']))
{
    header('Location: index.php');
    exit();
}

// Variable declaration
$uid = $pwd = "";
$uid_err = $pwd_err = "";

// Form method POST check
if (isset($_POST['login'])) {

    // Get the value from the input fields
    $uid = mysqli_real_escape_string($con, $_POST['uid']);
    $pwd = mysqli_real_escape_string($con, $_POST['pwd']);

    //Username check
    if (empty($uid))
    {
        $uid_err = "Name is required!";
    }
    else {

        if (strlen($uid) < 3 || strlen($uid) > 30)
        {
            $uid_err = "Name must between 3 to 30 characters.";
        }

        $uid = test_input($uid);

        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/", $uid))
        {
            $uid_err = "Only letters and white space allowed!";
        }
    }

    //Password check
    if (empty($pwd))
    {
        $pwd_err = "Password is required!";
    }
    else
    {
        if (strlen($pwd) < 8 || strlen($pwd) > 30)
        {
            $pwd_err = "Password must between 8 to 30 characters.";
        }
    }


    // Concate all error in one string value for easy check
    $all_err = $uid_err.$pwd_err;


    // If no error, proceed...
    if ($all_err == "")
    {

        // Created a template
        $sql = "SELECT * FROM users WHERE username = ?; ";
        // Create a prepared statement
        $stmt = mysqli_stmt_init($con);
        
        // Prepare the prepared statement
        if(!mysqli_stmt_prepare($stmt, $sql))
        {
            echo "SQL statement failed.";
        }
        else
        {
            // Bind parameters to the placeholder
            mysqli_stmt_bind_param($stmt, "s", $uid);
            // Run parameters inside database
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            // If there are data that match with the inserted username by the user
            if (mysqli_num_rows($result) == 1)
            {
                // Fetch the data
                $user_data = mysqli_fetch_assoc($result);

                // Verify the password
                if (password_verify($pwd, $user_data['password']))
                {
                    
                    // If verified, regenerate the session id and store it in the variable
                    session_regenerate_id();
                    $auth_code = session_id();

                    // Store session uid for authorization used
                    $_SESSION['uid'] = $uid;

                    // Store session uid into database as access token.
                    // Create a template
                    $auth_sql = "UPDATE users SET access_token = ? WHERE username = ?;";
                    // Create a prepared statement
                    $auth_stmt = mysqli_stmt_init($con);

                    // Prepare the prepared statement
                    if(!mysqli_stmt_prepare($auth_stmt, $auth_sql))
                    {
                        echo "SQL statement verify failed.";
                    }
                    else
                    {
                        // Bind parameters to the placeholder
                        mysqli_stmt_bind_param($auth_stmt, "ss", $auth_code, $uid);
                        // Execute the statement
                        mysqli_stmt_execute($auth_stmt);

                        header('Location: index.php');
                        exit();
                    }
                }
                else
                {
                    $pwd_err = "Incorrect password! Please try again.";
                }
            }
            else
            {
                $uid_err = "$uid is not exist!";
            }
        }
    }

}


?>

<?php

    $title = "Admin | Login"; 
    include('meta.php');
    $url_site = url_site();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Primary Meta Tags -->
    <title><?php echo $title ?></title>
    <meta name="title" content="Admin Helper">
    <meta name="description" content="Admin Management System is for admin to keep track their important things such as sales, profit and etc. of their company.">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $url_site ?>">
    <meta property="og:title" content="Admin Helper">
    <meta property="og:description" content="Admin Management System is for admin to keep track their important things such as sales, profit and etc. of their company.">
    <meta property="og:image" content="https://freesvg.org/img/1541658525.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php echo $url_site ?>">
    <meta property="twitter:title" content="Admin Helper">
    <meta property="twitter:description" content="Admin Management System is for admin to keep track their important things such as sales, profit and etc. of their company.">
    <meta property="twitter:image" content="https://freesvg.org/img/1541658525.png">

    <!-- Site Icon @ Favicon -->
    <link rel="icon" type="image/x-icon" href="https://freesvg.org/img/1541658525.png"/>


    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .reg-err {color: red; font-size: 15px; margin-left: 5px;}
    </style>

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-xl-10 col-lg-12 col-md-9">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <!-- Nested Row within Card Body -->
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    <form class="user" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

                                        <div class="form-group">
                                            <input type="text" name="uid" class="form-control form-control-user" minlength="3" maxlength="30" value="<?php echo $uid; ?>" placeholder="Username">
                                            <p class="reg-err"><?php if ($uid_err != "") {echo $uid_err;} ?></p>
                                        </div>

                                        <div class="form-group">
                                            <input type="password" name="pwd" class="form-control form-control-user" minlength="8" maxlength="30" placeholder="Password">
                                            <p class="reg-err"><?php if ($pwd_err != "") {echo $pwd_err;} ?></p>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>

                                        <input type="submit" name="login" value="Login" class="btn btn-primary btn-user btn-block">

                                    </form>
                                    <hr>
                                    <div class="text-center">
                                        <a class="small" href="forgot-password.html">Forgot Password?</a>
                                    </div>
                                    <div class="text-center">
                                        <a class="small" href="register.php">Create an Account!</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>