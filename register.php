<?php
session_start();
ob_start();

include('config.php');
include('func.php');

date_default_timezone_set("Asia/Kuala_Lumpur");

if (isset($_SESSION['uid']) && isset($_SESSION['verify_session']))
{

    // Assign current session
    $verify_sess_id = $_SESSION['verify_session'];
    $verify_sess_uid = $_SESSION['uid'];

    // Create a template
    $verify_sess_sql = "SELECT access_token FROM users WHERE username = ?;";
    // Create a prepared statement
    $verify_sess_stmt = mysqli_stmt_init($con);

    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($verify_sess_stmt, $verify_sess_sql))
    {
        echo "Verify Auths SQL statement failed.";

        header('Location: index.php');
        exit();

    }
    else
    {

        mysqli_stmt_bind_param($verify_sess_stmt, "s", $verify_sess_uid);
        mysqli_stmt_execute($verify_sess_stmt);
        
        // Get the result from database
        $verify_sess_result = mysqli_stmt_get_result($verify_sess_stmt);

        // If have result from database
        if (mysqli_num_rows($verify_sess_result) == 1)
        {
            $verify_sess_row = mysqli_fetch_assoc($verify_sess_result);
            
            // Compare session from database
            if ($verify_sess_id == $verify_sess_row['access_token'])
            {
                
                header('Location: index.php');
                exit();
            }
        }
    }
    
}

// Variable declaration
$uid = $email = $pwd = $c_pwd = "";
$uid_err = $email_err = $pwd_err = $c_pwd_err = "";

// Form method POST check
if (isset($_POST['signup'])) {

    // Get the value from the input fields
    $uid = mysqli_real_escape_string($con, $_POST['uid']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $pwd = mysqli_real_escape_string($con, $_POST['pwd']);
    $c_pwd = mysqli_real_escape_string($con, $_POST['c-pwd']);

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
    
    //Email check
    if (empty($email))
    {
        $email_err = "Email is required!";
    }
    else {

        if (strlen($email) < 6 || strlen($email) > 50)
        {
            $uid_err = "Email must between 6 to 50 characters.";
        }

        $email = test_input($email);

        // check if e-mail address is well-formed
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $email_err = "Invalid email format!";
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
    
   

    //Confirm password check
    if (empty($c_pwd))
    {
        $c_pwd_err = "Confirm password is required!";
    }
    else {

        if (strlen($c_pwd) < 8 || strlen($c_pwd) > 30)
        {
            $c_pwd_err = "Confirm Password must between 8 to 30 characters.";
        }

        //check password = confirm password
        if ($pwd != $c_pwd) {
            $c_pwd_err = "Confirm password is not match!";
        }
    }

    
    // Concate all error in one string value for easy check
    $all_err = $uid_err.$email_err.$pwd_err.$c_pwd_err;


    // If no error on the 1ST LAYER check, check the existing username and email address
    if ($all_err == "")
    {

        // Create template
        $exist_check_sql = "SELECT * FROM users WHERE username = ? OR email = ?;";
        // Create a prepared statement
        $exist_check_stmt = mysqli_stmt_init($con);

        // Prepare the prepared statement
        if (!mysqli_stmt_prepare($exist_check_stmt, $exist_check_sql))
        {
            echo "Exist SQL statement failed.";
        }
        else
        {
            mysqli_stmt_bind_param($exist_check_stmt, "ss", $uid, $email);
            mysqli_stmt_execute($exist_check_stmt);
            // Get the result
            $exist_check_result = mysqli_stmt_get_result($exist_check_stmt);

            // If there is the same username or email in database
            if (mysqli_num_rows($exist_check_result) != 0)
            {
                $exist_user_data = mysqli_fetch_assoc($exist_check_result);

                // If username already exist
                if ($uid == $exist_user_data['username'])
                {
                    $uid_err = "Username is already exist!";
                }

                // If email already exist
                if ($email == $exist_user_data['email'])
                {
                    $email_err = "Email is already exist!";
                }
            }
            // If no error in 2ND LAYER check, proceed to store data into database
            else
            {

                // Hash the password before insert into database
                $pwd = password_hash($pwd, PASSWORD_DEFAULT);
                $register_date = date("d/m/Y");

                // Create a template
                $sql = "INSERT INTO users (username, email, password, register_date) VALUE (?, ?, ?, ?);";   // Table users
                $socmed_sql = "INSERT INTO socmeds (username) VALUE (?);";  // Table socmeds
                $monthly_sales_sql = "INSERT INTO monthly_sales (username) VALUE (?);"; // Table montly_sales

                // Create a prepared statement
                $stmt = mysqli_stmt_init($con); // users table
                $socmed_stmt = mysqli_stmt_init($con);  // socmeds table
                $monthly_sales_stmt = mysqli_stmt_init($con);   // monthly_sales table

                // Prepare the prepared statement
                if (!mysqli_stmt_prepare($stmt, $sql) || !mysqli_stmt_prepare($socmed_stmt, $socmed_sql) || !mysqli_stmt_prepare($monthly_sales_stmt, $monthly_sales_sql))
                {
                    echo "SQL statement failed.";
                }
                else
                {
                    // Bind paremeters to the placeholder

                    // users table
                    mysqli_stmt_bind_param($stmt, "ssss", $uid, $email, $pwd, $register_date);
                    mysqli_stmt_execute($stmt);

                    // socmeds table
                    mysqli_stmt_bind_param($socmed_stmt, "s", $uid);
                    mysqli_stmt_execute($socmed_stmt);

                    // monthly_sales table
                    mysqli_stmt_bind_param($monthly_sales_stmt, "s", $uid);
                    mysqli_stmt_execute($monthly_sales_stmt);

                    header('Location: login.php');
                    exit();
                }

            }
        } 

    }
}

?>

<?php

    $title = "Admin | Register"; 
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

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                            </div>
                            <form class="user" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">

                                <div class="form-group">
                                    <input type="text" name="uid" class="form-control form-control-user" minlength="3" maxlength="30" value="<?php echo $uid; ?>" placeholder="Username">
                                    <p class="reg-err"><?php if ($uid_err != "") {echo $uid_err;} ?></p>
                                </div>

                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" minlength="6" maxlength="50" value="<?php echo $email; ?>" placeholder="Email">
                                    <p class="reg-err"><?php if ($email_err != "") {echo $email_err;} ?></p>
                                </div>

                                <div class="form-group">
                                    <input type="password" name="pwd" class="form-control form-control-user" minlength="8" maxlength="30" placeholder="Password">
                                    <p class="reg-err"><?php if ($pwd_err != "") {echo $pwd_err;} ?></p>
                                </div>
                                <div class="form-group">
                                    <input type="password" name="c-pwd" class="form-control form-control-user" minlength="8" maxlength="30" placeholder="Confirm Password">
                                    <p class="reg-err"><?php if ($c_pwd_err != "") {echo $c_pwd_err;} ?></p>
                                </div>

                                <input type="submit" name="signup" value="Register" class="btn btn-primary btn-user btn-block">
                                
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="forgot-password.php">Forgot Password?</a>
                            </div>
                            <div class="text-center">
                                <a class="small" href="login.php">Already have an account? Login!</a>
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