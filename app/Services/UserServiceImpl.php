<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Events\UsersCreated;
use App\Repository\UserRepository;
use Exception;
use App\Exceptions\ServiceException;
use App\Facades\UploadCloudImageFacade;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\UploadedFile;
  use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
class UserServiceImpl implements UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
  

  
    
   

    public function createUser(array $data)
    {
        // if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
        //     // Appeler le service d'upload pour sauvegarder la photo
        //     // dd($data);
        //     // $uploadedFileUrl = app(ImageUploadService::class)->uploadImage($data['photo'], 'image');
        //     $folder = 'images';
        //     // $filePath=Storage::path($file);
        //    $filePath = $data['photo']->getRealPath();
        //     // dd($filePath);
        //    // dd("sidy",$file,Storage::exists($file));
        //    $uploadedFileUrl = Cloudinary::upload($filePath, [
        //        'folder' => $folder
        //     ]);
        //     $uploadedFileUrl = $uploadedFileUrl->getSecurePath();
        //     // dd($uploadedFileUrl);
        //     // Vérifier si l'upload a réussi
        //     if ($uploadedFileUrl) {
        //         // Mettre à jour l'URL de la photo dans le tableau de données
        //         $data['photo'] = $uploadedFileUrl;
        //     } else {
        //         throw new \Exception("Erreur lors de l'upload de la photo.");
        //     }
        // }
        //  $user=$this->userRepository->createUser($data);
        // if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            //     $filePath = $data['photo']->store('temp');
            //     event(new UsersCreated($user,  $filePath));
            // }
            // dd($data);
          return $this->userRepository->createUser($data);
        }

   
}
