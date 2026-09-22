<?php
require_once __DIR__.'/functions.php';
start_secure_session();

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $conf  = $_POST['confirm_password'] ?? '';
    if ($first && $last && $email && $pass && $conf){
        if ($pass !== $conf) $error = 'Passwords do not match.';
        elseif ($db->queryValue("SELECT COUNT(*) FROM users WHERE email=?", [$email]) > 0) $error = 'Email already registered.';
        else {
            $role = 'user'; // always a player by default
            $credits = 2;
            $db->insert('users', [
                'first_name'=>$first,'last_name'=>$last,'email'=>$email,
                'password'=>password_hash($pass, PASSWORD_DEFAULT),
                'role'=>$role,'credits'=>$credits,'created_at'=>date('Y-m-d H:i:s')
            ]);
            header('Location: index.php?reg=1'); exit;
        }
    } else $error='All fields required.';
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register | Trivia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Create account</h2>
    <?php if($error): ?><div class="card" style="border-left:4px solid var(--accent)"><?php echo h($error); ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <?php csrf_field(); ?>
      <input class="input" type="text" name="first_name" placeholder="First name" required>
      <input class="input" type="text" name="last_name" placeholder="Last name" required>
      <input class="input" type="email" name="email" placeholder="Email" required>
      <input class="input" type="password" name="password" placeholder="Password" required>
      <input class="input" type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button class="btn" type="submit">Register</button>
    </form>
    <div class="footer"><a href="index.php">Back to sign in</a></div>
  </div>
</body>
</html>
