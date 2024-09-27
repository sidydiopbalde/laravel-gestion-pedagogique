<?php



namespace App\Jobs;

use App\Mail\AuthMail;
use App\Services\PdfService;
use App\Services\QrCodeService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendAuthEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $password;
    protected $nom;
    protected $prenom;

    protected $qrCodeService;
    protected $pdfService;

    public function __construct($email, $password, $nom, $prenom, QrCodeService $qrCodeService, PdfService $pdfService)
    {
        $this->email = $email;
        $this->password = $password;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }

    public function handle()
    {
        // Générer le QR code
        $qrCodeData = [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'password' => $this->password,
        ];

        // Chemin pour sauvegarder le QR code
        $fileName = 'qrcode_' . uniqid() . '.png';
        $qrCodePath = $this->qrCodeService->generateQrCode(json_encode($qrCodeData), $fileName);

        // Créer le dossier pour les emails s'il n'existe pas
        if (!file_exists(storage_path('app/public/auth_emails'))) {
            mkdir(storage_path('app/public/auth_emails'), 0755, true);
        }

        // Générer le PDF
        $pdfFilePath = storage_path('app/public/auth_emails/' . uniqid() . '_auth_info.pdf');
        $pdfData = [
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'email' => $this->email,
            'password' => $this->password,
            'qrCodePath' => $qrCodePath, // QR Code pour le PDF
        ];

   
        $this->pdfService->generatePdf('emails.auth', ['nom' => $this->nom, 'prenom' => $this->prenom, 'email' => $this->email, 'password' => $this->password, 'qrCodePath' => $qrCodePath], $pdfFilePath);

        Mail::to($this->email)->send(new AuthMail($this->nom, $this->prenom, $this->email, $this->password, $pdfFilePath));
    }
}



// namespace App\Jobs;

// use App\Mail\AuthMail;
// use Illuminate\Bus\Queueable;
// use Illuminate\Mail\Mailables;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Support\Facades\Mail;

// class SendAuthEmailJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $email;
//     protected $password;
//     protected $nom;
//     protected $prenom;

//     public function __construct($email, $password, $nom, $prenom)
//     {
//         $this->email = $email;
//         $this->password = $password;
//         $this->nom = $nom;
//         $this->prenom = $prenom;
//     }

//     public function handle()
//     {
//         Mail::to($this->email)->send(new AuthMail($this->nom, $this->prenom, $this->email, $this->password));
//     }
// }

