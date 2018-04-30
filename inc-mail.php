
<?php


/* Should get the base domain name for sending mails with noreplay…@domain.tld */
function inc_site_domain() {
//return parse_url($_SERVER['SERVER_NAME'], PHP_URL_HOST);
//FIXME: Generalize domain
return "tacos28.de";


}

function inc_send_mail($to, $subject, $body) {
$robot_name = "TaCoS28";
$domain = inc_site_domain();
$additional_headers = "";
$commandlineopts = "-f noreply@$domain -F $robot_name";
return mail($to, $subject, $body, $additional_headers, $commandlineopts);
}

function inc_mail_money_format($euro) {
return number_format($euro, 2, ",", ".") . "€";
}

function inc_mail_fee_summary($fee, $vbbdays) {
$vbbprice = 5.30;

$out = "\nSummary of Purchase\n----------\n"
. "1x\tAttendance of TaCoS 28\t" . inc_mail_money_format($fee) . "\n";

if($vbbdays != 0) {
$out .= $vbbdays . "x\t ABC-Ticket Tageskarte Berlin/Brandenburg\t" . inc_mail_money_format($vbbprice) . "\n";
}

$total = ($fee + $vbbdays * $vbbprice);
$out .= "==========\n"
. "\tTotal:\t" . inc_mail_money_format($total)
. "\n\n";

return array($out, $total);
}

function inc_send_attendee_validation_mail($attendee) {
$authcode = inc_attendee_authcode_string($attendee);
$amount = inc_mail_fee_summary(20, $attendee->vbb);

$body = "Dear $attendee->name,\n"
. "thank you for registering to attend to the 28th TaCoS, which will be held on June 8th through June 10th at the University of Potsdam.\n"
. "To confirm your registration attempt, please visit the link below.\n"
. "\n"
. get_site_url() . "/register-to-attend?authcode=" . $authcode
. "\n\n"
. "Your authentication code is:\n\n$authcode\n\n"
. "After validating your registration, use this code, or visit the above URL again, to check on the status of your account.\n\n"
. "In order to cover expenses, we ask attendees for a small registration fee.\n\n"
. $amount[0]
. "To conclude your registration please transfer the fee to the following account. Make sure to include your first and last name, as well as your authentication code in the transfer's stated purpose (Verwendungszweck) exactly as shown below.\n\n"
. "Account: \n"
. "Studierendenschaft UP\n"
. "DE38 1605 0000 3503 3160 85\n"
. "MBS Potsdam\n"
. "Amount: " . inc_mail_money_format($amount[1]) . "\n"
. "Transfer Purpose (Verwendungszweck): 'TaCoS FSR CogSys - $attendee->name $attendee->lastname #$authcode'\n"
. "\n"
. "We are looking forward to meeting you!\nSincerely, the TaCoS28 Mail Robot\n";
return inc_send_mail($attendee->email, "Thank you for registering to attend TaCoS28!", $body);
}


?>