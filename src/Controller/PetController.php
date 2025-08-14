<?php

namespace App\Controller;

use App\Entity\Pet;
use App\Service\PetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pet')]
class PetController extends AbstractController
{
    public function __construct(
        private PetService $petService,
    ) {
    }

    #[Route('/register', name: 'pet_register')]
    public function register(Request $request): Response
    {
        // Clear any existing session data and start fresh
        $session = $request->getSession();
        $session->remove('pet_registration');
        $session->set('pet_registration', []);

        return $this->redirectToRoute('pet_register_step1');
    }

    #[Route('/register/step1', name: 'pet_register_step1', methods: ['GET', 'POST'])]
    public function registerStep1(Request $request): Response
    {
        $session = $request->getSession();
        $petData = $session->get('pet_registration', []);

        // Get pet types for the dropdown
        $petTypes = $this->petService->getAllPetTypes();

        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $typeId = $request->request->get('type_id');

            // Validate required fields
            if (empty($name) || empty($typeId)) {
                $this->addFlash('error', 'Please fill in all required fields.');

                return $this->render('pet/register_step1.html.twig', [
                    'petTypes' => $petTypes,
                    'petData' => $petData,
                ]);
            }

            // Store step 1 data in session
            $petData['name'] = $name;
            $petData['type_id'] = $typeId;
            $session->set('pet_registration', $petData);

            return $this->redirectToRoute('pet_register_step2');
        }

        return $this->render('pet/register_step1.html.twig', [
            'petTypes' => $petTypes,
            'petData' => $petData,
        ]);
    }

    #[Route('/register/step2', name: 'pet_register_step2', methods: ['GET', 'POST'])]
    public function registerStep2(Request $request): Response
    {
        $session = $request->getSession();
        $petData = $session->get('pet_registration', []);

        // Redirect to step 1 if no data
        if (empty($petData['name']) || empty($petData['type_id'])) {
            return $this->redirectToRoute('pet_register_step1');
        }

        if ($request->isMethod('POST')) {
            $breedOption = $request->request->get('breed_option');
            $breedId = $request->request->get('breed_id');
            $customBreedName = $request->request->get('custom_breed_name');

            // Store step 2 data in session
            $petData['breed_option'] = $breedOption; // Always store the breed option

            if ('know_breed' === $breedOption && $breedId) {
                $petData['breed_id'] = $breedId;
                $petData['custom_breed_option'] = null;
                $petData['custom_breed_name'] = null;
            } else {
                $petData['breed_id'] = null;
                $petData['custom_breed_option'] = $breedOption;
                $petData['custom_breed_name'] = ('custom' === $breedOption) ? $customBreedName : null;
            }

            $session->set('pet_registration', $petData);

            return $this->redirectToRoute('pet_register_step3');
        }

        return $this->render('pet/register_step2.html.twig', [
            'petData' => $petData,
        ]);
    }

    #[Route('/register/step2-test', name: 'pet_register_step2_test', methods: ['GET'])]
    public function registerStep2Test(): Response
    {
        // Test step 2 with mock data
        $petData = [
            'name' => 'Test Pet',
            'type_id' => 7, // Dog type ID
        ];

        return $this->render('pet/register_step2.html.twig', [
            'petData' => $petData,
        ]);
    }

    #[Route('/register/step3-test', name: 'pet_register_step3_test', methods: ['GET'])]
    public function registerStep3Test(): Response
    {
        // Test step 3 with mock data
        $petData = [
            'name' => 'Test Pet',
            'type_id' => 7,
            'breed_option' => 'know_breed',
            'breed_id' => 1,
        ];

        $ageChoices = $this->petService->getAgeChoices();
        $sexChoices = Pet::getSexChoices();

        return $this->render('pet/register_step3.html.twig', [
            'petData' => $petData,
            'ageChoices' => $ageChoices,
            'sexChoices' => $sexChoices,
        ]);
    }

    #[Route('/register/step3', name: 'pet_register_step3', methods: ['GET', 'POST'])]
    public function registerStep3(Request $request): Response
    {
        $session = $request->getSession();
        $petData = $session->get('pet_registration', []);

        // Redirect to step 1 if no data
        if (empty($petData['name']) || empty($petData['type_id'])) {
            return $this->redirectToRoute('pet_register_step1');
        }

        $ageChoices = $this->petService->getAgeChoices();
        $sexChoices = Pet::getSexChoices();

        if ($request->isMethod('POST')) {
            $knowsBirthDate = $request->request->get('knows_birth_date');
            $dateOfBirth = $request->request->get('date_of_birth');
            $approximateAge = $request->request->get('approximate_age');
            $sex = $request->request->get('sex');

            // Store step 3 data in session for form persistence
            $petData['knows_birth_date'] = $knowsBirthDate;
            $petData['date_of_birth'] = $dateOfBirth;
            $petData['approximate_age'] = $approximateAge;
            $petData['sex'] = $sex;

            // Validate required fields
            if (empty($sex)) {
                $this->addFlash('error', 'Please fill in all required fields.');

                return $this->render('pet/register_step3.html.twig', [
                    'petData' => $petData,
                    'ageChoices' => $ageChoices,
                    'sexChoices' => $sexChoices,
                ]);
            }

            // Prepare complete data for pet creation
            $completeData = array_merge($petData, [
                'knows_birth_date' => $knowsBirthDate,
                'date_of_birth' => $dateOfBirth,
                'approximate_age' => $approximateAge,
                'sex' => $sex,
            ]);

            try {
                $pet = $this->petService->createPet($completeData);

                // Clear session data
                $session->remove('pet_registration');

                $this->addFlash('success', 'Pet registered successfully!');

                return $this->redirectToRoute('pet_summary', ['id' => $pet->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while saving the pet: '.$e->getMessage());
            }
        }

        return $this->render('pet/register_step3.html.twig', [
            'petData' => $petData,
            'ageChoices' => $ageChoices,
            'sexChoices' => $sexChoices,
        ]);
    }

    #[Route('/summary/{id}', name: 'pet_summary', methods: ['GET'])]
    public function summary(int $id): Response
    {
        $pet = $this->petService->findPet($id);

        if (!$pet) {
            throw $this->createNotFoundException('Pet not found');
        }

        return $this->render('pet/summary.html.twig', [
            'pet' => $pet,
        ]);
    }

    #[Route('/api/breeds/{petTypeId}', name: 'api_breeds_by_type', methods: ['GET'])]
    public function getBreedsByType(int $petTypeId): JsonResponse
    {
        $breeds = $this->petService->getBreedsByPetTypeId($petTypeId);

        $breedData = [];
        foreach ($breeds as $breed) {
            $breedData[] = [
                'id' => $breed->getId(),
                'name' => $breed->getName(),
                'isDangerous' => $breed->isDangerous(),
            ];
        }

        return new JsonResponse($breedData);
    }

    #[Route('/api/breed-danger/{breedId}', name: 'api_breed_danger', methods: ['GET'])]
    public function getBreedDanger(int $breedId): JsonResponse
    {
        $isDangerous = $this->petService->isBreedDangerous($breedId);

        return new JsonResponse(['isDangerous' => $isDangerous]);
    }

    #[Route('/list', name: 'pet_list', methods: ['GET'])]
    public function list(): Response
    {
        $pets = $this->petService->getAllPets();

        return $this->render('pet/list.html.twig', [
            'pets' => $pets,
        ]);
    }
}
