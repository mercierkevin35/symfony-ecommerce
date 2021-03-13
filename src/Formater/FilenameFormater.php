<?php

namespace App\Formater;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class FilenameFormater {

    public function formatImageName(UploadedFile $file): string {
        $fullname = &$_FILES['Product']['unique_name']['illustration']['file'];
        $filename = uniqid("product_", true);
        $extension = $file->guessExtension();
        $fullname = "$filename.$extension";

        return $fullname;
    }
}