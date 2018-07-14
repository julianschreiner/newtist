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
	}

	/**
	 * @param string $receiver
	 * @param array $artistData
	 */
	public function send($receiver, $artistData, $username){
		/* BUILD TEMPLATE */
		$template = file_get_contents('templates/infomail.html');
		$template = str_replace('##USERNAME##', $username, $template);
		$template = str_replace('##ALBUM##', $artistData['name'], $template);
		$template = str_replace('##ARTIST##', $artistData['allArtists'][0]['name'], $template);
		$template = str_replace('##TRACK/ALBUM##', $artistData['type'], $template);
		$template = str_replace('##RELEASE_DATE##', $artistData['release_date'], $template);
		$template = str_replace('##IMAGE##', $artistData['image'], $template);

		$t = <<< TPL
          		<li>##artist##</li>            
TPL;
		$r = '';
		foreach($artistData['allArtists'] as $artist){
			$r .= str_replace('##artist##', $artist['name'], $t);
		}
		$template = str_replace('##ALLARTISTS##', $r, $template);

		$template = str_replace('##ARTIST_URL##', $artistData['artistURL'], $template);
		$template = str_replace('##TRACK_URL##', $artistData['uri'], $template);

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
			$this->_mail->CharSet = "UTF-8";

			//Recipients
			$this->_mail->setFrom('business@julianschreiner.de', 'Newtist');
			$this->_mail->addAddress($receiver, $receiver);     // Add a recipient
			$this->_mail->addReplyTo('business@julianschreiner.de', 'Newtist');

			//Attachments
			//$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
			//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

			//Content
			$this->_mail->isHTML(true);                                  // Set email format to HTML
			$this->_mail->Subject = 'Artist Update ' . date('d.m.Y');
			$this->_mail->Body    =  $template;
			$this->_mail->AltBody = 'TEXT VERSION HERE OF HTML TEMPLATE';

			$this->_mail->send();
			return true;
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $this->_mail->ErrorInfo;
			return false;
		}
	}
}