<?php 
require __DIR__ . '/config.php';

if( ! function_exists( 'database_connection') ) {
    function database_connection(): mysqli {
        $connection = mysqli_connect( DB_HOST, DB_USER, DB_PASSWORD, DB_NAME ) or die('connection failed');
        return $connection;
    }
}

if( ! function_exists( 'redirect') ) {
    function redirect( $location ): void {
        $validatedLocation = filter_var( $location, FILTER_SANITIZE_URL );
        
        if ( $validatedLocation === false || $validatedLocation === null ) {
            echo "Invalid URL. Redirect failed.";
            exit;
        }
    
        // Use the header function to perform the redirect
        header( 'Location: ' . $validatedLocation );
        exit;
    }
}

if( ! function_exists( 'user_logout') ) {
    function user_logout( $id ): void {
        unset( $id );
        session_destroy();
    }
}
if( ! function_exists( 'asset_url') ) {
    function asset_url( $path ): string {
        $base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'];
        $base_url = ! empty( $_SERVER['SERVER_PORT'] ) ? $base_url . ':' . $_SERVER['SERVER_PORT'] : $base_url;

        return "{$base_url}/shopping-cart/assets/{$path}";
    }
}

if( ! function_exists( 'calculate_cart') ) {
    function calculate_cart( $price, $quantity ): int {
        $calculate = number_format( $price * $quantity );

        return $calculate;
    }
}

if( ! function_exists( 'empty_check') ) {
    function empty_check( $value = null, $default_value = null ): string|int {
        return ! empty( $value ) ? $value : $default_value;
    }
}

if( ! function_exists( 'get_notice') ) {
    function get_notice( $messages = [] ) {
        if( isset( $messages ) ){
            foreach( $messages as $message ) {
               echo '<div class="message" onclick="this.remove();">' . $message . '</div>';
            }
         }
    }
}

