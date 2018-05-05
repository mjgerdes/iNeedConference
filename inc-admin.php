<?php

function inc_admin_attendees_slug() {
return 'ineedconference-attendees';
}

function inc_attendee_flagstring($needs_attention) {
if ($needs_attention) {
return "<font color=#FF1111><b>!!!</b></font>";
} else {
return "";
}
}

function inc_attendee_table_statusstring($status) {
$colors = array(inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION) => "#551111",
inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_PAYMENT) => "#CCCC11",
inc_attendee_status_code(INC_ATTENDEE_STATUS_HELPER) => "#1111CC",
inc_attendee_status_code(INC_ATTENDEE_STATUS_ATTENDEE) => "#11FF11");

return "<font color=" . $colors[$status] . ">$status</font>";
}

// compares two attendee objects
// compare by status, if same status, compare by date
function inc_admin_internal_attendee_compare($a, $b) {
// function name is long so use i and j for a's and b's respective status code in numeric form
$i = inc_attendee_status_code_reverse($a->status);
$j = inc_attendee_status_code_reverse($b->status);

// compare this part inversely to get HELPER status on top etc.
if($i > $j) {
return -1;
}

if($i < $j) {
return 1;
}

//equal, check for date
if(strtotime($a->time) < strtotime($b->time)) {
return -1;
}

if(strtotime($a->time) > strtotime($b->time)) {
return 1;
}

//impossible! they are truly equal
return 0;
}

function inc_admin_attendees_table() {
   ob_start();
$attendees = inc_attendees();
usort($attendees, 'inc_admin_internal_attendee_compare');

echo "<h2>Overview</h2>";
echo "<table>";
echo "<tr>\n"
. "<th>!</th><th>Name</th><th>Status</th><th>Auth</th><th>Email</th><th>Reg Date</th><th>Note</th><th>Edit</th>"
. "</tr>";;
foreach($attendees as $attendee) {
echo "<tr>\n"
// flag
. "<td>" . inc_attendee_flagstring($attendee->needs_attention) . "</td>"
// name
. "<td>$attendee->name $attendee->lastname</td>"
// status
. "<td>" . inc_attendee_table_statusstring($attendee->status) . "</td>"
// auth
. "<td>" . inc_attendee_authcode_string($attendee) . "</td>"
// email
. "<td>$attendee->email</td>"
//  date
. "<td>$attendee->time</td>"
// note
. "<td>$attendee->note</td>"
// edit
. '<td>'
. '<form name="edit" method="get" id="f' . $attendee->id . '">'
. '<input type="hidden" name="page" value="' . inc_admin_attendees_slug() . '" />'
 . '<button form="f' . $attendee->id . '" type="submit" name="edit_attendee" value="' . $attendee->id . '">Edit</button>'
 . '</form></td>';
 echo "</tr>";
}
echo "</table>";

return ob_get_clean();
}

function inc_admin_attendees_control_panel() {
$out = "<h2>Control Panel</h2>";
$out .= "<h3>Mass E-Mails</h3>";
$out .= '<form name="control_panel" method="post">'; 
$out .= "<label><input type='checkbox' name='confirm_mass_email' value='yes' /> Really send validation email to all unvalidated accounts.</label>"
. "<button type='submit' name='mass_email' value='mass_email'>Spam those nerds!</button>";

$out .= "</form>";
return $out;
}

function inc_admin_attendees_edit($unsafeId) {
$attendee = inc_attendee_from_id($unsafeId);

if(!$attendee) {
return "Whoops, something went wrong. Couldn't load the specified attendee id.";
}

return inc_admin_attendees_edit_form($attendee);
}

function inc_admin_attendees_edit_form($attendee) {
$out = "<h2>Edit User</h2>";
$out .= '<form method="post">';

foreach((array) $attendee as $key => $value) {
if($key == "id") {
$out .= "<input type='hidden' name='$key' value='$value' />";
$out .= "<p>$key: $value";
} else if($key == "auth") {
$out .= "<input type='hidden' name='$key' value='$value' />";
$out .= "<p>$key: $value</p>";
} else if($key == "status") {
$out .= "current $key: [ $value ], <label>new $key: "
. "<select name='$key'>";
foreach(inc_internal_attendee_statustable() as $statuscode => $statusarray) {
$status = $statusarray[0];
$out .= "<option value='" . $status . "' ";
if($status == $value) {
$out .= " selected='selected' ";
}
$out .= " >" . $status . "</option>";
}
$out .= "</select";
}else if ($key == "note") {
$out .= "<textarea name='$key' rows='20' columns='60'>$value</textarea>";
} else {
$out .=
"<label>$key: " 
. "<input type='text' name='$key' value='$value' /></label>";
}
$out .= "<br/>";
}
$out .= '<button type="submit" name="submit_edit" value="submit_edit">Update</button>'
. '<button type="submit" name="resend_email" value="resend_email">Resend Email</button>'
. '<br/><button type="submit" name="delete_attendee" value="yes">Delete</button><input type="text" name="delete_confirm" placeholder="Enter 1q84 to confirm deletion" />'
. "</form>";

return $out;
}

