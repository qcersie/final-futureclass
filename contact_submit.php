<?php

    include('dbconnect.php');

    if (isset($_GET['id'])) {
        $coursesID = intval($_GET['id']);
        echo $coursesID;
    }

    if ($_POST['c1'] != "") {
        $contact1 = $_POST['c1'];
        $sql = "UPDATE courses SET contact1 =  '$contact1' WHERE coursesID = $coursesID";
        $r1 = mysqli_query($con,$sql);
    }
    if ($_POST['uc1'] != "") {
        $contact1u = $_POST['uc1'];
        $sql = "UPDATE courses SET url1 =  '$contact1u' WHERE coursesID = $coursesID";
        $r2 = mysqli_query($con,$sql);
    }
    if ($_POST['c2'] != "") {
        $contact2 = $_POST['c2'];
        $sql = "UPDATE courses SET contact2 =  '$contact2' WHERE coursesID = $coursesID";
        $r3 = mysqli_query($con,$sql);
    }
    if ($_POST['uc2'] != "") {
        $contact2u = $_POST['uc2'];
        $sql = "UPDATE courses SET url2 =  '$contact2u' WHERE coursesID = $coursesID";
        $r4 = mysqli_query($con,$sql);
    }
    if ($_POST['c4'] != "") {
        $contact4 = $_POST['c4'];
        $sql = "UPDATE courses SET contact4 =  '$contact4' WHERE coursesID = $coursesID";
        $r5 = mysqli_query($con,$sql);
    }
    if ($_POST['uc4'] != "") {
        $contact4u = $_POST['uc4'];
        $sql = "UPDATE courses SET url4 =  '$contact4u' WHERE coursesID = $coursesID";
        $r6 = mysqli_query($con,$sql);
    }
    if ($_POST['c3'] != "") {
        $contact3 = $_POST['c3'];
        $sql = "UPDATE courses SET contact3 =  '$contact3' WHERE coursesID = $coursesID";
        $r7 = mysqli_query($con,$sql);
    }
    if ($_POST['uc3'] != "") {
        $contact3u = $_POST['uc3'];
        $sql = "UPDATE courses SET url3 =  '$contact3u' WHERE coursesID = $coursesID";
        $r8 = mysqli_query($con,$sql);
    }
    if ($_POST['c5'] != "") {
        $contact5 = $_POST['c5'];
        $sql = "UPDATE courses SET contact5 =  '$contact5' WHERE coursesID = $coursesID";
        $r9 = mysqli_query($con,$sql);
    }
    if ($_POST['uc5'] != "") {
        $contact5u = $_POST['uc5'];
        $sql = "UPDATE courses SET url5 =  '$contact5u' WHERE coursesID = $coursesID";
        $r0 = mysqli_query($con,$sql);
    }

    if (mysqli_error($con) != "") {
        echo mysqli_error($con);
        echo "<br>";
    } else if ($_SESSION['userlevel'] == "teacher") {
        ("location: contact.php?id=$courheadersesID");
    } else if ($_SESSION['userlevel'] == "admin") {
        ("location: contact_admin.php?id=$courheadersesID");
    }

?>