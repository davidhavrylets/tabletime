<?php
// config/mail.php

return [
    'host'     => 'sandbox.smtp.mailtrap.io',
    'username' => 'e52a51669fa8f1',
    'password' => '4aab86fbb648fb',
    'port'     => 587, // Мы выбрали 587 (стандартный для TLS)
    'smtp_secure' => 'tls', // Используем 'tls', как рекомендовано (STARTTLS)
    'smtp_auth' => true,
    
    // Этот email может быть любым, Mailtrap все равно "поймает" письмо
    'from_email' => 'davidhavrilec@gmail.com', 
    'from_name'  => 'TableTime Support',
];