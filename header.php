<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="hdr-message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="hdr">

   <!-- Main navbar -->
   <div class="hdr-main">
      <div class="hdr-main-inner">

         <!-- Logo -->
         <a href="home.php" class="hdr-logo">Read<span>World.</span></a>

         <!-- Nav links -->
         <nav class="hdr-nav">
            <a href="home.php">Home</a>
            <a href="about.php">About</a>
            <a href="shop.php">Shop</a>
            <a href="contact.php">Contact</a>
            <a href="orders.php">Orders</a>
             <a href="login.php">Login</a>
            <span class="hdr-sep">|</span>
            <a href="register.php">Register</a>
         </nav>

         <!-- Icon group -->
         <div class="hdr-icons">
            <div id="menu-btn" class="fas fa-bars hdr-icon-btn"></div>
            <a href="search_page.php" class="fas fa-search hdr-icon-btn"></a>

            <div class="hdr-divider"></div>

            <!-- User dropdown -->
            <div class="hdr-user-wrap">
               <div id="user-btn" class="fas fa-user hdr-icon-btn" onclick="document.getElementById('hdr-user-panel').classList.toggle('open')"></div>
               <div class="hdr-user-panel" id="hdr-user-panel">
                  <p>username : <span><?php echo $_SESSION['user_name']; ?></span></p>
                  <p>email : <span><?php echo $_SESSION['user_email']; ?></span></p>
                  <a href="logout.php" class="hdr-logout-btn">Logout</a>
               </div>
            </div>

            <!-- Cart -->
            <div class="hdr-cart-wrap">
               <?php
                  $select_cart_number = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
                  $cart_rows_number = mysqli_num_rows($select_cart_number);
               ?>
               <a href="cart.php" class="hdr-icon-btn">
                  <i class="fas fa-shopping-cart"></i>
                  <?php if($cart_rows_number > 0): ?>
                  <span class="hdr-cart-badge"><?php echo $cart_rows_number; ?></span>
                  <?php endif; ?>
               </a>
            </div>
         </div>

      </div>
   </div>

</header>

<style>
/* ── Flash messages ── */
.hdr-message {
   display: flex;
   justify-content: space-between;
   align-items: center;
   padding: .55rem 1.5rem;
   background: #FFFBE6;
   color: #856404;
   border-bottom: 0.5px solid rgba(218,165,32,.3);
   font-size: 13px;
}
.hdr-message i {
   cursor: pointer;
   font-size: 13px;
   opacity: .6;
   color: #B8860B;
   transition: opacity .15s;
}
.hdr-message i:hover { opacity: 1; }

/* ── Top bar ── */
.hdr-topbar {
   background: #0f0e0b;
   border-bottom: 0.5px solid rgba(218,165,32,.1);
}
.hdr-topbar-inner {
   max-width: 1200px;
   margin: 0 auto;
   padding: .35rem 1.5rem;
   display: flex;
   justify-content: flex-end;
}
.hdr-topbar p {
   font-size: 12px;
   color: #888;
   display: flex;
   align-items: center;
   gap: .5rem;
}
.hdr-topbar a {
   color: #999;
   text-decoration: none;
   transition: color .15s;
}
.hdr-topbar a:hover { color: #DAA520; }
.hdr-sep { color: #333; }

/* ── Main navbar ── */
.hdr-main {
   background: #fff;
   border-bottom: 0.5px solid rgba(184,134,11,.2);
   position: sticky;
   top: 0;
   z-index: 999;
}
.hdr-main-inner {
   max-width: 1200px;
   margin: 0 auto;
   padding: 0 1.5rem;
   height: 62px;
   display: flex;
   align-items: center;
   justify-content: space-between;
   gap: 1.5rem;
}

/* ── Logo ── */
.hdr-logo {
   font-size: 20px;
   font-weight: 600;
   color: #1a1a1a;
   text-decoration: none;
   letter-spacing: -.5px;
   white-space: nowrap;
}
.hdr-logo span { color: #DAA520; }

/* ── Nav links ── */
.hdr-nav {
   display: flex;
   gap: 1.75rem;
   align-items: center;
}
.hdr-nav a {
   font-size: 13px;
   font-weight: 500;
   letter-spacing: .4px;
   color: #666;
   text-decoration: none;
   transition: color .15s;
}
.hdr-nav a:hover { color: #B8860B; }

/* ── Icon group ── */
.hdr-icons {
   display: flex;
   align-items: center;
   gap: 1.1rem;
}
.hdr-icon-btn {
   color: #666;
   font-size: 15px;
   cursor: pointer;
   text-decoration: none;
   transition: color .15s;
   display: flex;
   align-items: center;
}
.hdr-icon-btn:hover { color: #B8860B; }

.hdr-divider {
   width: 0.5px;
   height: 18px;
   background: rgba(184,134,11,.2);
}

/* ── User dropdown ── */
.hdr-user-wrap { position: relative; }

.hdr-user-panel {
   display: none;
   position: absolute;
   right: 0;
   top: calc(100% + 12px);
   background: #fff;
   border: 0.5px solid rgba(184,134,11,.25);
   border-radius: 10px;
   padding: .85rem 1.1rem;
   min-width: 210px;
   z-index: 1000;
}
.hdr-user-panel.open { display: block; }

.hdr-user-panel p {
   font-size: 13px;
   color: #888;
   padding: 3px 0;
}
.hdr-user-panel p span {
   color: #1a1a1a;
   font-weight: 500;
}
.hdr-logout-btn {
   display: block;
   margin-top: .75rem;
   text-align: center;
   background: linear-gradient(135deg, #B8860B, #DAA520);
   color: #fff !important;
   border-radius: 7px;
   padding: .45rem 0;
   font-size: 13px;
   font-weight: 500;
   text-decoration: none;
   transition: opacity .15s;
}
.hdr-logout-btn:hover { opacity: .88; }

/* ── Cart badge ── */
.hdr-cart-wrap { position: relative; }
.hdr-cart-badge {
   position: absolute;
   top: -7px;
   right: -9px;
   background: #DAA520;
   color: #fff;
   font-size: 10px;
   font-weight: 600;
   width: 17px;
   height: 17px;
   border-radius: 50%;
   display: flex;
   align-items: center;
   justify-content: center;
   line-height: 1;
}

/* ── Mobile: hide nav links, show hamburger ── */
@media (max-width: 768px) {
   .hdr-nav { display: none; }
   #menu-btn { display: block; }
}
@media (min-width: 769px) {
   #menu-btn { display: none; }
}
</style>

<script>
document.addEventListener('click', function(e) {
   var panel = document.getElementById('hdr-user-panel');
   var btn   = document.getElementById('user-btn');
   if (panel && !panel.contains(e.target) && e.target !== btn) {
      panel.classList.remove('open');
   }
});
</script>