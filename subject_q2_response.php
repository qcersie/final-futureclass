<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $qid = $_GET['id'];
    $coursesid = $_GET['cid'];
    $response = $_POST['radio'];
    $email = $_SESSION['email'];
    
    $sql = "REPLACE courses_good_response SET qid='$qid',userEmail = '$email', response='$response'";
    $result = mysqli_query($con,$sql);

    if($result){
        $rl = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM courses_good_response WHERE qid=$qid AND response='like'"));
        $like = $rl[0];
        $rd = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM courses_good_response WHERE qid=$qid AND response='dislike'"));
        $dislike = $rd[0];
        $update = mysqli_query($con,"UPDATE courses_good SET likeCount = $like, dislikeCount = $dislike WHERE ID=$qid");
        header('location: subject_q2.php?id='.$coursesid.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>