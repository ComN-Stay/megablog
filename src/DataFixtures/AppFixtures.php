<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\Articles;
use App\Entity\Categories;
use App\Entity\Comments;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $faker;
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->truncate($manager);
        
        $team = new Team;
        $team->setEmail('xavier.tezza@comnstay.fr');
        $hashedPassword = $this->passwordHasher->hashPassword(
            $team,
            'Legion@2023'
        );
        $team->setPassword($hashedPassword);
        $team->setRoles(['ROLE_ADMIN']);
        $team->setFirstname('Xavier');
        $team->setLastname('TEZZA');
        $manager->persist($team);

        $this->userFixtures($manager);
        $this->categoryFixtures($manager);
        $this->articleFixtures($manager);
        $this->commentFixtures($manager);
    }

    protected function userFixtures($manager) {
        for($i=1; $i<=10; $i++) {
            $user[$i] = new User;
            $user[$i]->setEmail('user' . $i . '@comnstay.fr');
            $user[$i]->setFirstname($this->faker->firstname());
            $user[$i]->setLastname($this->faker->lastname());
            $user[$i]->setAddress($this->faker->streetName());
            $user[$i]->setPostalCode($this->faker->postcode());
            $user[$i]->setTown($this->faker->city());
            $hashedPassword = $this->passwordHasher->hashPassword($user[$i], 'Legion@2023');
            $user[$i]->setPassword($hashedPassword);
            $user[$i]->setRoles(['ROLE_VISITOR']);
            $manager->persist($user[$i]);
        }
        $manager->flush();
    }

    protected function categoryFixtures($manager) {
        for($i=1; $i<=10; $i++) {
            $cat[$i] = new Categories;
            $cat[$i]->setName($this->faker->words(1, true));
            $cat[$i]->setDescription($this->faker->text(rand(30, 80)));
            $cat[$i]->setLogo('https://loremflickr.com/640/480/pets');
            $manager->persist($cat[$i]);
        }
        $manager->flush();
    }

    protected function articleFixtures($manager) {
        for($i=1; $i<=50; $i++) {
            $art[$i] = new Articles;
            $art[$i]->setTitle($this->faker->sentence(rand(3,6)));
            $art[$i]->setShortDescription($this->faker->text(rand(30, 80)));
            $art[$i]->setDescription($this->faker->text(rand(150, 400)));
            $art[$i]->setDateAdd($this->faker->dateTime());
            $art[$i]->setStatus(true);
            $art[$i]->setLogo('https://loremflickr.com/640/480/pets');
            $art[$i]->setFkTeam($this->getReferencedObject('App\Entity\Team', 1, $manager));
            $art[$i]->setFkCategory($this->getRandomReference(Categories::class, $manager));
            $manager->persist($art[$i]);
        }
        $manager->flush();
    }

    protected function commentFixtures($manager) {
        for($i=1; $i<=200; $i++) {
            $com[$i] = new Comments;
            $com[$i]->setText($this->faker->text(rand(30, 80)));
            $com[$i]->setDateAdd($this->faker->dateTime());
            $com[$i]->setFkArticle($this->getRandomReference(Articles::class, $manager));
            $com[$i]->setFkUser($this->getRandomReference(User::class, $manager));
            $status = ($i % 2 == 0) ? true : false;
            $com[$i]->setStatus($status);
            $manager->persist($com[$i]);
        }
        $manager->flush();
    }

    protected function getReferencedObject(string $className, int $id, object $manager) {
        return $manager->find($className, $id);
    }

    protected function getRandomReference(string $className, object $manager) {
        $list = $manager->getRepository($className)->findAll();
        return $list[array_rand($list)];
    }

    protected function truncate($manager) : void
    {
        /** @var Connection db */
        $db = $manager->getConnection();

        // start new transaction
        $db->beginTransaction();

        $sql = '
            SET FOREIGN_KEY_CHECKS = 0;
            TRUNCATE team;
            TRUNCATE user;
            TRUNCATE categories;
            TRUNCATE articles;
            TRUNCATE comments; 
            SET FOREIGN_KEY_CHECKS=1;
            ';
        $db->prepare($sql);
        $db->executeQuery($sql);

        $db->commit();
        $db->beginTransaction();
    }
}
