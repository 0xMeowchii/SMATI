<?php

if (session_status() === PHP_SESSION_NONE) {
    session_name('registrar');
    session_start();
}
date_default_timezone_set('Asia/Manila');


?>