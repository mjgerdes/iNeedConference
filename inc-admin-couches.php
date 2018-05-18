
<?php

function inc_admin_couches_slug() {
 return 'ineedconference-couches';
}

function inc_admin_couches_init() {
echo "<h1>iNeedConference Couches</h1>"
. "<div>";
// handle deletion/undeletion
if(isset($_POST['Delete'])) {
echo inc_admin_couches_toggle($_POST['Delete']);
} else if (isset($_POST['Undelete'])) {
echo inc_admin_couches_toggle($_POST['Undelete']);
}

// display two tables, one for active couches and one for deleted ones
$couches = inc_couches_all();
echo "<h2>Active Couches on the Bazaar</h2>"
. inc_admin_couches_table($couches, function ($couch) { return !$couch->deleted; }, "Delete")
. "<br/>";

echo "<h2>Couch Graveyard</h2>"
. "<p>These have been deleted by users and are not displayed to attendees on the bazaar.</p>"
. inc_admin_couches_table($couches, function ($couch) { return $couch->deleted; }, "Undelete");
echo "</div>";
}


function inc_admin_couches_table($couches, $select_predicate, $button_label) {
   ob_start();
echo "<table>"
. "<tr>"
. "<th>E-Mail</th>"
. "<th>Location</th>"
. "<th>Description</th>"
. "<th>Date Added</th>"
. "<th>Admin</th>"
. "</tr>";
foreach($couches as $couch) {
if(!$select_predicate($couch)) {
continue;
}
echo "<tr>"
// mail
. "<td>$couch->email</td>"
// location
. "<td>$couch->location</td>"
// description
. "<td>$couch->description</td>"
// date
. "<td>" . date("F jS", strtotime($couch->time)) . "</td>"
// admin
. "<td><form method='post'><button type='submit' name='$button_label' value='$couch->id'>$button_label</button></form></td>"
. "</tr>";
}

echo "</table>";
return ob_get_clean();
}

// delete a couch if its active, make active if its deleted
function inc_admin_couches_toggle($id) {
$couch = inc_couches_from_id($id);
if(!$couch) {
return "<p>Sorry, there seems to be a problem deleting/undeleting the couch. Id was not found!</p>";
}

if($couch->deleted) {
$couch->deleted = 0;
} else {
$couch->deleted = 1;
}

if(!inc_couches_update_unsafe($couch)) {
return "<p>Something went horribly wrong. Could not delete/undelete couch with id $couch->id!</p>";
}

// everything ok, no message
return "";
}
?>