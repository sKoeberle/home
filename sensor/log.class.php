<?php

/**
 * @author     : Stéphane KOEBERLE
 * @copyright  : Link To Business Technology
 */
class log
{

    private $datetime;
    private $content;

    /**
     * Construction de l'objet log
     *
     * @updated 20180216
     * @param string $type Type du log : ACCESS|DB|DEBUG|ERROR|EXEC
     * @param string $content Contenu du log à écrire
     * @throws phpmailerException
     */
    public function __construct($type = 'ACCESS|DB|DEBUG|ERROR|EXEC', $content)
    {

        $date = new DateTime('now', new DateTimeZone('Europe/Paris'));
        $this->datetime = $date->format('Ymd-H:i:s');
        $this->date = $date->format('Ymd');
        $this->time = $date->format('H:i:s');

        $type = strtoupper($type);
        if (in_array($type, ['ACCESS', 'DB', 'DEBUG', 'ERROR', 'EXEC'])) {
            $this->type = $type;
        } else {
            $this->type = 'UNKNOWN';
        }

        $this->content = $content;

        self::writeLogFile();

    }


    /**
     * Ecriture du log dans un fichier texte date_du_jour-log.txt dans le dossier /__log/
     *
     * @updated 20180216
     * @return void
     * @throws phpmailerException
     */
    private function writeLogFile()
    {

        if (ini_get('safe_mode')) {
            //
        } else {


            // Formatage de la ligne à ajouter
            $strSearch = ['<br>', '<br/>', '<br />', '\r\n', '\r', '\n'];
            $strReplace = ' ';
            $this->content = str_replace($strSearch, $strReplace, $this->content);
            $trace = "";


            // Traitement pour un log du type base de données
            if ($this->type == 'DB') {
                $this->content = preg_replace('/\s+/', ' ', $this->content);
            }

            // Traitement pour un log du type erreur ou si debug
            if ($this->type == 'ERROR' || __DEBUG === true) {


                // Backtrace
                $debug_backtrace = debug_backtrace();
                if (count($debug_backtrace) > 1) {
                    $backtrace = end($debug_backtrace);
                } else {
                    $backtrace = reset($debug_backtrace);
                }

                $function = $backtrace['function'];
                $file = str_replace($_SERVER['DOCUMENT_ROOT'] . "/home/sensor/", "", $backtrace['file']);
                $line = $backtrace['line'];

                $trace = "[{$file}:{$line}:{$function}] ";

            }


            // Formatage de la ligne
            $type = sprintf("%-7s", $this->type);
            $this->content = "[{$this->date}-{$this->time}] [{$type}] {$trace}{$this->content}\r\n";


            // Création du dossier __log si inexistant
            self::createFolder('/home/sensor/__log');


            // Ecriture du log dans le fichier log ouvert en mode 'a' (append)
            try {
                $handle = fopen($_SERVER['DOCUMENT_ROOT'] . "/home/sensor/__log/log.txt", "a+");
                if ($handle) {
                    fwrite($handle, $this->content);
                    fclose($handle);
                }

            } catch (Exception $e) {
                $this->sendLogByEmail($e);
            }


            // Envoi d'un email en cas d'erreur
            if ($this->type == 'ERROR') {
                $this->sendLogByEmail();
            }
        }
    }


    /**
     * Envoi du log par email si une erreur est survenue
     *
     * @updated 20180216
     * @param void
     * @return void
     * @throws phpmailerException
     */
    private function sendLogByEmail($exception = null)
    {
        if ($exception) {
            $this->content .= $exception;
        }

        if (!defined('__BR__')) {
            define('__BR__', '<br>');
        }


        $message = "Bonjour," . __BR__ . __BR__
            . "Voici le détail de l'erreur :" . __BR__ . __BR__
            . "Date : <b>" . $this->datetime . "</b>" . __BR__
            . "Utilisateur : <b>" . $_SESSION['user']['UID'] . "</b>" . __BR__
            . "Id session : <b>" . session_id() . "</b>" . __BR__
            . "Détail : <b>" . stripslashes($this->content) . "</b>" . __BR__ . __BR__
            . "_________" . __BR__
            . "Le robot";


        // Include class
        include_once($_SERVER['DOCUMENT_ROOT'] . '/__class/phpMailer/class.phpmailer.php');

        if (class_exists('PHPMailer')) {
            //Create a new PHPMailer instance
            $mail = new PHPMailer;


            //Set who the message is to be sent from
            $mail->setFrom($_SESSION['system']['system_email'], $_SESSION['system']['app_title']);


            //Set an alternative reply-to address
            //Set who the message is to be sent to
            $mail->addAddress($_SESSION['system']['admin_email'], 'Admin');


            //Set the subject line
            $mail->Subject = $_SESSION['system']['app_title'] . ' - LOG';


            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML($message);


            //Replace the plain text body with one created manually
            $mail->AltBody = 'This is a plain-text message body';


            //Attach the log file
            $mail->addAttachment($_SERVER['DOCUMENT_ROOT'] . '__log/log.txt');

        }
    }


    /**
     * Création d'un dossier
     *
     * @updated 20180215
     * @param string $folder Chemin relatif vers le dossier
     * @throws phpmailerException
     */
    function createFolder($folder)
    {
        $old = umask(0);

        if (!is_dir($_SERVER['DOCUMENT_ROOT'] . $folder)) {
            try {
                @mkdir($_SERVER['DOCUMENT_ROOT'] . $folder, 0777, true);
            } catch (Exception $e) {
                new log("ERROR", "An error occurred when trying to create folder " . $folder . ": " . $e->getMessage());
            }

        }

        umask($old);

    }
}