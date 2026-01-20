<?php
$errors=$errors??[];
$old=$old??['email'=>''];
$registered = isset($_GET['registered']) && $_GET['registered']==='1';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>Login - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Welcome Back</h2>
      <p class="card-subtitle">Login to manage your Smart Gym account</p>
    </div>

    <?php if($registered): ?>
      <div class="alert alert-success">✓ Registration successful! Please login to continue.</div>
    <?php endif; ?>
    
    <?php if(!empty($errors['general'])): ?>
      <div class="alert alert-error"><?=htmlspecialchars($errors['general'])?></div>
    <?php endif; ?>

    <form id="loginForm" method="post" action="/login" novalidate>
      <div class="form-group">
        <label for="email">Email Address</label>
        <input 
          id="email" 
          type="email"
          name="email" 
          value="<?=htmlspecialchars($old['email'])?>" 
          placeholder="admin@gym.com"
          autocomplete="email"
          required
        >
        <?php if(!empty($errors['email'])): ?>
          <span class="form-error"><?=htmlspecialchars($errors['email'])?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input 
          id="password" 
          type="password" 
          name="password" 
          placeholder="Enter your password"
          autocomplete="current-password"
          required
        >
        <?php if(!empty($errors['password'])): ?>
          <span class="form-error"><?=htmlspecialchars($errors['password'])?></span>
        <?php endif; ?>
      </div>

      <div id="clientError" class="alert alert-error hide"></div>
      
      <button type="submit" class="btn btn-primary btn-block btn-lg">
        Login
      </button>
    </form>

    <p class="text-center text-muted mt-5">
      Don't have an account? <a href="/register">Create one now</a>
    </p>
  </div>
</div>
<script src="/assets/js/auth.js"></script>
</body>
</html>
