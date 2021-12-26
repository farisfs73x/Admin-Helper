<?php

session_start();
ob_start();

include('config.php');
include('func.php');

// Variable declaration
$uid = $email = $address = "";
$position = "";
$phone = null;
$website = $github = $twitter = $instagram = $facebook = "-";

// Form error variable declaration
$email_err = $address_err = $position_err = 
$website_err = $github_err = $twitter_err = $instagram_err = 
$facebook_err = $phone_err = "";


if (isset($_SESSION['uid'])) {

    $uid = $_SESSION['uid'];

    // Create template
    $verify_auth_sql = "SELECT * FROM users WHERE username = ?;";
    // Create a prepared statement
    $verify_auth_stmt = mysqli_stmt_init($con);

    // Prepare the prepared statement
    if (!mysqli_stmt_prepare($verify_auth_stmt, $verify_auth_sql))
    {
        echo "Verify Auths SQL statement failed.";
        
        header('Location: login.php');
        exit();
    }
    else
    {
        mysqli_stmt_bind_param($verify_auth_stmt, "s", $uid);
        mysqli_stmt_execute($verify_auth_stmt);

        // Get the result
        $verify_auth_res = mysqli_stmt_get_result($verify_auth_stmt);

        if (mysqli_num_rows($verify_auth_res) == 1)
        {

            $row = mysqli_fetch_assoc($verify_auth_res);
            $auth_code = $row['access_token'];

            $email = $row['email'];
            $phone = $row['phone'];
            $address = $row['address'];
            $position = $row['position'];

            if ($auth_code != $_COOKIE['PHPSESSID'])
            {
                header('Location: login.php');
                exit();
            }
            else
            {
                $website = $_SESSION['website'];
                $github = $_SESSION['github'];
                $twitter = $_SESSION['twitter'];
                $instagram = $_SESSION['instagram'];
                $facebook = $_SESSION['facebook'];
                
                // Form method POST check
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save-changes']))
                {

                    // Get the value from the input fields
                    $position = mysqli_real_escape_string($con, $_POST['position']);
                    $website = mysqli_real_escape_string($con, $_POST['website']);
                    $github = mysqli_real_escape_string($con, $_POST['github']);
                    $twitter = mysqli_real_escape_string($con, $_POST['twitter']);
                    $instagram = mysqli_real_escape_string($con, $_POST['instagram']);
                    $facebook = mysqli_real_escape_string($con, $_POST['facebook']);


                    //Email check
                    if (empty($_POST['email'])) 
                    {
                        $email_err = "Email is required!";
                    }
                    else
                    {
                        $email = mysqli_real_escape_string($con, $_POST['email']);
                        $email = test_input($email);
                        // check if e-mail address is well-formed
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                        {
                            $email_err = "Invalid email format!";
                        }
                    }
                    
                    // Phone check
                    if (empty($_POST['phone']))
                    {
                        $phone = null;
                    }
                    else
                    {

                        $phone = mysqli_real_escape_string($con, $_POST['phone']);
                        
                        $phone = test_input($phone);
                        if ($phone > 999999999999 || $phone < 100000000)
                        {
                            $phone_err = "Invalid phone format!";
                        }
                        else if (is_numeric($phone) != 1)
                        {
                            $phone_err = "Please enter number only!";
                        }

                    }

                    // Address check
                    if (empty($_POST['address']))
                    {
                        $address = "";
                    }
                    else
                    {
                        $address = mysqli_real_escape_string($con, $_POST['address']);
                        $address = test_input($address);
                    }
                    
                    // Position check
                    if (empty($_POST['position']))
                    {
                        $position = "";
                    }
                    else
                    {
                        $position = mysqli_real_escape_string($con, $_POST['position']);
                        $position = test_input($position);
                    }

                    // Website check
                    if (empty($_POST['website']))
                    {
                        $website = "";
                    }
                    else
                    {
                        $website = mysqli_real_escape_string($con, $_POST['website']);

                        $website = test_input($website);

                        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$website))
                        {
                            $website_err = "Invalid URL";
                        }
                    }

                    // Github check
                    if (empty($_POST['github']))
                    {
                        $github = "";
                    }
                    else
                    {
                        $github = mysqli_real_escape_string($con, $_POST['github']);
                        $github = test_input($github);
                    }

                    // Twitter check
                    if (empty($_POST['twitter']))
                    {
                        $twitter = "";
                    }
                    else
                    {
                        $twitter = mysqli_real_escape_string($con, $_POST['twitter']);
                        $twitter = test_input($twitter);
                    }

                    // Instagram check
                    if (empty($_POST['instagram']))
                    {
                        $instagram = "";
                    }
                    else
                    {
                        $instagram = mysqli_real_escape_string($con, $_POST['instagram']);
                        $instagram = test_input($instagram);
                    }

                    // Facebook check
                    if (empty($_POST['facebook']))
                    {
                        $facebook = "";
                    }
                    else
                    {
                        $facebook = mysqli_real_escape_string($con, $_POST['facebook']);
                        $facebook = test_input($facebook);
                    }


                    // Concate all error in one string value for easy check
                    $all_err = $email_err.$phone_err.$address_err.
                    $position_err.$website_err.$github_err.$twitter_err.
                    $instagram_err.$facebook_err;


                    
                    if ($all_err == "")
                    {

                        // Create a template
                        $sql = "UPDATE users SET email = ?, phone = ?, address = ?, position = ? WHERE username = ?;";
                        // Create a prepared statement
                        $stmt = mysqli_stmt_init($con);

                        // Prepare the prepared statement
                        if (!mysqli_stmt_prepare($stmt, $sql))
                        {
                            echo "SQL statement 1 failed.";
                        }
                        else
                        {
                            // Bind paremeters to the placeholder
                            mysqli_stmt_bind_param($stmt, "sisss", $email, $phone, $address, $position, $uid);
                            mysqli_stmt_execute($stmt);


                            // Create a template
                            $socmed_sql = "UPDATE socmeds SET website = ?, github = ?, twitter = ?, instagram = ?, facebook = ? WHERE username = ?;";
                            // Create a prepared statement
                            $socmed_stmt = mysqli_stmt_init($con);

                            // Prepare the prepared statement
                            if (!mysqli_stmt_prepare($socmed_stmt, $socmed_sql))
                            {
                                echo "SQL statement 2 failed.";
                            }
                            else
                            {
                                // Bind paremeters to the placeholder
                                mysqli_stmt_bind_param($socmed_stmt, "ssssss", $website, $github, $twitter, $instagram, $facebook, $uid);
                                mysqli_stmt_execute($socmed_stmt);

                                header('Location: profile.php');
                                exit();
                            }

                        }

                    }

                }

            }

        }
        else
        {
            header('Location: login.php');
            exit();
        }
    }
}
else
{
    header('Location: login.php');
    exit();
}

