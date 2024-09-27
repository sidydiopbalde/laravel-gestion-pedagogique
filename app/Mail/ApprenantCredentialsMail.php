<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ApprenantCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $matricule;
    public $defaultPassword;
    public $qrcode;

    public function __construct($email, $defaultPassword, $matricule, $qrcode)
    {
        $this->email = $email;
        $this->matricule = $matricule;
        $this->defaultPassword = $defaultPassword;
        $this->qrcode = $qrcode; // Correct assignment
    }

    public function build()
    {
        // dd($this->email);
        if (empty($this->qrcode)) {
            Log::error("QR code non disponible pour : {$this->email}");
        }
    
        return $this->subject('Vos informations de connexion')
                    ->view('emails.relance_app')
                    ->with([
                        'email' => $this->email,
                        'password' => $this->defaultPassword,
                        'loginLink' => route('login'),
                        'qrcode' => $this->qrcode,
                    ])
                    ->attach($this->qrcode);
    }
    
}
