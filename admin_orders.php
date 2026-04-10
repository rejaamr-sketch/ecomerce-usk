<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if(isset($_POST['update_order'])){
   $order_update_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_update_id'") or die('query failed');
   $message[] = 'payment status has been updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Orders - Admin</title>

   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

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
         --success-bg:  #ecfdf5;
         --success-text:#065f46;
         --success-bdr: #a7f3d0;
         --pending-bg:  #fffbeb;
         --pending-text:#92400e;
         --pending-bdr: #fde68a;
         --radius:      12px;
         --shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
         --shadow-md:   0 4px 16px rgba(0,0,0,.08);
      }

      body {
         font-family: 'Inter', sans-serif;
         background: var(--bg);
         margin: 0;
      }

      /* ── Page header ── */
      .orders-page-header {
         background: var(--surface);
         border-bottom: 1px solid var(--border);
         padding: 1.6rem 2.5rem;
         display: flex;
         align-items: center;
         justify-content: space-between;
         gap: 1rem;
         flex-wrap: wrap;
      }
      .orders-page-header .left {
         display: flex;
         align-items: center;
         gap: 1.2rem;
      }
      .orders-page-header .icon-wrap {
         width: 44px;
         height: 44px;
         background: var(--gold-light);
         border-radius: 10px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: var(--gold-dark);
         font-size: 1.8rem;
         border: 1px solid var(--border);
      }
      .orders-page-header h1 {
         font-size: 2rem;
         font-weight: 600;
         color: var(--text-dark);
         letter-spacing: -.02em;
         margin: 0;
      }
      .orders-page-header .subtitle {
         font-size: 1.3rem;
         color: var(--text-muted);
         margin: 0;
      }
      .orders-page-header .meta {
         font-size: 1.25rem;
         color: var(--text-muted);
         display: flex;
         align-items: center;
         gap: .5rem;
         background: var(--gold-subtle);
         border: 1px solid var(--border);
         border-radius: 8px;
         padding: .6rem 1.2rem;
      }
      .orders-page-header .meta i {
         color: var(--gold-dark);
         font-size: 1.2rem;
      }

      /* ── Section ── */
      .orders {
         padding: 2.5rem;
      }
      .orders-wrapper {
         max-width: 1300px;
         margin: 0 auto;
      }

      /* ── Table wrap ── */
      .orders-table-wrap {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: var(--radius);
         overflow: hidden;
         box-shadow: var(--shadow);
      }

      /* ── Summary bar ── */
      .orders-summary {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 1.1rem 1.8rem;
         background: var(--gold-subtle);
         border-bottom: 1px solid var(--border);
         font-size: 1.35rem;
         color: var(--text-muted);
         flex-wrap: wrap;
         gap: .5rem;
      }
      .orders-summary strong { color: var(--text-dark); }
      .orders-summary .count-pill {
         display: inline-flex;
         align-items: center;
         gap: .4rem;
         background: var(--gold-light);
         color: var(--gold-dark);
         padding: .3rem .9rem;
         border-radius: 999px;
         font-size: 1.2rem;
         font-weight: 600;
         border: 1px solid rgba(201,168,76,.3);
      }

      /* ── Table ── */
      .orders-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 1.4rem;
      }
      .orders-table thead {
         background: var(--text-dark);
      }
      .orders-table thead th {
         padding: 1.2rem 1.4rem;
         text-align: left;
         color: rgba(255,255,255,.55);
         font-size: 1.1rem;
         font-weight: 500;
         text-transform: uppercase;
         letter-spacing: .08em;
         white-space: nowrap;
      }
      .orders-table thead th:first-child {
         color: var(--gold);
      }
      .orders-table tbody tr {
         border-bottom: 1px solid rgba(201,168,76,.1);
         transition: background .15s;
      }
      .orders-table tbody tr:last-child { border-bottom: none; }
      .orders-table tbody tr:hover { background: #fdf9f1; }
      .orders-table tbody td {
         padding: 1.3rem 1.4rem;
         color: var(--text-dark);
         vertical-align: middle;
      }

      /* ── ID badge ── */
      .order-id-badge {
         display: inline-block;
         background: var(--gold-light);
         color: var(--gold-dark);
         font-size: 1.15rem;
         font-weight: 700;
         padding: .3rem .9rem;
         border-radius: 999px;
         letter-spacing: .04em;
         border: 1px solid rgba(201,168,76,.3);
      }
      .uid-label {
         font-size: 1.15rem;
         color: var(--text-muted);
         display: block;
         margin-top: .3rem;
      }

      /* ── Customer ── */
      .customer-name {
         font-weight: 500;
         color: var(--text-dark);
         display: block;
         font-size: 1.45rem;
      }
      .customer-sub {
         font-size: 1.25rem;
         color: var(--text-muted);
         display: block;
         margin-top: .2rem;
      }

      /* ── Price ── */
      .price-cell {
         font-weight: 600;
         color: var(--gold-dark);
         font-size: 1.5rem;
      }

      /* ── Status badge ── */
      .status-badge {
         display: inline-flex;
         align-items: center;
         gap: .4rem;
         padding: .35rem 1rem;
         border-radius: 999px;
         font-size: 1.15rem;
         font-weight: 500;
         text-transform: capitalize;
         letter-spacing: .02em;
         white-space: nowrap;
      }
      .status-badge::before {
         content: '';
         width: 6px;
         height: 6px;
         border-radius: 50%;
         flex-shrink: 0;
      }
      .status-pending {
         background: var(--pending-bg);
         color: var(--pending-text);
         border: 1px solid var(--pending-bdr);
      }
      .status-pending::before { background: var(--pending-text); }
      .status-completed {
         background: var(--success-bg);
         color: var(--success-text);
         border: 1px solid var(--success-bdr);
      }
      .status-completed::before { background: var(--success-text); }

      /* ── Actions ── */
      .actions-cell form {
         display: flex;
         align-items: center;
         gap: .6rem;
         flex-wrap: nowrap;
      }
      .actions-cell select {
         padding: .55rem .9rem;
         font-size: 1.3rem;
         border: 1px solid var(--border);
         border-radius: 8px;
         background: var(--gold-subtle);
         color: var(--text-dark);
         cursor: pointer;
         font-family: 'Inter', sans-serif;
         transition: border-color .15s;
         max-width: 12rem;
      }
      .actions-cell select:focus {
         border-color: var(--gold);
         outline: none;
      }
      .btn-update {
         width: 34px;
         height: 34px;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.3rem;
         background: var(--gold);
         color: #fff;
         border: none;
         border-radius: 8px;
         cursor: pointer;
         transition: background .15s, transform .15s;
         flex-shrink: 0;
      }
      .btn-update:hover {
         background: var(--gold-dark);
         transform: scale(1.05);
      }
      .btn-delete {
         width: 34px;
         height: 34px;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.3rem;
         background: transparent;
         color: var(--danger);
         border: 1px solid var(--danger);
         border-radius: 8px;
         cursor: pointer;
         text-decoration: none;
         transition: background .15s, color .15s, transform .15s;
         flex-shrink: 0;
      }
      .btn-delete:hover {
         background: var(--danger);
         color: #fff;
         transform: scale(1.05);
      }

      /* ── Empty state ── */
      .empty-state {
         text-align: center;
         padding: 6rem 2rem;
         color: var(--text-muted);
      }
      .empty-icon {
         width: 60px;
         height: 60px;
         background: var(--gold-light);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 1.5rem;
         color: var(--gold-dark);
         font-size: 2.2rem;
         border: 1px solid var(--border);
      }
      .empty-state p {
         font-size: 1.7rem;
         color: var(--text-muted);
         margin: 0;
      }

      /* ── Responsive ── */
      @media (max-width: 900px) {
         .orders-table-wrap { overflow-x: auto; }
         .orders-table { min-width: 900px; }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- Page header -->
<div class="orders-page-header">
   <div class="left">
      <div class="icon-wrap"><i class="fas fa-receipt"></i></div>
      <div>
         <h1>Placed Orders</h1>
         <p class="subtitle">Kelola dan perbarui status pembayaran order</p>
      </div>
   </div>
   <div class="meta">
      <i class="fas fa-clock"></i>
      <?php date_default_timezone_set('Asia/Jakarta'); echo date('d M Y, H:i'); ?> WIB
   </div>
</div>

<section class="orders">
   <div class="orders-wrapper">

   <?php
      $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
      $total_orders = mysqli_num_rows($select_orders);
   ?>

   <?php if($total_orders > 0): ?>

   <div class="orders-table-wrap">

      <!-- Summary bar -->
      <div class="orders-summary">
         <span>
            Total <span class="count-pill"><i class="fas fa-box"></i> <?php echo $total_orders; ?> order</span>
         </span>
         <span>Klik <i class="fas fa-check" style="color:var(--gold-dark)"></i> untuk update &nbsp;|&nbsp; <i class="fas fa-trash" style="color:var(--danger)"></i> untuk hapus</span>
      </div>

      <table class="orders-table">
         <thead>
            <tr>
               <th>#</th>
               <th>Customer</th>
               <th>Kontak</th>
               <th>Alamat</th>
               <th>Produk</th>
               <th>Total</th>
               <th>Metode</th>
               <th>Tanggal</th>
               <th>Status</th>
               <th>Aksi</th>
            </tr>
         </thead>
         <tbody>
         <?php while($fetch_orders = mysqli_fetch_assoc($select_orders)): ?>
            <tr>
               <!-- ID -->
               <td>
                  <span class="order-id-badge">#<?php echo $fetch_orders['id']; ?></span>
                  <span class="uid-label">UID: <?php echo $fetch_orders['user_id']; ?></span>
               </td>

               <!-- Customer -->
               <td>
                  <span class="customer-name"><?php echo $fetch_orders['name']; ?></span>
               </td>

               <!-- Kontak -->
               <td>
                  <span class="customer-name" style="font-weight:400;"><?php echo $fetch_orders['email']; ?></span>
                  <span class="customer-sub"><?php echo $fetch_orders['number']; ?></span>
               </td>

               <!-- Alamat -->
               <td style="max-width:18rem;">
                  <span style="font-size:1.35rem;color:var(--text-mid);line-height:1.5;display:block;"><?php echo $fetch_orders['address']; ?></span>
               </td>

               <!-- Total produk -->
               <td style="text-align:center;">
                  <span style="font-size:1.5rem;font-weight:600;color:var(--text-dark);"><?php echo $fetch_orders['total_products']; ?></span>
               </td>

               <!-- Total harga -->
               <td>
                  <span class="price-cell">Rp<?php echo $fetch_orders['total_price']; ?></span>
               </td>

               <!-- Metode -->
               <td>
                  <span style="font-size:1.35rem;text-transform:capitalize;color:var(--text-mid);"><?php echo $fetch_orders['method']; ?></span>
               </td>

               <!-- Tanggal -->
               <td>
                  <span style="font-size:1.25rem;color:var(--text-muted);white-space:nowrap;"><?php echo $fetch_orders['placed_on']; ?></span>
               </td>

               <!-- Status -->
               <td>
                  <?php
                     $status = $fetch_orders['payment_status'];
                     $badge_class = ($status === 'completed') ? 'status-completed' : 'status-pending';
                  ?>
                  <span class="status-badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
               </td>

               <!-- Aksi -->
               <td class="actions-cell">
                  <form action="" method="post">
                     <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                     <select name="update_payment">
                        <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
                        <option value="pending">pending</option>
                        <option value="completed">completed</option>
                     </select>
                     <button type="submit" name="update_order" class="btn-update" title="Update status">
                        <i class="fas fa-check"></i>
                     </button>
                     <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>"
                        onclick="return confirm('Hapus order ini?');"
                        class="btn-delete" title="Hapus order">
                        <i class="fas fa-trash"></i>
                     </a>
                  </form>
               </td>
            </tr>
         <?php endwhile; ?>
         </tbody>
      </table>
   </div>

   <?php else: ?>

   <div class="orders-table-wrap">
      <div class="empty-state">
         <div class="empty-icon"><i class="fas fa-box-open"></i></div>
         <p>Belum ada order yang masuk.</p>
      </div>
   </div>

   <?php endif; ?>

   </div>
</section>

<script src="js/admin_script.js"></script>

</body>
</html>