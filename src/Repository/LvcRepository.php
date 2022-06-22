<?php

namespace App\Repository;

use App\Entity\Lvc;
use App\Service\Utils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lvc>
 *
 * @method Lvc|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lvc|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lvc[]    findAll()
 * @method Lvc[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LvcRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lvc::class);
    }

    public function add(Lvc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lvc $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * méthode qui enregistre en BDD des objets Lvc hydratés avec les données issues du 'scraping'
     * un tableau des objets insérés est retourné
     *
     * @param $data
     * @return void
     */
    public function saveData($data): void
    {
        // TODO : cette méthode duplique celle de l'Entité Cac : à voir si je peux créer un BaseRepository...
        foreach ($data as $item) {
            $entity = new Lvc();
            // je transforme le format 'May 23, 2022' en timestamp, puis j'en fais une date et enfin je l'enregistre comme DateTime
            $date = strtotime($item[0]);
            $date = date('d-m-Y', $date);
            $entity->setCreatedAt(\DateTime::createFromFormat('d-m-Y', $date));
            $entity->setClosing(Utils::fromString($item[1]));
            $entity->setOpening(Utils::fromString($item[2]));
            $entity->setHigher(Utils::fromString($item[3]));
            $entity->setLower(Utils::fromString($item[4]));

            $this->getEntityManager()->persist($entity);
        }
        $this->getEntityManager()->flush();
    }
}
