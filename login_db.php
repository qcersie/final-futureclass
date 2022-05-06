<?php 

    include('dbconnect.php');

    if (isset($_POST['email'])) {

        $email = $_POST['email'];
        $password = $_POST['password'];
        //$passwordenc = md5($password);
        $passwordenc = $password;

        $query = "SELECT * FROM user WHERE userEmail = '$email' AND password = '$passwordenc'";

        $result = mysqli_query($con, $query);

        if (mysqli_num_rows($result) == 1) {

            // successfull login notification
            $_SESSION['success'] = "Successfully login";

            $row = mysqli_fetch_array($result);
            $_SESSION['userid'] = $row['id'];
            $_SESSION['userlevel'] = $row['role'];
            $_SESSION['email'] = $row['userEmail'];

            // query subjects from dteachers


            if ($_SESSION['userlevel'] == 'teacher') {
                $querysub = "SELECT * FROM teachers WHERE mail = '$email'";
                $resultsub = mysqli_query($con, $querysub);
                $rowsub = mysqli_fetch_array($resultsub);
                $_SESSION['fname'] = $rowsub['fname'];
                $_SESSION['lname'] = $rowsub['lname'];
                $_SESSION['abb'] = $rowsub['abbreviation'];
                $_SESSION['user'] = $rowsub['title'] . " " . $rowsub['fname'] . " " . $rowsub['lname'];

                $resultcourse = mysqli_query($con, "SELECT * FROM teacher_course WHERE teachersID = '$_SESSION[userid]'");
            }

            if ($_SESSION['userlevel'] == 'student') {
                $querysub = "SELECT * FROM students WHERE mail = '$email'";
                $resultsub = mysqli_query($con, $querysub);
                $rowsub = mysqli_fetch_array($resultsub);
                $_SESSION['fname'] = $rowsub['fname'];
                $_SESSION['lname'] = $rowsub['lname'];
                $_SESSION['user'] = $rowsub['title'] . " " . $rowsub['fname'] . " " . $rowsub['lname'];
                $_SESSION['sgroup'] = $rowsub['sgroup'];

                $resultcourse = mysqli_query($con, "SELECT * FROM student_course WHERE studentsID = '$_SESSION[userid]'");
            }

            if ($_SESSION['userlevel'] == 'admin') {
                $_SESSION['user'] = 'admin';

                $resultcourse = mysqli_query($con, "SELECT * FROM courses WHERE semesterID = 2");
            }
        
            $_SESSION['courses'] = array();
            $_SESSION['coursesnameEN'] = array();

            if (isset($resultcourse)) {  
                while ($rowcourse = mysqli_fetch_array($resultcourse)) {

                    $_SESSION['courses'][] = $rowcourse['coursesID'];

                    $querysub = "SELECT * FROM courses WHERE coursesID = '$rowcourse[coursesID]'";
                    $resultsub = mysqli_query($con, $querysub);
                    $rowsub = mysqli_fetch_array($resultsub);
                    $_SESSION['coursesnameEN'][] = $rowsub['nameEN'];
                }
            }

            $id = $_SESSION['userid'];
            $allcourses = $_SESSION['courses'];

            foreach ($allcourses as $course) {
                //$querynoti = "SELECT * FROM notifications WHERE coursesID = '$course' AND userID = '$id' AND type = 'assess'";
                //if (mysqli_num_rows(mysqli_query($con, $querynoti)) == 0) {
                    //$addnoti = "INSERT INTO notifications (id, coursesID, userID, name, type, requestmentID, message, status, date)
                                //VALUES (NULL, '$course', '$id', '', 'assess', '', '', 'unread', CURRENT_TIMESTAMP)";
                    //$con -> query($addnoti);
                //}
        
            }


            header("Location: home_".$_SESSION['userlevel'].".php");


        } else {
            // unsuccessfull login notification
            $_SESSION['error'] = "Wrong username or password";
            //echo $_SESSION['error'];

            //echo "<script>alert('User หรือ Password ไม่ถูกต้อง);</script>";

            header("Location: login.php");
        }

    } else {
        header("Location: login.php");
    }


?>