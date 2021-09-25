<?php


namespace Ghost\Services;


use Ghost\Exception\ValidationException;
use Slim\Psr7\UploadedFile;

class FilesHandler
{


    /**
     * @param UploadedFile $file
     * @param string $dest
     * @param string|null $name
     * @return array
     */
    public function uploadImage (UploadedFile $file, string $dest, ?string $name = ''): array {


        $imageExtensions = ['.jpg', '.png', '.jpeg', '.gif'];
        $errors = [];

        if(empty($file->getFilePath()))
            throw new ValidationException('Veuillez ajouter une image ou votre fichier est trop volumineux.', ['Veuillez ajouter une image ou votre fichier est trop volumineux.']);
        if(empty($name))
            throw new ValidationException('Impossible de valider votre image', ['Veuillez ajouter un nom à l\'image.']);


        $path = $file->getFilePath();
        $extension = '.' . strtolower(pathinfo($file->getClientFilename(),PATHINFO_EXTENSION));
        $check = getimagesize($file->getFilePath());

        $fileDir = dirname(__DIR__).'/../public/assets/img/'.$dest.'/';

        $destination = $fileDir.$name.$extension;

        // Check if image file is a actual image or fake image
        if($check === false) $errors['fake'] = 'Votre fichier n\'est pas valide';
        // Check if file already exists
        if (!file_exists($fileDir)) $errors['file'] = 'Votre dossier de destination n\'existe pas.';
        // Check file size 2MB
        if ($file->getSize() > 20000000) $errors['size'] = 'Votre fichier est trop volumineux';
        // validate that the mimeType is valid
        if(!in_array($extension, $imageExtensions)) $errors['type'] = 'Veuillez utiliser un jpg, gif, jpeg ou png.';

        if(!empty($errors)) throw new ValidationException('Impossible de valider votre image', $errors);

        if (!move_uploaded_file($path, $destination))
            throw new ValidationException('Impossible de valider votre image', ['Impossible d\'envoyer votre image, veuillez contacter le developpeur du site.']);

        return [
            'dest' => $destination,
            'file' => $dest.'/'.$name.$extension
        ];

    }

    public function deleteImage (string $target): bool {

        $path = dirname(__DIR__). '/../public/assets/img/';
        if(is_file($path.$target)) return unlink($path.$target);
        
        return false;
    }

}