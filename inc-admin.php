<?php


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
. "<td>$attendee->name</td>"
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
. '<td><form method="get" id="edit_attendee' . $attendee->id . '_form"><button type="submit" name="edit_attendee" value="' . $attendee->id . '"><Edit</button>/form>';
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

?>