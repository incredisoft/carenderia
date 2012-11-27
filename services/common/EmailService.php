<?php
class EmailService{
	function send($to, $message, $from){
		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=iso-8859-1" . "\r\n";
		$headers .= 'From: '.$from."\r\n";
		
		mail($to, "Carenderia Order" ,$message, $headers);
		
		return "Success";
	}
}
?>

