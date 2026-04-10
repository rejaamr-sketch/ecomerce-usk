<?php

include 'config.php';

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, ($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, ($_POST['cpassword']));
   $user_type = $_POST['user_type'];

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'user already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
         $message[] = 'registered successfully!';
         header('location:login.php');
      }
   }

}

?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Daftar | Premium Book Store</title>

   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      :root {
         --gold: #c9a84c;
         --gold-light: #e8c96d;
         --gold-dark: #a07830;
         --gold-bg: rgba(201,168,76,0.08);
         --gold-border: rgba(201,168,76,0.22);
         --gold-border-focus: rgba(201,168,76,0.55);
         --bg: #0d0d0d;
         --surface: #161616;
         --surface-2: #1e1e1e;
         --text: #f5f0e8;
         --muted: #9a8f7a;
      }

      * { margin: 0; padding: 0; box-sizing: border-box; }

      body {
         font-family: 'Plus Jakarta Sans', sans-serif;
         background-color: var(--bg);
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
         padding: 40px 20px;
      }

      /* ---- NOTIFICATION ---- */
      .message-stack {
         position: fixed;
         top: 24px;
         left: 50%;
         transform: translateX(-50%);
         z-index: 9999;
         display: flex;
         flex-direction: column;
         gap: 10px;
         width: max-content;
         max-width: 90vw;
      }

      .message {
         background: var(--surface);
         border: 1px solid rgba(231,76,60,0.35);
         border-left: 4px solid #e74c3c;
         padding: 14px 20px;
         border-radius: 12px;
         display: flex;
         align-items: center;
         gap: 14px;
         font-size: 0.88rem;
         color: #f87171;
         font-weight: 500;
         animation: slideDown 0.35s ease forwards;
      }

      .message i {
         cursor: pointer;
         color: var(--muted);
         font-size: 14px;
         transition: color 0.2s;
         margin-left: 4px;
      }
      .message i:hover { color: var(--text); }

      @keyframes slideDown {
         from { opacity: 0; transform: translateY(-12px); }
         to   { opacity: 1; transform: translateY(0); }
      }

      /* ---- CARD ---- */
      .register-card {
         background: var(--surface);
         border: 1px solid var(--gold-border);
         border-radius: 24px;
         padding: 44px 40px;
         width: 100%;
         max-width: 440px;
         position: relative;
         overflow: hidden;
      }

      .register-card::before {
         content: '';
         position: absolute;
         top: 0; left: 10%; right: 10%;
         height: 1px;
         background: linear-gradient(90deg, transparent, var(--gold-light), transparent);
      }

      /* ---- HEADER ---- */
      .card-logo {
         width: 54px; height: 54px;
         background: var(--gold-bg);
         border: 1px solid var(--gold-border);
         border-radius: 16px;
         display: flex; align-items: center; justify-content: center;
         margin: 0 auto 20px;
         font-size: 22px;
         color: var(--gold);
      }

      .card-title {
         text-align: center;
         margin-bottom: 32px;
      }

      .card-title h1 {
         font-size: 1.7rem;
         font-weight: 800;
         color: var(--text);
         letter-spacing: -0.5px;
         margin-bottom: 6px;
      }

      .card-title h1 span { color: var(--gold-light); }

      .card-title p {
         font-size: 0.85rem;
         color: var(--muted);
      }

      /* ---- FORM ---- */
      .field-group {
         position: relative;
         margin-bottom: 16px;
      }

      .field-icon {
         position: absolute;
         left: 14px;
         top: 50%; transform: translateY(-50%);
         color: var(--gold);
         font-size: 15px;
         pointer-events: none;
         transition: color 0.2s;
      }

      .field-input {
         width: 100%;
         padding: 13px 16px 13px 42px;
         background: var(--surface-2);
         border: 1px solid rgba(201,168,76,0.15);
         border-radius: 12px;
         color: var(--text);
         font-size: 0.9rem;
         font-family: 'Plus Jakarta Sans', sans-serif;
         outline: none;
         transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
         appearance: none;
         -webkit-appearance: none;
      }

      .field-input::placeholder { color: var(--muted); }

      .field-input:focus {
         border-color: var(--gold-border-focus);
         background: rgba(201,168,76,0.04);
         box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
      }

      .field-input:focus ~ .field-icon { color: var(--gold-light); }

      .select-wrap { position: relative; }

      .select-arrow {
         position: absolute;
         right: 14px; top: 50%; transform: translateY(-50%);
         color: var(--gold);
         font-size: 13px;
         pointer-events: none;
      }

      /* ---- BUTTON ---- */
      .btn-register {
         width: 100%;
         padding: 14px;
         background: var(--gold);
         color: #1a0f00;
         border: none;
         border-radius: 12px;
         font-size: 0.95rem;
         font-weight: 800;
         font-family: 'Plus Jakarta Sans', sans-serif;
         cursor: pointer;
         transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
         margin-top: 6px;
         letter-spacing: 0.3px;
      }

      .btn-register:hover {
         background: var(--gold-light);
         transform: translateY(-2px);
         box-shadow: 0 8px 24px rgba(201,168,76,0.25);
      }

      .btn-register:active {
         transform: translateY(0) scale(0.98);
      }

      /* ---- DIVIDER ---- */
      .divider {
         display: flex;
         align-items: center;
         gap: 12px;
         margin: 20px 0 0;
      }

      .divider-line {
         flex: 1;
         height: 1px;
         background: rgba(201,168,76,0.15);
      }

      .divider span {
         font-size: 0.78rem;
         color: var(--muted);
      }

      /* ---- FOOTER ---- */
      .card-footer {
         text-align: center;
         margin-top: 16px;
         font-size: 0.85rem;
         color: var(--muted);
      }

      .card-footer a {
         color: var(--gold-light);
         font-weight: 700;
         text-decoration: none;
         transition: color 0.2s;
      }

      .card-footer a:hover {
         color: var(--gold);
         text-decoration: underline;
      }

      /* ---- RESPONSIVE ---- */
      @media (max-width: 480px) {
         .register-card { padding: 32px 24px; }
         .card-title h1 { font-size: 1.4rem; }
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   echo '<div class="message-stack">';
   foreach($message as $msg){
      echo '
      <div class="message">
         <i class="fas fa-circle-exclamation"></i>
         <span>'.$msg.'</span>
         <i class="fas fa-xmark" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
   echo '</div>';
}
?>

<div class="register-card">

   <div class="card-logo">
      <i class="fas fa-book-open"></i>
   </div>

   <div class="card-title">
      <h1>Buat Akun <span>Baru</span></h1>
      <p>Daftarkan diri Anda untuk mulai berbelanja buku</p>
   </div>

   <form action="" method="post">

      <div class="field-group">
         <input type="text" name="name" placeholder="Masukkan nama Anda" required class="field-input">
         <span class="field-icon"><i class="fas fa-user"></i></span>
      </div>

      <div class="field-group">
         <input type="email" name="email" placeholder="Masukkan email Anda" required class="field-input">
         <span class="field-icon"><i class="fas fa-envelope"></i></span>
      </div>

      <div class="field-group">
         <input type="password" name="password" placeholder="Buat kata sandi" required class="field-input">
         <span class="field-icon"><i class="fas fa-lock"></i></span>
      </div>

      <div class="field-group">
         <input type="password" name="cpassword" placeholder="Konfirmasi kata sandi" required class="field-input">
         <span class="field-icon"><i class="fas fa-shield-halved"></i></span>
      </div>

      <div class="field-group select-wrap">
         <select name="user_type" class="field-input">
            <option value="user">User</option>
            <option value="admin">Admin</option>
         </select>
         <span class="field-icon"><i class="fas fa-users"></i></span>
         <span class="select-arrow"><i class="fas fa-chevron-down"></i></span>
      </div>

      <input type="submit" name="submit" value="Daftar Sekarang" class="btn-register">

   </form>

   <div class="divider">
      <div class="divider-line"></div>
      <span>atau</span>
      <div class="divider-line"></div>
   </div>

   <p class="card-footer">Sudah punya akun? <a href="login.php">Masuk di sini</a></p>

</div>

</body>
</html>