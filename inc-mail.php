
<?php


/* Should get the base domain name for sending mails with noreplay…@domain.tld */
function inc_site_domain() {
//return parse_url($_SERVER['SERVER_NAME'], PHP_URL_HOST);
//FIXME: Generalize domain
return "tacos28.de";


}

function inc_send_mail($to, $subject, $body) {
$domain = inc_site_domain();
$additional_headers = "From: noreply@$domain" . "\r\n" . "Reply-To: noreply@$domain" . "\r\n";
return mail($to, $subject, $body, $additional_headers);
}

function inc_send_attendee_validation_mail($attendee) {
$authcode = inc_attendee_authcode_string($attendee);
$body = "Dear $attendee->name,\n"
. "thank you for registering to attend to the 28th TaCoS, which will be held on June 8th to June 10th at the University of Potsdam.\n"
. "To confirm your registration attempt, please visit the link below.\n"
. "\n"
. get_site_url() . "/register-to-attend?authcode=" . $authcode
. "\n"
. "Your authentication code is:\n$authcode\n"
. "After validating your registration, use this code, or visit the above URL again, to check on the status of your account.\n"
. "To complete your registration process, please send $10 to the following bank accFIXME: insert real text here\n"
. "\n"
. "We are looking forward to meeting you!\nSincerely, the TaCoS28 Mail Robot\n";
return inc_send_mail($attendee->email, "Thank you for registering to attend TaCoS28!", $body);
}


?>