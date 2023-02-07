<?php

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private $passwordHasher;
    public function __construct(UserPasswordHasherInterface $passwordHasher) {
    $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
    $this->loadPhotos($manager);
    $this->loadUsers($manager);
    $manager->flush();
    }

    public function loadPhotos(ObjectManager $manager)
    {
    for ($i = 1; $i <= 5; $i++){
    $photo = new Photo();
    $photo->setTitle('Photo numéro '.$i);
    $photo->setPostAt((new \DateTimeImmutable())->add(\DateInterval::createFromDateString('-'.$i.' week')));
    $manager->persist($photo);
    }
    $manager->flush(); // demande à doctrine de créer l’ordre sql INSERT
    }
    public function loadUsers(ObjectManager $manager): void
    {
        $user = new User();
        $user->setPseudo('User_1');
        $user->setEmail('user1@dwwm.fr');
        $user->setPassword($this->passwordHasher->hashPassword($user, 'user1'));
        $user->setRoles(['ROLE_INTERNAUTE']);
        $manager->persist($user);
        $manager->flush();
    }
}
 

