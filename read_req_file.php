<?php 

    include('dbconnect.php');

    if (isset($_GET['id'])){
        $id = $_GET['id'];
    }
    
    $sql = "SELECT * FROM requestment WHERE requestmentID = $id";
    $data = mysqli_fetch_array(mysqli_query($con,$sql));
    
    if ($data['file'] != "") {
        $type = $data['type'];
        header("Content-type: $type");
        echo $data['file'];
    }

?>