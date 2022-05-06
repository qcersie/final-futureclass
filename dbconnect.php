<?php

    $con = mysqli_connect("localhost","root","","learn") or die("error accured");

    session_start();

    //Set start and end date for assessment by mktime(hour, minute, second, month, day, year)
    $now = explode("-",date("Y-m-d",time()));
    if (intval($now[1]) > 0 && intval($now[1]) <= 5) {
        $semesterNOW = intval($now[0]-1)."/2";
    } else if (intval($now[1]) > 7 && intval($now[1]) <= 12) {
        $semesterNOW = $now[0]."/1";
    } else if (intval($now[1]) > 5 && intval($now[1]) <= 7) {
        $semesterNOW = $now[0]."/summmer";
    }

    $semester = mysqli_fetch_array(mysqli_query($con,"SELECT * FROM semester WHERE semesterName='$semesterNOW'"));
    $_SESSION['startassess'] = strtotime($semester['start']);
    $_SESSION['endassess'] = strtotime($semester['end']);
    $semID = $semester['semesterID'];

?>