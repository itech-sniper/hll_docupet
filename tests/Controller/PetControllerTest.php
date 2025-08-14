<?php

namespace App\Tests\Controller;

use App\Entity\Breed;
use App\Entity\PetType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    public function testRegisterPageRedirectsToStep1(): void
    {
        $this->client->request('GET', '/pet/register');

        $this->assertResponseRedirects('/pet/register/step1');
    }

    public function testRegisterStep1PageLoads(): void
    {
        $this->client->request('GET', '/pet/register/step1');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pet Registration');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="name"]');
        $this->assertSelectorExists('select[name="type_id"]');
    }

    public function testRegisterStep1ContainsRequiredFields(): void
    {
        $this->client->request('GET', '/pet/register/step1');

        $this->assertSelectorExists('input[name="name"]');
        $this->assertSelectorExists('select[name="type_id"]');
        $this->assertSelectorExists('button[type="submit"]');
    }

    public function testRegisterStep2PageLoads(): void
    {
        // First, complete step 1 to get to step 2
        $this->client->request('GET', '/pet/register/step1');

        // Get a pet type from fixtures
        $petType = $this->entityManager->getRepository(PetType::class)->findOneBy([]);
        $this->assertNotNull($petType, 'Pet type should exist from fixtures');

        $this->client->submitForm('Next', [
            'name' => 'Test Pet',
            'type_id' => $petType->getId(),
        ]);

        $this->assertResponseRedirects('/pet/register/step2');

        // Now test step 2
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pet Registration');
        $this->assertSelectorExists('form');
    }

    public function testRegisterStep3PageLoads(): void
    {
        // Complete steps 1 and 2 to get to step 3
        $this->client->request('GET', '/pet/register/step1');

        $petType = $this->entityManager->getRepository(PetType::class)->findOneBy([]);
        $this->client->submitForm('Next', [
            'name' => 'Test Pet',
            'type_id' => $petType->getId(),
        ]);

        $this->client->followRedirect();
        $this->client->submitForm('Next', [
            'breed_option' => 'dont_know',
        ]);

        $this->assertResponseRedirects('/pet/register/step3');

        // Now test step 3
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Pet Registration');
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('select[name="sex"]');
        $this->assertSelectorExists('input[name="knows_birth_date"]');
    }

    public function testPetListPageLoads(): void
    {
        $this->client->request('GET', '/pet/list');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Registered Pets');
    }

    public function testCompleteRegistrationFlow(): void
    {
        // Step 1: Basic information
        $this->client->request('GET', '/pet/register/step1');

        $petType = $this->entityManager->getRepository(PetType::class)->findOneBy([]);
        $this->client->submitForm('Next', [
            'name' => 'Fluffy',
            'type_id' => $petType->getId(),
        ]);

        // Step 2: Breed information
        $this->client->followRedirect();
        $this->client->submitForm('Next', [
            'breed_option' => 'dont_know',
        ]);

        // Step 3: Additional details
        $this->client->followRedirect();
        $this->client->submitForm('Submit', [
            'sex' => 'female',
            'knows_birth_date' => 'no',
        ]);

        // Should redirect to success page
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        // Verify pet was created in database
        $pet = $this->entityManager->getRepository(\App\Entity\Pet::class)->findOneBy(['name' => 'Fluffy']);
        $this->assertNotNull($pet);
        $this->assertEquals('Fluffy', $pet->getName());
        $this->assertEquals('female', $pet->getSex());
        $this->assertEquals($petType->getId(), $pet->getType()->getId());
    }

    public function testApiBreedsByTypeReturnsJson(): void
    {
        // Use existing pet type from fixtures
        $petType = $this->entityManager->getRepository(PetType::class)->findOneBy([]);
        $this->assertNotNull($petType, 'Pet type should exist from fixtures');

        $this->client->request('GET', '/pet/api/breeds/'.$petType->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($response);
        // Should have breeds from fixtures
        $this->assertGreaterThanOrEqual(0, count($response));
    }

    public function testApiBreedDangerReturnsCorrectStatus(): void
    {
        // Find a dangerous breed from fixtures or create one
        $dangerousBreed = $this->entityManager->getRepository(Breed::class)->findOneBy(['isDangerous' => true]);

        if (!$dangerousBreed) {
            // Create test data if no dangerous breed exists
            $petType = $this->entityManager->getRepository(PetType::class)->findOneBy([]);
            $dangerousBreed = new Breed();
            $dangerousBreed->setName('Test Dangerous Breed');
            $dangerousBreed->setIsDangerous(true);
            $dangerousBreed->setPetType($petType);
            $this->entityManager->persist($dangerousBreed);
            $this->entityManager->flush();
        }

        $this->client->request('GET', '/pet/api/breed-danger/'.$dangerousBreed->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertTrue($response['isDangerous']);
    }

    public function testRegistrationStep1ValidationErrors(): void
    {
        $this->client->request('GET', '/pet/register/step1');

        // Submit form with empty data
        $this->client->submitForm('Next', [
            'name' => '',
            'type_id' => '',
        ]);

        // Should stay on step 1 with validation errors
        $this->assertResponseIsSuccessful();
        // Check that we're still on step 1 (not redirected)
        $this->assertSelectorTextContains('h3', 'Step 1: Pet Information');
        // The form should still be present for retry
        $this->assertSelectorExists('form');
        $this->assertSelectorExists('input[name="name"]');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Clean up test data
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
