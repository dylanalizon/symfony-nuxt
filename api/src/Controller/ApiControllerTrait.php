<?php

namespace App\Controller;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

trait ApiControllerTrait
{
    /**
     * @param FormInterface $form
     *
     * @return JsonResponse
     */
    private function formErrorsJsonResponse(FormInterface $form): JsonResponse
    {
        $data = [
          'errors' => $this->getErrorsFromForm($form),
        ];

        return new JsonResponse($data, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getErrorsFromForm(FormInterface $form): array
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }
        return $errors;
    }
}
