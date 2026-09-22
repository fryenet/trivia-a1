<?php
require_once __DIR__.'/functions.php';
require_login();
require_admin();

// Summary
$summary = $db->query("
  SELECT bucket, COUNT(*) as cnt
  FROM games
  WHERE bucket IS NOT NULL
  GROUP BY bucket
  ORDER BY bucket
");

$total_users = (int)$db->queryValue("SELECT COUNT(*) FROM users");
$total_games = (int)$db->queryValue("SELECT COUNT(*) FROM games");
$active_trivia = $db->queryOne("SELECT id, title FROM trivia WHERE is_active=1 ORDER BY created_at DESC LIMIT 1");

// Recent non-admin users
$latest_users = $db->query("
  SELECT id, first_name, last_name, email, created_at
  FROM users
  WHERE role = 'user'
  ORDER BY created_at DESC
  LIMIT 5
");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin Home | Trivia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Admin Dashboard</h2>
    <div class="card">
      <p class="meta">Users: <strong><?php echo $total_users; ?></strong> | Games: <strong><?php echo $total_games; ?></strong></p>
      <p>Active Trivia: <strong><?php echo $active_trivia ? h($active_trivia['title']) : 'None'; ?></strong></p>
      <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px">
        <a class="btn" style="flex:1" href="admin.php">Manage Credits & Groups</a>
      </div>
    </div>

    <h3>Group Summary</h3>
    <div class="card">
      <?php if(!$summary): ?>
        <div class="meta">No results yet.</div>
      <?php else: ?>
        <?php foreach($summary as $row): ?>
          <p><a href="admin.php?bucket=<?php echo urlencode($row['bucket']); ?>"><?php echo h($row['bucket']); ?></a>: <strong><?php echo (int)$row['cnt']; ?></strong></p>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <h3>Recent users</h3>
    <?php if(!$latest_users): ?>
      <div class="card">None yet.</div>
    <?php else: foreach($latest_users as $u): ?>
      <?php
        // latest completed game score as "points"
        $points = $db->queryValue("SELECT score FROM games WHERE user_id=? AND score IS NOT NULL ORDER BY completed_at DESC, id DESC LIMIT 1", [$u['id']]);
        if ($points === false || $points === null) { $points = '-'; }
        // distinct groups
        $groups = $db->query("SELECT DISTINCT bucket FROM games WHERE user_id=? AND bucket IS NOT NULL ORDER BY bucket", [$u['id']]);
        $group_list = $groups ? implode(', ', array_map(fn($g)=>$g['bucket'], $groups)) : '-';
      ?>
      <div class="card">
        <div><strong><?php echo h($u['first_name'].' '.$u['last_name']); ?></strong></div>
        <div class="meta"><?php echo h($u['email']); ?> | <?php echo h($u['created_at']); ?></div>
        <div>Points: <strong><?php echo h($points); ?></strong></div>
        <div>Groups: <?php echo h($group_list); ?></div>
      </div>
    <?php endforeach; endif; ?>

    <div class="footer"><a href="logout.php">Logout</a></div>
  </div>
</body>
</html>
