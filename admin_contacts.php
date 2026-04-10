<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `message` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_contacts.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pesan Masuk - Admin</title>

   <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">

   <style>
      :root {
         --gold:        #c9a84c;
         --gold-light:  #f5e9c8;
         --gold-dark:   #a07830;
         --gold-subtle: #fdf6e3;
         --danger:      #dc2626;
         --danger-light:#fee2e2;
         --text-dark:   #1a1108;
         --text-mid:    #6b5e3e;
         --text-muted:  #9d8c6a;
         --bg:          #faf7f0;
         --surface:     #ffffff;
         --border:      rgba(201,168,76,.25);
         --radius:      12px;
         --shadow:      0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
         --shadow-md:   0 4px 12px rgba(0,0,0,.08);
      }

      body {
         font-family: 'Inter', sans-serif;
         background-color: var(--bg);
         margin: 0;
      }

      /* ── Page header bar ── */
      .messages-header {
         background: var(--surface);
         border-bottom: 1px solid var(--border);
         padding: 2rem 2.5rem;
         display: flex;
         align-items: center;
         gap: 1.2rem;
      }

      .messages-header .icon-wrap {
         width: 44px;
         height: 44px;
         background: var(--gold-light);
         border-radius: 10px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: var(--gold-dark);
         font-size: 1.8rem;
      }

      .messages-header h1 {
         font-size: 2rem;
         font-weight: 600;
         color: var(--text-dark);
         letter-spacing: -0.02em;
         margin: 0;
      }

      .messages-header .subtitle {
         font-size: 1.3rem;
         color: var(--text-muted);
         margin: 0;
      }

      /* ── Grid ── */
      .messages {
         padding: 2.5rem;
      }

      .messages .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(34rem, 1fr));
         gap: 1.6rem;
         max-width: 1200px;
         margin: 0 auto;
      }

      /* ── Card ── */
      .messages .box {
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: var(--radius);
         box-shadow: var(--shadow);
         overflow: hidden;
         transition: box-shadow .2s, transform .2s;
         display: flex;
         flex-direction: column;
      }

      .messages .box:hover {
         box-shadow: var(--shadow-md);
         transform: translateY(-2px);
      }

      /* gold top accent bar */
      .card-accent {
         height: 4px;
         background: var(--gold);
      }

      .card-body {
         padding: 2rem;
         flex: 1;
      }

      /* ── Info rows ── */
      .info-row {
         display: flex;
         align-items: flex-start;
         gap: 1rem;
         margin-bottom: 1.2rem;
         font-size: 1.45rem;
      }

      .info-row .icon-col {
         width: 30px;
         height: 30px;
         background: var(--gold-subtle);
         border-radius: 8px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: var(--gold-dark);
         font-size: 1.2rem;
         flex-shrink: 0;
      }

      .info-row .label {
         color: var(--text-muted);
         font-size: 1.2rem;
         margin-bottom: 2px;
      }

      .info-row .value {
         color: var(--text-dark);
         font-weight: 500;
         font-size: 1.45rem;
      }

      /* ── ID badge ── */
      .id-badge {
         display: inline-block;
         background: var(--gold-light);
         color: var(--gold-dark);
         padding: 2px 10px;
         border-radius: 999px;
         font-size: 1.2rem;
         font-weight: 600;
         letter-spacing: .02em;
      }

      /* ── Message block ── */
      .message-content {
         background: var(--gold-subtle);
         border-left: 3px solid var(--gold);
         border-radius: 0 8px 8px 0;
         padding: 1.2rem 1.4rem;
         margin-top: 0.8rem;
         font-size: 1.4rem;
         color: var(--text-mid);
         line-height: 1.7;
         font-style: italic;
      }

      /* ── Delete button ── */
      .card-footer {
         padding: 1.2rem 2rem;
         border-top: 1px solid var(--border);
         background: #fdf9f2;
      }

      .delete-btn {
         display: flex;
         align-items: center;
         justify-content: center;
         gap: .6rem;
         width: 100%;
         padding: .85rem 1.5rem;
         background: transparent;
         color: var(--danger);
         border: 1px solid var(--danger);
         border-radius: 8px;
         font-size: 1.4rem;
         font-weight: 500;
         cursor: pointer;
         text-decoration: none;
         transition: background .15s, color .15s;
         font-family: 'Inter', sans-serif;
      }

      .delete-btn:hover {
         background: var(--danger);
         color: #fff;
      }

      /* ── Empty state ── */
      .empty-state {
         grid-column: 1 / -1;
         background: var(--surface);
         border: 1px solid var(--border);
         border-radius: var(--radius);
         padding: 4rem 2rem;
         text-align: center;
      }

      .empty-state .empty-icon {
         width: 56px;
         height: 56px;
         background: var(--gold-light);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 1.5rem;
         color: var(--gold-dark);
         font-size: 2.2rem;
      }

      .empty-state p {
         font-size: 1.7rem;
         color: var(--text-muted);
         margin: 0;
      }
   </style>
</head>
<body>

<?php include 'admin_header.php'; ?>

<!-- Page header -->
<div class="messages-header">
   <div class="icon-wrap">
      <i class="fas fa-envelope-open-text"></i>
   </div>
   <div>
      <h1>Pesan Masuk</h1>
      <p class="subtitle">Kelola semua pesan dari pelanggan</p>
   </div>
</div>

<section class="messages">
   <div class="box-container">

   <?php
      $select_message = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
      if(mysqli_num_rows($select_message) > 0){
         while($fetch_message = mysqli_fetch_assoc($select_message)){
   ?>

   <div class="box">
      <div class="card-accent"></div>
      <div class="card-body">

         <!-- ID -->
         <div class="info-row">
            <div class="icon-col"><i class="fas fa-hashtag"></i></div>
            <div>
               <div class="label">ID User</div>
               <div class="value"><span class="id-badge"><?php echo $fetch_message['user_id']; ?></span></div>
            </div>
         </div>

         <!-- Nama -->
         <div class="info-row">
            <div class="icon-col"><i class="fas fa-user"></i></div>
            <div>
               <div class="label">Nama</div>
               <div class="value"><?php echo $fetch_message['name']; ?></div>
            </div>
         </div>

         <!-- Kontak -->
         <div class="info-row">
            <div class="icon-col"><i class="fas fa-phone"></i></div>
            <div>
               <div class="label">Kontak</div>
               <div class="value"><?php echo $fetch_message['number']; ?></div>
            </div>
         </div>

         <!-- Email -->
         <div class="info-row">
            <div class="icon-col"><i class="fas fa-at"></i></div>
            <div>
               <div class="label">Email</div>
               <div class="value"><?php echo $fetch_message['email']; ?></div>
            </div>
         </div>

         <!-- Isi Pesan -->
         <div style="font-size:1.3rem; color:var(--text-muted); margin-bottom:.5rem; font-weight:500;">
            <i class="fas fa-comment-dots" style="margin-right:.5rem; color:var(--gold);"></i> Isi Pesan
         </div>
         <div class="message-content">
            "<?php echo $fetch_message['message']; ?>"
         </div>

      </div>

      <div class="card-footer">
         <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>"
            onclick="return confirm('Hapus pesan ini?');"
            class="delete-btn">
            <i class="fas fa-trash-alt"></i> Hapus Pesan
         </a>
      </div>
   </div>

   <?php
         };
      } else {
   ?>
      <div class="empty-state">
         <div class="empty-icon"><i class="fas fa-folder-open"></i></div>
         <p>Belum ada pesan masuk!</p>
      </div>
   <?php
      }
   ?>

   </div>
</section>

<script src="js/admin_script.js"></script>

</body>
</html>