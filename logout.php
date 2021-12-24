<?php

    if(isset($_POST['logout']))
    {
        session_start();
        ob_start();
        include("config.php");

        $uid = $_SESSION['uid'];

        $rm_sql = "UPDATE users SET access_token = '' WHERE username = '$uid'";
        $result = mysqli_query($con, $rm_sql);

        if ($result) {
            
            unset($_SESSION['uid']);
            session_destroy();

            header('Location: index.php');
            exit();

        }

    }
    else 
    {
        header("Location: 404.php");
        exit();
    }
?>