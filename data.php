<?php
session_start();
include('config.php');

if (isset($_SESSION['uid']) && isset($_SESSION['verify_session']))
{
    $name = $_SESSION['uid'];

    $result = mysqli_query($con, "SELECT * FROM monthly_sales WHERE username = '$name'");
    
    $data = array();

    $row = mysqli_fetch_object($result);
    if ($row)
    {
        array_push($data, $row);
    }
    
    echo json_encode($data);
    exit();
    
}