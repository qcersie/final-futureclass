<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    $coursesID = $_GET['id'];
    $content = $_POST['content'];
    $regis = $_POST['regisall'];
    $sem = $_POST['sem'];
    $afile = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];

    if (isset($sem[0])) {
        if (isset($sem[1])) {
            if (isset($sem[2])) {
                $allsem = $sem[0].",".$sem[1].",".$sem[2];
            } else {
                $allsem = $sem[0].",".$sem[1];
            }
        } else $allsem = $sem[0]; 
        $sql = "UPDATE courses SET actionperiod = '$allsem'
                    WHERE coursesID = $coursesID";
        $result = mysqli_query($con,$sql);
    }

    if ($content != "") {
        $sql= "UPDATE courses SET content = '$content' 
                WHERE coursesID='$coursesID';";
        $result = mysqli_query($con,$sql);
    }
    
    if ($regis != "") {
        $sql= "UPDATE courses SET registotal = '$regis' 
                WHERE coursesID='$coursesID';";
        $result = mysqli_query($con,$sql);
    }

    if (isset($_POST['sec'])) {
        $sec = $_POST['sec'];
        $sql= "UPDATE courses SET sectiontype = '$sec' 
                WHERE coursesID='$coursesID';";
        $result = mysqli_query($con,$sql);
    }

    if ($_FILES['file']['error'] == '0') {
        $file = addslashes(file_get_contents($afile));
        $sql= "UPDATE courses SET picture='$file',pictype='$type' 
                WHERE coursesID='$coursesID';";
        $result = mysqli_query($con,$sql);
    } 


    if ($result) {
        if ($_SESSION['userlevel'] == "teacher") { 
            header('location: management.php?id='.$coursesID.'');
        } else if ($_SESSION['userlevel'] == "admin") { 
            header('location: management_admin.php?id='.$coursesID.'');
        }
    } else {
        echo mysqli_error($con);
        echo "<br>";
    }

?>