<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
};
require_once( plugin_dir_path( __FILE__ ) . 'includes/phpqrcode/qrlib.php');

QRcode::png($_SERVER['REQUEST_URI'],  false, $wp->query_vars['plqrecc'],$wp->query_vars['plqrzoom'],$wp->query_vars['plqrframe']);