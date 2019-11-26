<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    public function getByName($name, $firstname, $biography)
    {
        // On récupère le QueryBuilder car il permet de faire les resquet SQL.
        $qb = $this->createQueryBuilder('author');

        //On construit la requette sql mais en PHP.
        $query = $qb->select('author')
            //Traduire la requete en véritable requette SQL.
            ->where('author.name LIKE :name')
            ->andWhere('author.firstname LIKE :firstname')
            ->andWhere('author.biography LIKE :biography')

            //Executer la requette en BDD
            ->setParameter('name', '%' . $name . '%')
            ->setParameter('firstname', '%' . $firstname . '%')
            ->setParameter('biography', '%' . $biography . '%' )
            ->getQuery();

        $resultats = $query->getResult();

        return $resultats;
    }

    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
