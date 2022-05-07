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

    $coursesID = $_GET['id'];
    $sql = "SELECT * FROM courses WHERE coursesID=$coursesID";
    $result = mysqli_fetch_array(mysqli_query($con,$sql));
    $id = $_SESSION['userid'];

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
                             <span class="fs-5 d-none d-md-inline" style="font-family: 'Mitr', sans-serif"><?php echo $result['nameEN'] ?></span>
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

            <!-- **** -->
            <!--ส่วนที่สองข้อมูลตรงกลาง ขนาดหน้าจอใหญ่กว่าขนาดMdจะมีความกว้าง5คอลัมน์ ถ้าเล็กกว่าจะเรียงต่อกัน-->
            <div class="col-6 col-xs-6 col-md-5 bg-light" style="margin-top: 15px; ">
                <!--row-cols-1 : แถวนึงมี1อัน , g-3 : ช่องห่างระหว่างอัน-->
                <div class="row row-cols-1 g-1 bg-light">

                    <!--เริ่ม Coppy ตรงนี้ค้าบ -->
                    <!--Card About-->
                    <div class="col">
                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">About</h5>
                                <dl class="row">

                                    <!--gen card ประเมินอาจารย์-->
                                    <div id='about_sub'></div>
                                    <!--ดึงไฟล์JSมาใช้-->
                   
                                        <dt class="col-sm-6">รหัสวิชา</dt>
                                        <dd class="col-sm-6"><span><?php echo $result['subjectsID'] ?></span></dd>

                                        <dt class="col-sm-6">ชื่อวิชา (ภาษาไทย) </dt>
                                        <dd class="col-sm-6"><span><?php echo $result['nameTH'] ?></span></dd>

                                        <dt class="col-sm-6">ชื่อวิชา (ภาษาอังกฤษ) </dt>
                                        <dd class="col-sm-6"><span><?php echo $result['nameEN'] ?></span></dd>

                                        <dt class="col-sm-6">สาขา</dt>
                                        <dd class="col-sm-6"><span><?php echo $result['class'] ?></span></dd>

                                        <dt class="col-sm-6">จำนวนหน่วยกิต</dt>
                                        <dd class="col-sm-6"><span><?php echo $result['credit'] ?></span></dd>
                                        
                                        <!-- <dt class="col-sm-6">คำอธิบายวิชา</dt>
                                        <dd class="col-sm-6"><span><?php echo $result['content'] ?></span></dd> -->

                                        <!-- query ข้อมูล -->
                                        <?php 
                                            $regisnow = mysqli_fetch_array(
                                                mysqli_query($con,
                                                    "SELECT COUNT(*) FROM student_course WHERE coursesID = $coursesID"
                                                )
                                            )[0];
                                            $section = mysqli_fetch_array(
                                                mysqli_query($con,
                                                    "SELECT section FROM teacher_course WHERE teachersID = $id"
                                                )
                                            )[0];
                                        ?>

                                        <dt class="col-sm-6">จำนวนคนลงทะเบียน</dt>
                                        <dd class="col-sm-6"><span><?php echo $regisnow ?></span></dd>

                                        <dt class="col-sm-6">Active</dt>
                                        <dd class="col-sm-6"><span><?php echo $result['actionperiod'] ?></span></dd>

                                        <dt class="col-sm-6">Section</dt>
                                        <dd class="col-sm-6"><span><?php echo $section ?></span></dd>

                                </dl>
                            </div>
                        </div>
                    </div>

                <!-- Coppy ถึงตรงนี้พอคั้บ--> 
                
                    <!--Card Material-->
                    <div class="col">
                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">Material</h5>
                                <dl class="row">
                                    <!--gen card ประเมินอาจารย์-->
                                    <div id='mat_sub'></div>
                                    <!--ดึงไฟล์JSมาใช้-->
                                    <?php 
                                        $sql = "SELECT * FROM courses_material WHERE coursesID='$coursesID' ORDER BY chapnum ASC ";
                                        $r3 = mysqli_query($con,$sql);
                                        while($a = mysqli_fetch_array($r3)) {
                                            echo '
                                                <dt class="col-sm-4">Chapter '.$a['chapnum'].'</dt>
                                                <dd class="col-sm-8"><span>'.$a['content'].'</span></dd>
                                            ';
                                        }
                                    ?>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!--Card Prerequisite สมมติยังไม่่ต้องดึงข้อมูล-->
                    <div class="col">
                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">Prerequisite</h5>
                                <dl class="row">
                                <?php 
                                    $sql = "SELECT * FROM courses_prere WHERE coursesID='$coursesID' ORDER BY requiretype ASC ";
                                    $r4 = mysqli_query($con,$sql);
                                        while($a = mysqli_fetch_array($r4)) {
                                            echo '
                                                <dt class="col-sm-4">'.$a['requiretype'].'</dt>
                                                <dd class="col-sm-8"><span>'.$a['requirecontent'].'</span></dd>
                                            ';
                                        }
                                    ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- **** -->
            <!--ส่วนที่สามข้อมูลขวามือ ขนาดหน้าจอใหญ่กว่าขนาดMdจะมีความกว้าง5คอลัมน์ ถ้าเล็กกว่าจะเรียงต่อกัน-->
            <div class="col-5 col-xs-5 col-md-5 bg-light" style="margin-top: 15px;">
                <div class="row row-cols-1 g-1 bg-light">

                    <!--Card Staff-->
                    <div class="col">
                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">Staff</h5>
                                <dl class="row" id="aj_in_sub">
                                    <?php          
                                        $sql = "SELECT teachersID FROM teacher_course WHERE coursesID=$coursesID ORDER BY teachersID ASC";
                                        $r = mysqli_query($con, $sql);
                                        while($a = mysqli_fetch_array($r)) {
                                            $tid = $a['teachersID'];
                                            $sql = "SELECT * FROM teachers WHERE teachersID = $tid";
                                            $rsub = mysqli_fetch_array(mysqli_query($con, $sql));
                                            $subname = $rsub['title']." ".$rsub['fname']. " " .$rsub['lname']; 
                                            $sql2 = "SELECT * FROM teacher_course WHERE teachersID = $tid AND coursesID=$coursesID";
                                            $rsub = mysqli_fetch_array(mysqli_query($con, $sql2));
                                            echo '
                                                <div style="margin-bottom:2px;display:flex;flex-direction:row;align-items:center;justify-content:center>>">
                                                <dt class="col-sm-2" style="margin-bottom:0px;">
                                                <img src= "read_pic.php?id='.$tid.'" width="32" height="32" class="rounded-circle me-2" style="margin-right:0px">
                                                </dt>
                                                <dt class="col-sm-5">'.$subname.'</dt>
                                                <dd class="col-sm-5" style="margin-bottom:0px"><span> section '.$rsub['section'].'</span></dd>
                                                </div>
                                                ';
                                        }
                                    ?>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!--Card ช่องทางต่างๆ-->
                    <div class="col">
                        <div class="card" style="border-radius: 20px;">
                            <div class="card-body">
                                <h5 class="card-title text-danger">ช่องทางต่างๆ</h5>
                                <dl class="row">

                                    <dt class="col-sm-6">สั่งงาน</dt>
                                    <?php if ($result['url1'] != "") { ?>
                                    <dd class="col-sm-6"><span><a href ="<?php echo $result['url1']; ?>" style="text-decoration: none;color: black;"><?php echo $result['contact1'];?></a></span></dd>
                                    <?php } else { ?>
                                    <dd class="col-sm-6"><span><?php echo $result['contact1'];?></span></dd>
                                    <?php } ?>

                                    <dt class="col-sm-6">ส่งงาน</dt>
                                    <?php if ($result['url2'] != "") { ?>
                                    <dd class="col-sm-6"><span><a href ="<?php echo $result['url2']; ?>" style="text-decoration: none;color: black;"><?php echo $result['contact2'];?></a></span></dd>
                                    <?php } else { ?>
                                    <dd class="col-sm-6"><span><?php echo $result['contact2'];?></span></dd>
                                    <?php } ?>

                                    <dt class="col-sm-6">คลิป</dt>
                                    <?php if ($result['url3'] != "") { ?>
                                    <dd class="col-sm-6"><span><a href ="<?php echo $result['url3']; ?>" style="text-decoration: none;color: black;"><?php echo $result['contact3'];?></a></span></dd>
                                    <?php } else { ?>
                                    <dd class="col-sm-6"><span><?php echo $result['contact3'];?></span></dd>
                                    <?php } ?>

                                    <dt class="col-sm-6">ติดต่ออาจารย์</dt>
                                    <?php if ($result['url4'] != "") { ?>
                                    <dd class="col-sm-6"><span><a href ="<?php echo $result['url4']; ?>" style="text-decoration: none;color: black;"><?php echo $result['contact4'];?></a></span></dd>
                                    <?php } else { ?>
                                    <dd class="col-sm-6"><span><?php echo $result['contact4'];?></span></dd>
                                    <?php } ?>

                                    <dt class="col-sm-6">สอนผ่าน</dt>
                                    <?php if ($result['url5'] != "") { ?>
                                    <dd class="col-sm-6"><span><a href ="<?php echo $result['url5']; ?>" style="text-decoration: none;color: black;"><?php echo $result['contact5'];?></a></span></dd>
                                    <?php } else { ?>
                                    <dd class="col-sm-6"><span><?php echo $result['contact5'];?></span></dd>
                                    <?php } ?>

                                </dl>
                            </div>
                        </div>


                    </div>
                </div>
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