<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $mail=$_SESSION["email"];
    $coursesID=$_GET['id'];
    $topic=$_POST["topic"];
    $details=$_POST["details"];
    if (isset($_POST['AnonymousRequest'])) {
        $ano = $_POST['AnonymousRequest'];
    } else {
        $ano = '0';
    }
    
    $afile = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];
    echo $_FILES['file']['error'];
    
    if ($_FILES['file']['error'] == '0') {
        $file = addslashes(file_get_contents($afile));
        $sql= "REPLACE requestment SET topic='$topic',content='$details',coursesID='$coursesID',file='$file',type='$type', userEmail='$mail', anonymous=$ano, status=0,timestamp=(NOW());";
        $result = mysqli_query($con,$sql);
        $reqID = $con->insert_id;
    } else {
        $sql= "REPLACE requestment SET topic='$topic',content='$details',coursesID='$coursesID', userEmail='$mail',anonymous=$ano, status=0,timestamp=(NOW());";
        $result = mysqli_query($con,$sql);
        $reqID = $con->insert_id;
    }

    $sqlc = "SELECT coursesID FROM requestment WHERE requestmentID=$reqID";
    $resultc =  mysqli_fetch_array(mysqli_query($con,$sqlc));
    $coursesID = $resultc[0];
    $sqlnoti = "SELECT studentsID FROM student_course WHERE coursesID=$coursesID AND status = 1";
    $studentlist = mysqli_query($con,$sqlnoti);
    $sqlnoti2 = "SELECT teachersID FROM teacher_course WHERE coursesID=$coursesID AND status = 1";
    $teacherlist = mysqli_query($con,$sqlnoti2);

    if ($coursesID == 8 || $coursesID == '2102999') {
            
        require_once 'config.php';
        require 'vendor/autoload.php';

        $queryteacher = "SELECT * FROM teacher_course WHERE coursesID = '$coursesID'";
        $resultsteacher = mysqli_query($con, $queryteacher);

        while ($rowteacher = mysqli_fetch_array($resultsteacher)) {

            $qmail = "SELECT * FROM teachers WHERE teachersID = '$rowteacher[teachersID]'";
            $rmail = mysqli_fetch_array(mysqli_query($con, $qmail));

            $email = new \SendGrid\Mail\Mail(); 
            $email->setFrom("6130602521@student.chula.ac.th", "Future Class"); // sender email
            $email->setSubject("FutureClass: There is a new request in [".$coursesID."]");
            $email->addTo($rmail['mail'], $rmail['title'].$rmail['fname']." ".$rmail['lname']); // receiver email

            //$email->addContent("text/plain", $topic);
            
            $email->addContent(
                "text/html", "To see more details and reply to the request<br><br>
                Please visit <a href='localhost/learn_220313/login_page.php'>FutureClass</a> and login<br><br>
                Go to the page of ".$coursesID." -> [Request] Menu -> ".$topic." -> [Add comment]"
            );

            $sendgrid = new \SendGrid(SENDGRID_API_KEY);

            try {
                $response = $sendgrid->send($email);
                print $response->statusCode() . "\n";
                print_r($response->headers());
                print $response->body() . "\n";
            } catch (Exception $e) {
                echo 'Caught exception: '. $e->getMessage() ."\n";
            }
        }
    }
    
    if($result){
        while ($a = mysqli_fetch_array($studentlist)) {
            $studentID = $a['studentsID'];
            $addnoti = mysqli_query($con,"INSERT INTO notifications (coursesID,userID,type,requestmentID,message,status,date)
                       VALUES ($coursesID,'$studentID','post','$reqID','$topic','unread',NOW())");
        }    
        while ($b = mysqli_fetch_array($teacherlist)) {
            $teacherID = $b['teachersID'];
            $addnoti = mysqli_query($con,"INSERT INTO notifications (coursesID,userID,type,requestmentID,message,status,date)
                       VALUES ($coursesID,'$teacherID','post','$reqID','$topic','unread',NOW())");
        }    
        //header('location: request_student.php?id='.$coursesID.'');
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }
    header("location: request_student.php?id=$coursesID");
?>