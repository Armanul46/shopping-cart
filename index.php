<?php 

require_once 'config.php';
require_once 'helpers.php';
require_once 'partials/header.php'; 
session_start();

$user_id     = $_SESSION['user_id'];
$grand_total = 0;
$message     = [];
$connection  = database_connection();

if( ! isset( $user_id ) ) {
   redirect( 'pages/login.php' );
}

if( isset( $_GET['logout'] ) ) {
   user_logout( $_GET['logout'] );
   redirect( 'pages/login.php' );
}

if( isset( $_POST['add_to_cart'] ) ) {
   $product_quantity = empty_check( $_POST['product_quantity'], 0 );
   $product_name     = empty_check( $_POST['product_name'], '' );
   $product_price    = empty_check( $_POST['product_price'], 0 );
   $product_image    = empty_check( $_POST['product_image'], '' );

   $select_cart = mysqli_prepare( $connection, "SELECT * FROM cart WHERE name = ? AND user_id = ?" );

   mysqli_stmt_bind_param( $select_cart, 'si', $product_name, $user_id );
   mysqli_stmt_execute( $select_cart );
   mysqli_stmt_execute( $select_cart );

   $result_select_cart = mysqli_stmt_get_result( $select_cart );

   if ( mysqli_num_rows( $result_select_cart ) > 0 ) {
      $message[] = 'product already added to cart';
      mysqli_stmt_close( $select_cart );
   } else {
      
      $cart_insert = mysqli_prepare( $connection, "INSERT INTO cart( user_id, name, price, image, quantity ) VALUES( ?, ?, ?, ?, ? )" );
      mysqli_stmt_bind_param( $cart_insert, 'isdsi', $user_id, $product_name, $product_price, $product_image, $product_quantity );
      
      if ( mysqli_stmt_execute( $cart_insert ) ) {
         $message[] = 'product added to cart';
      }

      mysqli_stmt_close( $cart_insert );
   }
}

if( isset( $_POST['update_cart'] ) ) {
   $update_quantity = empty_check( $_POST['cart_quantity'], 0 );
   $update_id       = empty_check( $_POST['cart_id'], 0 );

   $updated_cart = mysqli_prepare( $connection, "UPDATE cart SET quantity = ? WHERE id = ?" );
   mysqli_stmt_bind_param( $updated_cart, 'ii', $update_quantity, $update_id );

   if ( mysqli_stmt_execute( $updated_cart ) ) {
      $message[] = 'updated cart';
   }
   
}

if ( isset( $_GET['remove'] ) ) {
   $remove_id = $_GET['remove'];
   
   $remove_cart = mysqli_prepare( $connection, "DELETE FROM cart WHERE id = ?" );
   mysqli_stmt_bind_param( $remove_cart, 'i', $remove_id );

   if ( mysqli_stmt_execute( $remove_cart ) ) {
      $message[] = 'Removed cart';
   }
   
}

if( isset(  $_GET['delete_all'] ) ) {
   mysqli_query( $connection, "DELETE FROM cart WHERE user_id = '$user_id'");

   $delete_all = mysqli_prepare( $connection, "DELETE FROM cart WHERE user_id = ?" );
   mysqli_stmt_bind_param( $delete_all, 'i', $remove_id );

   if ( mysqli_stmt_execute( $delete_all ) ) {
      redirect( 'index.php' );
   }
   
}
?>

<div class="container">

<div class="user-profile">
   <?php
   $select_user = mysqli_prepare( $connection, "SELECT * FROM user_form WHERE id = ?" );
   mysqli_stmt_bind_param( $select_user, 'i', $user_id );
   mysqli_stmt_execute( $select_user );
   mysqli_stmt_execute( $select_user );

   $result_select_user = mysqli_stmt_get_result( $select_user );

   if ( mysqli_num_rows( $result_select_user ) > 0 ) {
      mysqli_stmt_close( $select_user );
      $fetch_user = mysqli_fetch_assoc( $result_select_user );
   }
   ?>
   <p> username : <span><?php echo $fetch_user['name']; ?></span> </p>
   <p> email : <span><?php echo $fetch_user['email']; ?></span> </p>
   <div class="flex">
      <a href="login.php" class="btn">login</a>
      <a href="register.php" class="option-btn">register</a>
      <a href="index.php?logout=<?php echo $user_id; ?>" onclick="return confirm('are your sure you want to logout?');" class="delete-btn">logout</a>
   </div>

