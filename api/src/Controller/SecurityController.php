<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    use ApiControllerTrait;

    /**
     * @Route("/register", name="app_register", methods={"POST"})
     *
     * @SWG\Response(
     *     response=200,
     *     description="Returns the registered user",
     *     @Model(type=User::class, groups={"api"})
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Returns the validation errors",
     * )
     * @SWG\Parameter(
     *     name="user",
     *     in="body",
     *     type="object",
     *     description="The user informations",
     *     @Model(type=RegistrationFormType::class)
     * )
     * @SWG\Tag(name="Security")
     *
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     *
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);

        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json($user, Response::HTTP_OK, [], [
                'groups' => ['api'],
            ]);
        }

        return $this->formErrorsJsonResponse($form);
    }
}
