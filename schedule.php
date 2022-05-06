<?php

    include('dbconnect.php');

    if ($_SESSION['userlevel'] != "admin") {
        header('location: userlevel.php');
    }
    
    if (isset($_POST["semester"])) {
        $start = $_POST["startdate"]." ".$_POST["starttime"].":00";
        $end = $_POST["enddate"]." ".$_POST["starttime"].":00";
        $semester = $_POST["semester"];
        $back = $_POST["page"];
    } else {
        $_SESSION['msg'] = "Assessment Schedule Error";
    }
    
    $sql = "UPDATE semester SET start='$start', end='$end' WHERE semesterName = '$semester'";
    $result = mysqli_query($con,$sql);

    if ($result) {
        echo $_POST['page'];
        header("location: home_admin.php");
    } else {
        echo mysqli_error($con);
    }
?>