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
        
        // Determinar tipo de encriptación según puerto
        if ($config['port'] == 465) {
            $mail->SMTPSecure = 'ssl'; // Puerto 465 usa SSL
        } elseif ($config['port'] == 587) {
            $mail->SMTPSecure = 'tls'; // Puerto 587 usa TLS (STARTTLS)
        } else {
            // Sin encriptación por defecto
            $mail->SMTPSecure = '';
        }
        
        $mail->Port = $config['port'];
        $mail->setFrom($config['user'], $config['from_name']);
        $mail->addReplyTo($config['reply_to'], $config['from_name']);
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        
        return $mail;
    }
    
    /**
     * Obtener plantilla desde BD
     */
    private static function getPlantilla($tipo) {
        $db = Database::getInstance();
        $plantilla = $db->fetchOne(
            "SELECT asunto, cuerpo_html FROM emailsPlantillas 
             WHERE tipo = ? AND habilitado = 1 AND elim = 0 AND superado = 0 
             LIMIT 1",
            ['s', $tipo]
        );
        return $plantilla ?: null;
    }
    
    /**
     * Reemplazar variables en plantilla
     */
    private static function procesarPlantilla($plantilla, $variables = []) {
        $html = is_array($plantilla) ? $plantilla['cuerpo_html'] : $plantilla;
        foreach ($variables as $key => $value) {
            $html = str_replace('{' . $key . '}', $value, $html);
        }
        return $html;
    }
    
    /**
     * Procesar plantilla (versión pública para uso en controladores)
     */
    public static function procesarPlantillaPublic($plantilla, $variables = []) {
        return self::procesarPlantilla($plantilla, $variables);
    }
    
    /**
     * Enviar email genérico con HTML
     */
    public static function enviarEmail($email, $asunto, $cuerpoHtml) {
        $config = self::getConfig();
        
        // Validar configuración
        if (empty($config['user']) || empty($config['password'])) {
            throw new Exception("Credenciales de email no configuradas");
        }
        
        try {
            $mail = self::setupMailer();
            $mail->addAddress($email);
            $mail->Subject = $asunto;
            $mail->Body = $cuerpoHtml;
            
            $mail->send();
            error_log("MailHelper: Email enviado a {$email}");
            return true;
        } catch (Exception $e) {
            error_log("MailHelper Error enviando email a {$email}: " . $mail->ErrorInfo);
            throw new Exception("Error enviando email: " . $mail->ErrorInfo);
        }
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
        
        // Obtener plantilla desde BD
        $plantilla = self::getPlantilla('bienvenida');
        if (!$plantilla) {
            error_log("MailHelper: No se encontró plantilla de bienvenida");
            return false;
        }
        
        try {
            $mail = self::setupMailer();
            $mail->addAddress($email);
            $mail->Subject = $plantilla['asunto'];
            
            // Reemplazar variables
            $url = APP_URL . "/log?h={$hash}";
            $mail->Body = self::procesarPlantilla($plantilla, [
                'link_acceso' => $url
            ]);
            
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

