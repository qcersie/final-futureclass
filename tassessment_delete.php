<?php

    include('dbconnect.php');

    $qid = $_GET['id'];
    $id = $_SESSION['userid'];
    
    $del = mysqli_query($con, "UPDATE teachers_good SET status=1 WHERE ID=$qid");
    $del2 = mysqli_query($con, "UPDATE teachers_good_report SET action=1 WHERE ID=$qid");
    $del3 = mysqli_query($con, "UPDATE notifications_admin SET needAction = 0 WHERE type = 'tassess' AND ID = '$qid'");
    
    
    echo mysqli_error($con);
    header("location: report_teacher_assessment.php?id=$id");

?>