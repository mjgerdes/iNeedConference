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
inc_attendee_status_code(INC_ATTENDEE_STATUS_ATTENDEE) => "#11FF11");

return "<font color=" . $colors[$status] . ">$status</font>";
}

function inc_admin_attendees_table() {
   ob_start();
$attendees = inc_attendees();

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

// this is the actual admin panel page for attendees
function inc_admin_attendees_init() {
echo "<h1>iNeedConference Attendees</h1>"
. "<div>";
echo  inc_admin_attendees_table();

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