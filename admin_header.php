<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<style>
   :root {
      --gold:        #c9a84c;
      --gold-light:  #f5e9c8;
      --gold-dark:   #a07830;
      --gold-subtle: #fdf6e3;
      --text-dark:   #1a1108;
      --text-mid:    #6b5e3e;
      --text-muted:  #9d8c6a;
      --bg:          #faf7f0;
      --surface:     #ffffff;
      --border:      rgba(201,168,76,.2);
      --danger:      #dc2626;
      --danger-light:#fee2e2;
      --radius:      10px;
      --shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
      --shadow-md:   0 6px 20px rgba(0,0,0,.08);
   }

   /* ── Message banner ── */
   .message {
      position: sticky;
      top: 0;
      background: var(--gold-light);
      border-bottom: 1px solid rgba(201,168,76,.3);
      padding: 1.2rem 2rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 1100;
      gap: 1rem;
   }
   .message span {
      font-size: 1.5rem;
      color: var(--gold-dark);
      font-weight: 500;
   }
   .message i {
      cursor: pointer;
      color: var(--gold-dark);
      font-size: 1.6rem;
      opacity: .7;
      transition: opacity .15s, transform .2s;
   }
   .message i:hover {
      opacity: 1;
      transform: rotate(90deg);
   }

   /* ── Header shell ── */
   .header {
      background: var(--surface);
      border-bottom: 1px solid var(--border);
      position: sticky;
      top: 0;
      z-index: 1000;
      box-shadow: var(--shadow);
   }

   .header .flex {
      display: flex;
      align-items: center;
      justify-content: space-between;
      max-width: 1200px;
      margin: 0 auto;
      height: 6.4rem;
      padding: 0 2rem;
      position: relative;
   }

   /* ── Logo ── */
   .header .flex .logo {
      display: inline-flex;
      align-items: center;
      gap: .7rem;
      font-size: 1.8rem;
      font-weight: 700;
      color: var(--text-dark);
      text-decoration: none;
      letter-spacing: -.02em;
   }
   .header .flex .logo .logo-icon {
      width: 34px;
      height: 34px;
      background: var(--gold);
      border-radius: 9px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 1.4rem;
   }
   .header .flex .logo span {
      color: var(--gold-dark);
      font-weight: 400;
   }

   /* ── Navbar ── */
   .header .flex .navbar {
      display: flex;
      align-items: center;
      gap: .2rem;
   }
   .header .flex .navbar a {
      font-size: 1.35rem;
      font-weight: 500;
      color: var(--text-muted);
      text-decoration: none;
      padding: .55rem 1rem;
      border-radius: 8px;
      transition: background .15s, color .15s;
      letter-spacing: .01em;
   }
   .header .flex .navbar a:hover {
      color: var(--gold-dark);
      background: var(--gold-light);
   }
   .header .flex .navbar a.active {
      color: var(--gold-dark);
      background: var(--gold-light);
      font-weight: 600;
   }

   /* ── Icons ── */
   .header .flex .icons {
      display: flex;
      align-items: center;
      gap: .4rem;
   }
   .header .flex .icons div {
      width: 36px;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.6rem;
      color: var(--text-mid);
      cursor: pointer;
      border-radius: 9px;
      transition: background .15s, color .15s;
   }
   .header .flex .icons div:hover {
      background: var(--gold-light);
      color: var(--gold-dark);
   }

   #menu-btn { display: none; }

   /* ── Account dropdown ── */
   .header .flex .account-box {
      position: absolute;
      top: calc(100% + .6rem);
      right: 2rem;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow-md);
      padding: 1.6rem;
      width: 26rem;
      display: none;
      animation: dropIn .15s ease;
      z-index: 999;
   }
   .header .flex .account-box.active { display: block; }

   @keyframes dropIn {
      from { transform: translateY(6px); opacity: 0; }
      to   { transform: translateY(0);   opacity: 1; }
   }

   /* gold top strip on dropdown */
   .account-box::before {
      content: '';
      display: block;
      height: 3px;
      background: var(--gold);
      border-radius: 4px 4px 0 0;
      margin: -1.6rem -1.6rem 1.4rem;
   }

   .account-avatar {
      width: 44px;
      height: 44px;
      background: var(--gold-light);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--gold-dark);
      font-size: 1.8rem;
      margin: 0 auto 1.2rem;
      border: 2px solid rgba(201,168,76,.3);
   }

   .account-box p {
      font-size: 1.35rem;
      color: var(--text-muted);
      padding: .4rem 0;
      border-bottom: 1px solid var(--border);
   }
   .account-box p:last-of-type { border-bottom: none; }
   .account-box p span {
      color: var(--text-dark);
      font-weight: 600;
      float: right;
      max-width: 60%;
      text-align: right;
      word-break: break-all;
   }

   .account-box .delete-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: .5rem;
      margin-top: 1.2rem;
      width: 100%;
      text-align: center;
      background: transparent;
      color: var(--danger);
      border: 1px solid var(--danger);
      font-size: 1.35rem;
      padding: .8rem;
      border-radius: 8px;
      text-decoration: none;
      font-weight: 500;
      transition: background .15s, color .15s;
   }
   .account-box .delete-btn:hover {
      background: var(--danger);
      color: #fff;
   }

   .account-box div.auth-links {
      margin-top: 1rem;
      font-size: 1.2rem;
      color: var(--text-muted);
      text-align: center;
   }
   .account-box div.auth-links a {
      color: var(--gold-dark);
      text-decoration: none;
      font-weight: 500;
   }
   .account-box div.auth-links a:hover { text-decoration: underline; }

   /* ── Responsive ── */
   @media (max-width: 768px) {
      #menu-btn { display: flex; }

      .header .flex .navbar {
         position: absolute;
         top: 100%;
         left: 0; right: 0;
         background: var(--surface);
         border-top: 1px solid var(--border);
         border-bottom: 1px solid var(--border);
         flex-direction: column;
         align-items: stretch;
         padding: .8rem 1rem;
         gap: .2rem;
         clip-path: polygon(0 0,100% 0,100% 0,0 0);
         transition: clip-path .2s ease;
      }
      .header .flex .navbar.active {
         clip-path: polygon(0 0,100% 0,100% 100%,0 100%);
      }
      .header .flex .navbar a {
         font-size: 1.6rem;
         padding: 1rem 1.2rem;
      }
   }
