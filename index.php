<?php

    session_start();
    ob_start();
    include('config.php');
    include('func.php');

    date_default_timezone_set("Asia/Kuala_Lumpur");

    $month_update = $amount_update = 0;
    $month_update_err = $amount_update_err = "";
    
    if (isset($_SESSION['uid'])) {
        
        $uid = $_SESSION['uid'];

        $verify_auth = "SELECT access_token FROM users WHERE username = '$uid'";
        $verify_res = mysqli_query($con, $verify_auth);

        if (mysqli_num_rows($verify_res) == 1 ) {
            
            $row = mysqli_fetch_assoc($verify_res);
            $auth_code = $row['access_token'];

            if ($auth_code != $_COOKIE['PHPSESSID']) {

                header('Location: login.php');
                exit();
                
            }
            else
            {

                if (isset($_POST['add']) || isset($_POST['edit']))
                {
                    if (!empty($_POST['month-data-update']) && !empty($_POST['amount-data-update']))
                    {
                        $month_update = mysqli_real_escape_string($con, $_POST['month-data-update']);
                        $amount_update = mysqli_real_escape_string($con, $_POST['amount-data-update']);

                        $month_update = test_input($month_update);
                        $amount_update = test_input($amount_update);

                        if ($month_update > 12 || $month_update < 1)
                        {
                            $month_update_err = "Month need to be between 1 to 12 only.";
                        }
                        if (is_numeric($month_update) != 1)
                        {
                            $month_update_err = "Please enter number only!";
                        }

                        if ($amount_update > 5000000 || $amount_update < 1)
                        {
                            $amount_update_err = "Amount need to be between RM 1 to RM 5,000,000 only.";
                        }
                        if (is_numeric($amount_update) != 1)
                        {
                            $amount_update_err = "Please enter number only!";
                        }

                        $all_update_err = $amount_update_err.$month_update_err;


                        if ($all_update_err == "")
                        {

                            $month_check = array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december");
                            $month = $month_check[$month_update - 1];


                            // Create a template
                            $data_update_sql = "UPDATE monthly_sales SET $month = ? WHERE username = ?;";
                            // Create a prepared statement
                            $data_update_stmt = mysqli_stmt_init($con);

                            // Prepare the prepared statement
                            if (!mysqli_stmt_prepare($data_update_stmt, $data_update_sql))
                            {
                                echo "SQL statement 1 failed.";

                            }
                            else
                            {
                                // Bind paremeters to the placeholder
                                mysqli_stmt_bind_param($data_update_stmt, "ss", $amount_update, $uid);
                                mysqli_stmt_execute($data_update_stmt);


                            }
                        }


                    }

                }

                

                $monthly_data_sql = "SELECT * FROM monthly_sales WHERE username = '$uid'";
                $monthly_data_res = mysqli_query($con, $monthly_data_sql);

                if (mysqli_num_rows($monthly_data_res) == 1)
                {
                    $monthly_data_row = mysqli_fetch_assoc($monthly_data_res);

                    $jan = $monthly_data_row['january'];
                    $feb = $monthly_data_row['february'];
                    $mar = $monthly_data_row['march'];
                    $apr = $monthly_data_row['april'];
                    $may = $monthly_data_row['may'];
                    $jun = $monthly_data_row['june'];
                    $jul = $monthly_data_row['july'];
                    $aug = $monthly_data_row['august'];
                    $sep = $monthly_data_row['september'];
                    $oct = $monthly_data_row['october'];
                    $nov = $monthly_data_row['november'];
                    $dec = $monthly_data_row['december'];

                    $annual_sales = $jan + $feb + $mar + $apr + $may + $jun + $jul + $aug + $sep + $oct + $nov + $dec;
                    
                    $current_month = date('F');
                    $current_month = strtolower($current_month);
                    $monthly_sales = $monthly_data_row[$current_month];

                }



            }

        }
        else {
            header('Location: login.php');
            exit();
        }

    }
    else {
        header('Location: login.php');
        exit();
    }

?>


<?php

    $title = "Admin | Dashboard";
    include('private-top.php');

