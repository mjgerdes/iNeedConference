
<?php
/* inc-attendee.php
 Functions and types to deal with attendees. */
define("INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION", -1);
define("INC_ATTENDEE_STATUS_AWAIT_PAYMENT", 0);
define("INC_ATTENDEE_STATUS_ATTENDEE", 1);

function inc_internal_attendee_statustable() {
return array(
INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION => array("await_email_validation", "Registration is waiting to be email validated by the user."),
 INC_ATTENDEE_STATUS_AWAIT_PAYMENT => array("await_payment", "Validated but waiting for payment."),
 INC_ATTENDEE_STATUS_ATTENDEE => array("attendee", "All good! Payment has been received and your registration is complete. We are looking forward to meeting you at TaCoS 28!"));
}


function inc_attendee_status_code($n) {
$phplol =  inc_internal_attendee_statustable();
$phplol = $phplol[$n];
return $phplol[0];
}

function inc_attendee_status_code_pretty($n) {
 $phplol =  inc_internal_attendee_statustable();
 $phplol = $phplol[$n];
 return $phplol[1];
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
// FIXME: temporarily disabled for testing
//return false; // reenabled

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
 Validation has to be done at call-site!
 Returns the id of just inserted attendee or null if unsuccessful

FIXME: smelly, smelly function. Too many arguments, but it grew over time. oh well, split up into array or multiple functions or similar
*/
function inc_attendee_insert_from_valid($validName, $validLastname, $validEmail, $validNote, $validFood, $validUniversity, $validVbb, $validYoga) {
global $wpdb;
$table_name = inc_attendee_table_name();

 $res = $wpdb->insert( 
        $table_name, 
        array(
		"time" => current_time('mysql'),
		"auth" => inc_attendee_generate_auth(),
            'name' => $validName,
			"lastname" => $validLastname,
			"email" => $validEmail,
			"status" => inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION),
			"needs_attention" => 0,
			"note" => $validNote,
			"food" => $validFood,
			"university" => $validUniversity,
			"vbb" => $validVbb,
			"yoga" => $validYoga
        )
    );

if(!$res) {
return NULL;
}

return $wpdb->insert_id;
}

/* Updates an attendee's record. Expects an attendee object. */
function inc_attendee_update_status($attendee) {
global $wpdb;

$res = $wpdb->update(inc_attendee_table_name(),
array( "status" => $attendee->status),
array( "id" => $attendee->id),
array("%s"));

// as always, no idea what to do if this fails
return $res;
}


// for admin purposes
function inc_attendee_update_unsafe($attendee) {
global $wpdb;

$res = $wpdb->update(inc_attendee_table_name(),
(array)$attendee,
array( "id" => $attendee->id));

// as always, no idea what to do if this fails
return $res;
}


/* Retreives an attendee from DB by id */
function inc_attendee_from_id($id) {
global $wpdb;
$table_name = inc_attendee_table_name();

$phplol = $wpdb->get_results($wpdb->prepare("
SELECT * FROM $table_name
 WHERE id = %d",
$id));

if($phplol) {
return $phplol[0];
}

return NULL;
}

/* Get all attendees from DB.
Thisis meant for the admin panel. */
function inc_attendees() {
global $wpdb;
$table_name = inc_attendee_table_name();

return $wpdb->get_results("
SELECT * FROM $table_name");
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
if(count($arr) != 2) {
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

$arr = $wpdb->get_results($wpdb->prepare("
SELECT * FROM $table_name
 WHERE id = %d
 AND auth = %s",
 $authcode->id,
 $authcode->auth));

if(count($arr) != 1) {
// either there is zero, or something is very wrong with the db (duplicated primary keys)
return NULL;
}
return $arr[0];
}

function inc_attendee_validate_vbb($vbb, $vbb_choice) {
if(!$vbb) {
return 0;
}

$n = (int)$vbb_choice;
if($n < 1 || $n > 3) {
return 0;
}

return $n;
}





