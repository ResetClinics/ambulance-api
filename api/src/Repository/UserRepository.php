<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(int $id): User
    {
        /** @var User $user */
        if (!$user = $this->find($id)) {
            throw new EntityNotFoundException(
                sprintf('User id: %s is not found.', $id)
            );
        }
        return $user;
    }


    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function findOneByExternalId(int $externalId): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getByExternalId(int $externalId): User
    {
        if ($user =  $this->createQueryBuilder('u')
            ->andWhere('u.externalId = :externalId')
            ->setParameter(':externalId', $externalId)
            ->getQuery()
            ->getOneOrNullResult()){
            return  $user;
        }
        throw new \DomainException(sprintf('User externalId "%s" notfound.', $externalId));
    }

    public function getCountUsers(): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.externalId)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllByRole(string $role)
    {
        return $this->createQueryBuilder('u')
            ->where('u.roles LIKE :roles')
            ->andWhere('u.active = 1')
            ->andWhere('u.hideInReports != 1')
            ->setParameter('roles', '%"' . $role . '"%')
            ->orderBy('u.name')
            ->getQuery()
            ->getResult();
    }
}
