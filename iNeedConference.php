
<?php
/*
Plugin Name: iNeedConference
Plugin URI: http://wordpress.org/plugins/iNeedConference/
Description: Simple plugin to organize a conference.
Author: Marius Gerdes
Version: 0.1
Author URI: http://github.com/mjgerdes
*/

/*
* @package iNeedConference
 * @version 0.1
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
// FIXME: want to turn debug off after we're done
//define('WP_DEBUG', true);
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

function inc_talk_table_name() {
return inc_general_table_prefix() . 'talk';
}


function inccouches_table_name() {
return inc_general_table_prefix() . 'couches';
}

/* Creates database tables if none already exist.
This function is run upon plugin activation.
*/

function inc_create_attendee_sql() {
$table_name = inc_attendee_table_name();
global $wpdb;

$charset_collate = $wpdb->get_charset_collate();

$default_status = inc_attendee_status_code(-1);
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  auth tinytext NOT NULL DEFAULT '',
  name tinytext NOT NULL,
  lastname tinytext NOT NULL,
  email varchar(255) DEFAULT '' NOT NULL,
  status tinytext NOT NULL DEFAULT '',
  note text NOT NULL DEFAULT '',
  food tinytext NOT NULL DEFAULT '',
  university tinytext NOT NULL DEFAULT '',
  vbb tinyint(2) NOT NULL DEFAULT 0,
  yoga BOOL NOT NULL DEFAULT 0,
  needs_attention BOOL NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
) $charset_collate;";

return $sql;
}

function inc_create_talk_sql() {
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = inc_talk_table_name();
$default_status = inc_talk_status_code(-1);
$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  attendee_id mediumint(9) NOT NULL,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  title tinytext NOT NULL DEFAULT '',
  subtitle varchar(1024) DEFAULT '' NOT NULL,
  type tinytext NOT NULL DEFAULT '',
  description text NOT NULL DEFAULT '',
  status tinytext NOT NULL DEFAULT '',
  filename tinytext NOT NULL DEFAULT '',
  PRIMARY KEY  (id)
) $charset_collate;";

return $sql;
}



function inc_create_couches_sql() {
global $wpdb;
$charset_collate = $wpdb->get_charset_collate();
$table_name = inc_couches_table_name();

$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  email tinytext NOT NULL DEFAULT '',
  description text NOT NULL DEFAULT '',
  deleted BOOL NOT NULL DEFAULT 0,
  PRIMARY KEY  (id)
) $charset_collate;";

return $sql;
}

function inc_install() {
global $wpdb;
	global $inc_db_version;

$sql_attendee = inc_create_attendee_sql();
$sql_talk = inc_create_talk_sql();
$sql_couches = inc_create_couches_sql();

// this does the actual update
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql_attendee );
dbDelta( $sql_talk);
dbDelta( $sql_couches);

// if we need to change something in the future...
	add_option('inc_db_version', $inc_db_version);
}

// we only want to check for tables at plugin activation
register_activation_hook(__FILE__, 'inc_install');

require('inc-attendee.php');
require('inc-mail.php');
require('inc-shortcodes.php');
require("inc-admin.php");

function inc_admin_menu_setup() {
add_menu_page('iNeedConference Attendees', // page title
'IneedConference Attendees', // label
'manage_options', // access restriction
inc_admin_attendees_slug(), // slug
'inc_admin_attendees_init' /* function that is called when menu is
opened */
);

add_menu_page('iNeedConference Talks', // page title
'IneedConference Talks', // label
'manage_options', // access restriction
inc_admin_talks_slug(), // slug
'inc_admin_talks_init' /* function that is called when menu is
opened */
);
}

// admin panel
add_action('admin_menu', 'inc_admin_menu_setup');

?>