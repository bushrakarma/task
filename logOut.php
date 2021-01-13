<?php
    session_start();
    session_unset();
    session_destroy();
    ob_start();
    header('location: /task/Form.php');
