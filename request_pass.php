<?php

    include('dbconnect.php');

    $rid = $_GET['id'];
    $id = $_SESSION['userid'];
    
    //$del = mysqli_query($con, "UPDATE requestment SET status=1 WHERE requestmentID=$rid");
    //$del2 = mysqli_query($con, "DELETE FROM requestment_reply WHERE requestmentID=$rid");
    //$del3 = mysqli_query($con, "DELETE FROM requestment_response WHERE requestmentID=$rid");
    $del4 = mysqli_query($con, "DELETE FROM notifications WHERE requestmentID=$rid");
    $del5 = mysqli_query($con, "UPDATE notifications_admin SET needAction = 0 WHERE type = 'request' AND ID = '$rid'");
    $del6 = mysqli_query($con, "UPDATE request_report SET action=1 WHERE requestmentID=$rid");

    header("location: report.php?id=$id");

?>