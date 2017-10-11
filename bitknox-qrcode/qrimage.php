<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
};
require_once( plugin_dir_path( __FILE__ ) . 'includes/phpqrcode/qrlib.php');
QRcode::png($_SERVER['REQUEST_URI'],  false, QR_ECLEVEL_L,1);