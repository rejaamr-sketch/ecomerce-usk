<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Riwayat Pesanan | Premium Book Store</title>

   <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
         --border: rgba(201, 168, 76, 0.18);
         --border-hover: rgba(201, 168, 76, 0.45);
      }

      * { margin: 0; padding: 0; box-sizing: border-box; }

      body {
         font-family: 'Plus Jakarta Sans', sans-serif;
         background-color: var(--bg);
         color: var(--text);
         letter-spacing: -0.01em;
      }

      /* --- CONTAINER --- */
      .orders-container {
         max-width: 920px;
         margin: 80px auto;
         padding: 0 24px;
      }

      /* --- HEADER --- */
      .section-header {
         margin-bottom: 52px;
         display: flex;
         justify-content: space-between;
         align-items: flex-end;
      }

      .section-header h1 {
         font-size: 2.4rem;
         font-weight: 800;
         color: var(--text);
         letter-spacing: -1px;
         margin-bottom: 8px;
      }

      .section-header h1 span {
         color: var(--gold-light);
      }

      .section-header p {
         color: var(--muted);
         font-size: 0.9rem;
      }

      .header-icon {
         width: 56px; height: 56px;
         border-radius: 16px;
         background: rgba(201, 168, 76, 0.08);
         border: 1px solid var(--border);
         display: flex; align-items: center; justify-content: center;
         font-size: 1.4rem;
         color: var(--gold);
      }

      /* --- ORDER CARD --- */
      .order-card {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: 24px;
         margin-bottom: 28px;
         overflow: hidden;
         transition: all 0.3s ease;
         position: relative;
      }

      .order-card::before {
         content: '';
         position: absolute;
         top: 0; left: 8%; right: 8%;
         height: 1px;
         background: linear-gradient(90deg, transparent, var(--gold), transparent);
         opacity: 0;
         transition: opacity 0.3s;
      }

      .order-card:hover {
         transform: translateY(-5px);
         border-color: var(--border-hover);
      }

      .order-card:hover::before {
         opacity: 1;
      }

      /* --- CARD TOP --- */
      .card-top {
         padding: 22px 30px;
         border-bottom: 1px solid var(--border);
         display: flex;
         justify-content: space-between;
         align-items: center;
      }

      .order-id-pill {
         background: rgba(201, 168, 76, 0.1);
         border: 1px solid rgba(201, 168, 76, 0.25);
         padding: 5px 14px;
         border-radius: 100px;
         font-size: 0.78rem;
         font-weight: 700;
         color: var(--gold-light);
         letter-spacing: 0.5px;
      }

      .order-date {
         color: var(--muted);
         font-size: 0.82rem;
         font-weight: 600;
         display: flex;
         align-items: center;
         gap: 6px;
      }

      /* --- CARD BODY --- */
      .card-body {
         padding: 28px 30px;
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 28px;
      }

      .info-label {
         font-size: 0.72rem;
         font-weight: 700;
         text-transform: uppercase;
         letter-spacing: 0.08em;
         color: var(--gold);
         margin-bottom: 14px;
         display: flex;
         align-items: center;
         gap: 8px;
      }

      .info-value {
         font-size: 0.9rem;
         line-height: 1.65;
         font-weight: 500;
         color: var(--text);
      }

      .info-value .name-bold {
         font-weight: 800;
         font-size: 0.95rem;
         margin-bottom: 6px;
         color: var(--text);
      }

      .info-value .sub-info {
         color: var(--muted);
         font-size: 0.82rem;
         line-height: 1.8;
      }

      .product-box {
         background: var(--surface-2);
         padding: 16px 18px;
         border-radius: 14px;
         border: 1px solid var(--border);
      }

      .product-box .method-row {
         margin-top: 12px;
         padding-top: 12px;
         border-top: 1px solid var(--border);
         font-size: 0.78rem;
         color: var(--muted);
         display: flex;
         align-items: center;
         gap: 6px;
      }

      /* --- STATUS --- */
      .status-container {
         display: flex;
         align-items: center;
         gap: 9px;
         font-weight: 700;
         font-size: 0.82rem;
         padding: 6px 14px;
         border-radius: 100px;
      }

      .status-indicator {
         width: 8px; height: 8px;
         border-radius: 50%;
         position: relative;
         flex-shrink: 0;
      }

      .status-indicator::after {
         content: '';
         position: absolute;
         inset: 0;
         border-radius: 50%;
         background: inherit;
         animation: pulse 2s infinite;
      }

      @keyframes pulse {
         0%   { transform: scale(1); opacity: 0.8; }
         100% { transform: scale(2.8); opacity: 0; }
      }

      .pending .status-indicator  { background: #f59e0b; }
      .pending  { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.25); color: #fbbf24; }

      .completed .status-indicator { background: #10b981; }
      .completed { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.25); color: #34d399; }

      /* --- CARD BOTTOM --- */
      .card-bottom {
         padding: 20px 30px;
         background: var(--surface-2);
         border-top: 1px solid var(--border);
         display: flex;
         justify-content: space-between;
         align-items: center;
      }

      .total-label {
         color: var(--muted);
         font-weight: 600;
         font-size: 0.85rem;
         display: flex;
         align-items: center;
         gap: 8px;
      }

      .total-price {
         font-size: 1.4rem;
         font-weight: 800;
         color: var(--gold-light);
         letter-spacing: -0.5px;
      }

      /* --- EMPTY STATE --- */
      .empty-state {
         text-align: center;
         padding: 90px 0;
      }

      .empty-icon {
         width: 90px; height: 90px;
         border-radius: 28px;
         background: rgba(201, 168, 76, 0.07);
         border: 1px solid var(--border);
         display: flex; align-items: center; justify-content: center;
         margin: 0 auto 28px;
         font-size: 2.2rem;
      }

      .empty-state h3 {
         font-size: 1.4rem;
         font-weight: 800;
         color: var(--text);
         margin-bottom: 10px;
      }

      .empty-state p {
         color: var(--muted);
         font-size: 0.9rem;
         margin-bottom: 32px;
      }

      .btn-gold {
         display: inline-block;
         background: linear-gradient(135deg, var(--gold), var(--gold-dark));
         color: #0d0d0d;
         padding: 14px 36px;
         border-radius: 12px;
         font-weight: 800;
         font-size: 0.9rem;
         border: 1px solid var(--gold-light);
         transition: all 0.3s;
         text-decoration: none;
      }

      .btn-gold:hover {
         background: linear-gradient(135deg, var(--gold-light), var(--gold));
         transform: translateY(-3px);
         box-shadow: 0 10px 28px rgba(201,168,76,0.28);
      }

      /* --- RESPONSIVE --- */
      @media (max-width: 640px) {
         .card-body { grid-template-columns: 1fr; }
         .section-header h1 { font-size: 1.7rem; }
         .card-top { flex-direction: column; align-items: flex-start; gap: 14px; }
         .header-icon { display: none; }
      }
   </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="orders-container">

   <header class="section-header">
      <div>
         <h1>Riwayat <span>Pesanan</span></h1>
         <p>Pantau status pengiriman buku Anda</p>
      </div>
      <div class="header-icon">
         <i class="fas fa-box-archive"></i>
      </div>
   </header>

   <?php
      $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('query failed');
      if(mysqli_num_rows($order_query) > 0){
         while($fetch_orders = mysqli_fetch_assoc($order_query)){
            $status = strtolower($fetch_orders['payment_status']);
   ?>
   <div class="order-card">

      <div class="card-top">
         <div style="display:flex; align-items:center; gap:12px;">
            <span class="order-id-pill">#ORD-<?php echo substr(md5($fetch_orders['placed_on']), 0, 6); ?></span>
            <span class="order-date">
               <i class="far fa-calendar-alt"></i>
               <?php echo $fetch_orders['placed_on']; ?>
            </span>
         </div>
         <div class="status-container <?php echo $status; ?>">
            <div class="status-indicator"></div>
            <span><?php echo ucfirst($status); ?></span>
         </div>
      </div>

      <div class="card-body">

         <div>
            <div class="info-label"><i class="fas fa-truck-fast"></i> Informasi Pengiriman</div>
            <div class="info-value">
               <div class="name-bold"><?php echo $fetch_orders['name']; ?></div>
               <div class="sub-info">
                  <i class="fas fa-phone-flip"></i> <?php echo $fetch_orders['number']; ?><br>
                  <i class="fas fa-location-dot"></i> <?php echo $fetch_orders['address']; ?>
               </div>
            </div>
         </div>

         <div>
            <div class="info-label"><i class="fas fa-book"></i> Daftar Belanja</div>
            <div class="product-box">
               <div class="info-value" style="font-size:0.88rem;">
                  <?php echo $fetch_orders['total_products']; ?>
               </div>
               <div class="method-row">
                  <i class="fas fa-wallet"></i> Metode: <?php echo $fetch_orders['method']; ?>
               </div>
            </div>
         </div>

      </div>

      <div class="card-bottom">
         <span class="total-label"><i class="fas fa-receipt"></i> Total Pembayaran</span>
         <span class="total-price">Rp<?php echo number_format($fetch_orders['total_price'], 0, ',', '.'); ?></span>
      </div>

   </div>
   <?php
         }
      } else {
   ?>
   <div class="empty-state">
      <div class="empty-icon">📦</div>
      <h3>Belum ada riwayat pesanan</h3>
      <p>Yuk mulai temukan buku favoritmu di toko kami!</p>
      <a href="shop.php" class="btn-gold">Mulai Belanja</a>
   </div>
   <?php } ?>

</div>

<?php include 'footer.php'; ?>

</body>
</html>