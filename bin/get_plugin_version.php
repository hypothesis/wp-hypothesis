<?php

$fp = fopen( dirname( __DIR__ ) . '/hypothesis.php', 'r' );
$file_data = fread( $fp, 8192 );
fclose( $fp );
preg_match( '/Version: ([0-9\.]*)/', $file_data, $matches );
echo $matches[1];
