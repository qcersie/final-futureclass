<?php 

    include('dbconnect.php');

    $id = $_SESSION['userid'];
    $sql = "SELECT * FROM user WHERE id = $id";
    $data = mysqli_fetch_array(mysqli_query($con,$sql));
    $type = $data['type'];

    header("Content-type: $type");
    echo $data['file'];

?>