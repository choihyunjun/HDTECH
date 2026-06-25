<?php
session_start();
session_destroy();
header('Location: /mold/login.php');
exit;
