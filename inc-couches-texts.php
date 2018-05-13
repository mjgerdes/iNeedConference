
<?php
/*
 This file contains some helper functions to display texts like disclaimers etc. on the couches shortcode site. This is just for convenience so all the text is in one place.
 */

function inc_couches_text_disclaimer() {
  return "<h2>Disclaimer</h2>"
   . "<p>The TaCoS 28 organisation team assumes no responsibility for the quality, availability or basic serviceability of sleeping arrangements that are negotiated through this service. We offer only a platform for students to arrange lodging amongst themselves, and are involved only insofar as we participate by offering our own couches (i.e. sleeping accomodations) on this platform. Problems with arrangements must be taken up with the respective party of any sleeping arrangement, contract or agreement of lodging made through this service.</p>";
}

function inc_couches_texts_looking_explain() {
return "<p>Enter your auth code below to get an overview of possible accomodations by volunteering students from the Potsdam/Berlin area. You received an authcode as part of your registration. You must be <a href='http://tacos28.de/register-to-attend'>registered</a> and have a validated account to see this information.</p>"; 
}

function inc_couches_texts_overview_explain($attendee) {
return "<h3>The Couch Bazaar</h3>"
. "<p>There you go, $attendee->name! Below you will find a table of E-Mail addresses of fellow students who have offered to provide lodging to TaCoS 28 attendees like you. If you wish to take up the offer, then we ask that you contact these kind people o your own initiative and responsibility. Please understand that, while we try to keep this list as up-to-date as possible, you may have to try contacting several people.</p>";
}

function inc_couches_texts_overview_none() {
return "<br/><p>Sorry, it seems that there is no one offering right now. We are always actively pursuing more possibilities for accomodation, so please check again soon!</p>";
}

function inc_couches_texts_offering_explain() {
  return "<p>If you live in the Potsdam/Berlin area and want to offer lodging/couch/a dry, warm patch of floor to TaCoS attendees arriving from elsewhere, please fill in the information below. You will then receive not only an amiable guest, but also all our deepest gratitute.</p>"
  . "<p>Please understand that the information you provide will be accessible to registered attendees of TaCoS 28. This includes the e-mail address you provide. Any lodging arrangements have to be made between you and the people who might reach out to you. The TaCoS 28 organization team does not participate in the arrangements you may or may not make. You can remove yourself from this committment and retract your offer at any time and for any reason.</p>";
}

function inc_couches_texts_offering_thanks($email, $location, $description) {
return "<h2>Your couch is being offered!</h2>"
. "<p>Thank you very much for getting involved in TaCoS 28! Your generous hospitality is sincerely appreciated. Below is what we put on the Couch Bazaar. Expect to hear from homeless computational linguists pretty soon!</p>"
. "<pre>"
. "<br/>e-mail: $email"
. "<br/>location: $location"
. "<br/>description: $description"
. "<br/></pre>";
}

?>