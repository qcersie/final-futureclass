<?php

    include('dbconnect.php');

    if (isset($_GET['id'])){
        $id = $_GET['id'];
    }

    $sql = "SELECT * FROM user WHERE id = '$id'";  
    $data = mysqli_fetch_array(mysqli_query($con,$sql));
    
    if ($data['picture'] != "") {
        $type = $data['type'];
        header("Content-type: $type");
        echo $data['picture'];
    } else {
        $sqld = "SELECT * FROM user WHERE id = 'default'";  
        $d = mysqli_fetch_array(mysqli_query($con,$sqld));
        echo $d['picture'];
    }

?>