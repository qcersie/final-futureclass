<?php

    include('dbconnect.php');
    
    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $coursesID = $_GET['id'];
    $teachersID = $_GET['tid'];
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

    /* NEW!!! INSERT IN -> UPDATE */

    //$sqlcheck= "INSERT IN tassessment_count (coursesID,teachersID,userEmail,semesterPeriod,status) VALUES ($coursesID,'$teachersID','$email','test','1')";
    $sql2 = "REPLACE tassessment_count SET coursesID=$coursesID,teachersID=$teachersID,semesterPeriod='test',status=1,userEmail='$email'";
    $resultcheck = mysqli_query($con,$sql2);

    if ($resultcheck) {
        $sql= "REPLACE tassessment SET coursesID=$coursesID,teachersID=$teachersID,semesterPeriod='test',timestamp=(NOW());";
        $result = mysqli_query($con,$sql);
        $tassessmentID = mysqli_insert_id($con);

        $b = 1;
        while ($b <8) {
            $qsql= "REPLACE tassessment_score SET tquestionsID='$b',score='$Q[$b]',tassessmentID='$tassessmentID'";
            $qresult = mysqli_query($con,$qsql);
            $b = $b +1;
        }
        header('location: assessment_instructor_student.php?id='.$coursesID.'&tid='.$teachersID.'');

    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>