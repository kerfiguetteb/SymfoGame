<?php

namespace App\Tests\Unit;

use App\Entity\Image;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\File;

final class ImageTest extends TestCase
{
    /**
     * Test : vérifie que setImageFile() met à jour updatedAt
     */
    public function testSetImageFileUpdatesUpdatedAt(): void
    {
        // Création d'une instance de l'entité Image
        $image = new Image();

        // Création d'un fichier temporaire sur le système
        // tempnam() génère un chemin unique
        $tmpFile = tempnam(sys_get_temp_dir(), 'img_');

        // Écrit du contenu dans ce fichier (sinon File peut échouer)
        file_put_contents($tmpFile, 'fake content');

        // Création d'un objet File Symfony à partir du fichier temporaire
        $file = new File($tmpFile);

        // Vérifie qu'avant toute action, updatedAt est null
        self::assertNull($image->getUpdatedAt());

        // Appel de la méthode testée
        $image->setImageFile($file);

        // Vérifie que le fichier est bien stocké dans l'entité
        self::assertSame($file, $image->getImageFile());

        // Vérifie que updatedAt a été mis à jour automatiquement
        // (effet de bord important pour VichUploader)
        self::assertInstanceOf(\DateTimeImmutable::class, $image->getUpdatedAt());

        // Supprime le fichier temporaire pour éviter de polluer le système
        unlink($tmpFile);
    }
}