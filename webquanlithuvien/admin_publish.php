<?php

   include 'config.php';

   session_start();

   $admin_id = $_SESSION['admin_id']; //tạo session admin

   if(!isset($admin_id)){// session không tồn tại => quay lại trang đăng nhập
      header('location:login.php');
   }

   if(isset($_POST['add_publish'])){//Thêm nhà xuất bản vào 

      $name = mysqli_real_escape_string($conn, $_POST['name']);
      $address = mysqli_real_escape_string($conn, $_POST['address']);
      $website = mysqli_real_escape_string($conn, $_POST['website']);

      $select_publish_name = mysqli_query($conn, "SELECT name FROM `publishs` WHERE name = '$name'") or die('query failed');//truy vấn để kiểm tra nhà xuất bản đã tồn tại chưa

      if(mysqli_num_rows($select_publish_name) > 0){// tồn tại rồi thì thông báo
         $message[] = 'Nhà xuất bản đã tồn tại.';
      }else{
         $add_publish_query = mysqli_query($conn, "INSERT INTO `publishs`(name, address, website) VALUES('$name', '$address', '$website')") or die('query failed');

         if($add_publish_query){
         $message[] = 'Thêm nhà xuất bản thành công!';
         }else{
            $message[] = 'Không thể thêm nhà xuất bản này!';
         }
      }
   }

   if(isset($_GET['delete'])){//Xóa nhà xuất bản từ onclick <a></a> có href='delete'
      $delete_id = $_GET['delete'];
      $delete_publish_query = mysqli_query($conn, "DELETE FROM `publishs` WHERE id = '$delete_id'") or die('query failed');

      if($delete_publish_query){
         $message[] = 'Xóa nhà xuất bản thành công!';
         }else{
            $message[] = 'Không thể xóa nhà xuất bản này!';
         }
      header('location:admin_publish.php');
   }

   if(isset($_POST['update_publish'])){//Cập nhật nhà xuất bản vào nhà xuất bản từ submit có name='update_publish'

      $update_p_id = $_POST['update_p_id'];
      $update_name = $_POST['update_name'];

      mysqli_query($conn, "UPDATE `publishs` SET name = '$update_name' WHERE id = '$update_p_id'") or die('query failed');

      header('location:admin_publish.php');

   }

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Nhà xuất bản</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="css/admin_style.css">
   <style>
      .add-products form {
         border: none !important;
         box-shadow: rgba(149, 157, 165, 0.2) 0px 8px 24px;
      }
      .box {
         border: 1px solid #3670EB !important;
         background-color: #fff !important;
      }
      h1, h3 {
         color: #3670EB !important;
      }
      .btn {
         background-color: #3670EB;
      }
      *::-webkit-scrollbar-thumb{
         background-color: unset !important;
      }
   </style>
</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="add-products">

   <h1 class="title">Nhà xuất bản</h1>
   <form action="" method="post" enctype="multipart/form-data">
      <h3>Thêm Nhà xuất bản</h3>
      <input type="text" name="name" class="box" placeholder="Nhập tên NXB" required>
      <input type="text" name="address" class="box" placeholder="Nhập địa chỉ NXB" required>
      <input type="text" name="website" class="box" placeholder="Nhập địa chỉ website NXB" required>
      <input type="submit" value="Thêm" name="add_publish" class="btn">
   </form>

</section>

<section class="show-products">

   <div class="box-container">

      <?php
         $select_publishs = mysqli_query($conn, "SELECT * FROM `publishs`") or die('query failed');
         if(mysqli_num_rows($select_publishs) > 0){
            while($fetch_publishs = mysqli_fetch_assoc($select_publishs)){
      ?>
      <div style="height: -webkit-fill-available;" class="box">
         <div class="name"><?php echo $fetch_publishs['name']; ?></div>
         <div class="name"><?php echo $fetch_publishs['address']; ?></div>
         <a href="admin_publish.php?update=<?php echo $fetch_publishs['id']; ?>" class="option-btn">Cập nhật</a>
         <a href="admin_publish.php?delete=<?php echo $fetch_publishs['id']; ?>" class="delete-btn" onclick="return confirm('Xóa nhà xuất bản này?');">Xóa</a>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">Không có nhà xuất bản nào được thêm!</p>';  
      }
      ?>
   </div>

</section>

<section class="edit-product-form">

   <?php
      if(isset($_GET['update'])){//Hiện form cập nhật thông tin nhà xuất bản từ <a></a> có href='update'
         $update_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM `publishs` WHERE id = '$update_id'") or die('query failed');//lấy ra thông tin nhà xuất bản cần cập nhật
         if(mysqli_num_rows($update_query) > 0){
            while($fetch_update = mysqli_fetch_assoc($update_query)){
   ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Tên">
                  <input type="submit" value="Cập nhật" name="update_publish" class="btn"> <!-- submit form cập nhật -->
                  <input type="reset" value="Hủy"  onclick="window.location.href = 'admin_publish.php'" class="option-btn">
               </form>
   <?php
            }
         }
      }else{
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
   ?>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>