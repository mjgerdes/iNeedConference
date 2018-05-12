
<?php
/*
Functions for handling couches as db objects
*/

function inccouches_insert_from_valid($validEmail, $validLocation, $validDescription) {
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


?>