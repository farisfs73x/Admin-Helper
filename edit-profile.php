<?php

session_start();
ob_start();

include('config.php');
include('func.php');

// Variable declaration
$uid = $email = $phone = $address = "";
$position = "";
$website = $github = $twitter = $instagram = $facebook = "-";

// Form variable declaration
$email_err = $address_err = $position_err = 
$website_err = $github_err = $twitter_err = $instagram_err = 
$facebook_err = "";
$phone_err = 0;


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
                if (isset($_POST['save-changes']))
                {

                    // Get the value from the input fields
                    $email = mysqli_real_escape_string($con, $_POST['email']);
                    $phone = mysqli_real_escape_string($con, $_POST['phone']);
                    $address = mysqli_real_escape_string($con, $_POST['address']);
                    $position = mysqli_real_escape_string($con, $_POST['position']);
                    $website = mysqli_real_escape_string($con, $_POST['website']);
                    $github = mysqli_real_escape_string($con, $_POST['github']);
                    $twitter = mysqli_real_escape_string($con, $_POST['twitter']);
                    $instagram = mysqli_real_escape_string($con, $_POST['instagram']);
                    $facebook = mysqli_real_escape_string($con, $_POST['facebook']);


                    //Email check
                    if (empty($email))
                    {
                        $email_err = "Email is required!";
                    }
                    else {
                        $email = test_input($email);
                        // check if e-mail address is well-formed
                        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                        {
                            $email_err = "Invalid email format!";
                        }
                    }

                    // Phone check
                    $phone = test_input($phone);
                    if ($phone > 999999999999 || $phone < 100000000)
                    {
                        $phone_err = "Invalid phone format!";
                    }
                    else if (is_numeric($phone) != 1)
                    {
                        $phone_err = "Please enter number only!";
                    }

                    // Address check
                    $address = test_input($address);

                    // Position check
                    $position = test_input($position);

                    // Website check
                    $website = test_input($website);

                    // Github check
                    $github = test_input($github);

                    // Twitter check
                    $twitter = test_input($twitter);

                    // Instagram check
                    $instagram = test_input($instagram);

                    // Facebook check
                    $facebook = test_input($facebook);


                    // Concate all error in one string value for easy check
                    $all_err = $email_err.$address_err.
                    $position_err.$website_err.$github_err.$twitter_err.
                    $instagram_err.$facebook_err;


                    echo $all_err;
                    if ($all_err == "" && $phone_err == 0)
                    {
                        // UPDATE Customers
                        //     SET ContactName = 'Alfred Schmidt', City= 'Frankfurt'
                        //     WHERE CustomerID = 1;

                        // Create a template
                        $sql = "UPDATE users SET email = ?, phone = ?, address = ?, position = ? WHERE username = ?;";
                        // $sql = "UPDATE INTO users (phone, address, position) VALUE (?, ?, ?) WHERE username = ?;";
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


                            // header('Location: login.php');
                            // exit();


                            // Create a template
                            $socmed_sql = "UPDATE socmeds SET website = ?, github = ?, twitter = ?, instagram = ?, facebook = ? WHERE username = ?;";
                            // $socmed_sql = "INSERT INTO socmeds (website, github, twitter, instagram, facebook) VALUE (?, ?, ?, ?, ?, ?) WHERE username = ?;";
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


    // $verify_auth = "SELECT access_token FROM users WHERE username = '$uid'";
    // $verify_res = mysqli_query($con, $verify_auth);

    // if (mysqli_num_rows($verify_res) == 1) {

    //     $row = mysqli_fetch_assoc($verify_res);
    //     $auth_code = $row['access_token'];

    //     if ($auth_code != $_COOKIE['PHPSESSID']) {
    //         header('Location: login.php');
    //         exit();
    //     }
    // } else {
    //     header('Location: login.php');
    //     exit();
    // }
}
else
{
    header('Location: login.php');
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Admin | Profile</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        .reg-err {color: red; font-size: 15px; margin-left: 5px;}
        .fixed-uid {color: orange; padding: 0; margin: 0; font-size: 15px;}
    </style>

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.html">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">SB Admin <sup>2</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="index.html">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Interface
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities" aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Addons
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Charts -->
            <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in" aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg" alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg" alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60" alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $uid ?></span>
                                <img class="img-profile rounded-circle" src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container">
                    <div class="main-body">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">
                                            <img src="https://bootdey.com/img/Content/avatar/avatar6.png" alt="Admin" class="rounded-circle p-1 bg-primary" width="110">
                                            <div class="mt-3">
                                                <h4><?php echo $uid ?></h4>
                                                <?php echo "<p class="."text-secondary mb-1".">" .$position. "</p>" ?>
                                                <?php echo "<p class="."text-muted font-size-sm".">" .$address. "</p>"; ?>
                                                <!-- <button class="btn btn-primary">Follow</button> -->
                                                <!-- <button class="btn btn-outline-primary">Message</button> -->
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
                                            <?php echo "<span class="."text-secondary".">" .$website. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-github me-2 icon-inline">
                                                        <path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path>
                                                    </svg>Github</h6>
                                                <?php echo "<span class="."text-secondary".">" .$github. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-twitter me-2 icon-inline text-info">
                                                        <path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path>
                                                    </svg>Twitter</h6>
                                                <?php echo "<span class="."text-secondary".">" .$twitter. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-instagram me-2 icon-inline text-danger">
                                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                                                    </svg>Instagram</h6>
                                                <?php echo "<span class="."text-secondary".">" .$instagram. "</span>" ?>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap">
                                                <h6 class="mb-0"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-facebook me-2 icon-inline text-primary">
                                                        <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                                                    </svg>Facebook</h6>
                                                <?php echo "<span class="."text-secondary".">" .$facebook. "</span>" ?>
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
                                                <input name="uid" type="text" class="form-control" value="<?php echo $uid ?>" placeholder="Muhammad" disabled>
                                                <p class="fixed-uid">*Username cannot be changed.</p>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Email</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input name="email" type="text" class="form-control" value="<?php echo $email ?>" placeholder="muhammad@gmail.com">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Phone</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input name="phone" type="number" class="form-control" value="<?php echo $phone ?>" placeholder="01234567890">
                                            </div>
                                        </div>
                                        <!-- <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Mobile</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" class="form-control" value="(320) 380-4539">
                                            </div>
                                        </div> -->
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Address</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" placeholder="Lot 123, Kg. Mana-mana, 12345 Mana-mana, Selangor, Malaysia">
                                            </div>
                                        </div>

                                        <hr><hr>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Position</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="position" class="form-control" value="<?php echo $position ?>" placeholder="Business Manager">
                                            </div>
                                        </div>

                                        <hr><hr>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Website</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="website" class="form-control" value="<?php if ($website == "-") { $website = "";} echo $website ?>" placeholder="admin.com">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">GitHub</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="github" class="form-control" value="<?php if ($github == "-") { $github = "";} echo $github ?>" placeholder="admin">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Twitter</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="twitter" class="form-control" value="<?php if ($twitter == "-") { $twitter = "";} echo $twitter ?>" placeholder="admin">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Instagram</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="instagram" class="form-control" value="<?php if ($instagram == "-") { $instagram = "";} echo $instagram ?>" placeholder="@admin">
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-sm-3">
                                                <h6 class="mb-0">Facebook</h6>
                                            </div>
                                            <div class="col-sm-9 text-secondary">
                                                <input type="text" name="facebook" class="form-control" value="<?php if ($facebook == "-") { $facebook = "";} echo $facebook ?>" placeholder="admin">
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
                                <!-- <div class="row">
                                    <div class="col-sm-12">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="d-flex align-items-center mb-3">Project Status</h5>
                                                <p>Web Design</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: 80%" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <p>Website Markup</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-danger" role="progressbar" style="width: 72%" aria-valuenow="72" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <p>One Page</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: 89%" aria-valuenow="89" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <p>Mobile Template</p>
                                                <div class="progress mb-3" style="height: 5px">
                                                    <div class="progress-bar bg-warning" role="progressbar" style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <p>Backend API</p>
                                                <div class="progress" style="height: 5px">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: 66%" aria-valuenow="66" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2021</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <!-- <a class="btn btn-primary" href="logout.php">Logout</a> -->
                    <form method="POST" action="logout.php">
                        <input type="submit" name="logout" value="Logout" class="btn btn-primary">
                    </form>
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

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>

    <!-- Page level custom scripts -->
    <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script>

</body>

</html>