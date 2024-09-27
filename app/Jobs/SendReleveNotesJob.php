<?php

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ReleveService; // Service pour générer les relevés

class SendReleveNotesJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $apprenants;

    public function __construct(array $apprenants)
    {
        $this->apprenants = $apprenants;
    }

    // public function handle(ReleveService $releveService)
    // {
    //     // Boucle sur les apprenants et envoi des relevés
    //     foreach ($this->apprenants as $apprenant) {
    //         $releveService->envoyerReleve($apprenant);
    //     }
    // }
}

