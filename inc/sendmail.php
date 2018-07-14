<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 12.07.2018
 * Time: 18:12
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../email/PHPMailer/src/Exception.php';
require '../email/PHPMailer/src/PHPMailer.php';
require '../email/PHPMailer/src/SMTP.php';

/**
 * @param string $receiver
 * @param string artistName
 * @param string songName / albumName
 */

/** DEPREACTED  */

if(isset($argv[1]) && !empty($argv[1])){
	$toAdress = $argv[1];
	$mail = new PHPMailer(true);
	try {
		//Server settings
		//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'mail.lima-city.de';                    // Specify main and backup SMTP servers
		$mail->SMTPAuth = true;                               // Enable SMTP authentication
		$mail->Username = 'business@julianschreiner.de';                 // SMTP username
		$mail->Password = 'phpfye1337';                           // SMTP password
		$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
		$mail->Port = 587;                                    // TCP port to connect to

		//Recipients
		$mail->setFrom('business@julianschreiner.de', 'Newtist');
		$mail->addAddress($toAdress, $toAdress);     // Add a recipient
		$mail->addReplyTo('business@julianschreiner.de', 'Newtist');

		//Attachments
		//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
		//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = 'Artist Update XX_CURRENT_RELEASE_WEEK';
		$mail->Body    = file_get_contents('path/to/file.html');
		$mail->AltBody = 'TEXT VERSION HERE OF HTML TEMPLATE';

		$mail->send();
		echo 'Message has been sent';
	} catch (Exception $e) {
		echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
	}
}