?>


                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Earnings (Monthly)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $monthly_sales ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Earnings (Annual)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $annual_sales ?></div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Tasks
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">50%</div>
                                                </div>
                                                <div class="col">
                                                    <div class="progress progress-sm mr-2">
                                                        <div class="progress-bar bg-info" role="progressbar"
                                                            style="width: 50%" aria-valuenow="50" aria-valuemin="0"
                                                            aria-valuemax="100"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Requests Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Pending Requests</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->

                    <div class="row">

                        <!-- Area Chart -->
                        <div class="area-chart-fs col-xl-8 col-lg-7">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Earnings Overview</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>

                                        <!-- Add Modal -->
                                        <div class="modal fade" id="elegantModalAddForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <!--Content-->
                                                <div class="modal-content form-elegant">
                                                    <!--Header-->
                                                    <div class="modal-header text-center">
                                                        <h3 class="modal-title w-100 dark-grey-text font-weight-bold my-3" id="myModalLabel"><strong>Add Data</strong></h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <!--Body-->
                                                    <form class="modal-body mx-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                                        <!--Body-->
                                                        <div class="md-form mb-1 mt-0">
                                                            <input type="number" name="month-data-update" id="Form-email1" class="form-control validate" min="1" max="12" placeholder="Month (1 - 12)" required>
                                                            <?php if ($month_update_err != "") { echo "<script>alert('" . $month_update_err . "')</script>"; } ?>
                                                        </div>

                                                        <div class="md-form pb-3">
                                                            <input type="number" name="amount-data-update" id="Form-pass1" class="form-control validate" min="1" max="5000000" placeholder="Amount (RM)" required>
                                                            <?php if ($amount_update_err != "") { echo "<script>alert('" . $amount_update_err . "')</script>"; } ?>
                                                        </div>

                                                        <div class="text-center mb-3">
                                                            <input type="submit" class="btn blue-gradient btn-block btn-rounded z-depth-1a" name="add" value="Add"></input>
                                                        </div>
                                                        
                                                    </form>

                                                </div>
                                                <!--/.Content-->
                                            </div>
                                        </div>
                                        <!-- Modal -->

                                        <!-- Edit Modal -->
                                        <div class="modal fade" id="elegantModalEditForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog" role="document">
                                                <!--Content-->
                                                <div class="modal-content form-elegant">
                                                    <!--Header-->
                                                    <div class="modal-header text-center">
                                                        <h3 class="modal-title w-100 dark-grey-text font-weight-bold my-3" id="myModalLabel"><strong>Edit Data</strong></h3>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <!--Body-->
                                                    <form class="modal-body mx-4" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                                                        <!--Body-->
                                                        <div class="md-form mb-1 mt-0">
                                                            <input type="number" name="month-data-update" id="Form-email1" class="form-control validate" placeholder="Month (1 - 12)">
                                                        </div>

                                                        <div class="md-form pb-3">
                                                            <input type="number" name="amount-data-update" id="Form-pass1" class="form-control validate" placeholder="Amount (RM)">
                                                        </div>

                                                        <div class="text-center mb-3">
                                                            <input type="submit" class="btn blue-gradient btn-block btn-rounded z-depth-1a" name="edit" value="Edit"></input>
                                                        </div>
                                                        
                                                    </form>

                                                </div>
                                                <!--/.Content-->
                                            </div>
                                        </div>
                                        <!-- Modal -->
                                        
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="" data-toggle="modal" data-target="#elegantModalAddForm">Add data</a>
                                            <a class="dropdown-item" href="" data-toggle="modal" data-target="#elegantModalEditForm">Edit data</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-area">
                                        <canvas id="myAreaChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pie Chart -->
                        <div class="col-xl-4 col-lg-5">
                            <div class="card shadow mb-4">
                                <!-- Card Header - Dropdown -->
                                <div
                                    class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                                    <h6 class="m-0 font-weight-bold text-primary">Revenue Sources</h6>
                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                            aria-labelledby="dropdownMenuLink">
                                            <div class="dropdown-header">Dropdown Header:</div>
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>
                                    </div>
                                </div>
                                <!-- Card Body -->
                                <div class="card-body">
                                    <div class="chart-pie pt-4 pb-2">
                                        <canvas id="myPieChart"></canvas>
                                    </div>
                                    <div class="mt-4 text-center small">
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-primary"></i> Direct
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-success"></i> Social
                                        </span>
                                        <span class="mr-2">
                                            <i class="fas fa-circle text-info"></i> Referral
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-6 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Projects</h6>
                                </div>
                                <div class="card-body">
                                    <h4 class="small font-weight-bold">Server Migration <span
                                            class="float-right">20%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 20%"
                                            aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Sales Tracking <span
                                            class="float-right">40%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 40%"
                                            aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Customer Database <span
                                            class="float-right">60%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar" role="progressbar" style="width: 60%"
                                            aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Payout Details <span
                                            class="float-right">80%</span></h4>
                                    <div class="progress mb-4">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 80%"
                                            aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <h4 class="small font-weight-bold">Account Setup <span
                                            class="float-right">Complete!</span></h4>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 100%"
                                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Color System -->
                            <div class="row">
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-primary text-white shadow">
                                        <div class="card-body">
                                            Primary
                                            <div class="text-white-50 small">#4e73df</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-success text-white shadow">
                                        <div class="card-body">
                                            Success
                                            <div class="text-white-50 small">#1cc88a</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-info text-white shadow">
                                        <div class="card-body">
                                            Info
                                            <div class="text-white-50 small">#36b9cc</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-warning text-white shadow">
                                        <div class="card-body">
                                            Warning
                                            <div class="text-white-50 small">#f6c23e</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-danger text-white shadow">
                                        <div class="card-body">
                                            Danger
                                            <div class="text-white-50 small">#e74a3b</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-secondary text-white shadow">
                                        <div class="card-body">
                                            Secondary
                                            <div class="text-white-50 small">#858796</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-light text-black shadow">
                                        <div class="card-body">
                                            Light
                                            <div class="text-black-50 small">#f8f9fc</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 mb-4">
                                    <div class="card bg-dark text-white shadow">
                                        <div class="card-body">
                                            Dark
                                            <div class="text-white-50 small">#5a5c69</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">

                            <!-- Illustrations -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Illustrations</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center">
                                        <img class="img-fluid px-3 px-sm-4 mt-3 mb-4" style="width: 25rem;"
                                            src="img/undraw_posting_photo.svg" alt="...">
                                    </div>
                                    <p>Add some quality, svg illustrations to your project courtesy of <a
                                            target="_blank" rel="nofollow" href="https://undraw.co/">unDraw</a>, a
                                        constantly updated collection of beautiful svg images that you can use
                                        completely free and without attribution!</p>
                                    <a target="_blank" rel="nofollow" href="https://undraw.co/">Browse Illustrations on
                                        unDraw &rarr;</a>
                                </div>
                            </div>

                            <!-- Approach -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Development Approach</h6>
                                </div>
                                <div class="card-body">
                                    <p>Admin Helper makes extensive use of Bootstrap 4 utility classes in order to reduce
                                        CSS bloat and poor page performance. Custom CSS classes are used to create
                                        custom components and custom utility classes.</p>
                                    <p class="mb-0">Before working with this theme, you should become familiar with the
                                        Bootstrap framework, especially the utility classes.</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

<?php

    include('private-bottom.php');

?>