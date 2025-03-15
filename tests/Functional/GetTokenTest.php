<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Entity\User;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class GetTokenTest extends WebTestCase
{
    private KernelBrowser $client;

    public function testSuccessfulAuth(): void
    {
        $email = 'test@test.test';

        $password = 'abcdefghij';

        $user = new User();

        $container = $this->client->getContainer();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);

        $entityManager = $container->get(EntityManagerInterface::class);

        $user
            ->setEmail($email)
            ->setPassword($hashedPassword);

        $entityManager->persist($user);

        $entityManager->flush();
        
        $this->client->jsonRequest('POST', '/api/v1/getToken', [
            'username' => $email,
            'password' => $password,
        ]);

        $this->assertResponseStatusCodeSame(200);        
    }

    public function testInvalidPassword(): void
    {
        $email = 'test@test.test';

        $password = 'abcdefghij';

        $user = new User();

        $container = $this->client->getContainer();

        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $hashedPassword = $passwordHasher->hashPassword($user, $password);

        $entityManager = $container->get(EntityManagerInterface::class);

        $user
            ->setEmail($email)
            ->setPassword($hashedPassword);

        $entityManager->persist($user);

        $entityManager->flush();
        
        $this->client->jsonRequest('POST', '/api/v1/getToken', [
            'username' => $email,
            'password' => 'invalid',
        ]);

        $this->assertResponseStatusCodeSame(401);        
    }

    public function testUserNotFound(): void
    {
        $email = 'test2@test.test';

        $password = '122132423';
        
        $this->client->jsonRequest('POST', '/api/v1/getToken', [
            'username' => $email,
            'password' => $password,
        ]);

        $this->assertResponseStatusCodeSame(401);        
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }    

    protected function tearDown(): void
    {
        $purger = new ORMPurger(self::getContainer()->get(EntityManagerInterface::class));
        $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
        $purger->purge();
    }    
}