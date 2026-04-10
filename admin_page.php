<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard</title>

   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css?v=2">

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
         --radius:      14px;
         --shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
         --shadow-md:   0 6px 20px rgba(0,0,0,.08);
      }

      body {
         font-family: 'Inter', sans-serif;
         background: var(--bg);
         margin: 0;
      }

      .dash-header {
         background: var(--surface);
         border-bottom: 1px solid var(--border);
         padding: 1.8rem 2.5rem;
         display: flex;
         align-items: center;
         justify-content: space-between;
         flex-wrap: wrap;
         gap: 1rem;
      }
      .dash-header .left {
         display: flex;
         align-items: center;
         gap: 1.2rem;
      }
      .dash-header .icon-wrap {
         width: 46px;
         height: 46px;
         background: var(--gold);
         border-radius: 12px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: #fff;
         font-size: 1.9rem;
      }
      .dash-header h1 {
         font-size: 2rem;
         font-weight: 600;
         color: var(--text-dark);
         letter-spacing: -.02em;
         margin: 0;
      }
      .dash-header .subtitle {
         font-size: 1.3rem;
         color: var(--text-muted);
         margin: 0;
      }
      .dash-header .greeting {
         font-size: 1.3rem;
         color: var(--text-muted);
         background: var(--gold-subtle);
         border: 1px solid var(--border);
         border-radius: 8px;
         padding: .6rem 1.2rem;
         display: flex;
         align-items: center;
         gap: .6rem;
      }
      .dash-header .greeting i { color: var(--gold-dark); font-size: 1.2rem; }
      .dash-header .greeting strong { color: var(--text-dark); }

      .dashboard {
         padding: 2.5rem;
         max-width: 1200px;
         margin: 0 auto;
      }

      .section-label {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1.8rem;
      }
      .section-label .bar {
         width: 4px;
         height: 20px;
         background: var(--gold);
         border-radius: 999px;
      }
      .section-label span {
         font-size: 1.3rem;
         font-weight: 600;
         color: var(--text-muted);
         text-transform: uppercase;
         letter-spacing: .08em;
      }

      .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(24rem, 1fr));
         gap: 1.4rem;
      }

      .box {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: var(--radius);
         box-shadow: var(--shadow);
         padding: 2rem 2rem 1.8rem;
         display: flex;
         flex-direction: column;
         gap: .6rem;
         position: relative;
         overflow: hidden;
         transition: box-shadow .2s, transform .2s;
      }
      .box:hover {
         box-shadow: var(--shadow-md);
         transform: translateY(-2px);
      }

      .box::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0;
         height: 3px;
         border-radius: var(--radius) var(--radius) 0 0;
      }

      .box .bg-icon {
         position: absolute;
         right: -8px;
         bottom: -8px;
         font-size: 7rem;
         color: rgba(0,0,0,.04);
         pointer-events: none;
      }

      .box .card-icon {
         width: 38px;
         height: 38px;
         border-radius: 10px;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.5rem;
         margin-bottom: .4rem;
         flex-shrink: 0;
      }

      .box h3 {
         font-size: 2.6rem;
         font-weight: 700;
         color: var(--text-dark);
         letter-spacing: -.03em;
         line-height: 1;
         margin: 0;
      }

      .box p {
         font-size: 1.2rem;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: .08em;
         margin: 0;
      }

      .box.pending::before  { background: #f59e0b; }
      .box.pending .card-icon { background: #fffbeb; color: #92400e; }
      .box.pending p         { color: #92400e; }

      .box.completed::before  { background: #10b981; }
      .box.completed .card-icon { background: #ecfdf5; color: #065f46; }
      .box.completed p         { color: #065f46; }

      .box.orders::before  { background: var(--gold); }
      .box.orders .card-icon { background: var(--gold-light); color: var(--gold-dark); }
      .box.orders p         { color: var(--gold-dark); }

      .box.products::before  { background: #3b82f6; }
      .box.products .card-icon { background: #eff6ff; color: #1d4ed8; }
      .box.products p         { color: #1d4ed8; }

      .box.accounts::before  { background: #64748b; }
      .box.accounts .card-icon { background: #f1f5f9; color: #334155; }
      .box.accounts p         { color: #334155; }

      .box.messages::before  { background: #ef4444; }
      .box.messages .card-icon { background: #fef2f2; color: #991b1b; }
      .box.messages p         { color: #991b1b; }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- Page header -->
<div class="dash-header">
   <div class="left">
      <div class="icon-wrap"><i class="fas fa-chart-pie"></i></div>
      <div>
         <h1>Dashboard</h1>
         <p class="subtitle">Ringkasan statistik toko buku</p>
      </div>
   </div>
   <div class="greeting">
      <i class="fas fa-user-shield"></i>
      Halo, <strong><?php echo $_SESSION['admin_name']; ?></strong>
   </div>
</div>

<section class="dashboard">

   <div class="section-label">
      <div class="bar"></div>
      <span>Ringkasan Statistik</span>
   </div>

   <div class="box-container">

      <!-- Pending -->
      <div class="box pending">
         <?php
            $total_pendings = 0;
            $select_pending = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'pending'") or die('query failed');
            while($fetch_pendings = mysqli_fetch_assoc($select_pending)){
               $total_pendings += $fetch_pendings['total_price'];
            };
         ?>
         <div class="card-icon"><i class="fas fa-clock"></i></div>
         <h3>Rp <?php echo number_format($total_pendings, 0, ',', '.'); ?></h3>
         <p>Total Pendings</p>
         <i class="fas fa-clock bg-icon"></i>
      </div>

      <!-- Completed -->
      <div class="box completed">
         <?php
            $total_completed = 0;
            $select_completed = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'completed'") or die('query failed');
            while($fetch_completed = mysqli_fetch_assoc($select_completed)){
               $total_completed += $fetch_completed['total_price'];
            };
         ?>
         <div class="card-icon"><i class="fas fa-wallet"></i></div>
         <h3>Rp <?php echo number_format($total_completed, 0, ',', '.'); ?></h3>
         <p>Total Penghasilan</p>
         <i class="fas fa-wallet bg-icon"></i>
      </div>

      <!-- Orders -->
      <div class="box orders">
         <?php
            $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
         <div class="card-icon"><i class="fas fa-shopping-cart"></i></div>
         <h3><?php echo $number_of_orders; ?></h3>
         <p>Pesanan Masuk</p>
         <i class="fas fa-shopping-cart bg-icon"></i>
      </div>

      <!-- Products -->
      <div class="box products">
         <?php
            $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <div class="card-icon"><i class="fas fa-book"></i></div>
         <h3><?php echo $number_of_products; ?></h3>
         <p>Jumlah Produk</p>
         <i class="fas fa-book bg-icon"></i>
      </div>

      <!-- Users biasa -->
      <div class="box accounts">
         <?php
            $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <div class="card-icon"><i class="fas fa-users"></i></div>
         <h3><?php echo $number_of_users; ?></h3>
         <p>User Biasa</p>
         <i class="fas fa-users bg-icon"></i>
      </div>

      <div class="box accounts">
         <?php
            $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
            $number_of_admins = mysqli_num_rows($select_admins);
         ?>
         <div class="card-icon"><i class="fas fa-user-shield"></i></div>
         <h3><?php echo $number_of_admins; ?></h3>
         <p>Admin</p>
         <i class="fas fa-user-shield bg-icon"></i>
      </div>

      <div class="box accounts">
         <?php
            $select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
            $number_of_account = mysqli_num_rows($select_account);
         ?>
         <div class="card-icon"><i class="fas fa-user-circle"></i></div>
         <h3><?php echo $number_of_account; ?></h3>
         <p>Total Akun</p>
         <i class="fas fa-user-circle bg-icon"></i>
      </div>

      <!-- Pesan -->
      <div class="box messages">
         <?php
            $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
            $number_of_messages = mysqli_num_rows($select_messages);
         ?>
         <div class="card-icon"><i class="fas fa-envelope"></i></div>
         <h3><?php echo $number_of_messages; ?></h3>
         <p>Pesan Baru</p>
         <i class="fas fa-envelope bg-icon"></i>
      </div>

   </div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>