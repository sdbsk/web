<?php

namespace App\Repository;

use App\Entity\DarujmeCampaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DarujmeCampaign>
 */
class DarujmeCampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DarujmeCampaign::class);
    }
}
