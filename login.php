<?php
session_start();

// Simple in-memory user store using session (persists during browser session)
// On first load, seed a default user if none exist
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        'admin@um.edu.ph' => [
            'full_name' => 'Admin User',
            'username'  => 'admin',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
        ]
    ];
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (isset($_SESSION['users'][$email]) && password_verify($password, $_SESSION['users'][$email]['password'])) {
        $_SESSION['logged_in']  = true;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name']  = $_SESSION['users'][$email]['full_name'];
        // Redirect to a dashboard (just loop back with success for demo)
        header('Location: login.php?success=1');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}

$success = isset($_GET['success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CCE – Log In</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --yellow: #F9D900;
    --yellow-dark: #E5C800;
    --black: #111111;
    --white: #ffffff;
    --radius: 14px;
  }

  body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--yellow);
    font-family: 'Barlow', sans-serif;
  }

  .card {
    width: 820px;
    max-width: 98vw;
    min-height: 420px;
    background: var(--white);
    border-radius: 28px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    display: flex;
    overflow: hidden;
    position: relative;
  }

  /* ── LEFT PANEL ── */
  .left {
    flex: 1;
    background: var(--yellow);
    padding: 44px 36px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    position: relative;
    overflow: hidden;
    background-image: url('image/bg.png');
    background-size: 45%; 
    background-position: center right;
    background-repeat: no-repeat;
    opacity: 0.9; 
  }

  .left::after {
    content: '';
    position: absolute;
    right: -60px;
    top: 0;
    bottom: 0;
    width: 120px;
    background: var(--black);
    border-radius: 60px 0 0 60px;
    z-index: 2;
  }

  .school-label {
    font-size: 11px;
    font-weight: 600;
    letter-spacing: .12em;
    text-transform: uppercase;
    color: var(--black);
    opacity: .6;
    margin-bottom: 6px;
    position: relative; z-index: 1;
  }

  .school-name {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 30px;
    font-weight: 800;
    line-height: 1.05;
    color: var(--black);
    text-transform: uppercase;
    max-width: 200px;
    position: relative; z-index: 1;
  }

  .seal {
    margin-top: 40px;
    width: 110px;
    height: 110px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    position: relative; z-index: 1;
    animation: sealFloat 3s ease-in-out infinite;
  }

  @keyframes sealFloat {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-6px); }
  }

  .seal-img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    animation: sealFloat 3s ease-in-out infinite;
}

  .seal-icon { font-size: 22px; margin-bottom: 4px; }

  /* ── RIGHT PANEL ── */
  .right {
    width: 360px;
    flex-shrink: 0;
    background: var(--yellow);
    padding: 48px 36px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 3;
  }

  .welcome {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 36px;
    font-weight: 800;
    text-transform: uppercase;
    color: var(--black);
    margin-bottom: 4px;
  }

  .subtitle {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: var(--black);
    opacity: .55;
    margin-bottom: 28px;
  }

  .alert {
    padding: 10px 14px;
    border-radius: 8px;
    font-size: 13px;
    margin-bottom: 16px;
    font-weight: 500;
  }
  .alert-error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
  .alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }

  .field {
    margin-bottom: 14px;
  }

  .field input {
    width: 100%;
    padding: 13px 16px;
    border-radius: 10px;
    border: 2px solid transparent;
    background: rgba(255,255,255,.85);
    font-family: 'Barlow', sans-serif;
    font-size: 14px;
    color: var(--black);
    outline: none;
    transition: border-color .2s, background .2s;
  }

  .field input::placeholder { color: #888; }

  .field input:focus {
    border-color: var(--black);
    background: var(--white);
  }

  .hint {
    text-align: right;
    font-size: 12.5px;
    margin-bottom: 20px;
    color: var(--black);
    opacity: .7;
  }

  .hint a { color: var(--black); font-weight: 600; text-decoration: none; }
  .hint a:hover { text-decoration: underline; }

  .btn-login {
    width: 100%;
    padding: 14px;
    background: var(--black);
    color: var(--yellow);
    border: none;
    border-radius: 10px;
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 17px;
    font-weight: 700;
    letter-spacing: .1em;
    text-transform: uppercase;
    cursor: pointer;
    transition: background .2s, transform .1s;
  }

  .btn-login:hover  { background: #333; }
  .btn-login:active { transform: scale(.98); }

  /* ── ANIMATIONS ── */
  .card { animation: cardIn .5s cubic-bezier(.22,1,.36,1) both; }
  @keyframes cardIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  @media (max-width: 640px) {
    .left { display: none; }
    .right { width: 100%; border-radius: 28px; }
    .card  { border-radius: 28px; }
  }
</style>
</head>
<body>

<div class="card">
  <!-- LEFT -->
  <div class="left">
    <p class="school-label">University of Mindanao</p>
    <h1 class="school-name">College of Computing Education</h1>
    <div class="seal">
      <div class="seal-inner">
        <img src="image/logo.png" alt="CCE Seal" class="seal-img" />
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right">
    <h2 class="welcome">Welcome Back!</h2>
    <p class="subtitle">Log In</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success && isset($_SESSION['logged_in'])): ?>
      <div class="alert alert-success">
        Welcome back, <?= htmlspecialchars($_SESSION['user_name']) ?>! You are logged in.
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="field">
        <input type="email" name="email" placeholder="Email Address" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
      </div>
      <div class="field">
        <input type="password" name="password" placeholder="Password" required/>
      </div>
      <p class="hint">Don't have an account? <a href="register.php">Register</a></p>
      <button type="submit" class="btn-login">Log In</button>
    </form>
  </div>
</div>

</body>
</html>