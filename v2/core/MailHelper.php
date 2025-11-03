<?php
/**
 * MailHelper - Sistema de envío de emails
 */

require_once __DIR__ . '/PHPMailer6/src/Exception.php';
require_once __DIR__ . '/PHPMailer6/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer6/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    
    /**
     * Obtener configuración de email
     */
    private static function getConfig() {
        return [
            'host' => env('MAIL_HOST', 'vps-1306543-x.dattaweb.com'),
            'port' => env('MAIL_PORT', '465'),
            'user' => env('MAIL_USER', ''),
            'password' => env('MAIL_PASSWORD', ''),
            'from_name' => env('MAIL_FROM_NAME', 'CAPA ESTADISTICA'),
            'reply_to' => env('MAIL_REPLY_TO', 'capa@capa.org.ar'),
            'admin_email' => env('ADMIN_EMAIL', 'capa@capa.org.ar'),
            'debug' => env('ENVIRONMENT', 'production') === 'development' ? 2 : 0
        ];
    }
    
    /**
     * Configurar PHPMailer con valores base
     */
    private static function setupMailer() {
        $config = self::getConfig();
        
        $mail = new PHPMailer(true);
        $mail->SMTPDebug = $config['debug'];
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['user'];
        $mail->Password = $config['password'];
        $mail->SMTPSecure = 'ssl'; // Puerto 465 usa SSL
        $mail->Port = $config['port'];
        $mail->setFrom($config['user'], $config['from_name']);
        $mail->addReplyTo($config['reply_to'], $config['from_name']);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        return $mail;
    }
    
    /**
     * Enviar email de bienvenida a nuevo usuario
     */
    public static function enviarBienvenida($usuario, $email, $hash) {
        $config = self::getConfig();
        
        // Validar configuración
        if (empty($config['user']) || empty($config['password'])) {
            error_log("MailHelper: Credenciales de email no configuradas");
            return false;
        }
        
        try {
            $mail = self::setupMailer();
            $mail->addAddress($email);
            $mail->Subject = "CAPA - Link de acceso a sistema de carga Estadística de ventas anual";
            
            $url = APP_URL . "/v2/log/?h={$hash}";
            $mail->Body = "
                Estimado Socio
                <br><br>
                A continuación, encontrará link de acceso permanente al sistema de carga de la Estadística de ventas de CAPA.
                <br><br>
                Dicho link es exclusivo para su empresa para garantizar la confidencialidad de datos y no debe ser compartido con personal externo.
                <br><br>
                Sugerimos guardar el mismo en Favoritos/Marcadores de su navegador.
                <br><br>
                <a href='{$url}'>Link</a>: ({$url})
                <br><br>
                IMPORTANTE: Las cargas que realice en el sistema se actualizan de manera automática sin necesidad de un proceso de cierre.
                <br><br>
                Cualquier duda o consulta puede contactarnos al mail capa@capa.org.ar
            ";
            
            $mail->send();
            error_log("MailHelper: Email de bienvenida enviado a {$email}");
            return true;
        } catch (Exception $e) {
            error_log("MailHelper Error enviando bienvenida a {$email}: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Enviar notificación a admin sobre nuevo usuario
     */
    public static function notificarAdmin($did, $usuario, $email, $tipo) {
        $config = self::getConfig();
        
        if (empty($config['user']) || empty($config['password'])) {
            error_log("MailHelper: Credenciales de email no configuradas");
            return false;
        }
        
        try {
            $mail = self::setupMailer();
            $mail->addAddress($config['admin_email']);
            $mail->Subject = "Nuevo acceso CAPA ({$did}) - {$tipo}";
            $mail->Body = "Nuevo acceso para {$tipo} {$usuario} ({$did}) enviado por mail a {$email}";
            
            $mail->send();
            error_log("MailHelper: Notificación admin enviada para usuario {$did}");
            return true;
        } catch (Exception $e) {
            error_log("MailHelper Error notificando admin: " . $mail->ErrorInfo);
            return false;
        }
    }
}

