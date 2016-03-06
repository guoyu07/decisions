<?php
/*
Plugin Name: Decisions
Description: Not Options
Author: John P Bloch
Author URI: https://johnpbloch.com
License: MIT
*/

if ( ! class_exists( 'Closure' ) ) {
	_doing_it_wrong( __FILE__, 'This plugin requires PHP 5.3 or higher', '1.0' );

	return;
} else {
	require_once dirname( __FILE__ ) . '/src/load.php';
}