</style>

<header class="header">
   <div class="flex">

      <a href="admin_page.php" class="logo">
         <div class="logo-icon"><i class="fas fa-crown"></i></div>
         Admin<span>Panel</span>
      </a>

      <nav class="navbar">
         <a href="admin_page.php"><i class="fas fa-home" style="margin-right:.4rem;font-size:1.1rem"></i>Home</a>
         <a href="admin_products.php"><i class="fas fa-box" style="margin-right:.4rem;font-size:1.1rem"></i>Products</a>
         <a href="admin_orders.php"><i class="fas fa-receipt" style="margin-right:.4rem;font-size:1.1rem"></i>Orders</a>
         <a href="admin_users.php"><i class="fas fa-users" style="margin-right:.4rem;font-size:1.1rem"></i>Users</a>
         <a href="admin_contacts.php"><i class="fas fa-envelope" style="margin-right:.4rem;font-size:1.1rem"></i>Messages</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user-circle"></div>
      </div>

      <div class="account-box">
         <div class="account-avatar"><i class="fas fa-user-shield"></i></div>
         <p>Username: <span><?php echo $_SESSION['admin_name']; ?></span></p>
         <p>Email: <span><?php echo $_SESSION['admin_email']; ?></span></p>
         <a href="logout.php" class="delete-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
         <div class="auth-links">
            <a href="login.php">Login</a> &nbsp;|&nbsp; <a href="register.php">Register</a>
         </div>
      </div>

   </div>
</header>

<script>
   let userBtn = document.querySelector('#user-btn');
   let accountBox = document.querySelector('.header .flex .account-box');
   let menuBtn = document.querySelector('#menu-btn');
   let navbar = document.querySelector('.header .flex .navbar');

   userBtn.onclick = () => {
      accountBox.classList.toggle('active');
      navbar.classList.remove('active');
   }

   menuBtn.onclick = () => {
      navbar.classList.toggle('active');
      accountBox.classList.remove('active');
   }

   window.onscroll = () => {
      accountBox.classList.remove('active');
      navbar.classList.remove('active');
   }
</script>