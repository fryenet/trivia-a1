<?php
require_once __DIR__.'/db.php';

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        if (empty($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(16));
        }
    }
}
function csrf_field(){
    start_secure_session();
    echo '<input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'">';
}
function verify_csrf(){
    start_secure_session();
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(400);
        exit('Bad Request');
    }
}

function logged_in(){ start_secure_session(); return !empty($_SESSION['user_id']); }
function require_login(){ if(!logged_in()){ header('Location: index.php'); exit; } }

function current_user(){
    start_secure_session();
    if (!logged_in()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'role' => $_SESSION['role'],
        'credits' => (int)$_SESSION['credits']
    ];
}
function refresh_session_user(){
    start_secure_session();
    global $db;
    $u = $db->queryOne("SELECT id, first_name, last_name, email, role, credits FROM users WHERE id = ?", [$_SESSION['user_id']]);
    if ($u){
        $_SESSION['first_name'] = $u['first_name'];
        $_SESSION['last_name']  = $u['last_name'];
        $_SESSION['email']      = $u['email'];
        $_SESSION['role']       = $u['role'];
        $_SESSION['credits']    = (int)$u['credits'];
    }
}

function is_admin(){ $u=current_user(); return $u && $u['role']==='admin'; }
function require_admin(){ if(!is_admin()){ header('Location: home.php'); exit; } }

function start_game($user_id, $trivia_id){
    global $db;
    // Deduct 1 credit
    $credits = (int)$db->queryValue("SELECT credits FROM users WHERE id=?", [$user_id]);
    if ($credits < 1) return false;
    $db->query("UPDATE users SET credits = credits - 1 WHERE id = ?", [$user_id]);

    $game_id = $db->insert('games', [
        'user_id' => $user_id,
        'trivia_id' => $trivia_id,
        'started_at' => date('Y-m-d H:i:s'),
        'completed_at' => null,
        'score' => null,
        'bucket' => null
    ]);
    refresh_session_user();
    return $game_id;
}

function compute_score_and_bucket($game_id){
    global $db;
    $row = $db->queryOne("
      SELECT COUNT(*) AS total, SUM(CASE WHEN ga.is_correct=1 THEN 1 ELSE 0 END) AS correct
      FROM game_answers ga
      WHERE ga.game_id = ?", [$game_id]);
    $total = (int)$row['total'];
    $correct = (int)$row['correct'];
    if ($total === 0) return [0, null];
    $score = (int)round(($correct / $total) * 100, 0);

    $bucket = null;
    if ($score >= 10 && $score <= 30) $bucket = 'Group 1';
    elseif ($score >= 40 && $score <= 70) $bucket = 'Group 2';
    elseif ($score >= 80 && $score <= 100) $bucket = 'Group 3';

    $db->query("UPDATE games SET completed_at = NOW(), score = ?, bucket = ? WHERE id = ?", [$score, $bucket, $game_id]);
    return [$score, $bucket];
}
?>