function inc_admin_attendees_update() {
$attendee = inc_attendee_from_id($_POST['id']);

if(!$attendee) {
return "Whoops, couldn't get the attende to update. Some database error?";
}
$attendee_array = (array) $attendee;
foreach($attendee_array as $key => $value) {
$attendee_array[$key] = $_POST[$key];
}
// FIXME: function already converts to array, could save object and array conversion here
$res = inc_attendee_update_unsafe((object) $attendee_array);

if(!$res) {
return "Something just went horribly wrong.";
}

return "<p>Update of $attendee->name OK.</p>";
}

// resend the activation email (also prints to admin panel)
function inc_admin_attendees_resend_email() {
$out = "";
$attendee = inc_attendee_from_id($_POST['id']);

if(!$attendee) {
$out .= "<p>Error retrieveing attendee from DB. Something is wrong!</p>";
return $out;
}

if(!inc_send_attendee_validation_mail($attendee)) {
$out .= "<p>Error sending email!</p>";
} else {
// all good at this point
$out .= "<p>Sent following email:</p>";
}

$out .= "<pre>";
$out .= inc_send_attendee_validation_mail_body($attendee);
$out .= "</pre><br/>";

return $out;
}

// send validation emails to all unvalidated accounts
function inc_admin_attendees_mass_email() {
$out = "";
if($_POST['confirm_mass_email'] != 'yes') {
$out .= "<p>Sorry, to send mass validation emails, please confirm by ticking the checkbox.</p>";
return $out;
}

$attendees = inc_attendees();
$n = 0;
$errors = 0;


foreach($attendees as $attendee) {
//$		out .= "<p>" . var_dump($attendee->status) . "<br/>" . var_dump(inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION)) . "</p>";

if($attendee->status == inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_EMAIL_VALIDATION)) {
$n += 1;
if(!inc_send_attendee_validation_mail($attendee)) {
$errors += 1;
}
sleep(5);
}
}

$out .= "<p>Sent emails to $n unvalidated attendees. $errors errors.</p>";
return $out;
}

function inc_admin_attendees_delete_attendee() {
$out = "";
// check for confirmation

if(!isset($_POST['delete_confirm']) || $_POST['delete_confirm'] != "1q84") {
$out .= "<p>Sorry, deletion failed: Confirmation code missing or incorrect.</p>";
return $out;
}

//should be set but lets do it for sanity
if(!isset($_POST['delete_attendee']) || $_POST['delete_attendee'] != "yes") {
$out .= "<p>Sorry, something went horribly wrong while deleting attendee.</p>";
return $out;
}

// ok everything should be safe, let's drop a row
$attendee = inc_attendee_from_id($_POST['id']);
if(!inc_attendee_delete_attendee($attendee)) {
$out .= "<p>Database error on deletion attempt. Maybe id ($attendee->id) not found?</p>";
return $out;
}
// looks like it worked
$out .= "<p>Dropped attendee row with id $attendee->id from table. Goodbye $attendee->name, we hardly knew ye!</p>";
return $out;
}
// return an array with 'jey' and 'value' counts
//in an array of attendees
function inc_internal_sum_by_key($attendees, $key, $value) {
$nkey = 0;
$nvalue = 0;
foreach($attendees as $attendee) {
foreach((array) $attendee as $k => $v) {
if($k == $key) {
$nkey += 1;
if($value == $v) {
$nvalue += 1;
}
}
}
}
return array("key" => $nkey, "value" => $nvalue);
}

