<?php
    session_name('ADMIN');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/Admin/login.php')
?>