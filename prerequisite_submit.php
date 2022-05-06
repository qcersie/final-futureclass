<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $coursesID = $_GET['id'];
    $type = $_POST['sub1'];
    $content = $_POST['sub2'];

    $sql= "REPLACE courses_prere SET requirecontent='$content',coursesID = '$coursesID', requiretype='$type'";

    $result = mysqli_query($con,$sql);

    if($result){
        header('location: management.php?id='.$coursesID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>