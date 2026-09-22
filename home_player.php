<?php
require_once __DIR__.'/functions.php';
require_login();
refresh_session_user();
$u = current_user();

// Fetch ALL active trivia sets
$trivias = $db->query("SELECT * FROM trivia WHERE is_active=1 ORDER BY created_at DESC");

// Recent games for the player
$games = $db->query("
  SELECT g.*, t.title 
  FROM games g 
  INNER JOIN trivia t ON t.id=g.trivia_id 
  WHERE g.user_id=? 
  ORDER BY g.started_at DESC 
  LIMIT 10", [$u['id']]);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Home | Trivia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Welcome, <?php echo h($u['first_name']); ?>!</h2>
    <div class="card">
      <p class="meta">Credits: <strong><?php echo (int)$u['credits']; ?></strong></p>
    </div>

    <h3>Choose a Trivia</h3>
    <?php if(!$trivias): ?>
      <div class="card">No trivia available yet.</div>
    <?php else: ?>
      <?php foreach($trivias as $t): ?>
        <?php
          // Check if this user has completed this trivia
          $completed_count = (int)$db->queryValue("SELECT COUNT(*) FROM games WHERE user_id=? AND trivia_id=? AND score IS NOT NULL", [$u['id'], $t['id']]);
          $is_completed = $completed_count > 0;
        ?>
        <div class="card <?php echo $is_completed ? 'disabled' : ''; ?>">
          <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap">
            <div>
              <strong><?php echo h($t['title']); ?></strong>
              <div class="meta">Status: <?php echo $is_completed ? 'Completed' : 'Not completed'; ?></div>
            </div>
            <div style="min-width:200px">
              <?php if($is_completed): ?>
                <button class="btn disabled" disabled>Completed</button>
              <?php else: ?>
                <?php if ($u['credits']>0): ?>
                  <form method="post" action="start_game.php">
                    <?php csrf_field(); ?>
                    <input type="hidden" name="trivia_id" value="<?php echo (int)$t['id']; ?>">
                    <button class="btn" type="submit">Start</button>
                  </form>
                <?php else: ?>
                  <button class="btn disabled" disabled>Not enough credits</button>
                <?php endif; ?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <h3>Recent games</h3>
    <?php if(!$games): ?>
      <div class="card">No games yet.</div>
    <?php else: ?>
      <?php foreach($games as $g): ?>
        <div class="card">
          <div><strong><?php echo h($g['title']); ?></strong></div>
          <div class="meta"><?php echo h($g['started_at']); ?></div>
          <?php if(!is_null($g['score'])): ?>
            <p>Score: <strong><?php echo (int)$g['score']; ?></strong>
              <?php if($g['bucket']): ?>
                <span class="badge <?php echo ($g['bucket']=='Group 1'?'g1':($g['bucket']=='Group 2'?'g2':'g3')); ?>">
                  <?php echo h($g['bucket']); ?>
                </span>
              <?php else: ?>
                <span class="badge na">No group</span>
              <?php endif; ?>
            </p>
          <?php else: ?>
            <p><a href="play.php?game_id=<?php echo (int)$g['id']; ?>">Resume game</a></p>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <div class="footer"><a href="logout.php">Logout</a></div>
  </div>
</body>
</html>
