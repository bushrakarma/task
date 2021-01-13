<?php
    session_start();
    if (isset($_SESSION['user']) && isset($_SESSION['password'])) {
        header('Location: /task/home.php'.$_GET["path"].'/'.$_SESSION['user']);
    } else {
        header('Location: /task/Form.php');
    }
