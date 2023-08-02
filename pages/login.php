<?php 
require_once '../config.php';
require_once SHP_PATH . '/helpers.php';
require_once '../partials/header.php'; 
session_start();
$message    = [];
$connection = database_connection();
if( isset( $_POST['submit'] ) ) {
   $email    = mysqli_real_escape_string( $connection, $_POST['email'] );
   $password = mysqli_real_escape_string( $connection, md5( $_POST['password'] ) );

   $select_user = mysqli_prepare( $connection, "SELECT * FROM user_form WHERE email = ? AND password = ?" );

   mysqli_stmt_bind_param( $select_user, 'ss', $email, $password );
   mysqli_stmt_execute( $select_user );
   mysqli_stmt_execute( $select_user );

   $result_select_user = mysqli_stmt_get_result( $select_user );

   if( mysqli_num_rows( $result_select_user ) > 0 ) {
      $row = mysqli_fetch_assoc( $result_select_user );
      $_SESSION['user_id'] = $row['id'];
      redirect( '../index.php' );
   } else {
      $message[] = 'Login Fail!';
   }
   mysqli_stmt_close( $select_user );
}
?>
<body>

<?php
echo get_notice( $message );
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>login now</h3>
      <input type="email" name="email" required placeholder="enter email" class="box">
      <input type="password" name="password" required placeholder="enter password" class="box">
      <input type="submit" name="submit" class="btn" value="login now">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</div>

<?php require_once '../partials/footer.php'; ?>