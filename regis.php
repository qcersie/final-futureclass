<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $sid= $_POST["coursecode"];
    $password=$_POST["password"];
    $sec=$_POST["section"];
    $id = $_SESSION["userid"];

    $query = "SELECT * FROM courses WHERE subjectsID = '$sid' AND semesterID = $semID AND password = '$password'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) == 1) {
        $cid = mysqli_fetch_array($result)['coursesID'];
        /* NEW!!! check if the student is already in course */
        $qCheckStu = "SELECT COUNT(*) FROM student_course WHERE studentsID = '$id' AND coursesID = '$cid' AND section = '$sec' AND status = 1";
        $checkStu = mysqli_fetch_array(mysqli_query($con, $qCheckStu))[0];
        if ($checkStu == 0) {
            $addsub = mysqli_query($con, "REPLACE student_course SET studentsID = $id, coursesID = $cid, section = '$sec', status = 1");
            $check = mysqli_fetch_array(mysqli_query($con, "SELECT status FROM assessment_count WHERE coursesID='$cid' AND userEmail='$_SESSION[email]'"));
            if (!isset($check) || $check[0] != 1) {
                $addassess = mysqli_query($con, "REPLACE assessment_count SET coursesID='$cid',userEmail='$_SESSION[email]',semesterPeriod='test',status=0");
                /* NEW!!! long ass variables and loop for querying tassessment_count */
                $qTeacherInCourseAndSectionWhoActive =
                    "SELECT * FROM teacher_course
                    WHERE coursesID = '$cid' AND section = '$sec' AND status = 1";
                $rTeacherInCourseAndSectionWhoActive = mysqli_query($con, $qTeacherInCourseAndSectionWhoActive);
                if (mysqli_num_rows($rTeacherInCourseAndSectionWhoActive) > 0) {
                    while ($row = mysqli_fetch_array($rTeacherInCourseAndSectionWhoActive)) {
                        $addassess2 = mysqli_query($con, "REPLACE tassessment_count SET teachersID = '$row[teachersID]',userEmail = '$_SESSION[email]', status = 0, semesterPeriod = 'test', coursesID = '$cid'");
                    }
                }
                $addnoti = mysqli_query($con, "REPLACE notifications SET coursesID='$cid', userID='$id', name= '', type= 'assess', requestmentID= '', message= '', status= 'unread', date= CURRENT_TIMESTAMP");
                /* NEW!!! $addnoti2 */
                $addnoti2 = mysqli_query($con, "REPLACE notifications SET coursesID='$cid', userID='$id', name= '', type= 'tassess', requestmentID= '', message= '', status= 'unread', date= CURRENT_TIMESTAMP");
            }
        }
                header('location: home_student.php');
    } else {
        $_SESSION['msg'] = "Incorrect CoursesID or Password";
        echo "<br>";
        header('location: home_student.php');
    }

?>