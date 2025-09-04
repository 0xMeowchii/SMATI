<?php
    session_start();
    session_destroy();
    header('location:/SMATI/Admin/admin-login.php')
?>