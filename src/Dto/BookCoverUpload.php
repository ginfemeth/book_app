<?php

namespace App\Dto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

class BookCoverUpload
{
    #[Assert\NotNull]
    #[Assert\File(
        maxSize: '5M',
        mimeTypes: ['image/jpeg', 'image/png'],
        mimeTypesMessage: 'Seuls les fichiers JPEG ou PNG sont autorisés.'
    )]
    public ?File $file = null;
}
