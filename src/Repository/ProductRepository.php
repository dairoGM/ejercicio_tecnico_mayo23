<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByFilters($filters = [])
    {
        $qb = $this->createQueryBuilder('qb')
            ->select("
                    qb.id, 
                    qb.name, 
                    qb.price,                    
                    qb.tags,                    
                    qb.category, 
                    qb.description, 
                    qb.additionalInformation, 
                    qb.score, 
                    qb.sku, 
                    qb.images, 
                    qb.status, 
                    DateFormat(qb.registerDate, 'DD/MM/YYYY') as registerDate");
        if (is_array($filters)) {
            foreach ($filters as $key => $value) {
                $qb->andWhere('qb.' . $key . "='" . $value . "'");
            }
        }
        $qb->orderBy('qb.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function findByFiltersPaginations($filters = [], $limit = null, $offset = null)
    {
        $qb = $this->createQueryBuilder('qb')
            ->select("
                    qb.id, 
                    qb.name, 
                    qb.price,                    
                    qb.tags,                    
                    qb.category, 
                    qb.description, 
                    qb.additionalInformation, 
                    qb.score, 
                    qb.sku, 
                    qb.images, 
                    qb.status, 
                    DateFormat(qb.registerDate, 'DD/MM/YYYY') as registerDate");
        if (is_array($filters)) {
            foreach ($filters as $key => $value) {
                $qb->andWhere('qb.' . $key . "='" . $value . "'");
            }
        }
        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);
        $qb->orderBy('qb.name', 'ASC');

        return $qb->getQuery()->getResult();
    }

    public function getByNotStock()
    {
        $qb = $this->createQueryBuilder('qb')
            ->select("
                    qb.id, 
                    qb.name, 
                    qb.price,                   
                    qb.tags,                   
                    qb.category, 
                    qb.description, 
                    qb.additionalInformation, 
                    qb.score, 
                    qb.sku, 
                    qb.images, 
                    qb.status, 
                    DateFormat(qb.registerDate, 'DD/MM/YYYY') as registerDate");

        $subQuery = $this->getEntityManager()->getRepository('App\Entity\Stock')->createQueryBuilder('subQb')
            ->select('spa.id')
            ->innerJoin('subQb.product', 'spa');
        $exp = $qb->expr()->notIn('qb.id', $subQuery->getDQL());
        $qb->andWhere($exp);

        $qb->orderBy('qb.name', 'ASC');
        return $qb->getQuery()->getResult();
    }


    public function getSoldProducts()
    {
        $qb = $this->createQueryBuilder('qb')
            ->select("
                    qb.id, 
                    qb.name, 
                    qb.price,                   
                    qb.tags,                   
                    qb.category, 
                    qb.description, 
                    qb.additionalInformation, 
                    qb.score, 
                    qb.sku, 
                    qb.images, 
                    qb.status, 
                    DateFormat(qb.registerDate, 'DD/MM/YYYY') as registerDate");

        $subQuery = $this->getEntityManager()->getRepository('App\Entity\Sales')->createQueryBuilder('subQb')
            ->select('spa.id')
            ->innerJoin('subQb.product', 'spa');
        $exp = $qb->expr()->in('qb.id', $subQuery->getDQL());
        $qb->andWhere($exp);

        $qb->orderBy('qb.name', 'ASC');
        return $qb->getQuery()->getResult();
    }

}
