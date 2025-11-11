<?php
// services/MailService.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailService {
    
    private array $config;

    public function __construct() {
        $this->config = require __DIR__ . '/../config/mail.php';
    }

    public function sendEmail(string $toEmail, string $subject, string $body): bool {
        
        $mail = new PHPMailer(true); 

        try {
            // --- ОТЛАДКА ОТКЛЮЧЕНА ---
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER; 
            
            // Настройки сервера
            $mail->isSMTP();
            $mail->Host       = $this->config['host'];
            $mail->SMTPAuth   = $this->config['smtp_auth'];
            $mail->Username   = $this->config['username'];
            $mail->Password   = $this->config['password'];
            $mail->SMTPSecure = $this->config['smtp_secure'];
            $mail->Port       = $this->config['port'];
            
            // --- ОСТАВЛЯЕМ ИСПРАВЛЕНИЕ ДЛЯ WAMP SSL ---
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ];
            
            // Настройки кодировки и языка
            $mail->CharSet = 'UTF-8';
            $mail->setLanguage('ru', __DIR__ . '/../libraries/PHPMailer/src/language/');

            // Отправитель и Получатель
            $mail->setFrom($this->config['from_email'], $this->config['from_name']);
            $mail->addAddress($toEmail);

            // Содержимое
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
            
        } catch (Exception $e) {
            // Возвращаем "тихую" запись в лог
            error_log("Ошибка отправки письма на {$toEmail}: {$mail->ErrorInfo}");
            return false;
        }
    }
}