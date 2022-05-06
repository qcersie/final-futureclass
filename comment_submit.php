<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $comment=$_POST["comment"];
    if (isset($_POST["AnonymousComment"])){
        $ano = $_POST["AnonymousComment"];
    } else {
        $ano = 0;
    }
    $email = $_SESSION['email'];
    $reqID=$_GET['id'];


    $sql= "INSERT INTO requestment_reply SET reply='$comment',userEmail='$email',requestmentID='$reqID',anonymous=$ano,timestamp=(NOW());";

    $result = mysqli_query($con,$sql);

    $sqlc = "SELECT coursesID FROM requestment WHERE requestmentID=$reqID";
    $resultc =  mysqli_fetch_array(mysqli_query($con,$sqlc));
    $coursesID = $resultc[0];
    $sqlnoti = "SELECT studentsID FROM student_course WHERE coursesID=$coursesID";
    $studentlist = mysqli_query($con,$sqlnoti);
    $sqlnoti2 = "SELECT teachersID FROM teacher_course WHERE coursesID=$coursesID";
    $teacherlist = mysqli_query($con,$sqlnoti2);


    if($result){
        if($_SESSION['userlevel']=='student'){
            header('location: request.php?id='.$reqID.'');
        } else if($_SESSION['userlevel']=='teacher'){
            while ($a = mysqli_fetch_array($studentlist)) {
                $studentID = $a['studentsID'];
                $addnoti = mysqli_query($con,"INSERT INTO notifications (coursesID,userID,type,requestmentID,message,status,date)
                           VALUES ($coursesID,'$studentID','reply','$reqID','$comment','unread',NOW())");
            }    
            header('location: request_view.php?id='.$reqID.'');
        }
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>