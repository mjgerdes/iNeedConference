
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
// FIXME: want to turn debug off after we're done
define('WP_DEBUG', true);
define('INC_DIR', dirname( __FILE__ )); //an absolute path to this directory

global $inc_db_version;
$inc_db_version = '1.0';
/* Functions to get table names. Note that we have a prefix like
wp_inc_<tablename>
to distinguish our tables from wp native tables. */
function inc_general_table_prefix() {
global $wpdb;
return $wpdb->prefix . "inc_";
}

function inc_attendee_table_name() {
return inc_general_table_prefix() . 'attendee';
}

/* Creates database tables if none already exist.
This function is run upon plugin activation.
*/

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
  needs_attention BOOL NOT NULL DEFAULT 0,
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

include('inc-attendee.php');
include('inc-mail.php');
include('inc-shortcodes.php');


?>