<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

class BookDeleteProcessor implements ProcessorInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        /** @var Book $book */
        $book = $data;

        // Supprimer le fichier
        $coverPath = $book->getCoverImagePath();
        if ($coverPath) {
            $absolutePath = __DIR__ . '/../../public' . $coverPath;
            if (file_exists($absolutePath)) {
                unlink($absolutePath);
            }
        }

        // Supprimer l'entité
        $this->em->remove($book);
        $this->em->flush();

        return null;
    }
}
