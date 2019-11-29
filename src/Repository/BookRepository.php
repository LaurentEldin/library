<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function getByStyle($style, $title, $inStock, $author)
    {
        // On récupère le QueryBuilder car il permet de faire les resquet SQL.
        $qb = $this->createQueryBuilder('book');

        //On construit la requette sql mais en PHP.
        $query = $qb->select('book')
            //Traduire la requete en véritable requette SQL.
            ->where('book.style LIKE :style')
            ->andWhere('book.title LIKE :title')
            ->andWhere('book.author LIKE :author')

            //Executer la requette en BDD
            ->setParameter('style', '%' . $style . '%')
            ->setParameter('title', '%' . $title . '%')
            ->setParameter('author','%' . $author . '%');

        //Boucle pour savoir si le livre est dispo ou pas.
        if ($inStock === 'ok') {
            $query = $qb->andWhere('book.inStock = :inStock')
                ->setParameter('inStock', true);
        }

            //On rappel ici le Query car on l'a coupé plus haut avec un ";"
            $query = $qb->getQuery();
        $resultats = $query->getArrayResult();

        return $resultats;
    }

//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//
//    public function findByExampleField($value)
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }
//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
