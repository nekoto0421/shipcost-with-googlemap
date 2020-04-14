<?php
/*
 * Plugin Name: shipcost with googlemap
 * Description:This is a plugin which can calculate price by input starting point and endpoint on googlemap you can decide how to use it by yourself  
 * Author: nekoto
 * Plugin URI:https://wordpress.org/plugins/shipcost-with-googlemap
 * Version: 1.2
 */


if(!defined('GMSC_DIR')){
	define('GMSC_DIR', dirname(__FILE__));
}

define('GMSC_URL', plugin_dir_url(__FILE__));


function LoadGMSCplugin_Class(){
	include GMSC_DIR.'/includes/class_GMSCplugin-main.php';
	include GMSC_DIR.'/includes/class_GMSCplugin-cofunction.php';
	include GMSC_DIR.'/includes/class_GMSCplugin-setting.php';
	$GLOBALS['GMSC']=GMSC();
}

function GMSC(){
	return GMSC_Plugin::instance();
}
add_action('plugins_loaded', 'LoadGMSCplugin_Class');
?>