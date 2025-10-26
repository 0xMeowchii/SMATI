<?php
    session_name('student');
    session_start();
    session_unset();
    session_destroy();
    header('location:/SMATI/')
?>