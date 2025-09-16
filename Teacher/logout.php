<?php
    session_name('TEACHER');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/Teacher/teacher-login.php')
?>