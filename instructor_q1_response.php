<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $qid = $_GET['id'];
    $coursesid = $_GET['cid'];    
    $teachersID = $_GET['tid'];
    $response = $_POST['radio'];
    $email = $_SESSION['email'];
    
    $sql = "REPLACE teachers_good_response SET qid='$qid',userEmail = '$email', response='$response',timestamp = NOW()";
    $result = mysqli_query($con,$sql);

    if($result){
        $rl = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM teachers_good_response WHERE qid=$qid AND response='like'"));
        $like = $rl[0];
        $rd = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM teachers_good_response WHERE qid=$qid AND response='dislike'"));
        $dislike = $rd[0];
        $update = mysqli_query($con,"UPDATE teachers_good SET likeCount = $like, dislikeCount = $dislike WHERE ID=$qid");
        header('location: instructor_q1.php?id='.$coursesid.'&tid='.$teachersID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>