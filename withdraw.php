<?php

    include('dbconnect.php');

    $cid = $_GET['id'];
    $id = $_SESSION['userid'];

    $sql = "UPDATE student_course SET status = 0 WHERE studentsID=$id AND coursesID = $cid";
    $result = mysqli_query($con,$sql);

    $sql2 = "DELETE FROM notifications WHERE userID=$id AND coursesID = $cid";
    $result2 = mysqli_query($con,$sql2);

    if ($result && $result2) {
        header("location: home_student.php");
    }
    else {
        echo mysqli_error($con);
    }

?>