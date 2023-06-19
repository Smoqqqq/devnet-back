<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Theme;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $languages = ["php", "Javascript", "C", "Ruby", "Java", "C#"];
        $technos = ["Linux", "Windows", "Mac", "Embarqué", "Web", "Mobile"];

        $user = new User();
        $user->setEmail("paul@bhgroupe.fr")
            ->setUsername("Paul")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($user, "test"));

        $manager->persist($user);

        $user = new User();
        $user->setEmail("rz@gmail.com")
            ->setUsername("Romain")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($user, "test"));

        $manager->persist($user);

        $user = new User();
        $user->setEmail("admin@admin.net")
            ->setUsername("Mathieu")
            ->setRoles(["ROLE_USER"])
            ->setPassword($this->hasher->hashPassword($user, "test"));

        $manager->persist($user);

        foreach ($languages as $key => $language) {
            $category = new Category();
            $category->setTitle($language);

            $theme = new Theme();
            $theme->setTitle($technos[$key])
                ->addCategory($category);

            $manager->persist($category);
            $manager->persist($theme);
        }

        $manager->flush();
    }
}
