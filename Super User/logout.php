<?php
    session_name('superuser');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/Super User/login.php')
?>