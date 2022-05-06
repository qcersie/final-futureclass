<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $coursesID = $_GET['id'];
    $chapnum = $_POST['chapter'];
    $chapcontent = $_POST['content'];

    $sql= "REPLACE courses_material SET content='$chapcontent',coursesID = $coursesID, chapnum=$chapnum";

    $result = mysqli_query($con,$sql);

    if($result){
        header('location: management.php?id='.$coursesID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>