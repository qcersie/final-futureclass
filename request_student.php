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

    $sql2 = "SELECT * FROM requestment WHERE coursesID=$coursesID AND status=0 ORDER BY timestamp DESC";
    $r = mysqli_query($con, $sql2);

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
    <link rel="stylesheet" href="style2.css">
    
    <!-- **** -->
    <title>Subject</title>
  
</head>

<body>

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
                             <span class="fs-5 d-none d-md-inline" style="font-family: 'Mitr', sans-serif"><?php echo $result['nameEN'] ?></span>
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
            <div class="col-11 col-xs-11 col-md-10 bg-light" style="margin-top: 20px;">                    
                <div class="col">
                    <div class="card" style="border-radius: 20px;background-color:transparent;border-color:transparent;margin-top:0px;">        
                                <div class="row">
                                    <div class="col-sm-11 col-9">
                                        <ul class="list-inline" style="margin-bottom: 0px;">
                                            <h3> Request </h3>
                                            <p style="color:#6e6e6e; margin-bottom: 5px">Request เป็นส่วนที่นิสิตสามารถเพิ่มข้อเรียกร้องเกี่ยวกับรายวิชา</p>
                                            <li class="list-inline-item"><div style="width: 20px; height: 20px; border-radius: 50%; background:#D0312D;"></div></li>
                                            <li class="list-inline-item" style="color:#6e6e6e">ตนเองเพิ่ม</li>
                                            <li class="list-inline-item"><div style="width: 20px; height: 20px; border-radius: 50%; background:#303030;"></li>
                                            <li class="list-inline-item" style="color:#6e6e6e">คนอื่นเพิ่ม</li>
                                        </ul>
                                    </div>
                                    <div class="col-sm-1 col-3">
                                        <div style = "display: flex;justify-content:flex-end;position:absolute;top:50%;transform:translateY(-50%)">
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal"
                                        data-bs-whatever="@mdo">Add</button>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <hr>
                </div>
                    <?php
                        if (isset($_SESSION['error'])) {
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                        }
                    ?>
                    <!--เด้ง Model หลังจากกดปุ่ม Add-->
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered  ">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Request</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                            <form action="request_submit.php?id=<?php echo $coursesID;?>" method="POST" enctype="multipart/form-data">
                                <div class="modal-body">
                                        <!--ตัวรับ Topic-->
                                        <div class="mb-3">
                                            <label for="recipient-name" class="col-form-label">Topic:</label>
                                            <!-- ***เก็บ Topic *** -->
                                            <textarea type="text" class="form-control" id="topic-requestment" name="topic" required></textarea>
                                        </div>
                                        <!--ตัวรับ Details-->
                                        <div class="mb-3">
                                            <label for="message-text" class="col-form-label">Details:</label>
                                            <!-- ***เก็บ Details *** -->
                                            <textarea type="text" class="form-control" id="details-requestment" name="details"></textarea>
                                        </div>
                                        <!--ตัวรับ File-->
                                        <div class="mb-3">
                                            <!-- ***เก็บ File *** -->
                                            <input type="hidden" name="MAX_FILE_SIZE" value="25000000">
                                            <input type="file" id="myFile" name="file">
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="AnonymousRequest" id="AnRe" value=1>
                                                <label class="form-check-label" for="AnRe">
                                                Anonymous
                                                </label>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-danger">Add Request</button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>



                    <!--create card auto-->
                    <!--row-cols-1 : แถวนึงมี1อัน , g-3 : ช่องห่างระหว่างอัน-->
                    <div id='requestment_card' class="row row-cols-1 g-1 bg-light"></div>
                    <?php 
                        while($a = mysqli_fetch_array($r)) {
                            $sql = "SELECT * FROM students WHERE mail = '$a[userEmail]'";
                            if ($a['userEmail'] == $_SESSION['email']) {
                                $type = 'text-danger';
                                $color = "red";
                            } else {
                                $type = 'style = "#6E6E6E"';
                                $color = "#6E6E6E";
                            }
                            echo '
                                <div class="col">
                                <div class="card" style="border-radius: 20px;">
                                <div class="card-body">
                                <div class="container">
                                <div class="row">
                                <div class="col-10 col-sm-11">
                                <div class="container">
                                <h4 class="card-title '.$type.'">
                                <a href="request.php?id='.$a['requestmentID'].'" style="text-decoration:none;color:'.$color.';">
                                '.$a['topic'].'
                                </a>
                                </h4>
                                <p class="text-muted">
                                '.$a['timestamp'].'
                                </p>
                                </div>
                                </div>
                                <div class="col-2 col-sm-1">
                                <a href="request.php?id='.$a['requestmentID'].'"><svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" style="position:absolute;top:50%;transform:translateY(-50%)" fill="black" class="bi bi-chevron-right" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/></svg></a>
                                </div>
                                </div>
                                </div>
                                </div>
                                </div>
                                ';
                        }
                    ?>
                    <!-- เพิ่ม 1 -->
                    </div>
                </div>    
                    
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