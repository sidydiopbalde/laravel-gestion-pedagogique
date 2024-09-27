<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AuthMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nom;
    public $prenom;
    public $email;
    public $password;
    public $pdfFilePath;

    public function __construct($nom, $prenom, $email, $password, $pdfFilePath)
    {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
        $this->password = $password;
        $this->pdfFilePath = $pdfFilePath;
    }

    public function build()
    {
        return $this->view('emails.auth')
            ->subject('Lien d\'authentification')
            ->attach($this->pdfFilePath)  // Attacher le fichier PDF
            ->with([
                'nom' => $this->nom,
                'prenom' => $this->prenom,
                'email' => $this->email,
                'password' => $this->password,
            ]);
    }
}
