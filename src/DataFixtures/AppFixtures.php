<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Create 10 users
        for($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setAvatar($faker->imageUrl($width = 640, $height = 480));
            $user->setEmail($faker->email);
            $user->setUsername($faker->userName);
            $user->setPassword("password");
            $user->setCreatedAt(new DateTimeImmutable());
            $manager->persist($user);
        }

        // Create 10 categories
        for($i = 0; $i < 10; $i++) {
            $category = new Category();
            $category->setName("Category $i");
            $manager->persist($category);
        }

        // Create 10 tricks
        for($i = 0; $i < 10; $i++) {
            // $trick = new Trick();
            // $trick->setUserId(rand(1, 10));
            // $trick->setCategoryId(rand(1, 10));
            // $trick->setName("Trick $i");
            // $trick->setDescription("Description $i");
            // $trick->setCreatedAt(new DateTimeImmutable());
            // $trick->setUpdatedAt(new DateTimeImmutable());
            // $manager->persist($trick);
        }

        $manager->flush();
    }
}
