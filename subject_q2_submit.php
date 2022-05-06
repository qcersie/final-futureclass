<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $context=$_POST["comment"];
    $mail=$_SESSION["email"];
    $coursesID=$_GET['id'];

    $sql= "REPLACE courses_good SET coursesID=$coursesID,context='$context',timestamp=(NOW()),qtype='problem';";
    $result = mysqli_query($con,$sql);
    $q1ID = $con->insert_id;

    if($result){
        header('location: subject_q2.php?id='.$coursesID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>