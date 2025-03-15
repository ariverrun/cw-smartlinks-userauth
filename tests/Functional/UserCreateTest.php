<?php

declare(strict_types=1);

namespace Tests\Functional;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class UserCreateTest extends KernelTestCase
{
    public function testSuccessfulUserCreation(): void
    {
        self::bootKernel();

        $application = new Application(self::$kernel);

        $command = $application->find('app:user-create');
        $commandTester = new CommandTester($command);
        
        $commandTester->setInputs(['test3@test.com', 'ewfj84f3489']);
        
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger(self::getContainer()->get(EntityManagerInterface::class));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }
}