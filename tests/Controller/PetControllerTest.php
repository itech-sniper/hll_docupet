<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\PetType;
use App\Entity\Breed;

class PetControllerTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testRegisterPageLoads(): void
    {
        $this->client->request('GET', '/pet/register');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Register Your Pet');
        $this->assertSelectorExists('form#pet-registration-form');
    }

    public function testRegisterPageContainsRequiredFields(): void
    {
        $this->client->request('GET', '/pet/register');

        $this->assertSelectorExists('input[name="name"]');
        $this->assertSelectorExists('select[name="type_id"]');
        $this->assertSelectorExists('select[name="breed_id"]');
        $this->assertSelectorExists('input[name="sex"]');
        $this->assertSelectorExists('input[name="knows_birth_date"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testPetListPageLoads(): void
    {
        $this->client->request('GET', '/pet/list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Registered Pets');
    }

    public function testApiBreedsByTypeReturnsJson(): void
    {
        // Create test data
        $petType = new PetType();
        $petType->setName('Test Dog');
        $this->entityManager->persist($petType);

        $breed = new Breed();
        $breed->setName('Test Breed');
        $breed->setIsDangerous(false);
        $breed->setPetType($petType);
        $this->entityManager->persist($breed);

        $this->entityManager->flush();

        $this->client->request('GET', '/pet/api/breeds/' . $petType->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        $this->assertCount(1, $response);
        $this->assertEquals('Test Breed', $response[0]['name']);
        $this->assertFalse($response[0]['isDangerous']);
    }

    public function testApiBreedDangerReturnsCorrectStatus(): void
    {
        // Create test data
        $petType = new PetType();
        $petType->setName('Test Dog');
        $this->entityManager->persist($petType);

        $dangerousBreed = new Breed();
        $dangerousBreed->setName('Test Dangerous Breed');
        $dangerousBreed->setIsDangerous(true);
        $dangerousBreed->setPetType($petType);
        $this->entityManager->persist($dangerousBreed);

        $this->entityManager->flush();

        $this->client->request('GET', '/pet/api/breed-danger/' . $dangerousBreed->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($response['isDangerous']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up test data
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
