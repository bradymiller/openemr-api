<?php

header("Content-Type:text/xml");
$ignoreAuth = true;
require_once 'classes.php';
ini_set('display_errors', '1');
$xml_string = "";
$xml_string = "<forgetpassword>";

$email = $_POST['email'];

$strQuery = "SELECT id,username, password, firstname, lastname FROM medmasterusers WHERE email='" . $email . "'";
$result = $db->get_row($strQuery);

if ($result) {
    $xml_string .= "<status>0</status>";

    $password = rand_string(10);
    $password1 = sha1($password);
    
    $pin = substr(uniqid(rand()), 0, 4);
	$pin1 = sha1($pin);

    $strQuery1 = "UPDATE `medmasterusers` SET `password`='" . $password1 . "', `pin`='" . $pin1 . "' WHERE email = '" . $email . "'";
    $result1 = $db->query($strQuery1);
    
    $strQuery2 = "UPDATE `users` SET `password`='" . $password1 . "' WHERE email = '" . $email . "'";
    $result1 = $db->query($strQuery2);
    
    
    if ($result1 && $result1) {
        $mail = new PHPMailer();
        $mail->IsSendmail();
        $body = "<html><body>
						<table>
							<tr>
								<td>Your Password has been changed your new Username and Password are</td>
							</tr>
							<tr>
								<td>Here are the details of your account: </td>
							</tr>
							<tr>
								<td>Username: " . $result->username . "</td>
							</tr>
							<tr>
								<td>Password: " . $password . "</td>
							</tr>
							<tr>
								<td>Pin: " . $pin . "</td>
							</tr>
							<tr>
								<td>Thanks, <br />MedMaster Team</td>
							</tr>
						</table>
					</body></html>";
        $body = eregi_replace("[\]", '', $body);
        $mail->AddReplyTo("no-reply@mastermobileproducts.com", "MedMasterPro");
        $mail->SetFrom('no-reply@mastermobileproducts.com', 'MedMasterPro');
        $mail->AddAddress($email, $email);
        $mail->Subject = "MedMaster Account Signup";
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($body);

//        echo $body;
        if (!$mail->Send()) {
            $xml_string .= "<error>" . $mail->ErrorInfo . "</error>";
        } else {
            $xml_string .= "<reason>Email containing you username and password has been sent to your email address!</reason>";
        }
    }
} else {
    $xml_string .= "<status>-1</status>";
    $xml_string .= "<reason>Email Address not found in our records. Please contact support.</reason>";
}


$xml_string .= "</forgetpassword>";
echo $xml_string;
?>