<?php 
require_once '../config.php';
require_once SHP_PATH . '/helpers.php';
require_once '../partials/header.php'; 
$connection = database_connection();
$message    = [];

if ( isset( $_POST['submit'] ) ) {

   $name      = mysqli_real_escape_string( $connection, $_POST['name'] );
   $email     = mysqli_real_escape_string( $connection, $_POST['email'] );
   $password  = mysqli_real_escape_string( $connection, md5( $_POST['password'] ) );
   $confirm_password = mysqli_real_escape_string( $connection, md5( $_POST['cpassword'] ) );

   $select_user = mysqli_prepare( $connection, "SELECT * FROM user_form WHERE email = ? AND password = ?" );

   mysqli_stmt_bind_param( $select_user, 'ss', $email, $password );
   mysqli_stmt_execute( $select_user );
   mysqli_stmt_execute( $select_user );

   $result_select_user = mysqli_stmt_get_result( $select_user );
   
   if ( mysqli_num_rows( $result_select_user ) > 0 || $password !== $confirm_password ) {
      $message[] = 'User already exist!';
      if ( $password !== $confirm_password ) {
         $message[] = 'password not same';
      }
      mysqli_stmt_close( $select_user );
   } else {
      $registered = mysqli_query( $connection, "INSERT INTO user_form(name, email, password) VALUES( '$name', '$email', '$password' )" );

      $registered = mysqli_prepare( $connection, "INSERT INTO user_form(name, email, password) VALUES( ?, ?, ? )" );
      mysqli_stmt_bind_param( $registered, 'sss', $name, $email, $password );

      if( mysqli_stmt_execute( $registered ) ) {
         $message[] = "Registered Successfully"; 
      }

      mysqli_stmt_close( $registered );
      redirect('login.php');
   }
}
?>

<?php 
echo get_notice( $message );
?>
<div class="form-container">

   <form action="" method="post">
      <h3>register now</h3>
      <input type="text" name="name" required placeholder="enter username" class="box">
      <input type="email" name="email" required placeholder="enter email" class="box">
      <input type="password" name="password" required placeholder="enter password" class="box">
      <input type="password" name="cpassword" required placeholder="confirm password" class="box">
      <input type="submit" name="submit" class="btn" value="register now">
      <p>already have an account? <a href="login.php">login now</a></p>
   </form>

</div>

<?php require_once '../partials/footer.php'; ?>