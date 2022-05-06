<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $context=$_POST["comment"];
    $mail=$_SESSION["email"];
    $coursesID=$_GET['id'];
    $teachersID = $_GET['tid'];

    $sql= "REPLACE teachers_good SET coursesID=$coursesID,context='$context',teachersID='$teachersID',timestamp=(NOW()),qtype='suggestion';";
    $result = mysqli_query($con,$sql);
    $q1ID = $con->insert_id;

    if($result){
        header('location: instructor_q2.php?id='.$coursesID.'&tid='.$teachersID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>