?>



<?php

    $title = "Admin | Edit Profile"; 
    include('private-top.php');

?>

                <!-- Begin Page Content -->

                <div class="container">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Edit Profile</h1>
                    </div>

                    <div class="main-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">
                                            <img src="img/undraw_profile.svg" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                                            <div class="mt-3">
                                                <h4><?php echo $uid ?></h4>
                                                <?php echo "<p class="."text-secondary mb-1".">" .$position. "</p>" ?>
                                                <?php echo "<p class="."text-muted font-size-sm".">" .$address. "</p>"; ?>
                                            </div>
                                        </div>
                                        <hr class="my-4">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-globe me-2 icon-inline">
                                                        <circle cx="12" cy="12" r="10"></circle>
                                                        <line x1="2" y1="12" x2="22" y2="12"></line>
                                                        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                                    </svg>Website</h6>
                                            <?php if ($website == "") { $website = "-";} echo "<span class="."text-secondary".">" .$website. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github me-2 icon-inline">
                                                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                                                    </svg>Github</h6>
                                                <?php if ($github == "") { $github = "-";} echo "<span class="."text-secondary".">" .$github. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter me-2 icon-inline text-info">
                                                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                                                    </svg>Twitter</h6>
                                                <?php if ($twitter == "") { $twitter = "-";} echo "<span class="."text-secondary".">" .$twitter. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram me-2 icon-inline text-danger">
                                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                                    </svg>Instagram</h6>
                                                <?php if ($instagram == "") { $instagram = "-";} echo "<span class="."text-secondary".">" .$instagram. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook me-2 icon-inline text-primary">
                                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                                    </svg>Facebook</h6>
                                                <?php if ($facebook == "") { $facebook = "-";} echo "<span class="."text-secondary".">" .$facebook. "</span>" ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="card">
                                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Username</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="<?php echo $uid ?>" placeholder="Muhammad" disabled>
                                                <p class="fixed-uid">*Username cannot be changed.</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Email</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input name="email" type="email" class="form-control" minlength="6" maxlength="50" value="<?php echo $email ?>" placeholder="muhammad@gmail.com">
                                                <p class="reg-err"><?php if ($email_err != "") {echo $email_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Phone</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input name="phone" type="number" class="form-control" value="<?php echo $phone ?>" placeholder="01234567890">
                                                <p class="reg-err"><?php if ($phone_err != "") {echo $phone_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Address</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="address" class="form-control" minlength="3" maxlength="255" value="<?php echo $address ?>" placeholder="Lot 123, Kg. Mana-mana, 12345 Mana-mana, Selangor, Malaysia">
                                                <p class="reg-err"><?php if ($address_err != "") {echo $address_err;} ?></p>
                                            </div>
                                        </div>

                                        <hr><hr>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Position</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="position" class="form-control" minlength="3" maxlength="100" value="<?php echo $position ?>" placeholder="Business Manager">
                                                <p class="reg-err"><?php if ($position_err != "") {echo $position_err;} ?></p>
                                            </div>
                                        </div>

                                        <hr><hr>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Website</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="website" class="form-control" minlength="3" maxlength="255" value="<?php if ($website == "-") { $website = "";} echo $website ?>" placeholder="adminhelper.com">
                                                <p class="reg-err"><?php if ($website_err != "") {echo $website_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">GitHub</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="github" class="form-control" minlength="3" maxlength="100" value="<?php if ($github == "-") { $github = "";} echo $github ?>" placeholder="adminhelper">
                                                <p class="reg-err"><?php if ($github_err != "") {echo $github_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Twitter</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="twitter" class="form-control" minlength="3" maxlength="100" value="<?php if ($twitter == "-") { $twitter = "";} echo $twitter ?>" placeholder="adminhelper">
                                                <p class="reg-err"><?php if ($twitter_err != "") {echo $twitter_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Instagram</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="instagram" class="form-control" minlength="3" maxlength="50" value="<?php if ($instagram == "-") { $instagram = "";} echo $instagram ?>" placeholder="@adminhelper">
                                                <p class="reg-err"><?php if ($instagram_err != "") {echo $instagram_err;} ?></p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Facebook</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="facebook" class="form-control" minlength="3" maxlength="100" value="<?php if ($facebook == "-") { $facebook = "";} echo $facebook ?>" placeholder="adminhelper">
                                                <p class="reg-err"><?php if ($facebook_err != "") {echo $facebook_err;} ?></p>
                                            </div>
                                        </div>


                                        <div class="row">
                                            <div class="col-sm-3"></div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="submit" name="save-changes" class="btn btn-primary px-4" value="Save Changes">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

 

<?php

include('private-bottom.php');

?>
