<?php
/**
 * Created by PhpStorm.
 * User: Julian
 * Date: 14.07.2018
 * Time: 10:14
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../email/PHPMailer/src/Exception.php';
require '../email/PHPMailer/src/PHPMailer.php';
require '../email/PHPMailer/src/SMTP.php';

class SendMail
{
	private $_mail;
	private $_mailAdress;
	private $_password;


	public function __construct(){
		$this->_mail = new PHPMailer(true);
		$credFile = fopen("../smtp.ini", "r") or die("Unable to open file!");
		$creds = fread($credFile,filesize("../smtp.ini"));


		$this->_mailAdress = strtok($creds, ':');

		$password = strtok('');
		$this->_password = preg_replace('/\v(?:[\v\h]+)/', '', $password);

		fclose($credFile);
		var_dump($password);
	}

	/**
	 * @param string $receiver
	 * @param array $artistData
	 */
	public function send($receiver, $artistData){
		try {
			//Server settings
			//$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$this->_mail->isSMTP();                                      // Set mailer to use SMTP
			$this->_mail->Host = 'mail.lima-city.de';                    // Specify main and backup SMTP servers
			$this->_mail->SMTPAuth = true;                               // Enable SMTP authentication
			$this->_mail->Username = 'business@julianschreiner.de';                 // SMTP username
			$this->_mail->Password = 'phpfye1337';                           // SMTP password
			$this->_mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			$this->_mail->Port = 587;                                    // TCP port to connect to

			//Recipients
			$this->_mail->setFrom('business@julianschreiner.de', 'Newtist');
			$this->_mail->addAddress($receiver, $receiver);     // Add a recipient
			$this->_mail->addReplyTo('business@julianschreiner.de', 'Newtist');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$this->_mail->isHTML(true);                                  // Set email format to HTML
			$this->_mail->Subject = 'Artist Update XX_CURRENT_RELEASE_WEEK';
			$this->_mail->Body    = file_get_contents('path/to/file.html');
			$this->_mail->AltBody = 'TEXT VERSION HERE OF HTML TEMPLATE';

			$this->_mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $this->_mail->ErrorInfo;
		}
	}
}