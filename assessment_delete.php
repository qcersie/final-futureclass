<?php

    include('dbconnect.php');

    $qid = $_GET['id'];
    $id = $_SESSION['userid'];
    
    $del = mysqli_query($con, "UPDATE courses_good SET status=1 WHERE ID=$qid");
    $del2 = mysqli_query($con, "UPDATE notifications_admin SET needAction = 0 WHERE type = 'assess' AND ID = '$qid'");
    $del3 = mysqli_query($con, "UPDATE courses_good_report SET action=1 WHERE ID=$qid");
    
    echo mysqli_error($con);
    header("location: report_assessment.php?id=$id");

?>