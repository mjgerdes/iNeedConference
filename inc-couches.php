
<?php

include('inc-couches-texts.php');
include('inc-couches-db.php');

function inc_get_couches_shortcode() {
$out = "";

// v2 captcha
$out .= '     <script src="https://www.google.com/recaptcha/api.js" async defer></script>';
$sitekey = "6Ley5lUUAAAAAPnEgdgJLBrdltPgViedhmIWUXlZ";
$secretkey = "6Ley5lUUAAAAABpI_KnJohUOkU8KvP2taExe2iQQ";
$captcha = false;
if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$secretkey."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
}

if(isset($_POST['looking_submit'])) {
$res = inc_couches_looking_process();
$out .= $res[0];
$success = $res[1];

if($success) {
// this is all we do for the page in this case
return $out;
}
// if failure, then error message is already in $out
} else if(isset($_POST['offering_submit'])) {
if(!$captcha || $response.success != true) {
// captcha fail
$out .= "<p>Please retry the captcha. It seems that you are a robot!</p>";
} else {
$out .= inc_couches_offering_process();
}
$out .= "<br/>";
} else if(isset($_POST['deleting_submit'])) {
if(!$captcha || $response.success != true) {
// captcha fail
$out .= "<p>Please retry the captcha. It seems that you are a robot!</p>";
} else {
$out .= inc_couches_deleting_process();
}
$out .= "<br/>";
} else {
$out .= inc_couches_texts_intro()
. "<br/>";
}

// default functions of page
$out .= "<h2>I am ...</h2>"
. inc_couches_looking()
. "<br/>"
. inc_couches_offering($sitekey)
. "<br/>"
. inc_couches_deleting($sitekey)
. "<br/>";

$out .= inc_couches_text_disclaimer();
return $out;
}

function inc_couches_looking() {
$out = "";

$out .= "<h3>... looking for a place to stay in the Potsdam/Berlin area!</h3>"
. inc_couches_texts_looking_explain();

$out .= "<form method='POST'  id='looking_form'>"
. "<label for='looking_authcode'>Authentication Code</label>"
. "<input type='text' name='looking_authcode' name='looking_authcode' placeholder='e.g. 5-QIFES' />"
. "<br/><button type='submit' name='looking_submit' value='looking_submit'>Show me the couches!</button>"
. "</form>";

return $out;
}

// this function returns two values, a string (the usual out) and a bool indicating success or failure
// the intent is to reprint the default functions of the page on failure
function inc_couches_looking_process() {
$out = "";

if(!isset($_POST['looking_authcode']) || $_POST['looking_authcode'] == "") {
$out .= "<p>Please provide a valid authentication code.</p>";
return array($out, false);
}

$attendee = inc_attendee_from_authcode(inc_attendee_authcode_from_string($_POST['looking_authcode']));

if(!$attendee) {
$out .= "<p>The provided authcode appears to be invalid.</p>";
return array($out, false);
}

if($attendee->status == inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION)) {
$out .= "<p>Sorry $attendee->name, you must first validate your account as described in your validation email before you can view the couch surfing bazaar.</p>";
return array($out, false);
}

// all seems good
$out .= inc_couches_texts_overview_explain($attendee);
$couches = inc_couches();
if(!$couches) {
$out .= inc_couches_texts_overview_none();
return array($out, true);
}

$out .= "<table>"
. "<tr>"
. "<th>E-Mail</th>"
. "<th>Location</th>"
. "<th>Description</th>"
. "</tr>";

foreach($couches as $couch) {
$out .= "<tr>"
// email
. "<td><a href='mailto:$couch->email'>$couch->email</a></td>"
// location
. "<td>$couch->location</td>"
// description
. "<td>$couch->description</td>"
. "</tr>";
}
$out .= "</table>";

return array($out, true);
}

