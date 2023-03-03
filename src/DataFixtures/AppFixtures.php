<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\TrickComment;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher,
        private readonly UserRepository $userRepository
    ){}

    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Create 10 users
        for($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setAvatar($faker->imageUrl($width = 640, $height = 480));
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword($this->hasher->hashPassword($user, 'password'));
            $user->setCreatedAt(new DateTimeImmutable());
            $manager->persist($user);
        }

        $manager->flush();

        // Create 10 categories
        for($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName("Category $i");
            $manager->persist($category);
        }

        $manager->flush();

        // Create 10 tricks
        for($i = 0; $i < 50; $i++) {
            $trick = new Trick();
            $trick->setUser($user);
            $trick->setCategory($category);
            $trick->setName("Trick $i");
            $trick->setSlug("trick-$i");
            $trick->setFeatured(false);
            $trick->setDescription("Description $i");
            $trick->setCreatedAt(new DateTimeImmutable());
            $trick->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($trick);
        }

        $manager->flush();

        // ## Create 10 comments
        // for($i = 0; $i < 50; $i++) {
        //     $comment = new TrickComment();
        //     $comment->setUser($this->userRepository->find(1));
        //     $comment->setTrick($trick);
        //     $comment->setContent("Comment $i");
        //     $comment->setCreatedAt(new DateTimeImmutable());
        //     $manager->persist($comment);
        // }
    }
}
