<?php
require_once __DIR__.'/functions.php';
require_login();
$game_id = (int)($_GET['game_id'] ?? 0);
$game = $db->queryOne("SELECT g.*, t.title FROM games g INNER JOIN trivia t ON t.id=g.trivia_id WHERE g.id=? AND g.user_id=?", [$game_id, current_user()['id']]);
if (!$game || is_null($game['score'])) { header('Location: home.php'); exit; }
$badgeClass = 'na';
if ($game['bucket']==='Group 1') $badgeClass='g1';
elseif ($game['bucket']==='Group 2') $badgeClass='g2';
elseif ($game['bucket']==='Group 3') $badgeClass='g3';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Results</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Results</h2>
    <div class="card">
      <div><strong><?php echo h($game['title']); ?></strong></div>
      <p>Score: <strong><?php echo (int)$game['score']; ?></strong></p>
      <p>Group: <span class="badge <?php echo $badgeClass; ?>"><?php echo h($game['bucket'] ?? 'Not entered'); ?></span></p>
      <a class="btn btn-outline" href="home.php">Back to Home</a>
    </div>
  </div>
</body>
</html>
