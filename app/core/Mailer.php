<?php

/*
    Set up connection for mailer
*/

namespace app\core;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer {
    protected $mail;

    public function __construct()
    {
        try {
            $this->mail = new PHPMailer(true);

            $this->mail->isSMTP();
            $this->mail->Host = MAIL_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Port = 587;
            $this->mail->Username = MAIL_USERNAME;
            $this->mail->Password = MAIL_PASSWORD;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        
        } catch(Exception $e) {
            error_log("Error in connecting to mailer. Mailer Error: {$this->mail->ErrorInfo}");
        }
    }
}