function inc_admin_attendees_summary() {
$out = "";
$attendees = inc_attendees();


$actual_attendeesArr = inc_internal_sum_by_key($attendees, "status", inc_attendee_status_code(INC_ATTENDEE_STATUS_ATTENDEE));
$dbentries = $actual_attendeesArr['key'];
$actual_attendees = $actual_attendeesArr['value'];
$validatedArr = inc_internal_sum_by_key($attendees, "status", inc_attendee_status_code(INC_ATTENDEE_STATUS_AWAIT_PAYMENT));
$validated = $validatedArr['value'];
$unvalidated = $dbentries - ($validated + $actual_attendees);
$out .= "<h2>Summary</h2><p>";
$out .= "<br/>All done, payed for attendees: $actual_attendees";
$out .= "<br/>waiting for payment: $validated";
$out .= "   unvalidated accounts: $unvalidated";
$out .= "<br/>db entries total: $dbentries";

$veganArr = inc_internal_sum_by_key($attendees, "food", "vegan");
$vegArr = inc_internal_sum_by_key($attendees, "food", "vegetarian");
$vegans = $veganArr['value'];
$vegs = $vegArr['value'];
$others = $veganArr['key'] - ($vegans + $vegs);

$out .= "<br/>vegans: $vegans vegetarians: $vegs others: $others";


$vbbArr = inc_internal_sum_by_key($attendees, "vbb", "1");
$vbb = $vbbArr['value'];

// FIXME: make internal higher order
$vbbArr = inc_internal_sum_by_key($attendees, "vbb", "2");
$vbb += $vbbArr['value'];

$vbbArr = inc_internal_sum_by_key($attendees, "vbb", "3");
$vbb += $vbbArr['value'];



$yogaArr = inc_internal_sum_by_key($attendees, "yoga", "1");
$yoga = $yogaArr['value'];

$out .= "<br/>vbb: $vbb yoga: $yoga";

$out .= "</p>";
return $out;
}

// this is the actual admin panel page for attendees
function inc_admin_attendees_init() {
echo "<h1>iNeedConference Attendees</h1>"
. "<div>";

if(isset($_POST['submit_edit'])) {
echo inc_admin_attendees_update();
} else if(isset($_POST['resend_email'])) {
echo inc_admin_attendees_resend_email();
} else if(isset($_POST['mass_email'])) {
echo inc_admin_attendees_mass_email();
} else if(isset($_POST['delete_attendee']) && $_POST['delete_attendee'] == "yes") {
echo inc_admin_attendees_delete_attendee();
}

echo "<br/>";

if(isset($_GET['edit_attendee'])) {
// show the form
echo inc_admin_attendees_edit($_GET['edit_attendee']);
}

// always show the table
echo  inc_admin_attendees_table();

// control panel
echo "<br/>";
echo inc_admin_attendees_control_panel();
echo "<br/>";

// show a summary of some facts
echo inc_admin_attendees_summary();
echo "</div>";
}


/********
 Talks Stuff
********/


function inc_admin_talks_slug() {
return 'ineedconference-talks';
}

function inc_admin_talks_table() {
   ob_start();
echo '<table>';
echo '<tr>';
$headings = array("Attendee", "Title", "Subtitle", "Type", "Description", "PDF", "Submit Date", "Status", "Edit");
foreach($headings as $heading) {
echo "<th>$heading</th>";
}
echo '</tr>';

$talks = inc_talks();
foreach($talks as $talk) {
$attendee = inc_attendee_from_id($talk->attendee_id);
echo "<tr>";
echo "<td>$attendee->name</td>"
. "<td>$talk->title</td>"
. "<td>$talk->subtitle</td>";

$very_short_type = strtoupper(substr($talk->type, 0, 1));
echo "<td>$very_short_type</td>";

// descriptions might be long, display no more than 30 chars
if(count($talk->description) > 30) {
$short_description = substr($talk->description, 0, 27) . "...";
} else {
$short_description = $talk->description;
}

echo "<td><font alt='$talk->description'>$short_description</font></td>";

if($talk->filename) {
$pdf = "<a href='" . inc_talk_filename_http($talk->filename) . "'>PDF</a>";
} else {
$pdf = "N/A";
}

echo "<td>$pdf</td>";
echo "<td>$talk->time</td>"
. "<td>$talk->status</td>"
. "<td><form method='get'>"
. '<input type="hidden" name="page" value="' . inc_admin_talks_slug() . '" />'
. "<button type='submit' name='edit' value='" . $talk->id . "'>Edit</button></form></td>";
echo "</tr>";
}
echo '</table>';

return ob_get_clean();
}

function inc_admin_talks_init() {
echo "<h1>iNeedConference Talks</h1>";
echo "<div>";

echo inc_admin_talks_table();
echo "</div>";
}

?>