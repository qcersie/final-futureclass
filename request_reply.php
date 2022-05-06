<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['email']);
        header('location: login.php');
    }
    
    $rid = $_GET['id'];
    $response = $_POST['ldl'];
    $email = $_SESSION['email'];
    
    $sql = "REPLACE requestment_response SET requestmentID='$rid',userEmail = '$email', response='$response'";
    $result = mysqli_query($con,$sql);
    
    if($result){
        $rl = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM requestment_response WHERE requestmentID=$rid AND response='like'"));
        $like = $rl[0];
        $rd = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM requestment_response WHERE requestmentID=$rid AND response='dislike'"));
        $dislike = $rd[0];
        $update = mysqli_query($con,"UPDATE requestment SET likeCount = $like, dislikeCount = $dislike WHERE requestmentID=$rid");
        header('location: request.php?id='.$rid.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }


?>
