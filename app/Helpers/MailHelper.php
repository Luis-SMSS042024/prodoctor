<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class MailHelper
{
    /**
     * Send a beautiful HTML email with an OTP code.
     *
     * @param string $toEmail
     * @param string $subject
     * @param string $title
     * @param string $messageText
     * @param string $otpCode
     * @return bool
     */
    public static function sendOtpMail(string $toEmail, string $subject, string $title, string $messageText, string $otpCode): bool
    {
        $html = self::getHtmlTemplate($title, $messageText, $otpCode);

        try {
            Mail::html($html, function ($message) use ($toEmail, $subject) {
                $message->to($toEmail)
                    ->subject($subject);
            });
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send HTML email to {$toEmail}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the beautiful HTML template for emails.
     */
    private static function getHtmlTemplate(string $title, string $messageText, string $otpCode): string
    {
        $year = date('Y');
        return '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($title) . '</title>
        </head>
        <body style="margin: 0; padding: 0; background-color: #030914; font-family: \'Plus Jakarta Sans\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color: #cbd5e1; -webkit-font-smoothing: antialiased;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #030914; padding: 40px 0;">
                <tr>
                    <td align="center">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 500px; background-color: #07111e; border: 1px solid #13283f; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 50px rgba(0,0,0,0.65);">
                            <!-- Header / Logo -->
                            <tr>
                                <td style="padding: 24px 30px; background-color: #060c18; border-bottom: 1px solid #13283f; text-align: center;">
                                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="margin: 0 auto;">
                                        <tr>
                                            <td style="vertical-align: middle; padding-right: 12px;">
                                                <div style="width: 44px; height: 44px; border-radius: 12px; background-color: rgba(0, 223, 178, 0.1); border: 1px solid rgba(0, 223, 178, 0.2); text-align: center; line-height: 44px; display: inline-block;">
                                                    <!-- Cross Icon -->
                                                    <span style="font-size: 26px; color: #00dfb2; font-weight: bold; font-family: Arial, sans-serif; vertical-align: middle;">+</span>
                                                </div>
                                            </td>
                                            <td style="text-align: left; vertical-align: middle;">
                                                <span style="font-size: 22px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px; display: block; line-height: 1; font-family: \'Plus Jakarta Sans\', Arial, sans-serif;">ProDoctor</span>
                                                <span style="font-size: 9px; color: #8fa0b5; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-top: 4px; font-family: Arial, sans-serif;">Gabinete Médico</span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <!-- Body Content -->
                            <tr>
                                <td style="padding: 35px 35px 25px 35px; text-align: center;">
                                    <h2 style="font-size: 22px; font-weight: 800; color: #ffffff; margin: 0 0 16px 0; font-family: \'Plus Jakarta Sans\', Arial, sans-serif;">' . htmlspecialchars($title) . '</h2>
                                    <p style="font-size: 14px; color: #8fa0b5; line-height: 1.6; margin: 0 0 30px 0; font-family: Arial, sans-serif;">' . htmlspecialchars($messageText) . '</p>
                                    
                                    <!-- OTP Code Box -->
                                    <div style="background-color: #040a12; border: 1px solid #162d4a; border-radius: 12px; padding: 20px 0; margin-bottom: 30px; text-align: center;">
                                        <span style="font-size: 36px; font-weight: 900; color: #00dfb2; font-family: monospace; letter-spacing: 4px; padding-left: 4px;">' . htmlspecialchars($otpCode) . '</span>
                                    </div>
                                    
                                    <p style="font-size: 12px; color: #4b5f76; line-height: 1.6; margin: 0; font-family: Arial, sans-serif;">
                                        Este código es de uso único y tiene una validez temporal. <br>
                                        Si tú no solicitaste este correo, puedes ignorarlo de forma segura.
                                    </p>
                                </td>
                            </tr>
                            <!-- Footer -->
                            <tr>
                                <td style="padding: 20px 35px; background-color: #060c18; border-top: 1px solid #13283f; text-align: center; font-size: 11px; color: #4b5f76; font-family: Arial, sans-serif; font-weight: bold;">
                                    © ' . $year . ' ProDoctor. Todos los derechos reservados.<br>
                                    <span style="color: rgba(0, 223, 178, 0.45); display: block; margin-top: 6px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px;">Seguridad de Nivel Clínico</span>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ';
    }
}
