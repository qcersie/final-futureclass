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

    if ($_SESSION['userlevel'] != "admin") {
        header('location: userlevel.php');
    }
    
    $id = $_SESSION['userid'];
    $r = mysqli_query($con,"SELECT coursesID FROM courses WHERE semesterID = $semID ORDER BY subjectsID ASC");

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
    <!--แต่ง css ตามไฟล์ style.css-->
    <link rel="stylesheet" href="style.css">
    <!--เว็บที่ดึง icon มาใช้งาน-->
    <link rel="stylesheet" href="https://unpkg.com/@coreui/icons@2.0.0-beta.3/css/all.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- **** -->
    <title>My COURSE</title>
    
</head>

<body>

    <!-- ส่วนข้อมูลภายในเว็บ -->
    <div class="container-fluid h-100 bg-light">

        <!-- แถบเมนูด้านบน -->
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

                    <!-- สร้างปุ่มที่เมื่อแสดงผลในจอขนาดเล็กจะรวมเมนูไว้ในปุ่มนึง -->
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                        aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>

                    <!-- ส่วนเมนูที่ติดขอบด้านขวา -->
                    <div class="collapse navbar-collapse mt-1" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <!-- ส่วนปุ่มmycourse -->
                            <li class="nav-item">
                                <a href="home_admin.php" class="nav-link"> 
                                MY COURSE
                                </a>
                            </li>

                            <!--ส่วนเมนูreport-->
                            <li class="nav-item">
                                <a href="report.php" class="nav-link"><span class="material-icons">
                                        warning
                                    </span></a>
                            </li>
                            <!--ส่วนเมนูตั้งเวลาประเมิน-->
                            <li class="nav-item">
                                <button type="button" class="btn btn-dark position-relative" data-bs-toggle="modal"
                                    data-bs-target="#settime" style="color: rgb(202, 202, 202);"> <span
                                        class="material-icons">
                                        schedule
                                    </span>
                                </button>
                            </li>

                            <!-- ส่วนปุ่มแจ้งเตือน -->
                            <li class="nav-item" style="margin-right :20px">
                                <button type="button" class="btn btn-dark position-relative" id="liveToastBtn">
                                <span class="material-icons">notifications_active</span>
                                    <?php
                                        $querynotinum = "SELECT COUNT(*) AS numnoti FROM notifications_admin WHERE needAction = 1";
                                        $resultnotinum = mysqli_fetch_array(mysqli_query($con, $querynotinum));
                                        $notinum = $resultnotinum['numnoti'];
                                    ?>
                                    <?php 
                                        if ($notinum != 0) {
                                        echo "<span class='position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger'> $notinum </span>";
                                        } 
                                    ?>
                                </button>
                            </li>
                            <!-- ส่วนแสดงรูปและชื่อผู้ใช้งาน -->
                            <li class="nav-item">
                                <div class="dropdown">
                                    <a href="#"
                                    class="d-flex align-items-center fw-light text-white text-decoration-none dropdown-toggle"
                                    id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; margin-bottom: 5px;">
                                    <!-- ดึงรูปจากฐานข้อมูลแสดงผลที่ img src="ที่เก็บไฟล์รูปต่างๆ" -->
                                    <img src="./images/logo400.jpeg"
                                        width="32" height="32" class="rounded-circle me-2">
                                    <!-- ดึงชื่อผู้ใช้งานจากฐานข้อมูล ชื่อที่ถูกดึงอยู่ภายใต้ <strong> ชื่อนิสิต </strong> -->
                                    <strong> <?php echo $_SESSION['user']; ?> (<?php echo $_SESSION['userid']; ?>) </strong>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-dark text-small shadow" style="font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;">
                                        <li><a class="dropdown-item" href="home_admin.php?logout='1">Sign out</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>        
                    </div>
                </div>
            </nav> 
        <!-- จบ : แถบเมนูด้านบน -->                  
        </div>


        <!-- ส่วนข้อมูลภายในเว็บล่างเมนูหลักด้านบน -->
        <div class="row justify-content-md-center" style="margin-top: 5rem !important;">

            <!-- ถ้าใหญ่กว่าmdเรียงแถวละ 4 การ์ด ถ้าเล็กกว่าsmเรียงแถวละ 2 การ์ด ถ้าเล็กกว่าสุดเรียงแถวละการ์ด -->
            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3 bg-light">            
                <?php 
                    while($a = mysqli_fetch_array($r)) {
                        $sid = $a['coursesID'];
                        $sql = "SELECT * FROM courses WHERE coursesID = $sid";
                        $rsub = mysqli_fetch_array(mysqli_query($con, $sql));
                        $subname = $rsub['nameEN']; 
                        $subid = $rsub['subjectsID'];
                        echo '
                            <a href="subject_admin.php?id='.$sid.' "style="text-decoration:none; color: #000;">
                            <div class="col h-100">
                            <div class="card h-100" style="border-radius: 20px;">
                            <img id="myImg" src="read_subject_pic.php?id='.$sid.'" class="card-img-top" alt="...">
                            <div class="card-body">
                            <h5 class="card-title text-danger">
                            '.$subid.'
                            </h5>
                            <p class="card-text">
                            '.$subname.'
                            </p>
                            </div>
                            </div>
                            </div>
                            </a>
                            ';
                    }
                ?>
            </div>

         
            
        <!-- จบ : ส่วนข้อมูลภายในเว็บล่างเมนูหลักด้านบน -->
        </div>
    <!-- จบ : ส่วนข้อมูลภายในเว็บ -->
    </div>

    <script>
        <?php if ($notinum != 0) :?>
        var toastTrigger = document.getElementById('liveToastBtn')
        var toastLiveExample = document.getElementById('liveToast')
        var toastElements = document.querySelectorAll('.toast')

        for (var i = 0; i < toastElements.length; i++) {
            new bootstrap.Toast(toastElements[i]).show();
        }

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
    <script src="notihome.js"></script>

    <!-- **** -->
    <!-- รันแจ้งเตือน -->

    <?php
    if($notinum != 0) { ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 11"data-bs-autohide="false">
        <div aria-live="assertive" aria-atomic="true" class="position-relative">
            <div id="liveToast" class="toast" role="alert" aria-live="assertive"
                aria-atomic="true" >
                <button type="button" class="btn-close ms-2 mb-1 position-absolute end-0" 
                    style="background-color: rgb(219, 219, 219);" data-bs-dismiss="toast"
                    aria-label="close"></button>
                
                <?php
                    $query = "SELECT * FROM notifications_admin WHERE needAction = 1 ORDER BY adminNotiID DESC";
                    $results = mysqli_query($con, $query);
                    while ($row = mysqli_fetch_array($results)):
                ?>

                <!-- แจ้งเตือน Admin เมื่อมีคน report (กดปุ่ม flag) ข้อเรียกร้อง (request) -->

                <?php if ($row['type'] == 'request'):
                    $querycname = "SELECT * FROM courses WHERE coursesID IN (SELECT coursesID FROM requestment WHERE requestmentID = '$row[ID]')";
                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));              
                ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href="request_admin.php?id=<?php echo $resultcname['coursesID']; ?>&rid=<?php echo $row['ID']; ?>"
                        style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(161, 16,16);">
                            <img src="read_subject_pic.php?rid=<?php echo $row['ID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: whitesmoke;">
                                <?php
                                    echo $resultcname['nameEN'];
                                ?>
                            </div>
                            <small style="color: whitesmoke;">
                                <?php
                                    $qtimestamp = "SELECT timestamp FROM request_report WHERE rID = '$row[ID]'";
                                    $timestamp = mysqli_fetch_array(mysqli_query($con, $qtimestamp));
                                    //echo date('F j, Y, g:i a', strtotime($timestamp[0]));
                                    $pasttime = time() - strtotime($timestamp[0]);
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
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            <?php
                                $sql = "SELECT * FROM requestment WHERE requestmentID = '$row[ID]'";
                                $result = mysqli_fetch_array(mysqli_query($con, $sql));
                                $req = $result['topic'];
                                echo "Someone reported this request: ".$req;
                            ?>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <!-- แจ้งเตือน Admin เมื่อมีคน report (กดปุ่ม flag) ความคิดเห็น (ข้อดี ปัญหา ข้อเสนอแนะ) ในการประเมินรายวิชา (Subject Assessment) -->

                <?php if ($row['type'] == 'assess'): ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href="report_assessment.php" style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(161, 16,16);">
                            <img src="read_subject_pic.php?qid=<?php echo $row['ID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: whitesmoke;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID IN (SELECT coursesID FROM courses_good WHERE ID = '$row[ID]')";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                            <small style="color: whitesmoke;">
                                <?php
                                    $qtimestamp = "SELECT timestamp FROM courses_good_report WHERE ID = '$row[ID]'";
                                    $timestamp = mysqli_fetch_array(mysqli_query($con, $qtimestamp));
                                    //echo date('F j, Y, g:i a', strtotime($timestamp[0]));
                                    $pasttime = time() - strtotime($timestamp[0]);
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
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            <?php
                                    $sql = "SELECT * FROM courses_good WHERE ID = '$row[ID]'";
                                    $result = mysqli_fetch_array(mysqli_query($con, $sql));
                                    $qtype = $result['qtype'];
                                    echo "Someone reported ";
                                    if ($qtype == 'good') {
                                        echo "a good point of this subject.";
                                    } else if ($qtype == 'problem') {
                                        echo "a problem in subject assessment.";
                                    } else if ($qtype == 'suggestion') {
                                        echo "a suggestion in subject assessment.";
                                    }
                            ?>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <!-- แจ้งเตือน Admin เมื่อมีคน report (กดปุ่ม flag) ความคิดเห็น (ข้อดี ปัญหา ข้อเสนอแนะ) ในการประเมินรายวิชา (Subject Assessment) -->

                <?php if ($row['type'] == 'tassess'): ?>
                <div class="toast" role="alert" aria-live="assertive" aria-atomic="true"
                    data-bs-autohide="false" style="border-radius: 10px; background-color: whitesmoke; margin-top: 20px;">
                    <a href="report_assessment.php" style="text-decoration: none;">
                        <div class="toast-header" style="border-radius: 10px;background-color: rgb(161, 16,16);">
                            <img src="read_subject_pic.php?tqid=<?php echo $row['ID']; ?>"
                                width="35px" height="35px" class="rounded me-2" alt="...">
                            <div class="me-auto" style="font-weight:bold; color: whitesmoke;">
                                <?php
                                    $querycname = "SELECT * FROM courses WHERE coursesID IN (SELECT coursesID FROM teachers_good WHERE ID = '$row[ID]')";
                                    $resultcname = mysqli_fetch_array(mysqli_query($con, $querycname));
                                    $cname = $resultcname['nameEN'];
                                    echo $cname;
                                ?>
                            </div>
                            <small style="color: whitesmoke;">
                                <?php
                                    $qtimestamp = "SELECT timestamp FROM teachers_good_report WHERE ID = '$row[ID]'";
                                    $timestamp = mysqli_fetch_array(mysqli_query($con, $qtimestamp));
                                    //echo date('F j, Y, g:i a', strtotime($timestamp[0]));
                                    $pasttime = time() - strtotime($timestamp[0]);
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
                        <div class="toast-body" style="font-weight:bold; color: black;">
                            <?php
                                    $sql = "SELECT * FROM teachers_good WHERE ID = '$row[ID]'";
                                    $result = mysqli_fetch_array(mysqli_query($con, $sql));
                                    $qtype = $result['qtype'];

                                    $sql2 = "SELECT * FROM teachers WHERE teachersID IN (SELECT teachersID FROM teachers_good WHERE ID = '$row[ID]')";
                                    $result2 = mysqli_fetch_array(mysqli_query($con, $sql2));
                                    $tname = $result2['title'].$result2['fname']." ".$result2['lname'];

                                    echo "Someone reported ";
                                    if ($qtype == 'good') {
                                        echo "a good point of ".$tname;
                                    } else if ($qtype == 'suggestion') {
                                        echo "a suggestion in instructor assessment of ".$tname;
                                    }
                            ?>
                        </div>
                    </a>
                </div>
                <?php endif; ?>

                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php } ?>

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

    <!-- จบ : รันแจ้งเตือน -->
    <!-- modal ตั้งเวลาประเมิน-->
    <div class="modal fade" id="settime" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">ตั้งเวลาสำหรับการประเมิน
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!--เชื่อมกับphpในการรับข้อมูลต่างๆ-->
                    <p style="margin-bottom:0px;"> ช่วงเวลาการประเมินล่าสุด (<?php echo $semester['semesterName'] ?>) </p>
                    <p style="margin-bottom:8px;" class="text-danger"><?php echo $semester['start']." - ".$semester['end']; ?></p>
                    <form action="schedule.php" method="POST">
                        <label for="startdate" style="margin-right: 10px;margin-top: 10px;">วันเริ่มการประเมิน</label>
                        <input type="date" id="startdate" name="startdate">
                        <br>
                        <label for="enddate" style="margin-right: 10px;margin-top: 10px;">วันสิ้นสุดการประเมิน</label>
                        <input type="date" id="enddate" name="enddate">
                        <br>
                        <label for="starttime" style="margin-right: 10px;margin-top: 10px;">เวลาเริ่มและเวลาสิ้นสุดการประเมิน</label>
                        <input type="time" id="starttime" name="starttime">
                        <br>
                        <label for="semester" style="margin-right: 10px;margin-top: 10px;">ภาคการศึกษา</label>
                        <input type="text" id="semester" name="semester" placeholder = "20XX/X">
                        <hr>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-danger">Submit</button>
                        </div>
                        <input type="hidden" id="oldpage" name="oldpage" value="home_admin.php">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- จบ : modal ตั้งเวลาประเมิน-->
</body>

</html>