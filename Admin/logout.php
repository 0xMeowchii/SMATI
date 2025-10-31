<?php
    session_name('admin');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/Admin/login.php')
?>