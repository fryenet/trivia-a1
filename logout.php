<?php
require_once __DIR__.'/functions.php';
start_secure_session();
session_unset();
session_destroy();
header('Location: index.php');
exit;
