<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }
    
    $id= $_SESSION["userid"];
    
    if (isset($_POST["name"]) && ($_POST["name"] != "")) {
        $name=$_POST["name"];
        $sql1 = mysqli_query($con,"UPDATE students SET fname='$name' WHERE studentsID = '$id'");   
        $rowsub = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM students WHERE studentsID = '$id'"));
        $_SESSION['user'] = $rowsub['fname'] . " " . $rowsub['lname']; 
    }
    
    if (isset($_POST["surname"]) && ($_POST["surname"] != "")) {
        $surname=$_POST["surname"];
        $sql2 = mysqli_query($con,"UPDATE students SET lname='$surname' WHERE studentsID = '$id'");
        $rowsub = mysqli_fetch_array(mysqli_query($con, "SELECT * FROM students WHERE studentsID = '$id'"));
        $_SESSION['user'] = $rowsub['fname'] . " " . $rowsub['lname']; 
    }

    if (isset($_POST["password"]) && isset($_POST["conpassword"]) && ($_POST["password"] != "")) {
        $pw = $_POST["password"];
        if ($_POST["password"] == $_POST["conpassword"]) {
            $sql3 = mysqli_query($con,"UPDATE user SET password='$pw' WHERE id = '$id'");
        } else {
            echo $_SESSION['error'] = "Password does not match!";
        }
    }

    $afile = $_FILES['file']['tmp_name'];
    $type = $_FILES['file']['type'];
    
    if ($_FILES['file']['error'] == '0') {
        $file = addslashes(file_get_contents($afile));
        $sql= mysqli_query($con,"UPDATE user SET picture='$file',type='$type' WHERE id = '$id'");
    } else if ($_FILES['file']['tmp_name'] != "") {
        $_SESSION['error'] = "Photo Upload Error";    
    }

    $querysub = "SELECT * FROM students WHERE studentsID = '$id'";
    $resultsub = mysqli_query($con, $querysub);
    $rowsub = mysqli_fetch_array($resultsub);
    $_SESSION['user'] = $rowsub['title']." ".$rowsub['fname'] . " " . $rowsub['lname'];
    $_SESSION['fname'] = $rowsub['fname'];
    $_SESSION['lname'] = $rowsub['lname'];
    if (isset($_SESSION['error'])) {
        header('location: profile_edit_student.php');
    } else {
        header('location: profile_student.php');
    }

?>