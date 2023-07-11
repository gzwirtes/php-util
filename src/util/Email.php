<?php

namespace GZInfo\Util;

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    protected $host;
    protected $username;
    protected $password;
    protected $SMTP;
    protected $port;
    protected $nomeEmail;
    protected $nome;
    protected $emails;
    protected $emailsCCO;
    protected $emailsCC;
    protected $anexos;
    protected $titulo;
    protected $corpo;
    protected $delete;

    public function __construct()
    {
        $this->emails    = [];
        $this->emailsCCO = [];
        $this->emailsCC  = [];
        $this->anexos    = [];
        $this->delete    = true;
    }


    public function enviaEmail(){
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = $this->SMTP;
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';

            //Recipients
            $mail->setFrom($this->nomeEmail, $this->nome);

            foreach($this->emails as $email)
            {
                if ($email <> '')
                {
                    $mail->AddAddress($email);
                }
            }

            foreach($this->emailsCC as $email)
            {
                if ($email <> '')
                {
                    $mail->addCC($email);
                }
            }

            foreach($this->emailsCCO as $email)
            {
                if ($email <> '')
                {
                    $mail->addCC($email);
                }
            }

            // Attachments
            foreach($this->anexos as $anexo)
            {
                if ($anexo <> '')
                {
                    $mail->addAttachment($anexo);
                }
            }

            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $this->titulo;
            $mail->Body    = $this->corpo;

            $mail->send();

            if ($this->delete)
            {
                foreach($this->anexos as $anexo)
                {
                    if ($anexo <> '')
                    {
                        unlink($anexo);
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception('<b>Erro ao enviar o e-mail: </b> ' . $e->getMessage() );
        }
    }

    /**
     * Get the value of host
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the value of host
     *
     * @return  self
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Get the value of username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the value of username
     *
     * @return  self
     */
    public function setUsername(string $username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set the value of password
     *
     * @return  self
     */
    public function setPassword(string $password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get the value of SMTP
     */
    public function getSMTP()
    {
        return $this->SMTP;
    }

    /**
     * Set the value of SMTP
     *
     * @return  self
     */
    public function setSMTP(string $SMTP)
    {
        $this->SMTP = $SMTP;

        return $this;
    }

    /**
     * Get the value of port
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the value of port
     *
     * @return  self
     */
    public function setPort(string $port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the value of nomeEmail
     */
    public function getNomeEmail()
    {
        return $this->nomeEmail;
    }

    /**
     * Set the value of nomeEmail
     *
     * @return  self
     */
    public function setNomeEmail(string $nomeEmail)
    {
        $this->nomeEmail = $nomeEmail;

        return $this;
    }

    /**
     * Get the value of nome
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * Set the value of nome
     *
     * @return  self
     */
    public function setNome(string $nome)
    {
        $this->nome = $nome;

        return $this;
    }

    /**
     * Get the value of emails
     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set the value of emails
     *
     * @return  self
     */
    public function setEmails(Array $emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get the value of emailsCCO
     */
    public function getEmailsCCO()
    {
        return $this->emailsCCO;
    }

    /**
     * Set the value of emailsCCO
     *
     * @return  self
     */
    public function setEmailsCCO(Array $emailsCCO)
    {
        $this->emailsCCO = $emailsCCO;

        return $this;
    }

    /**
     * Get the value of emailsCC
     */
    public function getEmailsCC()
    {
        return $this->emailsCC;
    }

    /**
     * Set the value of emailsCC
     *
     * @return  self
     */
    public function setEmailsCC(Array $emailsCC)
    {
        $this->emailsCC = $emailsCC;

        return $this;
    }

    /**
     * Get the value of anexos
     */
    public function getAnexos()
    {
        return $this->anexos;
    }

    /**
     * Set the value of anexos
     *
     * @return  self
     */
    public function setAnexos(Array $anexos)
    {
        $this->anexos = $anexos;

        return $this;
    }

    /**
     * Get the value of titulo
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set the value of titulo
     *
     * @return  self
     */
    public function setTitulo(string $titulo)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get the value of corpo
     */
    public function getCorpo()
    {
        return $this->corpo;
    }

    /**
     * Set the value of corpo
     *
     * @return  self
     */
    public function setCorpo(string $corpo)
    {
        $this->corpo = $corpo;

        return $this;
    }

    /**
     * Get the value of delete
     */
    public function getDelete()
    {
        return $this->delete;
    }

    /**
     * Set the value of delete
     *
     * @return  self
     */
    public function setDelete(bool $delete)
    {
        $this->delete = $delete;

        return $this;
    }
}
