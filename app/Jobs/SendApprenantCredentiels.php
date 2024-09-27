<?php
use App\Mail\ApprenantCredentialsMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ApprenantFirebaseModel;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
class SendApprenantCredentials implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $apprenant;
    protected $password;
    public function __construct(ApprenantFirebaseModel $apprenant,$password)
    {
        if (!isset($apprenant->email)) {
            throw new \Exception("L'email de l'apprenant est manquante.");
        }
        $this->apprenant = $apprenant;
        $this->password = $password;
    }
    public function handle()
    {
        // Vérifie que l'apprenant a un email avant d'envoyer le mail
        if ($this->apprenant->email) {
            // Mail::to($this->apprenant->email)->send(new ApprenantCredentialsMail($this->apprenant,$this->password));
        }
    }
}