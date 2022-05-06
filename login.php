<?php

    include('dbconnect.php');
    if (isset($_SESSION['email'])) {
        $_SESSION['msg'] = "You are already logged in";
        if ($_SESSION['userlevel'] == 'student') {
            header ('location: home_student.php');
        }
        else if ($_SESSION['userlevel'] == 'teacher') {
            header ('location: home_teacher.php');
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p"
        crossorigin="anonymous"></script>
    <link rel="shortcut icon" href="./images/full-logo-b.png" type="image/x-icon">
    <link rel="stylesheet" href="style.css">
    <title>Sign in</title>

    
</head>

<body>
<div class="container-fluid h-100">
        <div class="row h-100">
            <div class="col-sm-6 bg-black h-100" style="display: flex; align-items: center; justify-content: center;">
                <img src="./images/full-logo-w.png" width="270px" height="250px" class="rounded mx-auto d-block" alt="web-logo-black">
            </div>
            <div class="col-sm-6 bg-white h-100" style="display: flex; align-items: center; justify-content: center;">
                <div class="container">
                    <h3 style="text-align: center;">Sign in</h3>
                    <!--action ระบุว่าจะส่งไปให้ฝั่ง database ยังไง label for="บอกว่าต้องการเก็บข้อมูลอะไร"-->
                    <form class="" action="login_db.php" method="post">
                        <div class="mb-4">
                            <label for="UserEmail" class="form-label">Email address</label>
                            <div class="decinput">
                                <input type="email" class="form-control" name="email"
                                    placeholder="6xxxxxxxxx@student.chula.ac.th" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="UserPassword" class="form-label">Password</label>
                            <div class="decinput">
                                <input type="password" class="form-control" name="password" required>
                            </div>
                        </div>
                        <div class="d-grid gap-2 col-6 mx-auto">
                            <button type="submit" class="btn btn-dark">Sign in</button>
                        </div>
                    </form>
                    
                    <?php// if (isset($_SESSION['msg'])) { ?>
                    <!--
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 20px;background-color: rgb(165, 0, 0);">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color:whitesmoke;">ERROR !!</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: whitesmoke;"></button>
                            </div>
                            <div class="modal-body" style="color:whitesmoke;justify-items: center; ">
                                <?php// echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
                            </div>
                        </div>
                    </div>
                    -->
                    <?php// } ?>
                    <?php// if (isset($_SESSION['error'])) { ?>
                    <!--
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 20px;background-color: rgb(165, 0, 0);">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel" style="color:whitesmoke;">ERROR !!</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: whitesmoke;"></button>
                            </div>
                            <div class="modal-body" style="color:whitesmoke;justify-items: center; ">
                                <?php// echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        </div>
                    </div>
                    -->
                    <?php// } ?>
                    <?php if (isset($_SESSION['error'])) { ?>
                        <div class="container" style="color: red; justify-items: center; align-items: center; margin-top: 20px;">
                            <h5>
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                    <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.146.146 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.163.163 0 0 1-.054.06.116.116 0 0 1-.066.017H1.146a.115.115 0 0 1-.066-.017.163.163 0 0 1-.054-.06.176.176 0 0 1 .002-.183L7.884 2.073a.147.147 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566z"/>
                                    <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0zM7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995z"/></svg>
                                </span>
                                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                            </h5>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</body>

</html>