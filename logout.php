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
            unset($_SESSION['verify_session']);
            unset($_SESSION['website']);
            unset($_SESSION['github']);
            unset($_SESSION['twitter']);
            unset($_SESSION['instagram']);
            unset($_SESSION['facebook']);
            
            session_destroy();

            header('Location: index.php');
            exit();

        }
        else
        {
            header('Location: login.php');
            exit();
        }

    }
    else 
    {
        header("Location: 404.php");
        exit();
    }
?>