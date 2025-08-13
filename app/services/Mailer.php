<?php

class Mailer {
    private $lastError = '';

    public function getLastError() {
        return $this->lastError;
    }

    public function sendHtml($toEmail, $subject, $htmlBody, $fromEmail = null, $fromName = null) {
        $fromEmail = $fromEmail ?: (defined('SMTP_FROM_EMAIL') ? SMTP_FROM_EMAIL : 'no-reply@example.com');
        $fromName = $fromName ?: (defined('SMTP_FROM_NAME') ? SMTP_FROM_NAME : 'Notificaciones');

        $headers = '';
        $headers .= 'From: ' . $this->formatAddress($fromEmail, $fromName) . "\r\n";
        $headers .= 'To: ' . $this->formatAddress($toEmail) . "\r\n";
        $headers .= 'Subject: ' . $this->encodeHeader($subject) . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        $message = $headers . "\r\n" . $htmlBody;

        return $this->sendSMTP($toEmail, $message, $fromEmail);
    }

    private function sendSMTP($toEmail, $message, $mailFrom) {
        $host = defined('SMTP_HOST') ? SMTP_HOST : '';
        $port = defined('SMTP_PORT') ? SMTP_PORT : 587;
        $username = defined('SMTP_USERNAME') ? SMTP_USERNAME : '';
        $password = defined('SMTP_PASSWORD') ? SMTP_PASSWORD : '';
        $secure = defined('SMTP_SECURE') ? SMTP_SECURE : 'tls';

        $timeout = 30;
        $errno = 0;
        $errstr = '';

        $fp = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT);
        if (!$fp) {
            $this->lastError = "No se pudo conectar al servidor SMTP: {$errstr}";
            return false;
        }

        stream_set_timeout($fp, $timeout);

        if (!$this->expect($fp, 220)) { return $this->fail($fp, 'Fallo en saludo inicial'); }

        $domain = $this->getLocalDomain();
        $this->write($fp, "EHLO {$domain}\r\n");
        if (!$this->expect($fp, 250)) { return $this->fail($fp, 'EHLO no aceptado'); }

        if ($secure === 'tls') {
            $this->write($fp, "STARTTLS\r\n");
            if (!$this->expect($fp, 220)) { return $this->fail($fp, 'STARTTLS no aceptado'); }
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                return $this->fail($fp, 'No se pudo iniciar TLS');
            }
            // EHLO nuevamente tras TLS
            $this->write($fp, "EHLO {$domain}\r\n");
            if (!$this->expect($fp, 250)) { return $this->fail($fp, 'EHLO post-TLS no aceptado'); }
        }

        // AUTH LOGIN
        $this->write($fp, "AUTH LOGIN\r\n");
        if (!$this->expect($fp, 334)) { return $this->fail($fp, 'AUTH LOGIN no aceptado'); }
        $this->write($fp, base64_encode($username) . "\r\n");
        if (!$this->expect($fp, 334)) { return $this->fail($fp, 'Usuario no aceptado'); }
        $this->write($fp, base64_encode($password) . "\r\n");
        if (!$this->expect($fp, 235)) { return $this->fail($fp, 'Contraseña no aceptada'); }

        // MAIL FROM / RCPT TO / DATA
        $this->write($fp, "MAIL FROM:<{$mailFrom}>\r\n");
        if (!$this->expect($fp, 250)) { return $this->fail($fp, 'MAIL FROM no aceptado'); }
        $this->write($fp, "RCPT TO:<{$toEmail}>\r\n");
        if (!$this->expect($fp, 250)) { return $this->fail($fp, 'RCPT TO no aceptado'); }
        $this->write($fp, "DATA\r\n");
        if (!$this->expect($fp, 354)) { return $this->fail($fp, 'DATA no aceptado'); }

        // Terminar datos con punto en línea sola
        $this->write($fp, $message . "\r\n.\r\n");
        if (!$this->expect($fp, 250)) { return $this->fail($fp, 'Mensaje no aceptado'); }

        $this->write($fp, "QUIT\r\n");
        fclose($fp);
        return true;
    }

    private function write($fp, $data) {
        fwrite($fp, $data);
    }

    private function expect($fp, $code) {
        $response = '';
        while (($line = fgets($fp, 515)) !== false) {
            $response .= $line;
            // Respuestas multilinea terminan cuando el 4to char es espacio
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }
        if (strpos($response, (string)$code) !== 0) {
            $this->lastError = "Esperado {$code}, recibido: " . trim($response);
            return false;
        }
        return true;
    }

    private function fail($fp, $reason) {
        $this->lastError = $reason;
        if (is_resource($fp)) {
            fclose($fp);
        }
        return false;
    }

    private function formatAddress($email, $name = null) {
        if ($name) {
            return sprintf('"%s" <%s>', $this->encodeHeader($name), $email);
        }
        return $email;
    }

    private function encodeHeader($text) {
        // Codificación UTF-8 compatible
        if (preg_match('/[\x80-\xFF]/', $text)) {
            return '=?UTF-8?B?' . base64_encode($text) . '?=';
        }
        return $text;
    }

    private function getLocalDomain() {
        $host = $_SERVER['SERVER_NAME'] ?? 'localhost';
        return preg_replace('/[^a-zA-Z0-9.-]/', '', $host);
    }
}


