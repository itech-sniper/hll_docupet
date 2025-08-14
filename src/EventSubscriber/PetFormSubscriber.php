<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use App\Entity\Pet;
use App\Exception\PetValidationException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Event subscriber for pet form handling and validation
 */
class PetFormSubscriber implements EventSubscriberInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();

        // Pre-process form data before submission
        if (is_array($data)) {
            // Normalize pet name (trim whitespace, capitalize first letter)
            if (isset($data['name'])) {
                $data['name'] = ucfirst(trim($data['name']));
            }

            // Handle breed selection logic
            if (isset($data['breed_option'])) {
                switch ($data['breed_option']) {
                    case 'dont_know':
                        $data['breed_id'] = null;
                        $data['custom_breed_name'] = null;
                        break;
                    case 'mix':
                        $data['breed_id'] = null;
                        $data['custom_breed_name'] = 'Mixed Breed';
                        break;
                    case 'custom':
                        $data['breed_id'] = null;
                        // Keep custom_breed_name as provided
                        break;
                    case 'know_breed':
                        // Keep breed_id as selected
                        $data['custom_breed_name'] = null;
                        break;
                }
            }

            // Handle birth date logic
            if (isset($data['knows_birth_date'])) {
                if ($data['knows_birth_date'] === 'no') {
                    $data['date_of_birth'] = null;
                    // Keep approximate_age
                } else {
                    $data['approximate_age'] = null;
                    // Keep date_of_birth
                }
            }

            $event->setData($data);
        }
    }

    public function onPostSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $pet = $form->getData();

        // Additional validation after form submission
        if ($pet instanceof Pet && $form->isSubmitted()) {
            $violations = $this->validator->validate($pet);
            
            if (count($violations) > 0) {
                // Add violations to form
                foreach ($violations as $violation) {
                    $form->addError(new \Symfony\Component\Form\FormError($violation->getMessage()));
                }
            }

            // Custom business logic validation
            $this->validateBusinessRules($pet, $form);
        }
    }

    private function validateBusinessRules(Pet $pet, $form): void
    {
        // Example: Validate dangerous breed requirements
        if ($pet->isDangerousAnimal()) {
            // Could add additional validation for dangerous animals
            // For example, require additional documentation
        }

        // Example: Validate age consistency
        if ($pet->getDateOfBirth() && $pet->getAge()) {
            $calculatedAge = $pet->getDateOfBirth()->diff(new \DateTime())->y;
            if (abs($calculatedAge - $pet->getAge()) > 1) {
                $form->addError(new \Symfony\Component\Form\FormError(
                    'The provided age does not match the date of birth'
                ));
            }
        }

        // Example: Validate pet name uniqueness (if required)
        // This could check against existing pets for the same owner
    }
}
