<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\BookCoverUpload;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BookCoverUploadProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private string $uploadDir = __DIR__ . '/../../public/uploads/books'
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        // 1) Récupérer la Request
        $request = $context['request'];

        // 2) Récupérer le fichier envoyé
        $file = $request->files->get('file');

        if (!$file) {
            throw new \Exception("Invalid data for cover upload.");
        }

        // 3) Récupérer le Book
        $book = $this->em->getRepository(Book::class)->find($uriVariables['id']);

        if (!$book) {
            throw new \Exception("Book not found.");
        }

        // 4) Vérifier la taille du fichier (max 5 Mo)
        $maxSize = 5 * 1024 * 1024; // 5 Mo
        if ($file->getSize() > $maxSize) {
            throw new \Exception("Le fichier dépasse la taille maximale autorisée de 5 Mo.");
        }

        // 5) Vérifier le type MIME autorisé (JPEG + PNG)
        $allowedMimeTypes = ['image/jpeg', 'image/png'];
        $mimeType = $file->getMimeType();

        if (!in_array($mimeType, $allowedMimeTypes, true)) {
            throw new \Exception("Seuls les formats d’image JPEG et PNG sont autorisés.");
        }

        // 6) Déterminer l’extension en fonction du MIME
        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            default => throw new \Exception("Format non supporté."),
        };

        // 7) Supprimer l’ancienne image si elle existe
        if ($book->getCoverImagePath()) {
            $oldPath = __DIR__ . '/../../public' . $book->getCoverImagePath();
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // 8) Générer un nom unique et déplacer le fichier
        $filename = uniqid('cover_') . '.' . $extension;
        $file->move('uploads/books', $filename);

        // 9) Mettre à jour l’entité
        $book->setCoverImagePath('/uploads/books/' . $filename);

        // 10) Sauvegarder
        $this->em->flush();

        return $book;
    }
}
