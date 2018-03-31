<?php

define("INC_TALK_STATUS_PENDING_REVIEW", -1);
define("INC_TALK_STATUS_REJECTED", 0);
define("INC_TALK_STATUS_ACCEPTED_AWAIT_CONFIRM", 1);
define("INC_TALK_STATUS_ACCEPTED_CONFIRMED", 2);

function inc_internal_talk_statustable() {
return array(
INC_TALK_STATUS_PENDING_REVIEW => array("pending_review", "Contribution is waiting to be reviewed."),
INC_TALK_STATUS_REJECTED => array("rejected", "The contribution has been rejected."),
INC_TALK_STATUS_ACCEPTED_AWAIT_CONFIRM => array("accepted_await_confirm", "The contribution has been accepted by the TaCoS28 review board, but is waiting to be confirmed by the contributor"),
INC_TALK_STATUS_ACCEPTED_CONFIRMED => array("accepted_and_confirmed", "The contribution has been accepted and is confirmed by the contributor. It's going to happen!"));
}

function inc_talk_status_code($n) {
$phplol = inc_internal_talk_statustable();
$phplol = $phplol[$n];
return $phplol[0];
}

function inc_talk_status_pretty($n) {
$phplol = inc_internal_talk_statustable();
$phplol = $phplol[$n];
return $phplol[1];
}

/* Given a pdf filename (from the db), returns a link to that pdf */
function inc_talk_filename_http($filename) {
$site = get_site_url();
return $site . "/wp-content/plugins/iNeedConference/pdf/" . $filename;
}

/* Inserts a new talk into the database.
 Validation has to be done at call-site!
 Returns the id of just inserted talk or null if unsuccessful */
function inc_talk_insert_from_valid($validAttendee_id, $validTitle, $validSubtitle, $validType, $validDescription, $validFilename) {
global $wpdb;
$table_name = inc_talk_table_name();

 $res = $wpdb->insert( 
        $table_name, 
        array(
		"time" => current_time('mysql'),
		"attendee_id" => $validAttendee_id,
"title" => $validTitle,
"subtitle" => $validSubtitle,
"type" => $validType,
"description" => $validDescription,
"filename" => $validFilename,
"status" => inc_talk_status_code(INC_TALK_STATUS_PENDING_REVIEW)
        )    );

if(!$res) {
return NULL;
}

return $wpdb->insert_id;
}

function inc_talks() {
global $wpdb;
$table_name = inc_talk_table_name();

return $wpdb->get_results("
SELECT * FROM $table_name");
}



?>
