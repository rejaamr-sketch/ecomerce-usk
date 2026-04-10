<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){

   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, ($_POST['password']));

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){

      $row = mysqli_fetch_assoc($select_users);

      if($row['user_type'] == 'admin'){

         $_SESSION['admin_name'] = $row['name'];
         $_SESSION['admin_email'] = $row['email'];
         $_SESSION['admin_id'] = $row['id'];
         header('location:admin_page.php');

      }elseif($row['user_type'] == 'user'){

         $_SESSION['user_name'] = $row['name'];
         $_SESSION['user_email'] = $row['email'];
         $_SESSION['user_id'] = $row['id'];
         header('location:home.php');

      }

   }else{
      $message[] = 'incorrect email or password!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login - Selamat Datang</title>

   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      :root {
         --bg: #0d0d0d;
         --gold: #c9a84c;
         --gold-light: #e8c96d;
         --gold-dark: #a07830;
         --surface: #161616;
         --surface-2: #1e1e1e;
         --text: #f5f0e8;
         --muted: #9a8f7a;
         --border: rgba(201, 168, 76, 0.2);
         --border-hover: rgba(201, 168, 76, 0.45);
      }

      * {
         margin: 0; padding: 0;
         box-sizing: border-box;
         font-family: 'Plus Jakarta Sans', sans-serif;
      }

      body {
         background: var(--bg);
         display: flex;
         align-items: center;
         justify-content: center;
         min-height: 100vh;
         padding: 20px;
         position: relative;
         overflow: hidden;
      }

      /* Decorative background orbs */
      body::before {
         content: '';
         position: fixed;
         top: -120px; left: -120px;
         width: 420px; height: 420px;
         border-radius: 50%;
         background: radial-gradient(circle, rgba(201,168,76,0.1) 0%, transparent 70%);
         pointer-events: none;
      }

      body::after {
         content: '';
         position: fixed;
         bottom: -100px; right: -100px;
         width: 360px; height: 360px;
         border-radius: 50%;
         background: radial-gradient(circle, rgba(201,168,76,0.08) 0%, transparent 70%);
         pointer-events: none;
      }

      /* --- CARD --- */
      .form-container {
         background: var(--surface);
         border: 1px solid var(--border);
         padding: 52px 44px;
         border-radius: 28px;
         width: 100%;
         max-width: 420px;
         text-align: center;
         position: relative;
         z-index: 1;
         animation: fadeIn 0.5s ease;
      }

      /* gold top accent line */
      .form-container::before {
         content: '';
         position: absolute;
         top: 0; left: 10%; right: 10%;
         height: 2px;
         background: linear-gradient(90deg, transparent, var(--gold), transparent);
         border-radius: 2px;
      }

      @keyframes fadeIn {
         from { opacity: 0; transform: translateY(24px); }
         to   { opacity: 1; transform: translateY(0); }
      }

      /* --- LOGO ICON --- */
      .logo-icon {
         width: 68px; height: 68px;
         border-radius: 20px;
         background: rgba(201, 168, 76, 0.1);
         border: 1px solid rgba(201, 168, 76, 0.3);
         display: flex; align-items: center; justify-content: center;
         margin: 0 auto 24px;
         font-size: 1.7rem;
      }

      /* --- HEADING --- */
      .form-container h3 {
         font-size: 1.75rem;
         font-weight: 800;
         color: var(--text);
         margin-bottom: 8px;
         letter-spacing: -0.5px;
      }

      .form-container p.subtitle {
         color: var(--muted);
         margin-bottom: 36px;
         font-size: 0.9rem;
         line-height: 1.5;
      }

      /* --- INPUT GROUP --- */
      .input-group {
         position: relative;
         margin-bottom: 16px;
         text-align: left;
      }

      .input-group .input-icon {
         position: absolute;
         left: 18px;
         top: 50%;
         transform: translateY(-50%);
         color: var(--muted);
         font-size: 0.9rem;
         pointer-events: none;
         transition: color 0.3s;
         z-index: 1;
      }

      .box {
         width: 100%;
         padding: 15px 18px 15px 48px;
         background: var(--surface-2);
         border: 1px solid var(--border);
         border-radius: 14px;
         font-size: 0.9rem;
         color: var(--text);
         transition: border-color 0.3s, box-shadow 0.3s;
         box-sizing: border-box;
      }

      .box::placeholder { color: var(--muted); }

      .box:focus {
         border-color: var(--gold);
         outline: none;
         box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.12);
      }

      .box:focus ~ .input-icon {
         color: var(--gold);
      }

      /* --- BUTTON --- */
      .btn {
         width: 100%;
         padding: 16px;
         background: linear-gradient(135deg, var(--gold), var(--gold-dark));
         color: #0d0d0d;
         border: 1px solid var(--gold-light);
         border-radius: 14px;
         font-size: 0.95rem;
         font-weight: 800;
         cursor: pointer;
         transition: all 0.3s ease;
         margin-top: 8px;
         letter-spacing: 0.3px;
      }

      .btn:hover {
         background: linear-gradient(135deg, var(--gold-light), var(--gold));
         transform: translateY(-3px);
         box-shadow: 0 10px 30px rgba(201, 168, 76, 0.3);
      }

      .btn:active {
         transform: translateY(0);
      }

      /* --- FOOTER LINK --- */
      .form-container .footer-text {
         margin-top: 28px;
         font-size: 0.85rem;
         color: var(--muted);
      }

      .form-container .footer-text a {
         color: var(--gold-light);
         text-decoration: none;
         font-weight: 700;
         transition: color 0.2s;
      }

      .form-container .footer-text a:hover {
         color: var(--gold);
         text-decoration: underline;
      }

      /* --- ALERT MESSAGE --- */
      .message {
         position: fixed;
         top: 24px;
         left: 50%;
         transform: translateX(-50%);
         background: var(--surface);
         border: 1px solid rgba(231, 76, 60, 0.35);
         border-left: 4px solid #e74c3c;
         padding: 14px 22px;
         border-radius: 14px;
         display: flex;
         align-items: center;
         gap: 14px;
         z-index: 1000;
         min-width: 280px;
         box-shadow: 0 8px 30px rgba(0,0,0,0.4);
         animation: slideDown 0.4s ease;
      }

      @keyframes slideDown {
         from { opacity: 0; transform: translate(-50%, -16px); }
         to   { opacity: 1; transform: translate(-50%, 0); }
      }

      .message span {
         color: #e74c3c;
         font-weight: 600;
         font-size: 0.88rem;
      }

      .message i {
         cursor: pointer;
         color: var(--muted);
         margin-left: auto;
         transition: color 0.2s;
      }

      .message i:hover { color: var(--text); }

      /* --- DIVIDER --- */
      .divider {
         display: flex;
         align-items: center;
         gap: 12px;
         margin: 24px 0 0;
         color: var(--muted);
         font-size: 0.78rem;
      }

      .divider::before,
      .divider::after {
         content: '';
         flex: 1;
         height: 1px;
         background: var(--border);
      }
   </style>
</head>
<body>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<div class="form-container">

   <form action="" method="post">
      <h3>Selamat Datang</h3>
      <p class="subtitle">Silakan masuk ke akun Anda</p>

      <div class="input-group">
         <input type="email" name="email" placeholder="Alamat Email" required class="box">
      </div>

      <div class="input-group">
         <input type="password" name="password" placeholder="Kata Sandi" required class="box">
      </div>

      <input type="submit" name="submit" value="Masuk Sekarang" class="btn">

      <div class="divider">atau</div>

      <p class="footer-text">Belum punya akun? <a href="register.php">Daftar Sekarang</a></p>
   </form>

</div>

</body>
</html>