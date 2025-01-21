<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
//    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
//        $this->passwordHasher = $passwordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@gmail.com');
        $user->setPassword($this->passwordHasher->hashPassword(
            $user,
            'password'
        ));
        $manager->persist($user);

        $taskRecent = new Task();
        $taskRecent->setName('Tâche récente');
        $taskRecent->setDescription('Tâche créée il y a 3 jours.');
        $taskRecent->setAuthor($user);
        $taskRecent->setCreatedAt(new \DateTime('-3 days'));
        $taskRecent->setUpdatedAt(new \DateTime());
        $manager->persist($taskRecent);

        $taskOld = new Task();
        $taskOld->setName('Tâche ancienne');
        $taskOld->setDescription('Tâche créée il y a 10 jours.');
        $taskOld->setAuthor($user);
        $taskOld->setCreatedAt(new \DateTime('-10 days'));
        $taskOld->setUpdatedAt(new \DateTime());
        $manager->persist($taskOld);


        $user1= new User();
        $user1->setEmail('user1@gmail.com');
        $user1->setPassword($this->passwordHasher->hashPassword(
            $user1,
            'password1'
        ));
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        $user2= new User();
        $user2->setEmail('user2@gmail.com');
        $user2->setPassword($this->passwordHasher->hashPassword(
            $user2,
            'password2'
        ));
        $user2->setRoles(['ROLE_ADMIN']);
        $manager->persist($user2);

        $user3= new User();
        $user3->setEmail('user3@gmail.com');
        $user3->setPassword($this->passwordHasher->hashPassword(
            $user3,
            'password3'
        ));
        $user3->setRoles(['ROLE_USER']);
        $manager->persist($user3);

        $user4= new User();
        $user4->setEmail('user4@gmail.com');
        $user4->setPassword($this->passwordHasher->hashPassword(
            $user4,
            'password4'
        ));
        $user4->setRoles(['ROLE_ADMIN']);
        $manager->persist($user4);




        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
