<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $coursesID = $_GET['id'];
    $email = $_SESSION['email'];
    
    if (isset($_POST['Q1'])) {
        $Q[1] = $_POST['Q1'];
    } 
    if (isset($_POST['Q2'])) {
        $Q[2] = $_POST['Q2'];
    } 
    if (isset($_POST['Q3'])) {
        $Q[3] = $_POST['Q3'];
    } 
    if (isset($_POST['Q4'])) {
        $Q[4] = $_POST['Q4'];
    } 
    if (isset($_POST['Q5'])) {
        $Q[5] = $_POST['Q5'];
    } 
    if (isset($_POST['Q6'])) {
        $Q[6] = $_POST['Q6'];
    } 
    if (isset($_POST['Q7'])) {
        $Q[7] = $_POST['Q7'];
    } 
    if (isset($_POST['Q8'])) {
        $Q[8] = $_POST['Q8'];
    }
    if (isset($_POST['Q9'])) {
        $Q[9] = $_POST['Q9'];
    } 
    if (isset($_POST['Q10'])) {
        $Q[10] = $_POST['Q10'];
    }  

    /*****/
    $sqlcheck = "UPDATE assessment_count SET status = 1 WHERE coursesID = $coursesID AND userEmail = '$email'";
    $resultcheck = mysqli_query($con,$sqlcheck);

    if ($resultcheck) {
        $sql= "REPLACE assessment SET coursesID=$coursesID,semesterPeriod='test',timestamp=(NOW());";
        $result = mysqli_query($con,$sql);
        $assessmentID = mysqli_insert_id($con);

        // if ($coursesID == 9){
        //     $b = 8;
        // } else if ($coursesID == 8) {
        //     $b = 11;
        // } else {
        //     $b = 9;
        // }

        // $a = 1;
        // while ($a < $b) {
        //     $qsql= "REPLACE assessment_score SET coursesID='$coursesID',questionID='$a',score='$Q[$a]',assessmentID='$assessmentID'";
        //     $qresult = mysqli_query($con,$qsql);
        //     $a = $a +1;
        // }

        if ($coursesID == 8) {
            $i = 21; $lastQ = 30; $a = 1;
        } else if ($coursesID == 9) {
            $i = 31; $lastQ = 37; $a = 1;
        } else {    
            $i = 1; $lastQ = 10; $a = 1;
        }
        while ($i <= $lastQ) {
            $qsql= "REPLACE assessment_score SET coursesID = '$coursesID', questionID = '$i', score = '$Q[$a]', assessmentID = '$assessmentID'";
            $qresult = mysqli_query($con,$qsql);
            $a++;
            $i++;
        }

    }
    echo mysqli_error($con);
    header('location: assessment_subject_student.php?id='.$coursesID.'');

?>