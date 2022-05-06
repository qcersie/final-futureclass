<?php

    include('dbconnect.php');

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['email']);
        header('location: login.php');
    }

    if ($_SESSION['userlevel'] != "student") {
        header('location: userlevel.php');
    }

    if (!isset($_SESSION['userid'])) {
        $_SESSION['msg'] = "User ID Error";
        header('location: error.php');
    }

    if (!isset( $_GET['id'])) {
        $_SESSION['msg'] = "Courses ID Error";
        header('location: error.php');;
    }
    
    $id = $_SESSION['userid'];
    $coursesID = $_GET['id'];
    $sql = "SELECT * FROM courses WHERE coursesID=$coursesID";
    $result = mysqli_fetch_array(mysqli_query($con,$sql));

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--ส่วนที่ดึง bootstrap มาใช้งาน-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <!-- ส่วนที่นำ logo มาใส่เมื่อเปิด tabใหม่-->
    <link rel="shortcut icon" href="./images/full-logo-b.png" type="image/x-icon">
    <!--เว็บที่ดึง icon มาใช้งาน-->
    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- **** -->
    <!-- แต่ง css ตามไฟล์ style.css -->
    <link rel="stylesheet" href="style.css">
    
    <!-- **** -->
    <title>Subject</title>
  
</head>

