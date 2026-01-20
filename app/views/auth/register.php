<?php
$errors=$errors??[];
$old=$old??['role'=>'member','full_name'=>'','email'=>'','phone'=>''];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="/assets/css/style.css">
  <title>Register - Smart Gym</title>
</head>
<body>
<div class="auth-container">
  <div class="card animate-fade-in">
    <div class="card-header">
      <h2 class="card-title">Join Smart Gym</h2>
      <p class="card-subtitle">Create your account to get started</p>
    </div>

    <?php if(!empty($errors['general'])): ?>
      <div class="alert alert-error"><?=htmlspecialchars($errors['general'])?></div>
    <?php endif; ?>

    <form id="regForm" method="post" action="/register" novalidate>
      <div class="form-group">
        <label for="role">Account Type</label>
        <select id="role" name="role">
          <option value="member" <?=$old['role']==='member'?'selected':''?>>Member</option>
          <option value="trainer" <?=$old['role']==='trainer'?'selected':''?>>Trainer</option>
          <option value="admin" <?=$old['role']==='admin'?'selected':''?>>Admin</option>
        </select>
      </div>

      <div class="form-group">
        <label for="full_name">Full Name</label>
        <input 
          id="full_name" 
          type="text"
          name="full_name" 
          value="<?=htmlspecialchars($old['full_name'])?>"
          placeholder="John Doe"
          required
        >
        <?php if(!empty($errors['full_name'])): ?>
          <span class="form-error"><?=htmlspecialchars($errors['full_name'])?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="email">Email Address</label>
        <input 
          id="email" 
          type="email"
          name="email" 
          value="<?=htmlspecialchars($old['email'])?>"
          placeholder="john@example.com"
          autocomplete="email"
          required
        >
        <?php if(!empty($errors['email'])): ?>
          <span class="form-error"><?=htmlspecialchars($errors['email'])?></span>
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="phone">Phone Number (optional)</label>
        <input 
          id="phone" 
          type="tel"
          name="phone" 
          value="<?=htmlspecialchars($old['phone'])?>"
          placeholder="01712345678"
          autocomplete="tel"
        >
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input 
          id="password" 
          type="password" 
          name="password"
          placeholder="Minimum 6 characters"
          autocomplete="new-password"
          required
        >
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm Password</label>
        <input 
          id="confirm_password" 
          type="password" 
          name="confirm_password"
          placeholder="Re-enter your password"
          autocomplete="new-password"
          required
        >
      </div>

      <div id="clientError" class="alert alert-error hide"></div>
      
      <button type="submit" class="btn btn-primary btn-block btn-lg">
        Create Account
      </button>
    </form>

    <p class="text-center text-muted mt-5">
      Already have an account? <a href="/login">Login here</a>
    </p>
  </div>
</div>
<script src="/assets/js/register.js"></script>
</body>
</html>
