<?php

include('dbconnect.php');

$qid = $_GET['id'];
$cid = $_GET['cid'];
$email = $_SESSION['email'];

if ($_SESSION['userlevel'] == 'teacher') {
    $qn = $_GET['qn']."_teacher";
}
else if ($_SESSION['userlevel'] == 'student') {
    $qn = $_GET['qn'];
}

$sql = mysqli_query($con, "REPLACE INTO courses_good_report (ID,userEmail,report) VALUES ($qid,'$email',1)");
$sql2 = mysqli_query($con, "INSERT INTO notifications_admin (adminNotiID, type, ID, needAction) VALUES ('', 'assess', '$qid', 1)");

if($sql){
    header("location: subject_$qn.php?id=$cid");

} else {
    echo mysqli_error($con);
    echo "<br>";
}

?>