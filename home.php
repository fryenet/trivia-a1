<?php
require_once __DIR__.'/functions.php';
require_login();
refresh_session_user();
if (is_admin()) {
    header('Location: home_admin.php'); exit;
} else {
    header('Location: home_player.php'); exit;
}
