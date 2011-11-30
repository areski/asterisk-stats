<?php
	function get_pdf($id){
		$name = SCHEDULER_PDF_PATH."-".$id.".pdf";
		exec(dirname(__FILE__)."/export_pdf_scheduler.php $id > $name");
		return $name;
	}

	function send_mail($to,$file,$subject){
		$ret = 1;
		// Mail_mime
		$crlf = "\n";
		$hdrs = array(
			'From'    => SCHEDULER_MAIL_FROM ,
			'Subject' => "$subject"
		);
		$text="Report ".$subject." from Asterisk CDR";
		$html="<html><body> Report <b>".$subject."</b> from Asterisk CDR </body></html>";
		$mime = new Mail_mime($crlf);

		$mime->setTXTBody($text);
		$mime->setHTMLBody($html);
		$mime->addAttachment($file,'application/octet-stream');

		$body = $mime->get();
		$hdrs = $mime->headers($hdrs);

		$mail =& Mail::factory('mail');

		if (!$mail->send($to, $hdrs, $body)){
			$ret = 0;
		}
		
		return $ret;
	}

	function open_log(){
		$fd = 0;
		$fd = fopen(SCHEDULER_LOGFILE,"a");
		return $fd;
	}

	function write_log($fp,$buffer){
		$ret=0;
		
		$len_buf = strlen($buffer);
		$write_bytes = fwrite($fp,$buffer,$len_buf);
		if ($write_bytes == $len_buf) 
			$ret=1;
			
		return $ret;
	}

	function close_log($fp){
		fclose($fp);
	}
?>
