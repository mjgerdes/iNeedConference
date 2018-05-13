
<?php
/*
Functions for handling couches as db objects
*/

function inc_couches_all() {
global $wpdb;
$table_name = inc_couches_table_name();

return $wpdb->get_results("
SELECT * FROM $table_name");
}

function inc_couches() {
global $wpdb;
$table_name = inc_couches_table_name();

return $wpdb->get_results("
SELECT * FROM $table_name WHERE deleted = 0");
}

function inc_couches_from_id($id) {
global $wpdb;
$table_name = inc_couches_table_name();

$phplol = $wpdb->get_results($wpdb->prepare("
SELECT * FROM $table_name
 WHERE id = %d",
$id));

if($phplol) {
return $phplol[0];
}

return NULL;
}


// returns an array of couches that match the email
function inc_couches_from_email($id) {
global $wpdb;
$table_name = inc_couches_table_name();

return $wpdb->get_results($wpdb->prepare("
SELECT * FROM $table_name
 WHERE email = %s",
$email));
}


function inc_couches_insert_from_valid($validEmail, $validLocation, $validDescription) {
global $wpdb;
$table_name = inc_couches_table_name();

 $res = $wpdb->insert( 
        $table_name, 
        array(
		"time" => current_time('mysql'),
			"email" => $validEmail,
  "location" => $validLocation,
"description" => $validDescription,
			"deleted" => 0,
        )
    );

if(!$res) {
return NULL;
}

return $wpdb->insert_id;
}

// this is more for admin purposes
function inc_couches_update_unsafe($couch) {
global $wpdb;

$res = $wpdb->update(inc_couches_table_name(),
(array)$couch,
array( "id" => $couch->id));

// as always, no idea what to do if this fails
return $res;
}

function inc_couches_delete($couch) {
global $wpdb;
$couch->deleted = 1;

$res = $wpdb->update(inc_couches_table_name(),
array( "deleted" => $couch->deleted),
array( "id" => $couch->id),
array("%s"));

// as always, no idea what to do if this fails
return $res;
}

?>