<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class ArchiveServiceFactory
{
    // public static function create(): ArchiveServiceCommunInterface
    // {
    //     $service = env('ARCHIVE_SERVICE', 'MongoDB'); // Par défaut, on utilise MongoDB
    //     Log::info($service);

    //     if ($service === 'firebase') {
    //         return app(FirebaseServiceInterface::class);
    //     }

    //     return app(ArchiveService::class);
    // }
}
