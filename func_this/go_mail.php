<?php
  function go_mail($to_name, $to_email_address,
                   $email_subject, $email_text,
                   $from_email_name, $from_email_address) {
    
    $forwarding_to = '';
    $reply_address = $from_email_address;
    $reply_address_name = $from_email_name;
    $path_to_attachments = '';
    $path_to_more_attachments = '';
    
    if (SEND_EMAILS != 'true') return false;
    
    require_once (FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_INCLUDES . 'extern/phpmailer/PHPMailerAutoload.php');
    
    $mail = new PHPMailer();
    $mail->PluginDir = FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_INCLUDES . 'extern/phpmailer/';
    $mail->CharSet = CHARSET;
    $mail->SetLanguage($_SESSION['language'], FOLDER_ABSOLUT_CATALOG . FOLDER_RELATIV_INCLUDES . 'extern/phpmailer/language/');
    
    if (EMAIL_TRANSPORT == 'smtp') {
      $mail->IsSMTP();
      $mail->SMTPKeepAlive = true; // set mailer to use SMTP
      $mail->SMTPAuth = (SMTP_AUTH == 'true') ? true : false; // turn on SMTP authentication true/false
      $mail->SMTPSecure = (defined('SMTP_SECURE') && SMTP_SECURE != 'none') ? SMTP_SECURE : ''; // turn on SMTP secure ssl or tls
      $mail->Port = SMTP_PORT; // SMTP port
      $mail->Username = SMTP_USERNAME; // SMTP username
      $mail->Password = SMTP_PASSWORD; // SMTP password
      $mail->Host = SMTP_MAIN_SERVER.';'.SMTP_BACKUP_SERVER; // specify main and backup server "smtp1.example.com;smtp2.example.com"
    }
    
    if (EMAIL_TRANSPORT == 'sendmail') { // set mailer to use SMTP
      $mail->IsSendmail();
      $mail->Sendmail = SENDMAIL_PATH;
    }
    
    if (EMAIL_TRANSPORT == 'mail') {
      $mail->IsMail();
    }
    
    // decode html2txt
    $html_array = array('<br />', '<br/>', '<br>');
    $txt_array = array(" \n", " \n", " \n");
    $text = str_replace($html_array, $txt_array, $email_text);
    
    // remove html tags
    $text = strip_tags($text);
    $text = html_entity_decode($text, ENT_NOQUOTES, CHARSET);
    
    if (EMAIL_USE_HTML == 'true') { // set email format to HTML
      $mail->IsHTML(true);
      $mail->Body = $email_text;
      $mail->AltBody = $text;
    } else {
      $mail->IsHTML(false);
      $mail->Body = $text;
    }
    
    $mail->From = $from_email_address;
    $mail->Sender = $from_email_address;
    $mail->FromName = $from_email_name;
    $mail->AddAddress($to_email_address, $to_name);
    if ($forwarding_to != '') {
      $forwarding = explode(',', $forwarding_to);
      foreach ($forwarding as $forwarding_address) {
        $mail->AddBCC(trim($forwarding_address));
      }
    }
    $mail->AddReplyTo($reply_address, $reply_address_name);
    
    $mail->WordWrap = (int)EMAIL_WORD_WRAP; // set word wrap
    
    //create attachments array for better handling
    $attachments = attachments_array($path_to_attachments,$path_to_more_attachments);
    // add attachments
    for( $i = 0, $n = count($attachments); $i < $n; $i++) {
      $mail->AddAttachment($attachments[$i]);
    }
    $mail->Subject = $email_subject;
    
    if (!$mail->Send()) {
      trigger_error('Mailer Error - ' . $mail->ErrorInfo, E_USER_WARNING);
    }
  }
?>