</div>

<div class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container">
      <?php
         $select_product = mysqli_prepare( $connection, "SELECT * FROM products" );
         mysqli_stmt_execute( $select_product );
         mysqli_stmt_execute( $select_product );

         $result_select_product = mysqli_stmt_get_result( $select_product );
         if( mysqli_num_rows( $result_select_product ) > 0 ) {
            
            while( $fetch_product = mysqli_fetch_array( $result_select_product ) ) {
        
      ?>

         <form method="post" class="box" action="">
            <img src="images/<?php echo $fetch_product['image'] ?>" alt="">
            <div class="name"><?php echo $fetch_product['name'] ?></div>
            <div class="price">$<?php echo $fetch_product['price'] ?>/-</div>
            <input type="number" min="1" name="product_quantity" value="1">
            <input type="hidden" name="product_image" value="<?php echo $fetch_product['image'] ?>">
            <input type="hidden" name="product_name" value="<?php echo $fetch_product['name'] ?>">
            <input type="hidden" name="product_price" value="<?php echo $fetch_product['price'] ?>">
            <input type="submit" value="add to cart" name="add_to_cart" class="btn">
         </form>
   
         <?php 
            }
         }
         ?>
   </div>

</div>

<div class="shopping-cart">

   <h1 class="heading">shopping cart</h1>

   <table>
      <thead>
         <th>image</th>
         <th>name</th>
         <th>price</th>
         <th>quantity</th>
         <th>total price</th>
         <th>action</th>
      </thead>
      <tbody>
         <?php 

            $cart_query = mysqli_prepare( $connection, "SELECT * FROM cart WHERE user_id = ?" );
            mysqli_stmt_bind_param( $cart_query, 'i', $user_id );
            mysqli_stmt_execute( $cart_query );
            mysqli_stmt_execute( $cart_query );

            $result_cart_query = mysqli_stmt_get_result( $cart_query );

            if( mysqli_num_rows( $result_cart_query ) > 0 ) {
               while( $fetch_cart = mysqli_fetch_assoc( $result_cart_query ) ) {
            
         ?>
         <tr>
            <td><img src="images/<?php echo $fetch_cart['image'] ?>" height="100" alt=""></td>
            <td><?php echo $fetch_cart['name'] ?></td>
            <td>$<?php echo $fetch_cart['price'] ?>/-</td>
            <td>
               <form action="" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id'];?>">
                  <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity'];?>">
                  <input type="submit" name="update_cart" value="update" class="option-btn">
               </form>
            </td>
            <td>$<?php echo $sub_total =  calculate_cart( $fetch_cart['price'], $fetch_cart['quantity'] ); ?>/-</td>
            <td><a href="index.php?remove=<?php echo $fetch_cart['id']; ?>" class="delete-btn" onclick="return confirm('remove item from cart?');">remove</a></td>
         </tr>
      <?php 
      $grand_total += $sub_total;
               }
            }
      ?>
      <tr class="table-bottom">
         <td colspan="4">grand total :</td>
         <td>$<?php echo empty_check( $grand_total, 0 )?>/-</td>
         <td><a href="index.php?delete_all" onclick="return confirm('delete all from cart?');" class="delete-btn">delete all</a></td>
      </tr>
   </tbody>
   </table>

   <div class="cart-btn">  
      <a href="#" class="btn <?php echo ( $grand_total > 1 ) ? '' : 'disabled'; ?>">proceed to checkout</a>
   </div>

</div>

</div>

<?php require_once 'partials/footer.php'; ?>