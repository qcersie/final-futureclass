<?php

    include('dbconnect.php');

    $rid = $_GET['id'];
    $sql = mysqli_query($con, "INSERT INTO request_report (rID,userEmail,report) VALUES ('$rid','$_SESSION[email]',1)");
    $sql2 = mysqli_query($con, "INSERT INTO notifications_admin (adminNotiID, type, ID, needAction) VALUES ('', 'request', '$rid', 1)");

    if ($_SESSION['userlevel'] == 'teacher') {
        header("location: request_view.php?id=$rid");
    }
    else if ($_SESSION['userlevel'] == 'student') {
        header("location: request.php?id=$rid");
    }

?>