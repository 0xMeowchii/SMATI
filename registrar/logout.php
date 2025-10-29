<?php
    session_name('registrar');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/registrar/login.php')
?>