function inc_couches_offering($sitekey) {
$out = "";
$out .= "<h3>... offering a place to stay at!</h3>"
. inc_couches_texts_offering_explain()
. "<br/><form method='post' id='offering_form'>"
. "<label for='offering_email'>Your E-Mail address where people can reach you</label>"
. "<input required type='email' name='offering_email' id='offering_email' />"
. "<label for='offering_location'>A rough estimate of your location (i.e. Potsdam, or Berlin Kreuzberg)</label>"
. "<input required type='text' name='offering_location' id='offering_location' />"
. "<label for='offering_description'>A description of what you can offer (i.e. sleeping possibilities, amount of space, your preferences or restrictions etc.)</label>"
. "<textarea required name='offering_description' id='offering_description'></textarea>"
. "<div class='g-recaptcha' data-sitekey='" . $sitekey . "'></div>"
. "<br/><button type='submit' name='offering_submit' id='offering_submit' value='offering_submit'>Submit my offer</button>"
. "</form>";

return $out;
}

// returns a message
function inc_couches_offering_process() {
if(!isset($_POST['offering_email']) || !filter_var($_POST['offering_email'], FILTER_VALIDATE_EMAIL)) {
 // case if no valid email provided
return "<p>Thank you for offering lodging, but please, provide a valid e-mail address!</p>";
}

if(!isset($_POST['offering_location']) || $_POST['offering_location'] == "") {
return "<p>Thank you for trying to offer lodging. However, please give a rough estimate of your location.</p>";
}

if(!isset($_POST['offering_description']) || $_POST['offering_description'] == "") {
return "<p>Sorry, please provide a description of the sleeping arrangements you offer. And thanks for trying!</p>";
}

if(!isset($_POST['offering_submit']) || $_POST['offering_submit'] != "offering_submit") {
return "<p>Something just went horribly wrong trying to process your offer. Oh dear!</p>";
}

// all good
$email = $_POST['offering_email']; // valid because of filter check
$location = strip_tags($_POST['offering_location'], "");
$description = strip_tags($_POST['offering_description'], "");

// insert a new couch
$id = inc_couches_insert_from_valid($email, $location, $description);
if(!$id) {
return "<p>Sorry, it seems there was a problem inserting your sleeping arrangement into the database. Please try again later or contact the webmaster.</p>";
}

return inc_couches_texts_offering_thanks($email, $location, $description);
}


function inc_couches_deleting($sitekey) {
$out = "";

$out .= "<h3>... done offering my couch. Remove me from the system!</h3>"
. inc_couches_texts_deleting_explain()
. "<form method='post' id='deleting_form'>"
. "<label for='deleting_email'>The E-Mail address you provided</label>"
. "<input type='text' name='deleting_email' id='deleting_email' />"
. "<div class='g-recaptcha' data-sitekey='" . $sitekey . "'></div>"
. "<br/><button type='submit' name='deleting_submit' id='deleting_submit' value='deleting_submit'>Take my couch off the list</button>"
. "</form>";

return $out;
}

function inc_couches_deleting_process() {
// returns a message

if(!isset($_POST['deleting_email']) || $_POST['deleting_email'] == "") {
return "<p>If you wish to delete yourself from the couch bazaar, please provide an e-mail address!</p>";
}

if(!filter_var($_POST['deleting_email'], FILTER_VALIDATE_EMAIL)) {
return "<p>Please provide a valid e-mail address if you wish to remove yourself from the bazaar.</p>";
}

// all good
$email = $_POST['deleting_email'];

$couches = inc_couches_from_email($email);
if(empty($couches)) {
return "<p>Sorry, could not remove you from the bazaar, because we could not find that e-mail address in our system! Maybe you have already removed yourself?</p>";
}

foreach($couches as $couch) {
// note that this doesn't actually delete, just flags the couch as deleted
inc_couches_delete($couch);
}

return "<p>Ok, removed $email from the bazaar. Thanks for participating, and have fun with your CL student (if you got one)!</p>";
}
?>