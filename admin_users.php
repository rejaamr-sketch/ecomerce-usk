<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manajemen User - Admin</title>

   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      :root {
         --gold-50:  #FFFBF0;
         --gold-100: #FFF3CC;
         --gold-200: #FFE58A;
         --gold-400: #F0C040;
         --gold-500: #D4A017;
         --gold-600: #B8860B;
         --gold-700: #9A6F00;
         --gold-800: #7A5500;
         --gold-900: #4A3200;

         --dark:    #1E1A10;
         --surface: #FFFFFF;
         --bg:      #FBF7EE;
         --border:  rgba(180, 140, 20, 0.18);
         --shadow:  0 4px 20px rgba(180, 140, 20, 0.10);
         --radius:  1.2rem;
      }

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
         font-family: 'Poppins', sans-serif;
         background-color: var(--bg);
         color: var(--dark);
      }

      /* ── Page Header ── */
      .page-header {
         display: flex;
         align-items: center;
         gap: 1rem;
         padding: 2.5rem 3rem 0;
      }

      .page-header .icon-wrap {
         width: 4.8rem;
         height: 4.8rem;
         border-radius: 1rem;
         background: var(--gold-400);
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 2rem;
         color: var(--gold-900);
         flex-shrink: 0;
      }

      .page-header h1 {
         font-size: 2.4rem;
         font-weight: 700;
         color: var(--gold-700);
         letter-spacing: .02em;
      }

      .page-header span {
         font-size: 1.4rem;
         color: var(--gold-600);
         font-weight: 400;
      }

      /* ── Stats Cards ── */
      .users-section {
         padding: 2.5rem 3rem;
      }

      .stats-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(20rem, 1fr));
         gap: 1.6rem;
         margin-bottom: 2.8rem;
      }

      .stat-card {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: 1.4rem;
         padding: 2rem 2.2rem;
         display: flex;
         align-items: center;
         gap: 1.6rem;
         box-shadow: var(--shadow);
         transition: transform .25s, box-shadow .25s;
         position: relative;
         overflow: hidden;
      }

      .stat-card::before {
         content: '';
         position: absolute;
         top: 0; left: 0; right: 0;
         height: 3px;
         background: var(--gold-400);
      }

      .stat-card:hover {
         transform: translateY(-4px);
         box-shadow: 0 10px 28px rgba(180,140,20,.15);
      }

      .stat-icon {
         width: 5rem;
         height: 5rem;
         border-radius: 1rem;
         background: var(--gold-100);
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 2rem;
         color: var(--gold-600);
         flex-shrink: 0;
      }

      .stat-icon.admin {
         background: var(--dark);
         color: var(--gold-400);
      }

      .stat-info label {
         display: block;
         font-size: 1.2rem;
         font-weight: 500;
         color: var(--gold-600);
         text-transform: uppercase;
         letter-spacing: .08em;
         margin-bottom: .3rem;
      }

      .stat-info strong {
         display: block;
         font-size: 3.2rem;
         font-weight: 700;
         color: var(--gold-800);
         line-height: 1;
      }

      /* ── Table Card ── */
      .table-card {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: 1.8rem;
         overflow: hidden;
         box-shadow: var(--shadow);
      }

      .table-toolbar {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 1.8rem 2.4rem;
         border-bottom: 1px solid var(--border);
         background: var(--gold-50);
         flex-wrap: wrap;
         gap: 1rem;
      }

      .toolbar-left {
         display: flex;
         align-items: center;
         gap: .8rem;
      }

      .toolbar-left h2 {
         font-size: 1.6rem;
         font-weight: 600;
         color: var(--gold-800);
      }

      .count-pill {
         background: var(--gold-100);
         color: var(--gold-700);
         border: 1px solid var(--gold-200);
         font-size: 1.2rem;
         font-weight: 600;
         padding: .3rem 1rem;
         border-radius: 2rem;
      }

      .search-box {
         display: flex;
         align-items: center;
         gap: .8rem;
         background: var(--surface);
         border: 1.5px solid var(--border);
         border-radius: .8rem;
         padding: .8rem 1.4rem;
         transition: border-color .2s;
      }

      .search-box:focus-within {
         border-color: var(--gold-500);
      }

      .search-box i {
         color: var(--gold-400);
         font-size: 1.4rem;
      }

      .search-box input {
         border: none;
         outline: none;
         font-size: 1.4rem;
         font-family: 'Poppins', sans-serif;
         color: var(--dark);
         background: transparent;
         width: 18rem;
      }

      .search-box input::placeholder { color: #C0B090; }

      /* ── Table ── */
      .users-table {
         width: 100%;
         border-collapse: collapse;
         font-size: 1.45rem;
      }

      .users-table thead tr {
         background: var(--dark);
      }

      .users-table thead th {
         padding: 1.4rem 2rem;
         text-align: left;
         font-size: 1.15rem;
         font-weight: 500;
         text-transform: uppercase;
         letter-spacing: .1em;
         color: rgba(255,255,255,.5);
         white-space: nowrap;
      }

      .users-table thead th:first-child {
         color: var(--gold-400);
      }

      .users-table tbody tr {
         border-bottom: 1px solid var(--border);
         transition: background .15s;
      }

      .users-table tbody tr:last-child { border-bottom: none; }

      .users-table tbody tr:hover { background: var(--gold-50); }

      .users-table tbody td {
         padding: 1.4rem 2rem;
         vertical-align: middle;
         color: var(--dark);
      }

      /* ID Badge */
      .uid-badge {
         display: inline-block;
         background: var(--gold-100);
         color: var(--gold-700);
         font-size: 1.2rem;
         font-weight: 600;
         padding: .35rem .9rem;
         border-radius: .5rem;
         border: 1px solid var(--gold-200);
         letter-spacing: .04em;
      }

      /* Avatar */
      .user-cell {
         display: flex;
         align-items: center;
         gap: 1.2rem;
      }

      .user-avatar {
         width: 3.8rem;
         height: 3.8rem;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.5rem;
         font-weight: 700;
         flex-shrink: 0;
         text-transform: uppercase;
      }

      .avatar-user  { background: var(--gold-100); color: var(--gold-700); border: 1.5px solid var(--gold-200); }
      .avatar-admin { background: var(--dark);     color: var(--gold-400); border: 1.5px solid #444; }

      .user-name {
         font-weight: 500;
         color: var(--dark);
         font-size: 1.5rem;
      }

      /* Email */
      .email-text {
         color: #7A6A40;
         font-size: 1.4rem;
      }

      /* Type Badge */
      .type-badge {
         display: inline-flex;
         align-items: center;
         gap: .5rem;
         padding: .4rem 1.1rem;
         border-radius: 2rem;
         font-size: 1.2rem;
         font-weight: 600;
         text-transform: uppercase;
         letter-spacing: .06em;
      }

      .type-admin {
         background: var(--dark);
         color: var(--gold-400);
         border: 1px solid #555;
      }

      .type-user {
         background: var(--gold-100);
         color: var(--gold-700);
         border: 1px solid var(--gold-200);
      }

      /* Delete Button */
      .btn-delete {
         display: inline-flex;
         align-items: center;
         gap: .5rem;
         padding: .7rem 1.4rem;
         font-size: 1.3rem;
         font-weight: 500;
         font-family: 'Poppins', sans-serif;
         background: #fff5f5;
         color: #b91c1c;
         border: 1.5px solid #fecaca;
         border-radius: .7rem;
         cursor: pointer;
         transition: .2s;
         text-decoration: none;
      }

      .btn-delete:hover {
         background: #fee2e2;
         border-color: #f87171;
      }

      /* Empty State */
      .empty-state {
         text-align: center;
         padding: 6rem 2rem;
      }

      .empty-state .empty-icon {
         font-size: 5rem;
         color: var(--gold-200);
         margin-bottom: 1.5rem;
      }

      .empty-state p {
         font-size: 1.8rem;
         color: var(--gold-600);
         font-weight: 500;
      }

      .empty-state small {
         font-size: 1.4rem;
         color: #B0A080;
      }

      /* Responsive */
      @media (max-width: 768px) {
         .users-section { padding: 2rem 1.5rem; }
         .table-card { overflow-x: auto; }
         .users-table { min-width: 640px; }
         .search-box input { width: 12rem; }
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- Page Header -->
<div class="page-header">
   <div class="icon-wrap"><i class="fas fa-users"></i></div>
   <div>
      <h1>Manajemen User</h1>
      <span>Kelola semua akun pengguna</span>
   </div>
</div>

<section class="users-section">

   <?php
      $all_users   = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
      $all_admins  = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
      $all_normal  = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
      $total       = mysqli_num_rows($all_users);
      $total_admin = mysqli_num_rows($all_admins);
      $total_user  = mysqli_num_rows($all_normal);
   ?>

   <!-- Stats -->
   <div class="stats-grid">
      <div class="stat-card">
         <div class="stat-icon"><i class="fas fa-users"></i></div>
         <div class="stat-info">
            <label>Total Akun</label>
            <strong><?php echo $total; ?></strong>
         </div>
      </div>
      <div class="stat-card">
         <div class="stat-icon"><i class="fas fa-user"></i></div>
         <div class="stat-info">
            <label>User Biasa</label>
            <strong><?php echo $total_user; ?></strong>
         </div>
      </div>
      <div class="stat-card">
         <div class="stat-icon admin"><i class="fas fa-user-shield"></i></div>
         <div class="stat-info">
            <label>Admin</label>
            <strong><?php echo $total_admin; ?></strong>
         </div>
      </div>
   </div>

   <!-- Table Card -->
   <div class="table-card">

      <div class="table-toolbar">
         <div class="toolbar-left">
            <h2><i class="fas fa-list" style="color:var(--gold-400);margin-right:.5rem;font-size:1.4rem;"></i>Daftar Akun</h2>
            <span class="count-pill"><?php echo $total; ?> akun</span>
         </div>
         <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama / email...">
         </div>
      </div>

      <table class="users-table" id="usersTable">
         <thead>
            <tr>
               <th>#</th>
               <th>User</th>
               <th>Email</th>
               <th>Tipe</th>
               <th>Aksi</th>
            </tr>
         </thead>
         <tbody>
            <?php
               $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
               while($fetch_users = mysqli_fetch_assoc($select_users)):
                  $is_admin   = $fetch_users['user_type'] === 'admin';
                  $initials   = strtoupper(substr($fetch_users['name'], 0, 1));
                  $avatar_cls = $is_admin ? 'avatar-admin' : 'avatar-user';
                  $type_cls   = $is_admin ? 'type-admin'   : 'type-user';
                  $type_icon  = $is_admin ? 'fa-user-shield' : 'fa-user';
            ?>
            <tr>
               <td><span class="uid-badge">#<?php echo $fetch_users['id']; ?></span></td>

               <td>
                  <div class="user-cell">
                     <div class="user-avatar <?php echo $avatar_cls; ?>"><?php echo $initials; ?></div>
                     <span class="user-name"><?php echo $fetch_users['name']; ?></span>
                  </div>
               </td>

               <td><span class="email-text"><?php echo $fetch_users['email']; ?></span></td>

               <td>
                  <span class="type-badge <?php echo $type_cls; ?>">
                     <i class="fas <?php echo $type_icon; ?>" style="font-size:1.1rem;"></i>
                     <?php echo $fetch_users['user_type']; ?>
                  </span>
               </td>

               <td>
                  <a href="admin_users.php?delete=<?php echo $fetch_users['id']; ?>"
                     onclick="return confirm('Hapus user ini?');"
                     class="btn-delete">
                     <i class="fas fa-trash"></i> Hapus
                  </a>
               </td>
            </tr>
            <?php endwhile; ?>
         </tbody>
      </table>

      <?php if($total == 0): ?>
      <div class="empty-state">
         <div class="empty-icon"><i class="fas fa-users-slash"></i></div>
         <p>Belum ada user terdaftar</p>
         <small>Akun yang terdaftar akan muncul di sini</small>
      </div>
      <?php endif; ?>

   </div>
</section>

<script src="js/admin_script.js"></script>
<script>
   /* Live search filter — client side only, no PHP changed */
   const searchInput = document.getElementById('searchInput');
   const rows = document.querySelectorAll('#usersTable tbody tr');

   searchInput.addEventListener('input', function() {
      const q = this.value.toLowerCase();
      rows.forEach(row => {
         const text = row.innerText.toLowerCase();
         row.style.display = text.includes(q) ? '' : 'none';
      });
   });
</script>

</body>
</html>