<?php
    session_start();
    session_destroy();
    header('location:/SMATI/Teacher/teacher-login.php')
?>