<?php

namespace App\DataFixtures;

use App\Entity\Tasks;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Service\PasswordHasherService;

class AppFixtures extends Fixture
{
    private $passwordHasherService;

    public function __construct(PasswordHasherService $passwordHasherService)
    {
        $this->passwordHasherService = $passwordHasherService;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUsername('backend_dev');
        $hashedPassword1 = $this->passwordHasherService->hashPassword('backend_dev');
        $user1->setPassword($hashedPassword1);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('frontend_dev');
        $hashedPassword2= $this->passwordHasherService->hashPassword('frontend_dev');
        $user2->setPassword($hashedPassword2);
        $manager->persist($user2);

        $task1 = new Tasks();
        $task1->setTitle('Research PHP');
        $task1->setDescription('PHP is an open-source server-side scripting language that many devs use for web development. It is also a general-purpose language that you can use to make lots of projects, including Graphical User Interfaces (GUIs) ');
        $task1->setUser($user1);
        $manager->persist($task1);

        $task2 = new Tasks();
        $task2->setTitle('Research Nodejs');
        $task2->setDescription('Node. js (Node) is an open source, cross-platform runtime environment for executing JavaScript code. Node is used extensively for server-side programming, making it possible for developers to use JavaScript for client-side and server-side code without needing to learn an additional language.');
        $task2->setUser($user1);
        $manager->persist($task2);

        $task3 = new Tasks();
        $task3->setTitle('Research Golang');
        $task3->setDescription('Go, also called Golang or Go language, is an open source programming language that Google developed. Software developers use Go in an array of operating systems and frameworks to develop web applications, cloud and networking services, and other types of software.');
        $task3->setUser($user1);
        $manager->persist($task3);

        $task4 = new Tasks();
        $task4->setTitle('Research HTML');
        $task4->setDescription('HTML is the standard markup language for Web pages.');
        $task4->setUser($user2);
        $manager->persist($task4);

        $task5 = new Tasks();
        $task5->setTitle('Research Javascript');
        $task5->setDescription('JavaScript (JS) is a lightweight interpreted (or just-in-time compiled) programming language with first-class functions.');
        $task5->setUser($user2);
        $manager->persist($task5);

        $manager->flush();
    }
}
