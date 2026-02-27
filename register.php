<?php
session_start();

// Initialize user store if not present
if (!isset($_SESSION['users'])) {
    $_SESSION['users'] = [
        'admin@um.edu.ph' => [
            'full_name' => 'Admin User',
            'username'  => 'admin',
            'password'  => password_hash('admin123', PASSWORD_DEFAULT),
        ]
    ];
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $username  = trim($_POST['username'] ?? '');
    $password  = $_POST['password'] ?? '';

    if (!$full_name || !$email || !$username || !$password) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (isset($_SESSION['users'][$email])) {
        $error = 'An account with this email already exists.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Register the user in session
        $_SESSION['users'][$email] = [
            'full_name' => $full_name,
            'username'  => $username,
            'password'  => password_hash($password, PASSWORD_DEFAULT),
        ];
        $success = 'Account created successfully! You can now log in.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>CCE – Register</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@700;800&family=Barlow:wght@400;500;600&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  :root {
    --yellow: #F9D900;
    --black: #111111;
    --white: #ffffff;
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
    min-height: 440px;
    background: var(--white);
    border-radius: 28px;
    box-shadow: 0 20px 60px rgba(0,0,0,.18);
    display: flex;
    overflow: hidden;
    animation: cardIn .5s cubic-bezier(.22,1,.36,1) both;
  }

  @keyframes cardIn {
    from { opacity: 0; transform: translateY(24px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ── LEFT PANEL (form side) ── */
  .left {
    flex: 1;
    background: var(--yellow);
    padding: 48px 44px;
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
    line-height: 1.05;
    margin-bottom: 6px;
  }

  .subtitle {
    font-family: 'Barlow Condensed', sans-serif;
    font-size: 18px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--black);
    margin-bottom: 24px;
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

  .field { margin-bottom: 12px; }

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

  .btn-register {
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
    margin-top: 4px;
    margin-bottom: 14px;
  }

  .btn-register:hover  { background: #333; }
  .btn-register:active { transform: scale(.98); }

  .hint {
    font-size: 12.5px;
    color: var(--black);
    opacity: .7;
  }

  .hint a { color: var(--black); font-weight: 600; text-decoration: none; }
  .hint a:hover { text-decoration: underline; }

  /* ── RIGHT PANEL (seal side) ── */
  .right {
    width: 320px;
    flex-shrink: 0;
    background: var(--yellow);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }

  .right::before {
    content: '';
    position: absolute;
    left: -60px;
    top: 0; bottom: 0;
    width: 120px;
    background: var(--black);
    border-radius: 0 60px 60px 0;
  }

  .right-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    margin-left: 40px;
  }

  .seal-img {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: 0 4px 20px rgba(0,0,0,.2);
    animation: sealFloat 3s ease-in-out infinite;
}

  @keyframes sealFloat {
    0%,100% { transform: translateY(0); }
    50%      { transform: translateY(-7px); }
  }

  .seal-icon { font-size: 26px; margin-bottom: 4px; }

  @media (max-width: 640px) {
    .right { display: none; }
    .left  { border-radius: 28px; }
    .card  { border-radius: 28px; }
  }
</style>
</head>
<body>

<div class="card">
  <!-- LEFT – form -->
  <div class="left">
    <h2 class="welcome">Welcome to the<br>Department!</h2>
    <p class="subtitle">Register Here!</p>

    <?php if ($error): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
      <div class="field">
        <input type="text" name="full_name" placeholder="Full Name" required
               value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"/>
      </div>
      <div class="field">
        <input type="email" name="email" placeholder="Email Address" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/>
      </div>
      <div class="field">
        <input type="text" name="username" placeholder="Username" required
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"/>
      </div>
      <div class="field">
        <input type="password" name="password" placeholder="Password" required/>
      </div>
      <button type="submit" class="btn-register">Register</button>
      <p class="hint">Already have an account? <a href="login.php">Log In</a></p>
    </form>
  </div>

  <!-- RIGHT – seal -->
  <div class="right">
    <div class="right-content">
      <div class="seal">
        <div class="seal-inner">
          <img src="image/logo.png" alt="CCE Seal" class="seal-img" />
      </div>
    </div>
  </div>
</div>

</body>
</html>