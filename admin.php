<?php
require_once __DIR__.'/functions.php';
require_login();
require_admin();

$notice = '';
// Add credits
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_credits_user'], $_POST['amount'])) {
    verify_csrf();
    $uid = (int)$_POST['add_credits_user'];
    $amt = max(1, min(100, (int)$_POST['amount']));
    // Ensure target user is not admin
    $role = $db->queryValue("SELECT role FROM users WHERE id=?", [$uid]);
    if ($role === 'user') {
        $db->query("UPDATE users SET credits = credits + ? WHERE id = ?", [$amt, $uid]);
        $notice = "Added $amt credit(s).";
    } else {
        $notice = "Cannot add credits to admins.";
    }
}

// Buckets summary
$summary = $db->query("
  SELECT bucket, COUNT(*) as cnt
  FROM games
  WHERE bucket IS NOT NULL
  GROUP BY bucket
  ORDER BY bucket
");

// Players by bucket (optional filter)
$bucket = $_GET['bucket'] ?? '';
$players = [];
if ($bucket) {
    $players = $db->query("
      SELECT u.first_name, u.last_name, u.email, g.score, g.started_at
      FROM games g
      JOIN users u ON u.id=g.user_id
      WHERE g.bucket = ?
      ORDER BY g.score DESC, g.started_at DESC
    ", [$bucket]);
}

// Only non-admin users in dropdown
$users = $db->query("SELECT id, first_name, last_name, email, credits FROM users WHERE role='user' ORDER BY last_name ASC, first_name ASC");
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Admin</h2>
    <?php if($notice): ?><div class="card" style="border-left:4px solid var(--accent2)"><?php echo h($notice); ?></div><?php endif; ?>

    <h3>Credits</h3>
    <div class="card">
      <form method="post" class="meta" style="display:block">
        <?php csrf_field(); ?>
        <label>User</label>
        <select class="input" name="add_credits_user" required>
          <?php foreach($users as $u){ ?>
            <option value="<?php echo (int)$u['id']; ?>"><?php echo h($u['first_name'].' '.$u['last_name'].' ('.$u['email'].') - credits: '.$u['credits']); ?></option>
          <?php } ?>
        </select>
        <label>Amount</label>
        <input class="input" type="number" name="amount" min="1" max="100" value="1">
        <button class="btn" type="submit">Add Credits</button>
      </form>
    </div>

    <h3>Groups</h3>
    <div class="card">
      <?php if(!$summary): ?>
        <div class="meta">No results yet.</div>
      <?php else: ?>
        <?php foreach($summary as $row): ?>
          <p><a href="?bucket=<?php echo urlencode($row['bucket']); ?>"><?php echo h($row['bucket']); ?></a>: <strong><?php echo (int)$row['cnt']; ?></strong></p>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?php if($bucket): ?>
      <h3><?php echo h($bucket); ?> - Players</h3>
      <?php if(!$players): ?>
        <div class="card">No players in this group.</div>
      <?php else: foreach($players as $p): ?>
        <div class="card">
          <div><strong><?php echo h($p['first_name'].' '.$p['last_name']); ?></strong> - <?php echo h($p['email']); ?></div>
          <div class="meta">Score: <?php echo (int)$p['score']; ?> | <?php echo h($p['started_at']); ?></div>
        </div>
      <?php endforeach; endif; ?>
    <?php endif; ?>

    <div class="footer"><a href="home_admin.php">Back</a></div>
  </div>
</body>
</html>
