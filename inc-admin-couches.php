
<?php

function inc_admin_couches_slug() {
 return 'ineedconference-couches';
}

function inc_admin_couches_init() {
echo "<h1>iNeedConference Couches</h1>"
. "<div>";

// display two tables, one for active couches and one for deleted ones
$couches = inc_couches_all();
echo "<h2>Active Couches on the Bazaar</h2>"
. inc_admin_couches_table($couches, function ($couch) { return !$couch->deleted; })
. "<br/>";

echo "<h2>Couch Graveyard</h2>"
. "<p>These have been deleted by users and are not displayed to attendees on the bazaar.</p>"
. inc_admin_couches_table($couches, function ($couch) { return $couch->deleted; });
echo "</div>";
}


function inc_admin_couches_table($couches, $select_predicate) {
   ob_start();
echo "<table>"
. "<tr>"
. "<th>E-Mail</th>"
. "<th>Location</th>"
. "<th>Description</th>"
. "<th>Date Added</th>"
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
. "</tr>";
}

echo "</table>";
return ob_get_clean();
}
?>