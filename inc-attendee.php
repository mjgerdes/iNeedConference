
<?php
/* inc-attendee.php
 Functions and types to deal with attendees. */

function inc_attendee_status_code($n) {
$phplol =  array( -1 => "await_email_validation",
 0 => "await_payment",
 1 => "attendee",
 666 => "needs_attention");
return $phplol[$n]; // can't do this all in one statement - thanks php!
}

/* this is what we put in the auth field in the attendee table.
It's not really necessary, which is hilarious, and we never check it, but it has to be there or the auth string we give to users won't look like an auth string!
Also, I suppose it serves the function of preventing people from figuring out the registration status of other users. */
function inc_attendee_generate_auth() {
$length = 5;
    return substr(str_shuffle(str_repeat($x='ABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

/* Query the DB to see if an email already exists. */
function inc_attendee_email_exists($email) {
global $wpdb;
$table_name = inc_attendee_table_name();
$wpdb->get_results($wpdb->prepare(
"SELECT id from $table_name
WHERE email = %s",
$email));

// query was done, we only need to know the rows affected
return $wpdb->num_rows > 0;
}


/* Inserts a new attendee into the database.
 Validation has to be done at call-site! */
function inc_attendee_insert_from_valid($validName, $validEmail, $validNote) {
global $wpdb;
$table_name = inc_attendee_table_name();

    return $wpdb->insert( 
        $table_name, 
        array(
		"time" => current_time('mysql'),
		"auth" => inc_attendee_generate_auth(),
            'name' => $validName,
			"email" => $validEmail,
			"status" => inc_attendee_status_code(-1),
			"note" => $validNote
        )
    );
}

/*
 *  Authcode stuff
 *Note that the following laws hold
 * Let $attendee be a valid attendee
 *  $attendee == inc_attendee_from_authcode(inc_attendee_authcode_from_string(inc_attendee_authcode_string($attendee))) 
 */

/* Given an authcode string, return an object with auth and id of attendee. */
function inc_attendee_authcode_from_string($authcode_string) {
$arr = explode("-", $authcode_string);
if(count($arr != 2)){
return false;
}

return (object) array("id" => $arr[0], "auth" => $arr[1]);
}

/* Given an attendee object, return its authcode string (the thing we want to give to users) */
function inc_attendee_authcode_string($attendee) {
return $attendee->id . "-" . $attendee->auth;
}

/* Given a user supplied authcode object, return the matching attendee or null if none exists. */
function inc_attendee_from_authcode($authcode) {
global $wpdb;
$table_name = inc_attendee_table_name();

return $wpdb->get_results($wpdb->prepare("
SELECT * FROM $table_name
 WHERE id = %d
 AND auth = %s",
 $authcode->id,
 $authcode->auth));
}



