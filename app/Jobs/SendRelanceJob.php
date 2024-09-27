<?php
namespace App\Jobs;

use App\Mail\ApprenantCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRelanceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nom;
    protected $password;
    protected $email;
    protected $qrcode;
    public $matricule;

    public function __construct($nom, $password, $email, $matricule, $qrcode)
    {
        $this->nom = $nom;
        $this->password = $password;
        $this->email = $email;
        $this->qrcode = $qrcode; // Correct assignment
        $this->matricule = $matricule;
    }

    public function handle()
    {
        // Envoi à l'apprenant (plutôt que l'email hardcodé)
        Mail::to($this->email)->send(
            new ApprenantCredentialsMail($this->email, $this->password, $this->matricule, $this->qrcode)
        );
    }
}
