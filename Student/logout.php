<?php
    session_name('STUDENT');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/Student/student-login.php')
?>