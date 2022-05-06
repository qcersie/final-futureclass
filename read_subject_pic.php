<?php

    include('dbconnect.php');

    if (isset($_GET['id'])){
        $id = $_GET['id'];
    }

    if (isset($_GET['rid'])){
        $id = mysqli_fetch_array(mysqli_query($con,"SELECT coursesID FROM requestment WHERE requestmentID='$_GET[rid]'"))[0];
    }

    if (isset($_GET['qid'])){
        $id = mysqli_fetch_array(mysqli_query($con,"SELECT coursesID FROM courses_good WHERE ID='$_GET[qid]'"))[0];
    }

    if (isset($_GET['tqid'])){
        $id = mysqli_fetch_array(mysqli_query($con,"SELECT coursesID FROM teachers_good WHERE ID='$_GET[tqid]'"))[0];
    }

    $sql = "SELECT * FROM courses WHERE coursesID = $id";  
    $data = mysqli_fetch_array(mysqli_query($con,$sql));
    
    if ($data['picture'] != "") {
        $type = $data['pictype'];
        header("Content-type: $type");
        echo $data['picture'];
    } else {
        $sqld = "SELECT * FROM user WHERE id = 'default'";  
        $d = mysqli_fetch_array(mysqli_query($con,$sqld));
        echo $d['picture'];
    }

?>