<body class="bg-light">

    <!-- ส่วนข้อมูลภายในเว็บ -->
    <div class="container-fluid bg-light">
        
        <!-- แถบเมนูหลักด้านบน -->
        <div class="row">
            <nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top" style="border-radius: 10px;">
                <div class="container-fluid" style="min-height:71px;">
                    <!-- ส่วน Logo learn -->
                    <a href="#" class="navbar-brand">
                        <img src="./images/full-logo-w.png"  alt="" width="35" height="35" 
                            style="margin-right: 15px; margin-top: 1px;">
                        <div class="d-inline-block" 
                        style="margin-top: 15px;" >
                        <p class=" d-none d-sm-block">Future class | EECU </p>
                        </div>
                    </a>
                    
                    <!--สร้างปุ่มที่เมื่อแสดงผลในจอขนาดเล็กจะรวมเมนูไว้ในปุ่มนึง-->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- ส่วนเมนูที่ติดขอบด้านขวา -->
                    <div class="collapse navbar-collapse mt-1" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <!--ส่วนปุ่มmycourse-->
                            <li class="nav-item" >
                                <a href="home_student.php" class="nav-link" 
                                style="font-family: 'Mitr', sans-serif"> 
                                MY COURSE</a>
                            </li>

                            <!--ส่วนปุ่มให้นิสิตเพิ่มรายวิชาได้ด้วยตนเอง-->
                             <li class="nav-item">
                                <button type="button" class="btn btn-dark position-relative" style="color:#949494;font-weight:bolder;font-family: 'Mitr', sans-serif;" data-bs-toggle="modal" data-bs-target="#regismodal">REGISTER</button>
                            </li>

                            <!-- ส่วนปุ่มแจ้งเตือน -->
                            <li class="nav-item" style="margin-right :20px">
                                <button type="button" class="btn btn-dark position-relative" id="liveToastBtn">
                                <span class="material-icons">notifications_active</span>
                                    <?php
                                        $querynotinum = "SELECT COUNT(*) AS numnoti FROM notifications WHERE (type = 'post' OR type = 'reply') AND userID = '$id' AND status = 'unread'";
                                        $resultnotinum = mysqli_fetch_array(mysqli_query($con, $querynotinum));
                                        $notinum = $resultnotinum['numnoti'];
                                        if (time() >= $_SESSION['startassess'] && time() <= $_SESSION['endassess']) {
                                            $haveAssessNoti = array();
                                            $qAssess = "SELECT * FROM notifications WHERE type = 'assess' AND userID = '$id' AND status = 'unread'";
                                            $rAssess = mysqli_query($con, $qAssess);
                                            if (mysqli_num_rows($rAssess) > 0) {
                                                while ($row = mysqli_fetch_array($rAssess)) {
                                                    $notinum++;
                                                    $haveAssessNoti[] = $row['coursesID'];
                                                }
                                            }
                                            $qTassess = "SELECT * FROM notifications WHERE type = 'tassess' AND userID = '$id' AND status = 'unread'";
                                            $rTassess = mysqli_query($con, $qTassess);
                                            if (mysqli_num_rows($rTassess) > 0) {
                                                while ($row = mysqli_fetch_array($rTassess)) {
                                                    if (!in_array($row['coursesID'], $haveAssessNoti)) {
                                                    $notinum++;
                                                    }
                                                }
                                            }
                                        }
                                    ?>
                                    <?php 
                                        if ($notinum != 0){
                                        echo "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'> $notinum </span>";
                                        } 
                                    ?>
                                </button>
                            </li>
                            <!--ส่วนแสดงรูปและชื่อผู้ใช้งาน -->
                            <li class="nav-item">
                                <div class="dropdown">
                                    <a href="#"
                                    class="d-flex align-items-center fw-light text-white text-decoration-none dropdown-toggle"
                                    id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; margin-bottom: 5px;">
                                    <!-- ดึงรูปจากฐานข้อมูลแสดงผลที่ img src="ที่เก็บไฟล์รูปต่างๆ"-->
                                    <img src="read_profile_pic.php"
                                        width="32" height="32" class="rounded-circle me-2">
                                    <!--ดึงชื่อผู้ใช้งานจากฐานข้อมูล ชื่อที่ถูกดึงอยู่ภายใต้ <strong> ชื่อนิสิต </strong> -->
                                    <strong> <?php echo $_SESSION['user']; ?> </strong>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;">
                                        <li><a class="dropdown-item" href="profile_student.php">Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="home_student.php?logout='1">Sign out</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>        
                    </div>
                </div>
            </nav>                   
        </div>

                                                    
        <!-- ส่วนข้อมูลภายในเว็บล่างเมนูหลักด้านบน -->
        <div class="row flex-nowrap w-100" style="margin-top: 5rem !important;">

            <!--ส่วนเมนูย่อยด้านข้าง เมื่อหน้าจอขนาดเล็กจะแสดงผลเพียง Icon รายการต่างๆ ขนาดเมนู col-md-2-->
            <div class="col col-xs-1 col-md-2 px-sm-2 px-0 bg-dark" style="border-radius: 20px; border-left: 5px solid rgb(161, 16,16); margin-top: 20px;">
                <ul class="nav flex-column sticky-top">
                     <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-2 min-vh-100 w-100">
                         <!-- แสดงชื่อวิชาที่ดึงข้อมูลจากฐานข้อมูล โดยชื่อวิชาจะแสดงภายใต้ <span class="fs-5 d-none d-md-inline"> ชื่อวิชา </span>-->
                         <a href="#" class="d-flex align-items-center pb-3 mb-md-0 me-md-auto text-white text-decoration-none">
                             <span class="fs-5 d-none d-md-inline" style="font-family: 'Mitr', sans-serif"><?php echo $result['nameEN']; ?></span>
                        </a>
                        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                            <!--แสดงรายละเอียดเมนูย่อยต่าง ๆ โดย <i class="cil-search"></i> คือการนำ icon ต่างๆมาแสดงหน้าชื่อเมนู -->
                            <li class="list active">
                                <a href="subject_student.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                     <span class="icon"><i class="cil-search"></i></span>
                                     <span class="title ms-1 d-none d-md-inline">About</span>
                                </a>
                            </li>
                            <li class="list">
                                <a href="request_student.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                    <span class="icon"><i class="cil-dinner"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Request</span></a>
                            </li>
                            <li class="list-A">
                                <a href="#" class="nav-link px-0 align-middle">
                                    <span class="icon"><i class="cil-chart"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Assessment</span>
                                </a>                
                            <li class="sub-list" style = "margin-left: 20px;">
                                <a href="assessment_subject_student.php?id=<?php echo($coursesID) ?>" class="nav-link px-0">
                                    <span class="icon"><i class="cil-spreadsheet"></i></span>
                                    <span class="title d-none d-md-inline">Subject</span></a>
                            </li>
                            <li class="sub-list" style = "margin-left: 20px;">
                                <a href="assessment_instructor.php?id=<?php echo($coursesID) ?>" class="nav-link px-0">
                                    <span class="icon"><i class="cil-people"></i></span>
                                    <span class="title d-none d-md-inline">Instructor</span></a>
                            </li>
                            <!-- <li class="list">
                                <a href="management.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                    <span class="icon"><i class="cil-settings"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Management</span>
                                </a>
                            </li> -->

                            <!--ส่วนออกจากรายวิชา-->
                            <li class="list">
                                <button type="button" class="btn position-relative" style="padding-left:0px;color: rgb(175, 53, 53);font-family: 'Mitr', sans-serif;" data-bs-toggle="modal" data-bs-target="#leavemodal">
                                    <span class="icon"><i class="cil-exit-to-app"></i></span>
                                    <span class="title d-none d-md-inline">Leave</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </ul>
            </div>

            <!-- ส่วนข้อมูลด้านข้างเมนูย่อย -->
            <div class="col-11 col-xs-11 col-md-10" style="margin-top: 20px;">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="assessment_subject_student.php?id=<?php echo $coursesID; ?>">คะแนน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="subject_q1.php?id=<?php echo $coursesID; ?>">ข้อดี</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="subject_q2.php?id=<?php echo $coursesID; ?>">ปัญหาที่พบ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="subject_q3.php?id=<?php echo $coursesID; ?>">ข้อเสนอแนะ</a>
                    </li>
                </ul>
                <?php 
                    $check = mysqli_fetch_array(mysqli_query($con,"SELECT status FROM assessment_count WHERE coursesID = $coursesID AND userEmail = '$_SESSION[email]'"));
                    if (time() < $_SESSION['startassess'] && time() > $_SESSION['endassess']) { ?>
                    <h3 class="mt-2">ระบบยังไม่เปิดให้ทำการประเมิน</h3>
                    <hr>
                <?php } else if ($check[0] == 1) {
                    $qteacher = "SELECT * FROM teacher_course WHERE coursesID = '$coursesID'";
                    $rteacher = mysqli_query($con, $qteacher);
                    $teacherInCourse = array();
                    if (mysqli_num_rows($rteacher) > 0) {
                        while ($row = mysqli_fetch_array($rteacher)) {
                            $teacherInCourse[] = $row['teachersID'];
                        }
                    }
                    $teacherEmail = array();
                    foreach ($teacherInCourse as $teacher) {
                        $qemailteacher = "SELECT mail FROM teachers WHERE teachersID = '$teacher'";
                        $remailteacher = mysqli_query($con, $qemailteacher);
                        if (mysqli_num_rows($remailteacher) > 0) {
                            $resultEmail = mysqli_fetch_array($remailteacher);
                            $emailTeacher = $resultEmail[0];
                        }
                        $teacherEmail[] = $emailTeacher;
                    }
                    $qrequest = "SELECT * FROM requestment WHERE coursesID = '$coursesID'";
                    $rrequest = mysqli_query($con, $qrequest);
                    $requestInCourse = array();
                    if (mysqli_num_rows($rrequest) > 0) {
                        while ($row = mysqli_fetch_array($rrequest)) {
                            $requestInCourse[] = array("requestmentID"=>$row['requestmentID'], "checkTeacherReply"=>0);
                        }
                    }
                    $n = 0;
                    $countTeacherReply = 0;
                    foreach ($requestInCourse as $request) {
                        $qreply = "SELECT * FROM requestment_reply WHERE requestmentID = '$request[requestmentID]'";
                        $rreply = mysqli_query($con, $qreply);
                        if (mysqli_num_rows($rreply) > 0) {
                            while ($row = mysqli_fetch_array($rreply)) {
                                if (in_array($row['userEmail'], $teacherEmail)) {
                                    $requestInCourse[$n]['checkTeacherReply'] = 1;
                                    $countTeacherReply ++;
                                    break 1;
                                }
                            }
                        }
                        $n++;
                    }
                
                    $query_assess_num = "SELECT * FROM assessment_count WHERE coursesID = '$coursesID' AND status = 1";
                    $results_assess_num = mysqli_query($con, $query_assess_num);
                    $assess_num = mysqli_num_rows($results_assess_num);
                
                    $query_req_num = "SELECT * FROM requestment WHERE coursesID = '$coursesID'";
                    $results_req_num = mysqli_query($con, $query_req_num);
                    $req_num = mysqli_num_rows($results_req_num);
                
                    /*
                    $query_question_num = "SELECT MAX(questionID) AS QuestionNum
                                            FROM assessment_score WHERE coursesID = '$coursesID'";
                    $results_question_num = mysqli_fetch_array(mysqli_query($con, $query_question_num));
                    $question_num = $results_question_num['QuestionNum'];
                    */
                
                    // force $question_num = 8 for subject assessment except for some courses *****
                    $question_num = 8;
                    if ($coursesID == 8) {
                        $question_num = 10;
                    } else if ($coursesID == 9) {
                        $question_num = 7;
                    }                
                
                    // query for pie chart
                    $query_pie = "SELECT
                    CASE
                    WHEN status = 1 THEN 'นิสิตที่ทำการประเมินแล้ว'
                    WHEN status = 0 THEN 'นิสิตที่ยังไม่ทำการประเมิน'
                    END AS status,
                    COUNT(userEmail) AS number
                    FROM assessment_count
                    WHERE coursesID = $coursesID
                    GROUP BY status;";
                    $results_pie = mysqli_query($con, $query_pie);
                    $pie_chart_data = array();
                    while ($result = mysqli_fetch_array($results_pie)) {
                    $pie_chart_data[] = array($result['status'], (int)$result['number']);
                    }
                    $pie_chart_data = json_encode($pie_chart_data);
                
                    // query for average scroe
                    $query_av_score = "SELECT questionID AS questionNo,
                                        AVG(score) AS score_average
                                        FROM assessment_score
                                        WHERE coursesID = '$coursesID'
                                        GROUP BY questionNo
                                        ORDER BY questionNo";
                    $resultsav = mysqli_query($con, $query_av_score);
                    $column_chart_dataav = array(array("Question No.", "คะแนนเฉลี่ย"));
                    /* ***** */
                    while ($result = mysqli_fetch_array($resultsav)) {
                        if ($coursesID == 8) {$column_chart_dataav[] = array("ข้อ ".($result['questionNo'] - 20), round($result['score_average'],2));}
                        else if ($coursesID == 9) {$column_chart_dataav[] = array("ข้อ ".($result['questionNo'] - 30), round($result['score_average'],2));}
                        else {$column_chart_dataav[] = array("ข้อ ".$result['questionNo'], round($result['score_average'],2));}
                    }
                    $column_chart_dataav = json_encode($column_chart_dataav);
                
                    $today = date("l jS \of F Y h:i A");
                
                echo<<<XYZ
                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                <script type="text/javascript">
                 
                    // Load the Visualization API and the charts package.
                    google.load('visualization', '1.0', {'packages':['corechart']});
                
                    // Set a callback to run when the Google Visualization API is loaded.
                    google.setOnLoadCallback(drawChart);
                
                    // Callback that creates and populates a data table,
                    // instantiates the pie chart, passes in the data and
                    // draws it.
                    function drawChart() {
                
                        // Create the data table.
                        var data = new google.visualization.DataTable();
                        data.addColumn('string', 'Age Range');
                        data.addColumn('number', 'Number');
                        data.addRows({$pie_chart_data});
                
                        // Set chart options
                        var options = {title:'',
                            titleTextStyle: {fontName: 'Lato', fontSize: 18, bold: true},
                                        height: 400,
                                        is3D: true,
                            colors:['#a11010','#d62322','#8DA9BF','#F2C38D','#E6AC03','#F09B35', '#D94308', '#013453'],
                            chartArea:{left:30,top:30,width:'100%',height:'80%'}};
                
                        // Instantiate and draw our chart, passing in some options.
                        var chart_div = document.getElementById('pie_chart_div');
                        var chart = new google.visualization.PieChart(chart_div);
                        
                        // Wait for the chart to finish drawing before calling the getIm geURI() method.
                        google.visualization.events.addListener(chart, 'ready', function ()      {
                        chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
                        });
                
                        chart.draw(data, options);
                    }
                
                    // Make the charts responsive
                    jQuery(document).ready(function(){
                    jQuery(window).resize(function(){
                        drawChart();
                    });
                    });
                 
                </script>
                
                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                <script type="text/javascript">
                
                    // Load the Visualization API and the charts package.
                    google.load('visualization', '1.0', {'packages':['corechart']});
                
                    // Column Chart
                    google.setOnLoadCallback(drawColumnChart);
                
                    function drawColumnChart() {
                        var data = google.visualization.arrayToDataTable({$column_chart_dataav});
                
                        var options = {
                        //title: 'Average score of each question',
                        //titleTextStyle: {fontName: 'Lato', fontSize: 18, bold: true},
                        hAxis: {textStyle: {fontName: 'TH Sarabun New', fontSize: 16}},
                        vAxis: {
                            textStyle: {fontName: 'TH Sarabun New', fontSize: 20},
                            viewWindow: {min: 0, max: 5}
                        },
                        height: 500,
                        chartArea:{left: 50, top: 50, bottom: 50, width: '100%', height: '85%'},
                        legend: { position: "none" },
                        colors:['#a11010','#C6D9AC']
                        };
                
                        // Instantiate and draw our chart, passing in some options.
                        var chart_div = document.getElementById('column_chart_divav');
                        var chart = new google.visualization.ColumnChart(chart_div);
                
                        // Wait for the chart to finish drawing before calling the getIm geURI() method.
                        google.visualization.events.addListener(chart, 'ready', function ()      {
                        chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
                        });
                
                        chart.draw(data, options);
                    }
                
                    // Make the charts responsive
                    jQuery(document).ready(function(){
                        jQuery(window).resize(function(){
                            drawColumnChart();
                        });
                    });
                
                </script>
                
                XYZ;
                
                /* ***** */
                if ($coursesID == 8) {$i = 21; $lastquestion = 30;} else if ($coursesID == 9) {$i = 31; $lastquestion = 37;} else {$i = 1; $lastquestion = 8;}
                $qnum = 1;
                while ($i <= $lastquestion) {
                
                    // query for bar chart(s)
                    $query_score_vote = "SELECT
                    SUM(CASE WHEN score = 1 THEN 1 ELSE 0 END) AS score1,
                    SUM(CASE WHEN score = 2 THEN 1 ELSE 0 END) AS score2,
                    SUM(CASE WHEN score = 3 THEN 1 ELSE 0 END) AS score3,
                    SUM(CASE WHEN score = 4 THEN 1 ELSE 0 END) AS score4,
                    SUM(CASE WHEN score = 5 THEN 1 ELSE 0 END) AS score5
                    FROM assessment_score WHERE coursesID = '$coursesID' AND questionID = ".$i;
                    $results_score_count = mysqli_query($con, $query_score_vote);
                    $resultscc = mysqli_fetch_array($results_score_count);
                    $column_chart_data = array(array("Score", "จำนวน"));
                    for ($j = 1; $j <= 5; $j++) {
                        $score = "score".$j;
                        $column_chart_data[] = array($j." คะแนน", round($resultscc[$score], 2));
                    }
                    $column_chart_data = json_encode($column_chart_data);
                
                
                echo<<<PQR
                <script type="text/javascript" src="https://www.google.com/jsapi"></script>
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                <script type="text/javascript">
                
                    // Load the Visualization API and the charts package.
                    google.load('visualization', '1.0', {'packages':['corechart']});
                
                    // Column Chart
                    google.setOnLoadCallback(drawColumnChart);
                
                    function drawColumnChart() {
                        var data = google.visualization.arrayToDataTable({$column_chart_data});
                
                        var options = {
                        title: 'ผลการประเมินจากคำถามข้อที่ $qnum',
                        titleTextStyle: {fontName: 'TH Sarabun New', fontSize: 36, bold: true},
                        hAxis: {textStyle: {fontName: 'TH Sarabun New', fontSize: 20}},
                        vAxis: {textStyle: {fontName: 'TH Sarabun New', fontSize: 20}},
                        height: 500,
                        chartArea:{left:50,top:50,width:'100%',height:'85%'},
                        legend: { position: "top" },
                        colors:['#a11010','#C6D9AC']
                        };
                
                        // Instantiate and draw our chart, passing in some options.
                        var chart_div = document.getElementById('column_chart_div' + $i);
                        var chart = new google.visualization.ColumnChart(chart_div);
                
                        // Wait for the chart to finish drawing before calling the getIm geURI() method.
                        google.visualization.events.addListener(chart, 'ready', function ()      {
                        chart_div.innerHTML = '<img src="' + chart.getImageURI() + '">';
                        });
                
                        chart.draw(data, options);
                    }
                
                    // Make the charts responsive
                    jQuery(document).ready(function(){
                        jQuery(window).resize(function(){
                            drawColumnChart();
                        });
                    });
                
                </script>
                
                PQR;
                $i++;
                $qnum++;
                }
                
                ?>
                    <h3 class="mt-2">ขอบคุณสำหรับการประเมิน</h3>
                    <hr>

                    <div class="row">
                        <div class="col-6 col-sm-6 col-md-6">
                        <div class="card" style="border-radius: 20px;">
                                <div class="card-body">
                                    <h5 class="card-title text-danger">คะแนนเฉลี่ยการประเมินแบบเลือกคำตอบ</h5>
                                    <dl class="row">
                                        <?php
                                            $query_av_score = "SELECT questionID AS questionNo,
                                            AVG(score) AS score_average
                                            FROM assessment_score
                                            WHERE coursesID = '$coursesID'
                                            GROUP BY questionNo
                                            ORDER BY questionNo";
                                            $resultsav = mysqli_query($con, $query_av_score);
                                            if (mysqli_num_rows($resultsav) > 0):
                                                while ($result = mysqli_fetch_array($resultsav)):
                                        ?>
                                        <!-- ***** -->
                                        <dt class="col-sm-3">ข้อที่ <?php if ($coursesID == 8) {echo $result['questionNo'] - 20;} else if ($coursesID == 9) {echo $result['questionNo'] - 30;} else {echo $result['questionNo'];} ?></dt>
                                        <dd class="col-sm-9">&nbsp;&nbsp;<span><?php echo round($result['score_average'], 2); ?> คะแนน</span></dd>
                                        <?php
                                                endwhile;
                                            else:
                                                for ($x = 1; $x <= $question_num; $x++):
                                        ?>
                                        <dt class="col-sm-3">ข้อที่ <?php echo $x; ?></dt>
                                        <dd class="col-sm-9">&nbsp;&nbsp;<span>- คะแนน</span></dd>
                                        <?php
                                                endfor;
                                            endif;
                                        ?>
                                    </dl>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 col-md-6 col-sm-6">
                            <h5 style="margin-top: 20px;">คะแนนการประเมินรายข้อ</h5>

                            <?php
                                if ($coursesID == 8) {
                                    $query_question = "SELECT * FROM questions WHERE questionsID >= 21 AND questionsID <= 30 ORDER BY questionsID";
                                } else if ($coursesID == 9) {
                                    $query_question = "SELECT * FROM questions WHERE questionsID >= 31 AND questionsID <= 37 ORDER BY questionsID";
                                } else {
                                    $query_question = "SELECT * FROM questions WHERE questionsID <= '$question_num' ORDER BY questionsID";
                                }
                                $results_question = mysqli_query($con, $query_question);
                                while ($result = mysqli_fetch_array($results_question)):
                            ?>
                            <div class="card" style="border-radius: 20px;">

                                <div class="card-body">
                                    <h5 class="card-title text-danger">ข้อ <?php if ($coursesID == 8) {echo ($result['questionsID'] - 20).". ".$result['content'];} else if ($coursesID == 9) {echo ($result['questionsID'] - 30).". ".$result['content'];} else {echo $result['questionsID'].". ".$result['content'];} ?></h5>
                                    <dd><?php echo $result['caption']; ?></dd>
                                    <!--ปุ่มเพื่อเปิด modal แสดงคะแนนรายข้อแบบละเอียด -->
                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#staticBackdrop<?php echo $result['questionsID']; ?>" style="border-radius: 20px;">
                                            More <i class="cil-chart"></i>
                                        </button>
                                    </div>

                                    <!-- modal รายละเอียดคะแนนรายข้อ -->
                                    <div class="modal fade" id="staticBackdrop<?php echo $result['questionsID']; ?>" data-bs-backdrop="static"
                                        data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="staticBackdropLabel">
                                                        ข้อ <?php if ($coursesID == 8) {echo ($result['questionsID'] - 20).". ".$result['content'];} else if ($coursesID == 9) { echo ($result['questionsID'] - 30).". ".$result['content']; }  else {echo $result['questionsID'].". ".$result['content'];} ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <?php
                                                        $query_score = "SELECT
                                                        SUM(CASE WHEN score = 1 THEN 1 ELSE 0 END) AS score1,
                                                        SUM(CASE WHEN score = 2 THEN 1 ELSE 0 END) AS score2,
                                                        SUM(CASE WHEN score = 3 THEN 1 ELSE 0 END) AS score3,
                                                        SUM(CASE WHEN score = 4 THEN 1 ELSE 0 END) AS score4,
                                                        SUM(CASE WHEN score = 5 THEN 1 ELSE 0 END) AS score5
                                                        FROM assessment_score
                                                        WHERE coursesID = '$coursesID' AND questionID = '$result[questionsID]'
                                                        ORDER BY questionID";
                                                        $results_score = mysqli_query($con, $query_score);
                                                        $resultsc = mysqli_fetch_array($results_score);
                                                        $maxvote = max($resultsc['score1'], $resultsc['score2'], $resultsc['score3'], $resultsc['score4'], $resultsc['score5']);

                                                        for ($i = 1; $i <= 5; $i++):
                                                    ?>
                                                    <dd><?php echo $i; ?> คะแนน</dd>
                                                    <dd>
                                                        <div class="row">
                                                            <div class="col-8">
                                                                <div class="progress">
                                                                    <div class="progress-bar bg-danger" role="progressbar"
                                                                        style="width: <?php $score = "score".$i; echo $resultsc[$score]/$maxvote*100; ?>%;"></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-4"><?php echo $resultsc[$score]; ?> people</div>
                                                        </div>
                                                    </dd>
                                                    <?php
                                                        endfor;
                                                    ?>
                                        
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                        

                    <?php } else if($coursesID == 8) { ?>
                    <h3 class="mt-2">การประเมินแบบเลือกคะแนน</h3>
                    <p style="color:#6e6e6e">[ เป็นการประเมินช่วง2สัปดาห์สุดท้ายก่อน/หลังจบภาคเรียน โดยนิสิตสามารถทำการประเมินได้เพียง1ครั้ง ]</p>
                    <hr>
                    <form action="assessment_subject_submit.php?id=<?php echo $coursesID; ?>" method="POST">
                        <!--คำถามที่1แบบเลือกคะเเนน-->

                        <label class="form-label"style="margin-bottom:0px;">1.การใช้งานเข้าเว็บไซต์เข้าใจง่ายหรือไม่ </label><br>
                        <label class="form-text" style="margin-top:0px;">1 คะแนน หมายถึง ไม่เข้าใจเลย 5 คะแนน หมายถึง เข้าใจง่ายที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่2แบบเลือกคะเเนน-->
                        <label class="form-label">2.หารายวิชาที่ลงทะเบียนเรียนในเทอมสะดวกหรือไม่</label><br>
                        <!-- <label class="form-text">1 คะแนน หมายถึง ไม่สะดวก 5 คะแนน หมายถึง สะดวก</label> -->
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-1" value="5" required>
                                <label class="form-check-label" for="inlineRadio1">สะดวก</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-5" value="1">
                                <label class="form-check-label" for="inlineRadio3">ไม่สะดวก</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่3แบบเลือกคะเเนน-->
                        <label class="form-label">3.หากมีการใช้งานเว็บไซต์ future class นิสิตจะใช้งานเป็นประจำหรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข 1 หมายถึงไม่ใช้เลย หมายเลข 5 หมายถึงใช้งานสม่ำเสมอ</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่4แบบเลือกคะเเนน-->
                        <label class="form-label">4.หน้าตาของเว็บไซต์มีความสวยงามระดับใด</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่สวยงามเลย หมายเลข 5
                            หมายถึงสวยงามที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่5แบบเลือกคะเเนน-->
                        <label class="form-label">5.นิสิตทำประเมินในระบบ CUCAS ครบหรือไม่</label><br>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">ไม่เคยประเมินเลย</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">ประเมินบางครั้ง</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">ประเมินทุกเทอม</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่6แบบเลือกคะเเนน-->
                        <label class="form-label">6.นิสิตคิดว่าการใช้เว็บไซต์ Future class จะเพิ่มประสิทธิภาพในการเรียน การสอนหรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึง ไม่เพิ่มเลย หมายเลข 5 หมายถึง เพิ่มมากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่7แบบเลือกคะเเนน-->
                        <label class="form-label">7.นิสิตคิดว่าเว็บไซต์ Future class สามารถใช้ทดแทน application ในการติดต่อสื่อสารอื่นๆ ระหว่างอาจารย์และนิสิต เช่น line หรือไม่</label><br>
                        
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">ไม่สามารถทดแทนได้</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">ทดแทนได้บางส่วน</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">สามารถทดแทนได้</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่8แบบเลือกคะเเนน-->
                        <label class="form-label">8.นิสิตคิดว่าระบบreport จะสามารถป้องกันนิสิตที่ต้องการก่อกวนได้หรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่สามารถป้องกันได้ หมายเลข 5 หมายถึง ป้องกันได้มากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-1" value="1">
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-3" value="3" required>
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                         <!--คำถามที่9แบบเลือกคะเเนน-->
                         <label class="form-label">9.ระบบนี้ทำให้นิสิตกล้าถามคำถามมากขึ้นหรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึง น้อยที่สุด หมายเลข 5 หมายถึง มากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q9" id="input-Q9-1" value="1">
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q9" id="input-Q9-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q9" id="input-Q9-3" value="3" required>
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q9" id="input-Q9-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q9" id="input-Q9-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                         <!--คำถามที่10แบบเลือกคะเเนน-->
                         <label class="form-label">10.การแสดงความเห็นแบบ Anonymous จะทำให้นิสิตแสดงความเห็นแบบตรงไปตรงมาหรือไม่</label><br>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q10" id="input-Q10-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">ใช่</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q10" id="input-Q10-2" value="0">
                                <label class="form-check-label" for="inlineRadio2">ไม่</label>
                            </div>
                        </div>
                        <hr>
                        <!--ส่งข้อมูล-->
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </form>
                <?php } else if($coursesID == 9) { ?>
                    <h3 class="mt-2">การประเมินแบบเลือกคะแนน</h3>
                    <hr>
                    <form action="assessment_subject_submit.php?id=<?php echo $coursesID; ?>" method="POST">
                        <!--คำถามที่1แบบเลือกคะเเนน-->

                        <label class="form-label">1.การใช้งานเข้าเว็บไซต์เข้าใจง่ายหรือไม่ </label><br>
                        <label class="form-text">1 คะแนน หมายถึง ไม่เข้าใจเลย 5 คะแนน หมายถึง เข้าใจง่ายที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่2แบบเลือกคะเเนน-->
                        <label class="form-label">2.หากมีการใช้งานเว็บไซต์ future class อาจารย์จะใช้งานเป็นประจำหรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข 1 หมายถึงไม่ใช้เลย หมายเลข 5 หมายถึงใช้งานสม่ำเสมอ</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่4แบบเลือกคะเเนน-->
                        <label class="form-label">3.หน้าตาของเว็บไซต์มีความสวยงามระดับใด</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่สวยงามเลย หมายเลข 5 หมายถึงสวยงามที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่4แบบเลือกคะเเนน-->
                        <label class="form-label">4.สรุปคะแนนต่างๆในระบบเข้าใจง่ายหรือไม่ </label><br>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">เข้าใจง่าย</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">เข้าใจยาก</label>
                            </div>
                        </div>
                        <hr>
                    
                        <!--คำถามที่6แบบเลือกคะเเนน-->
                        <label class="form-label">5.อาจารย์คิดว่าการใช้เว็บไซต์ Future class จะเป็นเครื่องมือที่ป้อนกลับการเรียนการสอนได้เร็วกว่าและดีกว่าระบบ CU-CAS หรือไม่</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึง ไม่เพิ่มเลย หมายเลข 5 หมายถึง เพิ่มมากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่7แบบเลือกคะเเนน-->
                        <label class="form-label">6.อาจารย์คิดว่าเว็บไซต์ Future class สามารถใช้ทดแทน application ในการติดต่อสื่อสารอื่นๆ ระหว่างอาจารย์และนิสิต เช่น line หรือไม่</label><br>
                        
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">ไม่สามารถทดแทนได้</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">ทดแทนได้บางส่วน</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">สามารถทดแทนได้</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่8แบบเลือกคะเเนน-->
                        <label class="form-label">7.อาจารย์คิดว่าระบบช่วยให้มองเห็นภาพรวมของวิชามากขึ้นว่านิสิตส่วนมากมีความเห็นอย่างไรต่อวิชา</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่ช่วยเลย หมายเลข 5 หมายถึง ช่วยมากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-1" value="1">
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--ส่งข้อมูล-->
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </form>
                <?php } else { ?>
                    <br>
                    <h3>การประเมินแบบเลือกคะแนน</h3>
                    <hr>
                    <form action="assessment_subject_submit.php?id=<?php echo $coursesID; ?>" method="POST">
                        <!--คำถามที่1แบบเลือกคะเเนน-->

                        <label class="form-label">1.ความเหมาะสมของปริมาณเนื้อหาต่อเวลาเรียน</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่เหมาะสมที่สุด หมายเลข 5
                            หมายถึงเหมาะสมที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q1" id="input-Q1-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่2แบบเลือกคะเเนน-->
                        <label class="form-label">2.เนื้อหาภายในห้องเรียนตรงกับที่ระบุไว้ในประมวลรายวิชา</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่เหมาะสมที่สุด หมายเลข 5
                            หมายถึงเหมาะสมที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q2" id="input-Q2-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่3แบบเลือกคะเเนน-->
                        <label class="form-label">3.ความยากง่ายของเนื้อหา</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงง่ายที่สุด หมายเลข 5 หมายถึงยากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q3" id="input-Q3-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่4แบบเลือกคะเเนน-->
                        <label class="form-label">4.ความเหมาะสมของปริมาณงานต่อเวลาที่กำหนด</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่เหมาะสมที่สุด หมายเลข 5
                            หมายถึงเหมาะสมที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q4" id="input-Q4-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่5แบบเลือกคะเเนน-->
                        <label class="form-label">5.ความเหมาะสมของรูปแบบการเรียนการสอน</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงไม่เหมาะสมที่สุด หมายเลข 5
                            หมายถึงเหมาะสมที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q5" id="input-Q5-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่6แบบเลือกคะเเนน-->
                        <label class="form-label">6.ความยากง่ายของข้อสอบ</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึง ง่ายที่สุด หมายเลข 5 หมายถึง ยากที่สุด</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q6" id="input-Q6-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--คำถามที่7แบบเลือกคะเเนน-->
                        <label class="form-label">7.จำนวนครั้งในการสอบ</label><br>

                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-1" value="1" required>
                                <label class="form-check-label" for="inlineRadio1">ไม่มีการสอบเลย</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">สอบน้อยเกินไป</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-3" value="3">
                                <label class="form-check-label" for="inlineRadio3">เหมาะสม</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">มาก</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q7" id="input-Q7-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">มากเกินไป</label>
                            </div>
                        </div>
                        <hr>

                        <!--คำถามที่8แบบเลือกคะเเนน-->
                        <label class="form-label">8.ความเข้าใจในเนื้อหาที่เรียนและสามารถนำไปประยุกต์ใช้ได้</label><br>
                        <label class="form-text">โดยหมายเลข1 หมายถึงน้อย หมายเลข 5 หมายถึง มาก</label>
                        <div class="form-check">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-1" value="1">
                                <label class="form-check-label" for="inlineRadio1">1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-2" value="2">
                                <label class="form-check-label" for="inlineRadio2">2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-3" value="3" required>
                                <label class="form-check-label" for="inlineRadio3">3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-4" value="4">
                                <label class="form-check-label" for="inlineRadio3">4</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="Q8" id="input-Q8-5" value="5">
                                <label class="form-check-label" for="inlineRadio3">5</label>
                            </div>
                        </div>
                        <hr>
                        <!--ส่งข้อมูล-->
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </form>
                <?php } ?>
                    
            <!-- จบ : ส่วนข้อมูลด้านข้างเมนูย่อย -->
            </div>
        
        <!-- จบ : ส่วนข้อมูลภายในเว็บล่างเมนูหลักด้านบน -->
        </div>
    <!-- จบ : ส่วนข้อมูลภายในเว็บ -->
    </div>

    <script>
        <?php if ($notinum != 0) :?>
        var toastTrigger = document.getElementById('liveToastBtn')
        var toastLiveExample = document.getElementById('liveToast')

        if (toastTrigger) {
        toastTrigger.addEventListener('click', function () {
            //var toast = new bootstrap.Toast(toastLiveExample)
            var toastElements = document.querySelectorAll('.toast')
            for (var i = 0; i < toastElements.length; i++) {
                new bootstrap.Toast(toastElements[i]).show();
            }
            //toast.show()
        })
        }
        <?php endif; ?>
    </script>
    
    <!-- **** -->
    <!-- รันแจ้งเตือน -->
    <?php
    if($notinum > 0) { ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11"data-bs-autohide="false">
        <div aria-live="assertive" aria-atomic="true" class="position-relative">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive"
                aria-atomic="true" >
                <button type="button" class="btn-close ms-2 mb-1 position-absolute end-0" 
                    style="background-color: rgb(219, 219, 219);" data-bs-dismiss="toast"
                    aria-label="close"></button>

                <!-- แจ้งเตือนนิสิตให้ทำการประเมิน -->
                <?php
                    if ($_SESSION['userlevel'] == 'student'):
                        if (time() >= $_SESSION['startassess'] && time() < $_SESSION['endassess']):

                            $querynoti = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND type = 'assess'";
                            $resultsnoti = mysqli_query($con, $querynoti);

                            if (mysqli_num_rows($resultsnoti) > 0) {
                                while ($row = mysqli_fetch_array($resultsnoti)) {
                                    if ($row['status'] == 'unread') {
                                        $queryassessstatus = "SELECT * FROM assessment_count WHERE userEmail = '$_SESSION[email]' AND coursesID = '$row[coursesID]'";
                                        $resultassessstatus = mysqli_fetch_array(mysqli_query($con, $queryassessstatus));
                                        if ($resultassessstatus['status'] == 1) {
                                            $updatenoti = mysqli_query($con, "UPDATE notifications SET status = 'read'
                                            WHERE userID = '$_SESSION[userid]' AND coursesID = '$row[coursesID]' AND type = 'assess'");
                                        }
                                    }
                                }
                            }

                            $querynoti = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND type = 'tassess'";
                            $resultsnoti = mysqli_query($con, $querynoti);

                            if (mysqli_num_rows($resultsnoti) > 0) {
                                while ($row = mysqli_fetch_array($resultsnoti)) {
                                    if ($row['status'] == 'unread') {
                                        $queryUndoneTassess = "SELECT COUNT(*) FROM tassessment_count WHERE userEmail = '$_SESSION[email]' AND coursesID = '$row[coursesID]' AND status = 0";
                                        $undoneTassess = mysqli_fetch_array(mysqli_query($con, $queryUndoneTassess))[0];
                                        if ($undoneTassess == 0) {
                                            $updatenoti = mysqli_query($con, "UPDATE notifications SET status = 'read'
                                            WHERE userID = '$_SESSION[userid]' AND coursesID = '$row[coursesID]' AND type = 'tassess'");
                                        }
                                    }
                                }
                            }
                            
                            $querynoti = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND status = 'unread' AND type = 'assess'";
                            $resultsnoti = mysqli_query($con, $querynoti);

                            if (mysqli_num_rows($resultsnoti) > 0):
                                while ($row = mysqli_fetch_array($resultsnoti)):
                ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href=<?php
                                $nid = $row['id'];
                                $cid = $row['coursesID'];
                                $ntype = $row['type'];
                                echo "notification_link3.php?id=$cid&nid=$nid&ntype=$ntype";
                            ?>
                        style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(255, 215, 0)">
                            <img src="read_subject_pic.php?id=<?php echo $row['coursesID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: black;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID = '$row[coursesID]'";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                            <small style="color: black;">
                                <?php
                                    $enddate = date("jS F h:i A", $_SESSION['endassess']);
                                    echo "Until ".$enddate;
                                ?>
                            </small>
                        </div>
                        <?php
                            $queryTassessNoti = "SELECT COUNT(*) FROM notifications WHERE userID = '$_SESSION[userid]' AND coursesID = '$row[coursesID]' AND type = 'tassess' AND status = 'unread'";
                            $unreadTassessNoti = mysqli_fetch_array(mysqli_query($con, $queryTassessNoti))[0];
                            if ($unreadTassessNoti > 0):
                        ?>
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            Please do Subject and Instructor Assessment
                        </div>
                        <?php
                            else:
                        ?>
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            Please do Subject Assessment
                        </div>
                        <?php
                            endif;
                        ?>
                    </a>
                </div>
                <?php
                                endwhile;
                            endif;
                                $querynotiTassess = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND status = 'unread' AND type = 'tassess'";
                                $resultsnotiTassess = mysqli_query($con, $querynotiTassess);
                                if (mysqli_num_rows($resultsnotiTassess) > 0):
                                    while ($row = mysqli_fetch_array($resultsnotiTassess)):
                                        $querynotiassess = "SELECT COUNT(*) FROM notifications WHERE userID = '$_SESSION[userid]' AND coursesID = '$row[coursesID]' AND status = 'unread' AND type = 'assess'";
                                        $notiassess = mysqli_fetch_array(mysqli_query($con, $querynotiassess))[0];    
                                        if ($notiassess == 0):            
                ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href=<?php
                                $nid = $row['id'];
                                $cid = $row['coursesID'];
                                $ntype = $row['type'];
                                echo "notification_link3.php?id=$cid&nid=$nid&ntype=$ntype";
                            ?>
                        style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(255, 215, 0)">
                            <img src="read_subject_pic.php?id=<?php echo $row['coursesID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: black;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID = '$row[coursesID]'";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                            <small style="color: black;">
                                <?php
                                    $enddate = date("jS F h:i A", $_SESSION['endassess']);
                                    echo "Until ".$enddate;
                                ?>
                            </small>
                        </div>
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            Please do Instructor Assessment
                        </div>
                    </a>
                </div>
                <?php
                                    endif;
                                endwhile;
                            endif;
                        endif;
                    endif;
                ?>

                <!-- แจ้งเตือนอาจารย์เมื่อหมดช่วงประเมิน และมีการสรุปผลแล้ว -->
                <?php
                    if ($_SESSION['userlevel'] == 'teacher'):
                        if (time() >= $_SESSION['endassess']):
                            $querynoti = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND type = 'assess' AND status = 'unread'";
                            $resultsnoti = mysqli_query($con, $querynoti);

                            if (mysqli_num_rows($resultsnoti) > 0):
                                while ($rownoti = mysqli_fetch_array($resultsnoti)):
                ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href="assess_instructor_teacher.php
                        ?cid=<?php echo array_search($rownoti['coursesID'], $_SESSION['courses']); ?>
                        &nid=<?php echo $rownoti['id']; ?>&read=1"
                        style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(255, 215, 0)">
                            <img src="read_subject_pic.php?id=<?php echo $row['coursesID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: black;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID = '$rownoti[coursesID]'";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                        </div>
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            Summary of instructor assessment is ready
                        </div>
                    </a>
                </div>
                <?php
                                endwhile;
                            endif;
                        endif;
                    endif;
                ?>

                    <!-- แจ้งเตือนเรื่องการโพส request ถึงอาจารย์และนิสิตในวิชา และแจ้งเตือนการตอบกลับโดยแจ้งเตือนถึงนิสิตในวิชา เฉพาะการตอบกลับจากอาจารย์เท่านั้น) -->
                <?php
                    $query = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND (type = 'post' OR type = 'reply') ORDER BY date DESC";
                    $results = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_array($results)):
                ?>

                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href=<?php
                                $nid = $row['id'];
                                $reqid = $row['requestmentID'];
                                $ntype = $row['type'];
                                echo "notification_link3.php?id=$reqid&nid=$nid&ntype=$ntype";
                            ?>
                        style="text-decoration: none;"> 
                        <div class="toast-header" style="border-radius: 10px;background-color:
                            <?php if ($row['status'] == 'unread') {
                                if ($_SESSION['userlevel'] == 'teacher') {
                                    echo "rgb(161, 16,16);";
                                }
                                elseif ($_SESSION['userlevel'] == 'student') {
                                    echo "rgb(161, 16,16);";
                                }
                            } elseif ($row['status'] == 'read') {
                                echo "grey;";
                            }
                            ?>
                            ">
                            <img src="read_subject_pic.php?id=<?php echo $row['coursesID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="
                                <?php
                                if ($row['status'] == 'unread') {
                                    echo "font-weight:bold;";
                                }
                                ?>
                                color: whitesmoke;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID = '$row[coursesID]'";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                            <small style="color: whitesmoke;">
                                <?php
                                //echo date('F j, Y, g:i a',strtotime("now"));
                                $pasttime = time() - strtotime($row['date']);
                                if ($pasttime >= 60*60*24*547.5) {
                                    $pt = round(($pasttime)/60/60/24/365);
                                    echo $pt." years ago";
                                } elseif ($pasttime >= 60*60*24*365) {
                                    echo "a year ago";
                                } elseif ($pasttime >= 60*60*24*45) {
                                    $pt = round(($pasttime)/60/60/24/30);
                                    echo $pt." months ago";
                                } elseif ($pasttime >= 60*60*24*30) {
                                    echo "a month ago";
                                } elseif ($pasttime >= 60*60*24*10.5) {
                                    $pt = round(($pasttime)/60/60/24/7);
                                    echo $pt." weeks ago";
                                } elseif ($pasttime >= 60*60*24*7) {
                                    echo "a week ago";
                                } elseif ($pasttime >= 60*60*36) {
                                    $pt = round(($pasttime)/60/60/24);
                                    echo $pt." days ago";
                                } elseif ($pasttime >= 60*60*24) {
                                    echo "a day ago";
                                } elseif ($pasttime >= 60*90) {
                                    $pt = round(($pasttime)/60/60);
                                    echo $pt." hours ago";
                                } elseif ($pasttime >= 60*60) {
                                    echo "1 hour ago";
                                } elseif ($pasttime >= 90) {
                                    $pt = round(($pasttime)/60);
                                    echo $pt." mins ago";
                                } elseif ($pasttime >= 60) {
                                    echo "1 min ago";
                                } else {
                                    echo "a moment ago";
                                }
                                ?>
                            </small>
                        </div>
                        <div class="toast-body" style="
                            <?php
                            if ($row['status'] == 'unread') {
                                echo "font-weight:bold;";
                            }
                            ?>
                            color: black;">
                            <?php
                                if ($row['type'] == 'post') {
                                    echo "There's a new request: ".$row['message'];
                                } elseif ($row['type'] == 'reply' && $_SESSION['userlevel'] == 'student') {
                                    $sql = "SELECT * FROM requestment WHERE requestmentID = $row[requestmentID]";
                                    $result = mysqli_fetch_array(mysqli_query($con, $sql));
                                    $req = $result['topic'];
                                    echo "Your teacher has replied to this request: ".$req;
                                }
                            ?>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php } ?>




    <!-- จบ : รันแจ้งเตือน -->
    <!-- modal การเพิ่มรายวิชาด้วยตนเอง-->
    <div class="modal fade" id="regismodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">REGISTER</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--เชื่อมกับphpในการรับข้อมูลต่างๆ-->
                        <form action="regis.php">
                            <div class="mb-4">
                                <label for="coursecode" class="form-label">Course Number</label>
                                <div class="decinput">
                                    <input type="text" class="form-control" id="coursecode" name="coursecode" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="coursepassword" class="form-label">Course Password</label>
                                <div class="decinput">
                                    <input type="text" class="form-control" id="coursepassword" name="password" required>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="coursesec" class="form-label">Course Section</label>
                                <div class="decinput">
                                    <input type="text" class="form-control" id="coursesec" name="section" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2 col-6 mx-auto">
                                <button type="submit" class="btn btn-danger">Submit</button>
                            </div>
                        </form>
                </div>
            </div>
        </div>
    </div>
    <!-- จบ : modal การเพิ่มรายวิชาด้วยตนเอง-->

    <!-- modal เพื่อ leave ออกจากรายวิชา-->
    <div class="modal fade" id="leavemodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Leave</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--เชื่อมกับphpในการรับข้อมูลต่างๆ-->
                            <div class="mb-4">
                                <label for="coursecode" class="form-label">ต้องการยืนยันที่จะออกจากรายวิชานี้เนื่องจากท่านได้ลด/ถอนรายวิชาจากระบบแล้ว</label>
                            </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a type="button" class="btn btn-danger" href="withdraw.php?id=<?php echo $coursesID;?>">Leave</a>
                </div>
            </div>
        </div>
    </div>
    <!-- จบ : modal เพื่อ leave ออกจากรายวิชา -->
                            
</body>
</html>