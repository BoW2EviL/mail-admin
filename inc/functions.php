<?php
function hash_password($password) {
    if (@is_readable('/dev/urandom')) {
        $f=fopen('/dev/urandom', 'rb');
        $salt=fread($f, 4);
        fclose($f);
    } else {
        die('Could not query /dev/urandom');
    }
    return '{SSHA}' . base64_encode(sha1( $password.$salt, TRUE ). $salt);
}
function go( $location ) {
    echo "<script>window.location='" . $location . "'></script>";
}
function securePage() {
    if( ! isset( $_SESSION['mail-admin'] ) ) {
        header( "Location: login.php" );
    }
}
function plugins_process( $page , $location ) {
    $plugin_dir = scandir( "plugins" );
    foreach( $plugin_dir as $dir ) {
        if( $dir !== ".." && $dir !== "." ) {
            $file_to_load = "plugins/" . $dir . "/" . $page . "_" . $location . ".php";
            if( file_exists( $file_to_load ) ) {
                require $file_to_load;
            }
        }
    }
}
function watchdog( $entry ) {
    // Header
    $stamp = date( "Y-m-d H:i" );
    if( isset( $_SESSION['mail-admin'] ) ) {
        $header = $stamp . " (" . $_SESSION['mail-admin'] . "): ";
    } else {
        $header = $stamp . ": ";
    }
    // Check if log file exists
    if( ! file_exists( "usr/admin.log" ) ) {
        // Create the file
        $create = fopen( "usr/admin.log" , "w" );
        fwrite( $create , NULL );
        fclose( $create );
    }
    // Add entry to the log file
    $log = fopen( "usr/admin.log" , "a" );
    fwrite( $log , "\n" . $header . $entry );
    fclose( $log );
}
function globalOnly() {
    require 'inc/relmset.php';
    if( $_SESSION['admin_level'] !== "global" ) {
        die( "Access Denied!" );
    }
}
function gen_uuid() {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}

?>
