<?php

    include('dbconnect.php');

    $nid = $_GET['nid'];
    $cid = $_GET['id'];
    $ntype = $_GET['ntype'];

    if ($ntype == 'post' || $ntype == 'reply') {

        $reqid = $_GET['id'];

        $updatenoti = mysqli_query($con,"UPDATE notifications SET status='read' WHERE id=$nid");
        if ($_SESSION['userlevel'] == 'student') {
            header('location: request.php?id='.$reqid);
        }
        else if ($_SESSION['userlevel'] == 'teacher') {
            header('location: request_view.php?id='.$reqid);
        }
    } else if ($ntype == 'assess') {

        if ($_SESSION['userlevel'] == 'student') {
            header('location: assessment_subject_student.php?id='.$cid);
        }
        if ($_SESSION['userlevel'] == 'teacher') {
            $updatenoti = mysqli_query($con,"UPDATE notifications SET status='read' WHERE id=$nid");
            header('location: assessment_subject_teacher.php?id='.$cid);
        }
    } else if ($ntype == 'tassess') {

        if ($_SESSION['userlevel'] == 'student') {
            header('location: assessment_instructor.php?id='.$cid);
        }

    }

?>