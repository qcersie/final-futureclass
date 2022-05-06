<?php

    include('dbconnect.php');
    //session_start();

    if (!isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You must log in first";
        header('location: login.php');
    }

    if (isset($_GET['logout'])) {
        session_destroy();
        unset($_SESSION['email']);
        header('location: login.php');
    }
    
    if (isset($_GET['id'])) {
        $coursesID = $_GET['id'];
    }

    if ($_SESSION['userlevel'] != "teacher") {
        header('location: userlevel.php');
    }

    if (!isset($_SESSION['userid'])){
        $_SESSION['msg'] = "User ID Error";
        header('location: error.php');
    }
    
    if (!isset($_GET['id'])){
        $_SESSION['msg'] = "Course ID Error";
        header('location: error.php');
    }

    $sql = "SELECT * FROM courses WHERE coursesID='$coursesID'";
    $courses = mysqli_fetch_array(mysqli_query($con, $sql));
    $id = $_SESSION['userid'];

    // force $question_num = 7 for teacher assessment
    $question_num = 7;

    // query for pie chart
    $query_pie = "SELECT
    CASE
    WHEN status = 1 THEN 'นิสิตที่ทำการประเมินแล้ว'
    WHEN status = 0 THEN 'นิสิตที่ยังไม่ทำการประเมิน'
    END AS status,
    COUNT(userEmail) AS number
    FROM tassessment_count
    WHERE coursesID = '$coursesID' AND teachersID = '$id'
    GROUP BY status;";
    $results_pie = mysqli_query($con, $query_pie);
    $pie_chart_data = array();
    while ($result = mysqli_fetch_array($results_pie)) {
    $pie_chart_data[] = array($result['status'], (int)$result['number']);
    }
    $pie_chart_data = json_encode($pie_chart_data);

    // query for average scroe
    $query_av_score = "SELECT tquestionsID AS questionNo,
    AVG(score) AS score_average
    FROM tassessment_score
    WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')
    GROUP BY questionNo
    ORDER BY questionNo";
    $resultsav = mysqli_query($con, $query_av_score);
    $column_chart_dataav = array(array("Question No.", "คะแนนเฉลี่ย"));
    while ($result = mysqli_fetch_array($resultsav)) {
    $column_chart_dataav[] = array("ข้อ ".$result['questionNo'], round($result['score_average'],2));
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

for ($i = 1; $i <= $question_num; $i++) {

    // query for bar chart(s)
    $query_score_vote = "SELECT
    SUM(CASE WHEN score = 1 THEN 1 ELSE 0 END) AS score1,
    SUM(CASE WHEN score = 2 THEN 1 ELSE 0 END) AS score2,
    SUM(CASE WHEN score = 3 THEN 1 ELSE 0 END) AS score3,
    SUM(CASE WHEN score = 4 THEN 1 ELSE 0 END) AS score4,
    SUM(CASE WHEN score = 5 THEN 1 ELSE 0 END) AS score5
    FROM tassessment_score
    WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')
    AND tquestionsID = ".$i;
    $results_score_count = mysqli_query($con, $query_score_vote);
    $resultscc = mysqli_fetch_array($results_score_count);
    $column_chart_data = array(array("Score", "Score Count"));
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
        title: 'ผลการประเมินจากคำถามข้อที่ $i',
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

}
    
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

<body>

    <!-- ส่วนข้อมูลภายในเว็บ -->
    <div class="container-fluid h-100 bg-light">
        
        <!-- แถบเมนูหลักด้านบน -->
        <div class="row">
            <nav class="navbar navbar-expand-sm navbar-dark bg-dark fixed-top" style="border-radius: 10px;">
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
                                <a href="home_teacher.php" class="nav-link" 
                                style="font-family: 'Mitr', sans-serif"> 
                                MY COURSE</a>
                            </li>

                            <!-- ***** -->
                            <!-- ถ้าจะเพิ่มเมนูเพิ่มวิชา -->
                            <!-- <li class="nav-item" >

                            </li> -->


                            <!--ส่วนปุ่มแจ้งเตือน-->
                            <li class="nav-item" style="margin-right :20px">
                                <button type="button" class="btn btn-dark position-relative" id="liveToastBtn">
                                <span class="material-icons">notifications_active</span>
                                    <?php
                                        $querynotinum = "SELECT COUNT(*) AS numnoti FROM notifications WHERE userID = '$id' AND status = 'unread'";
                                        if (time() < $_SESSION['startassess'] || time() >= $_SESSION['endassess']) {
                                        $querynotinum .= "AND NOT type = 'assess'";
                                        }
                                        $resultnotinum = mysqli_fetch_array(mysqli_query($con, $querynotinum));
                                        $notinum = $resultnotinum['numnoti'];
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
                                    id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="font-family: 'Mitr', sans-serif margin-bottom: 5px;">
                                    <!-- ดึงรูปจากฐานข้อมูลแสดงผลที่ img src="ที่เก็บไฟล์รูปต่างๆ"-->
                                    <img src="read_profile_pic.php"
                                        width="32" height="32" class="rounded-circle me-2">
                                    <!--ดึงชื่อผู้ใช้งานจากฐานข้อมูล ชื่อที่ถูกดึงอยู่ภายใต้ <strong> ชื่อนิสิต </strong> -->
                                    <strong> <?php echo $_SESSION['user']; ?> </strong>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="font-family: 'Mitr', sans-serif">
                                        <li><a class="dropdown-item" href="profile_teacher.php">Profile</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="home_teacher.php?logout='1">Sign out</a></li>
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
                             <span class="fs-5 d-none d-md-inline" style="font-family: 'Mitr', sans-serif"><?php echo $courses['nameEN']; ?></span>
                        </a>
                        <ul class="nav nav-pills flex-column mb-sm-auto mb-0 align-items-center align-items-sm-start" id="menu">
                            <!--แสดงรายละเอียดเมนูย่อยต่าง ๆ โดย <i class="cil-search"></i> คือการนำ icon ต่างๆมาแสดงหน้าชื่อเมนู -->
                            <li class="list active">
                                <a href="subject_teacher.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                     <span class="icon"><i class="cil-search"></i></span>
                                     <span class="title ms-1 d-none d-md-inline">About</span>
                                </a>
                            </li>
                            <li class="list">
                                <a href="request_teacher.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                    <span class="icon"><i class="cil-dinner"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Request</span></a>
                            </li>
                            <li class="list-A">
                                <a href="#" class="nav-link px-0 align-middle">
                                    <span class="icon"><i class="cil-chart"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Assessment</span>
                                </a>                
                            <li class="sub-list" style = "margin-left: 20px;">
                                <a href="assessment_subject_teacher.php?id=<?php echo($coursesID) ?>" class="nav-link px-0">
                                    <span class="icon"><i class="cil-spreadsheet"></i></span>
                                    <span class="title d-none d-md-inline">Subject</span></a>
                            </li>
                            <li class="sub-list" style = "margin-left: 20px;">
                                <a href="assessment_instructor_teacher.php?id=<?php echo($coursesID) ?>" class="nav-link px-0">
                                    <span class="icon"><i class="cil-people"></i></span>
                                    <span class="title d-none d-md-inline">Instructor</span></a>
                            </li>
                            <li class="list">
                                <a href="management.php?id=<?php echo($coursesID) ?>" class="nav-link align-middle px-0">
                                    <span class="icon"><i class="cil-settings"></i></span>
                                    <span class="title ms-1 d-none d-md-inline">Management</span>
                                </a>
                            </li>

                                <!-- **** -->
                                <!-- เพิ่มเมนูออกจากวิชา -->
                                <!-- <li class="list">

                                </li> -->

                            </li>
                        </ul>
                    </div>
                </ul>
            </div>

            <!-- ส่วนข้อมูลด้านข้างเมนูย่อย -->
            <div class="col-11 col-xs-11 col-md-10 bg-light" style="margin-top: 20px;">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="assessment_instructor_teacher.php?id=<?php echo $coursesID; ?>">คะแนน</a>
                        </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="instructor_q1_teacher.php?id=<?php echo $coursesID; ?>">ข้อดี</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="instructor_q2_teacher.php?id=<?php echo $coursesID; ?>">ข้อเสนอแนะ</a>
                    </li>
                </ul>
                <br>
                <!--card แสดงข้อมูลรายละเอียดรายวิชาและการประเมิน ฝั่งด้านซ้าย-->
                <div class="row">
                    <div class="col-6 col-sm-6 col-md-6">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <h3>สรุปผลการประเมินอาจารย์</h3>
                            <?php
                                $qcheckassess = "SELECT COUNT(*) FROM tassessment_score
                                WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')";
                                $checkassess = mysqli_fetch_array(mysqli_query($con, $qcheckassess));
                                if ($checkassess[0] != 0):        
                            ?>
                            <form method='post' action = 'save_chart_to_pdf.php?id=<?php echo $coursesID; ?>&for=ins' id='savePDFForm'>
                                <input type='hidden' id='htmlContentHidden' name='htmlContent' value=''>
                                <button type='submit' onclick="window.open('TeacherReport.pdf')" id="downloadBtn" class="btn btn-dark" style="border-radius: 10px;">
                                    Print PDF
                                    <i class="cil-print"></i>
                                </button>
                            </form>                           
                            <?php
                                endif;
                            ?>
                        </div>

                        <?php

                        $assesscount = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM tassessment_count WHERE coursesID=$coursesID AND teachersID = '$id' AND status = 1"))[0];
                        $goodcount = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM teachers_good WHERE coursesID=$coursesID AND teachersID='$id' AND qtype='good' AND status = 0"));
                        $goodnow = $goodcount[0];
                        $suggestcount = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM teachers_good WHERE coursesID=$coursesID AND teachersID='$id' AND qtype='suggestion' AND status = 0"));
                        $suggestnow = $suggestcount[0];
                            
                        ?>

                        <div class="card" style="border-radius: 20px; margin-bottom: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">Overview</h5>
                                <dl class="row">
                                    <dt class="col-sm-6">รหัสประจำตัวผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $id; ?></span></dd>

                                    <dt class="col-sm-6">ชื่อผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $_SESSION['user']; ?></span></dd>

                                <?php if ($courses['sectiontype'] == "แยก section") {
                                    $teachersec = mysqli_fetch_array(mysqli_query($con,"SELECT section FROM teacher_course WHERE coursesID=$coursesID AND teachersID = $id"))[0];
                                    $secregis = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM student_course WHERE coursesID=$coursesID AND section = $teachersec AND status='1'"))[0];
                                    $regisnow = $secregis;
                                ?>

                                    <dt class="col-sm-6">จำนวนนิสิตภายใน section</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $secregis; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนนิสิตที่ทำการประเมินผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $assesscount; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนข้อดีต่อผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $goodnow; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนข้อเสนอแนะต่อผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $suggestnow; ?></span></dd>
                                
                                <?php } ?>
                                <?php if ($courses['sectiontype'] == "รวม section") {
                                    $regisnow = mysqli_fetch_array(mysqli_query($con,"SELECT COUNT(*) FROM student_course WHERE coursesID=$coursesID AND status='1'"))[0];
                                ?>

                                    <dt class="col-sm-6">จำนวนนิสิตที่ลงทะเบียน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $regisnow."/".$courses['registotal']; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนนิสิตที่ทำการประเมินผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $assesscount; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนข้อดีต่อผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $goodnow; ?></span></dd>

                                    <dt class="col-sm-6">จำนวนข้อเสนอแนะต่อผู้สอน</dt>
                                    <dd class="col-sm-6">&nbsp;&nbsp;<span><?php echo $suggestnow; ?></span></dd>
                                
                                <?php } ?>
                                </dl>
                            </div>
                        </div>

                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">คะแนนเฉลี่ยการประเมินแบบเลือกคำตอบ</h5>
                                <dl class="row">
                                    <?php
                                        $query_av_score = "SELECT tquestionsID AS questionNo,
                                        AVG(score) AS score_average
                                        FROM tassessment_score
                                        WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')
                                        GROUP BY questionNo
                                        ORDER BY questionNo";
                                        $resultsav = mysqli_query($con, $query_av_score);
                                        if (mysqli_num_rows($resultsav) > 0):
                                            while ($result = mysqli_fetch_array($resultsav)):
                                    ?>
                                    <dt class="col-sm-3">ข้อที่ <?php echo $result['questionNo']; ?></dt>
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

                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">กราฟคะแนนเฉลี่ยจากการประเมิน</h5>
                                <?php
                                    $checkresultsav = mysqli_query($con, $query_av_score);
                                    if (mysqli_num_rows($checkresultsav) > 0):
                                ?>
                                <p id="column_chart_divav" width="400px" height="350px"></p>
                                <?php
                                    else:
                                ?>
                                <p align="center">-</p>
                                <?php
                                    endif;
                                ?>
                            </div>
                        </div>


                        
                    </div>

                    <br>
                    <!--card แสดงกราฟการประเมิน ฝั่งครึ่งหน้าขวา-->
                    <!--รายละเอียดคะแนนข้อต่างๆ-->
                    <div class="col-6 col-md-6 col-sm-6">
                        <h5 style="margin-top: 20px;">คะแนนการประเมินรายข้อ</h5>

                        <?php
                            $query_question = "SELECT * FROM tquestions WHERE tquestionsID <= '$question_num' ORDER BY tquestionsID";
                            $results_question = mysqli_query($con, $query_question);
                            while ($result = mysqli_fetch_array($results_question)):
                        ?>
                        <div class="card" style="border-radius: 20px;">

                            <div class="card-body">
                                <h5 class="card-title text-danger">ข้อ <?php echo $result['tquestionsID'].". ".$result['content']; ?></h5>
                                <dd><?php echo $result['caption']; ?></dd>
                                <!--ปุ่มเพื่อเปิด modal แสดงคะแนนรายข้อแบบละเอียด -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                        data-bs-target="#staticBackdrop<?php echo $result['tquestionsID']; ?>" style="border-radius: 20px;">
                                        More <i class="cil-chart"></i>
                                    </button>
                                </div>

                                <!-- modal รายละเอียดคะแนนรายข้อ -->
                                <div class="modal fade" id="staticBackdrop<?php echo $result['tquestionsID']; ?>" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">
                                                    ข้อ <?php echo $result['tquestionsID'].". ".$result['content']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <?php
                                                    $qcheckassess = "SELECT COUNT(*) FROM tassessment_score
                                                                        WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')";
                                                    $checkassess = mysqli_fetch_array(mysqli_query($con, $qcheckassess));
                                                    if ($checkassess[0] == 0):
                                                ?>
                                                <p align="center">ขณะนี้ยังไม่มีนิสิตที่ทำการประเมิน</p>
                                                <?php
                                                    else:
                                                        $query_score = "SELECT
                                                        SUM(CASE WHEN score = 1 THEN 1 ELSE 0 END) AS score1,
                                                        SUM(CASE WHEN score = 2 THEN 1 ELSE 0 END) AS score2,
                                                        SUM(CASE WHEN score = 3 THEN 1 ELSE 0 END) AS score3,
                                                        SUM(CASE WHEN score = 4 THEN 1 ELSE 0 END) AS score4,
                                                        SUM(CASE WHEN score = 5 THEN 1 ELSE 0 END) AS score5
                                                        FROM tassessment_score
                                                        WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')
                                                        AND tquestionsID = '$result[tquestionsID]'
                                                        ORDER BY tquestionsID";
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
                                                    endif;
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
            <!-- จบ : ส่วนข้อมูลด้านข้างเมนูย่อย -->
            </div>
        
        <!-- จบ : ส่วนข้อมูลภายในเว็บล่างเมนูหลักด้านบน -->
        </div>
    <!-- จบ : ส่วนข้อมูลภายในเว็บ -->
    <div id="pie_chart_div" style="width: 33.33%; display: none;"></div>
    <?php for ($i = 1; $i <= $question_num; $i++): ?>
        <div id="column_chart_div<?php echo $i; ?>" style="width: 33.33%; display: none;"></div>
    <?php endfor; ?>

    
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
    if($notinum[0] != 0) { ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11"data-bs-autohide="false">
        <div aria-live="assertive" aria-atomic="true" class="position-relative">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive"
                aria-atomic="true" >
                <button type="button" class="btn-close ms-2 mb-1 position-absolute end-0" 
                    style="background-color: rgb(219, 219, 219);" data-bs-dismiss="toast"
                    aria-label="close"></button>
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
                            
                            $querynoti = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND type = 'assess'";
                            $resultsnoti = mysqli_query($con, $querynoti);

                            if (mysqli_num_rows($resultsnoti) > 0):
                                while ($row = mysqli_fetch_array($resultsnoti)):
                                    if ($row['status'] == 'unread'):
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
                            You've got an assessment to do
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
                    $query = "SELECT * FROM notifications WHERE userID = '$_SESSION[userid]' AND NOT type = 'assess' ORDER BY date DESC";
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
    
    

</body>

</html>

<script>
    <?php
        
        $qcoursename = "SELECT * FROM courses WHERE coursesID = '$coursesID'";
        $rcoursename = mysqli_fetch_array(mysqli_query($con, $qcoursename));
    
        $subjectsID = $rcoursename['subjectsID'];
        $coursename = $rcoursename['nameEN'];
        $coursesnameTH = $rcoursename['nameTH'];

        $teachername = $_SESSION['user'];

        $qGoodNum = "SELECT COUNT(*) FROM teachers_good
                        WHERE coursesID = '$coursesID' AND teachersID = '$id' AND qtype = 'good'";
        $rGoodNum = mysqli_fetch_array(mysqli_query($con, $qGoodNum));
        $goodNum = $rGoodNum[0];
        
        $qSugNum = "SELECT COUNT(*) FROM teachers_good
                        WHERE coursesID = '$coursesID' AND teachersID = '$id' AND qtype = 'suggestion'";
        $rSugNum = mysqli_fetch_array(mysqli_query($con, $qSugNum));
        $sugNum = $rSugNum[0];
        
    ?>
jQuery(document).ready(function() {
    jQuery("#downloadBtn").on("click", function() {
        
        var htmlContent = '<head>';
        htmlContent += '<style>';
        //htmlContent += 'body {font-family: 'Sarabun', sans-serif;}';
        htmlContent += '.center {display: block; margin-left: auto; margin-right: auto; width: 50%;}';
        htmlContent += '.left {float: left; width: 50%; margin: 0 15px 0 0;}';
        htmlContent += '* {box-sizing: border-box;}';
        htmlContent += '.column {float: left; width: 33%;}';
        htmlContent += '.row::after {content: ""; clear: both; display: table;}';
        htmlContent += 'table, th, td {border: 0.5px solid black; border-collapse: collapse;}';
        htmlContent += '</style>';
        htmlContent += '</head>';

        htmlContent += '<body>';

        htmlContent += '<table align="center" style="width: 750px;">';
        htmlContent += '<tr>';
        htmlContent += '<th align="right" style="border: none;" rowspan="3"> <img src="./images/full-logo-b.png" width="90" height="80"></th>';
        htmlContent += '<th align="center" style="height: 60px; font-size: 40px; border: none;">';
        htmlContent += 'รายงานสรุปผลการประเมินอาจารย์';
        htmlContent += '</th>';
        htmlContent += '</tr>';
        htmlContent += '<tr>';
        htmlContent += '<td align="center" style="height: 60px; font-size: 36px; border: none;">';
        htmlContent += '<?php echo "สำหรับ ".$teachername." ในการเรียนการสอนรายวิชา" ?>';
        htmlContent += '</td>';
        htmlContent += '</tr>';
        htmlContent += '<tr>';
        htmlContent += '<td align="center" style="height: 60px; font-size: 36px; border: none;">';
        htmlContent += '<?php echo $subjectsID.' '.$coursesnameTH; ?>';
        htmlContent += '</td>';
        htmlContent += '</tr>';

        htmlContent += '</table>';

        htmlContent += '<div align="right" style="font-size: 20px; color: grey;">พิมพ์เมื่อวันที่ <?php echo $today; ?></div>';
        htmlContent += '<br>';
        
        htmlContent += '<div class="row">';

        htmlContent += '<div style="float: left; width: 49.5%;">';
        htmlContent += '<div style="font-size: 22px; font-weight: bold;">เปอร์เซ็นต์ของนิสิตที่ทำการประเมิน</div>';
        htmlContent += jQuery("#pie_chart_div").html();
        htmlContent += '</div>';

        htmlContent += '<div style="float: left; width: 49.5%;">';
        htmlContent += '<div style="font-size: 22px; font-weight: bold;">คะแนนเฉลี่ยของคำถามทั้งหมด</div>';
        htmlContent += jQuery("#column_chart_divav").html();
        htmlContent += '</div>';

        htmlContent += '</div>';

        htmlContent += '<div class="row">';

        htmlContent += '<div style="float: left; width: 49.5%;">';
        htmlContent += '<div style="font-size: 22px; font-weight: bold;">ภาพรวมการประเมินอาจารย์</div>';
        htmlContent += '</div>';

        htmlContent += '</div>';

        htmlContent += '<div class="row">';

        htmlContent += '<table style="width: 100%;">';

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px">จำนวนนิสิตที่ลงทะเบียน</td>';
        htmlContent += '<td align="center" style="font-size: 18px"><?php echo $regisnow; ?></td>';
        htmlContent += '</tr>';

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px">จำนวนนิสิตที่ทำการประเมิน</td>';
        htmlContent += '<td align="center" style="font-size: 18px"><?php echo $assesscount; ?> (<?php echo round($assesscount/$regisnow*100, 2); ?>%)</td>';
        htmlContent += '</tr>';

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px">จำนวนข้อดีต่อผู้สอน</td>';
        htmlContent += '<td align="center" style="font-size: 18px"><?php echo $goodNum; ?></td>';
        htmlContent += '</tr>';

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px">จำนวนข้อเสนอแนะต่อผู้สอน</td>';
        htmlContent += '<td align="center" style="font-size: 18px"><?php echo $sugNum; ?></td>';
        htmlContent += '</tr>';


        htmlContent += '</table>';

        htmlContent += '</div>';

        htmlContent += '<br>';

        htmlContent += '<div class="row">';
        htmlContent += '<div style="font-size: 22px; font-weight: bold; page-break-before: always;">ผลการประเมินรายข้อ</div>';
        htmlContent += '</div>';

        var i = 1;

        <?php
            
            $scoreav2 = array();
            $query_av_score2 = "SELECT tquestionsID AS questionNo,
            AVG(score) AS score_average
            FROM tassessment_score
            WHERE tassessmentID IN (SELECT tassessmentID FROM tassessment WHERE coursesID = '$coursesID' AND teachersID = '$id')
            GROUP BY questionNo
            ORDER BY questionNo";
            $resultsav2 = mysqli_query($con, $query_av_score2);
            
            if (mysqli_num_rows($resultsav2) > 0) {
                
                while ($rowav = mysqli_fetch_array($resultsav2)) {
                    $scoreav2[] = round($rowav['score_average'],2);
                }        
                
            }
            
            $m = 0;

            // query only question 1-7
            
            $qquestion = "SELECT * FROM tquestions WHERE tquestionsID <= 7";
            $rquestions = mysqli_query($con, $qquestion);
            while ($row = mysqli_fetch_array($rquestions)):
            
        ?>

        htmlContent += '<div class="row" ';
        <?php if ($m != 0 && $m % 2 == 0): ?>
            htmlContent += 'style="page-break-before: always;"';
        <?php endif; ?>
        htmlContent += '>';

        htmlContent += '<div style="float: left; width: 66%; font-size: 22px;">';
        htmlContent += 'คำถามที่ <?php echo $row["tquestionsID"]." - ".$row["content"]; ?>';
        htmlContent += '</div>';

        htmlContent += '<br>';

        htmlContent += '<div style="float: left; width: 66%; font-size: 20px;">';
        htmlContent += '<?php echo $row["caption"]; ?>';
        htmlContent += '<br>';
        htmlContent += 'คะแนนเฉลี่ยจากการประเมิน <?php echo $scoreav2[$m]; ?> คะแนน';
        htmlContent += '</div>';

        htmlContent += '<div class="column">';
        htmlContent += '<div style="width: 100%;">';
        htmlContent += jQuery("#column_chart_div" + i).html();
        htmlContent += '</div>';
        htmlContent += '</div>';

        htmlContent += '</div>';

        htmlContent += '<br>';

        i += 1;

        <?php
            $m ++;
            endwhile;
        ?>

        // ตารางข้อดี

        htmlContent += '<div class="row" style="page-break-before: always;">';
        htmlContent += '<p align="left" style="font-size: 22px; font-weight: bold;">';
        htmlContent += 'ข้อดีของการเรียนการสอน';
        htmlContent += '</p>';
        htmlContent += '</div>';

        htmlContent += '<table style="width: 100%;">';
        htmlContent += '<tr>';
        htmlContent += '<th align="center" style="font-size: 18px;">';
        htmlContent += 'ข้อดีของการเรียนการสอน';
        htmlContent += '</th>';
        htmlContent += '<th align="center" width="150" style="font-size: 18px;">';
        htmlContent += 'จำนวนผู้เห็นด้วย';
        htmlContent += '</th>';
        htmlContent += '<th align="center" width="150" style="font-size: 18px;">';
        htmlContent += 'จำนวนผู้ไม่เห็นด้วย';
        htmlContent += '</th>';
        htmlContent += '</tr>';

        <?php
            
            $qgood = "SELECT * FROM teachers_good
                        WHERE coursesID = '$coursesID' AND teachersID = '$id' AND qtype = 'good'
                        ORDER BY likeCount DESC";
            $resultsgood = mysqli_query($con, $qgood);
            if (mysqli_num_rows($resultsgood) > 0):
                while ($row = mysqli_fetch_array($resultsgood)):
            
        ?>

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px;"><?php echo $row['context']; ?></td>';
        htmlContent += '<td align="center" style="font-size: 18px;"><?php echo $row['likeCount']; ?></td>';
        htmlContent += '<td align="center" style="font-size: 18px;"><?php echo $row['dislikeCount']; ?></td>';
        htmlContent += '</tr>';

        <?php
                endwhile;
            else:
        ?>
        
        htmlContent += '<tr>';
        htmlContent += '<td align="center" style="font-size: 18px;">- ยังไม่มีผู้แสดงความคิดเห็นในหัวข้อนี้ -</td>';
        htmlContent += '<td align="center" style="font-size: 18px;">-</td>';
        htmlContent += '<td align="center" style="font-size: 18px;">-</td>';
        htmlContent += '</tr>';

        <?php
            endif;
        ?>

        htmlContent += '</table>';

        htmlContent += '<br>';

        // ตารางข้อเสนอแนะ

        htmlContent += '<div class="row">';
        htmlContent += '<p align="left" style="font-size: 22px; font-weight: bold;">';
        htmlContent += 'ข้อเสนอแนะจากการประเมินอาจารย์';
        htmlContent += '</p>';
        htmlContent += '</div>';

        htmlContent += '<table style="width: 100%;">';
        htmlContent += '<tr>';
        htmlContent += '<th align="center" style="font-size: 18px;">';
        htmlContent += 'ข้อเสนอแนะจากการประเมินอาจารย์';
        htmlContent += '</th>';
        htmlContent += '<th align="center" width="150" style="font-size: 18px;">';
        htmlContent += 'จำนวนผู้เห็นด้วย';
        htmlContent += '</th>';
        htmlContent += '<th align="center" width="150" style="font-size: 18px;">';
        htmlContent += 'จำนวนผู้ไม่เห็นด้วย';
        htmlContent += '</th>';
        htmlContent += '</tr>';

        <?php
            
            $qgood = "SELECT * FROM teachers_good
                        WHERE coursesID = '$coursesID' AND teachersID = '$id' AND qtype = 'suggestion'
                        ORDER BY likeCount DESC";
            $resultsgood = mysqli_query($con, $qgood);
            if (mysqli_num_rows($resultsgood) > 0):
                while ($row = mysqli_fetch_array($resultsgood)):
            
        ?>

        htmlContent += '<tr>';
        htmlContent += '<td align="left" style="font-size: 18px;"><?php echo $row['context']; ?></td>';
        htmlContent += '<td align="center" style="font-size: 18px;"><?php echo $row['likeCount']; ?></td>';
        htmlContent += '<td align="center" style="font-size: 18px;"><?php echo $row['dislikeCount']; ?></td>';
        htmlContent += '</tr>';

        <?php
                endwhile;
            else:
        ?>

        htmlContent += '<tr>';
        htmlContent += '<td align="center" style="font-size: 18px;">- ยังไม่มีผู้แสดงความคิดเห็นในหัวข้อนี้ -</td>';
        htmlContent += '<td align="center" style="font-size: 18px;">-</td>';
        htmlContent += '<td align="center" style="font-size: 18px;">-</td>';
        htmlContent += '</tr>';
        
        <?php
            endif;
        ?>

        htmlContent += '</table>';



        htmlContent += '</body>';


        /*
        htmlContent += jQuery("#pie_chart_div").html();
        htmlContent += jQuery("#column_chart_divav").html();
        htmlContent += jQuery("#column_chart_div1").html();
        htmlContent += jQuery("#column_chart_div2").html();
        htmlContent += jQuery("#column_chart_div3").html();
        */

        jQuery("#htmlContentHidden").val(htmlContent);

        // submit the form

        jQuery('#savePDFForm').submit();

    });
});
</script>