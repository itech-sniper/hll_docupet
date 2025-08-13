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
        private PetService $petService
    ) {
    }

    #[Route('/register', name: 'pet_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $data = $request->request->all();
            
            try {
                $pet = $this->petService->createPet($data);
                
                return $this->redirectToRoute('pet_summary', ['id' => $pet->getId()]);
            } catch (\Exception $e) {
                $this->addFlash('error', 'An error occurred while saving the pet: ' . $e->getMessage());
            }
        }

        $petTypes = $this->petService->getAllPetTypes();
        $ageChoices = $this->petService->getAgeChoices();

        return $this->render('pet/register.html.twig', [
            'petTypes' => $petTypes,
            'ageChoices' => $ageChoices,
            'sexChoices' => Pet::getSexChoices(),
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
