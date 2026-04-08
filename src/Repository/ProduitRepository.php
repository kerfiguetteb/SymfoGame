<?php

namespace App\Repository;

use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    // requete qui recupere les produit avec les images 
    // regle le probleme N+1
public function findAllWithImages(): array
{
    // On crée un QueryBuilder Doctrine pour l'entité principale (alias "p")
    // "p" sera utilisé comme alias dans la requête (ex: p.id, p.images)
    return $this->createQueryBuilder('p')
        // On fait une LEFT JOIN avec la relation "images" de l'entité p
        // Cela permet de récupérer les images associées au produit
        // LEFT JOIN permet de garder les produits même s'ils n'ont pas d'images
        ->leftJoin('p.images', 'i')
        // On ajoute "i" (les images) dans le SELECT
        // Cela permet à Doctrine de charger les images en même temps que les produits
        // et d'éviter le problème N+1 queries
        ->addSelect('i')
        // On trie les résultats par l'id du produit en ordre décroissant
        // Les éléments les plus récents apparaîtront en premier
        ->orderBy('p.id', 'DESC')
        // Transforme le QueryBuilder en objet Query exécutable
        ->getQuery()

        // Exécute la requête et retourne le résultat
        // Le résultat sera un tableau d'entités p avec leurs images chargées
        ->getResult();
}
    //    /**
    //     * @return Produit[] Returns an array of Produit objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Produit
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
