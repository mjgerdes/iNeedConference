
<?php
/**
 * @package iNeedConference
 * @version 0.1
 */
/*
Plugin Name: iNeedConference
Plugin URI: http://wordpress.org/plugins/iNeedConference/
Description: Simple plugin to organize a conference.
Author: Marius Gerdes
Version: 0.1
Author URI: http://github.com/mjgerdes
*/


defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
define('WP_DEBUG', true);
define('INC_DIR', dirname( __FILE__ )); //an absolute path to this directory

global $inc_db_version;
$inc_db_version = '1.0';
/* Functions to get table names. Note that we have a prefix like
wp__table_name, i.e. two underscores to visually distinguish our
tables from wp native tables.
*/
function inc_attendee_table_name() {
global $wpdb;
return $wpdb->prefix . '_attendee';
}

/* Creates database tables if none already exist.
This function is run upon plugin activation.
*/

function inc_attendee_status_code($n) {
$phplol =  array( -1 => "await_email_validation",
 0 => "await_payment",
 1 => "attendee",
 666 => "needs_attention");
return $phplol[$n];
}

function inc_install() {
global $wpdb;
	global $inc_db_version;
	
$table_name = inc_attendee_table_name();
$charset_collate = $wpdb->get_charset_collate();

$default_status = inc_attendee_status_code(-1);
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  auth tinytext NOT NULL DEFAULT '',
  name tinytext NOT NULL,
  email varchar(255) DEFAULT '' NOT NULL,
  status tinytext NOT NULL DEFAULT '',
  note text NOT NULL DEFAULT '',
  PRIMARY KEY  (id)
) $charset_collate;";

// this does the actual update
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );

// if we need to change something in the future...
	add_option('inc_db_version', $inc_db_version);
}

// we only want to check for tables at plugin activation
register_activation_hook(__FILE__, 'inc_install');


/* this is what we put in the auth field in the attendee table.
It's not really necessary, which is hilarious, and we never check it, but it has to be there or the auth string we give to users won't look like an auth string!
Also, I suppose it serves the function of preventing people from figuring out the registration status of other users. */
function inc_generate_auth() {
$length = 5;
    return substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}


include('inc-shortcodes.php');


?>