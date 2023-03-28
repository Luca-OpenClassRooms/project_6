<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Trick;
use App\Entity\TrickComment;
use App\Entity\TrickMedia;
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

        $users = $manager->getRepository('App\Entity\User')->findAll();
        $categories = $manager->getRepository('App\Entity\Category')->findAll();

        // Create 10 tricks
        for($i = 0; $i < 50; $i++) {
            $trick = new Trick();
            $trick->setUser($users[array_rand($users)]);
            $trick->setCategory($categories[array_rand($categories)]);
            $trick->setName("Trick $i");
            $trick->setSlug("trick-$i");
            $trick->setFeatured(false);
            $trick->setDescription($faker->paragraphs($nb = 3, $asText = true));
            $trick->setCreatedAt(new DateTimeImmutable());
            $trick->setUpdatedAt(new DateTimeImmutable());
            $manager->persist($trick);
        }

        $manager->flush();

        ## Create 10 comments
        $tricks = $manager->getRepository('App\Entity\Trick')->findAll();

        foreach($tricks as $trick) {
            for($i = 0; $i < 10; $i++) {
                $comment = new TrickComment();
                $comment->setUser($users[array_rand($users)]);
                $comment->setTrick($trick);
                $comment->setContent($faker->paragraphs($nb = 1, $asText = true));
                $comment->setCreatedAt(new DateTimeImmutable());
                $manager->persist($comment);
            }

            for($i = 0; $i < 10; $i++) {
                $media = new TrickMedia();
                $media->setTrick($trick);
                $media->setType('image');
                $media->setContent($faker->imageUrl($width = 640, $height = 480, 'trick'));
                $media->setTitle($faker->sentence($nbWords = 6, $variableNbWords = true));
                $manager->persist($media);
            }
        }

        $manager->flush();
    }
}
