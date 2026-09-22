<?php
require_once __DIR__.'/functions.php';
start_secure_session();

$error = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['email'], $_POST['password'])) {
    verify_csrf();
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    if ($email && $pass){
        $u = $db->queryOne("SELECT * FROM users WHERE email = ?", [$email]);
        if ($u && password_verify($pass, $u['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']   = $u['id'];
            $_SESSION['email']     = $u['email'];
            $_SESSION['first_name']= $u['first_name'];
            $_SESSION['last_name'] = $u['last_name'];
            $_SESSION['role']      = $u['role'];
            $_SESSION['credits']   = (int)$u['credits'];
            header('Location: home.php'); exit;
        } else {
            $error = 'Invalid credentials.';
        }
    } else {
        $error = 'All fields required.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login | Trivia</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <h2>Sign in</h2>
    <?php if(!empty($_GET['reg'])): ?><div class="card">Registration successful. You can sign in now.</div><?php endif; ?>
    <?php if($error): ?><div class="card" style="border-left:4px solid var(--accent)"><?php echo h($error); ?></div><?php endif; ?>
    <form method="post" autocomplete="off">
      <?php csrf_field(); ?>
      <input class="input" type="email" name="email" placeholder="Email" required>
      <input class="input" type="password" name="password" placeholder="Password" required>
      <button class="btn" type="submit">Login</button>
    </form>
    <div class="footer"><a href="register.php">Create account</a></div>
  </div>
</body>
</html>
