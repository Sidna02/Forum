<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Image;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
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

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(User $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(User $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findLatestMember(): ?User
    {
        $result = $this->_em->createQuery("SELECT u FROM App:User u ORDER BY u.registeredAt DESC")
            ->setMaxResults(1)
            ->getResult();
        if (empty($result)) {
            return null;
        }
        return $result[0];
    }
    public function countUsers(): int
    {
        $query = $this->_em->createQuery('SELECT count(u) as count FROM App:User u');
        return $query->getOneOrNullResult()['count'];
    }
    public function countBirthDatesByYear()
    {
        $emConfig = $this->_em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('YEAR', 'DoctrineExtensions\Query\Mysql\Year');
        $query = $this->_em->createQuery('SELECT YEAR(u.birthdate) y, count(u) c  FROM App:User u WHERE u.birthdate is NOT NULL GROUP BY y ORDER BY y ASC');
        return $users = $query->getResult();
    }
    public function countRegistrationsByDay()
    {
        $rsm = new ResultSetMapping();
        $query = $this->_em->createQuery("SELECT DATE_FORMAT(u.registeredAt, '%Y-%m-%d') date, count(u) c from App:User u WHERE u.registeredAt is NOT NULL GROUP BY date  ");
        return $query->getResult();
    }
    public function fetchUserImage(User $user): ?Image
    {
        $query = $this->_em->createQuery("SELECT i FROM App:Image i  Where i.id = ?1")
            ->setParameter(1, $user->getProfileImage());
        return $query->getOneOrNullResult();
    }
}
