<?php
require_once __DIR__.'/functions.php';
require_login();
verify_csrf();
$u = current_user();
$trivia_id = (int)($_POST['trivia_id'] ?? 0);

// ensure trivia exists and has at least 10 questions
$question_count = (int)$db->queryValue("SELECT COUNT(*) FROM questions WHERE trivia_id=?", [$trivia_id]);
if ($question_count < 10){
    header('Location: home.php'); exit;
}

$game_id = start_game($u['id'], $trivia_id);
if (!$game_id){ header('Location: home.php'); exit; }

header('Location: play.php?game_id='.$game_id);
exit;
