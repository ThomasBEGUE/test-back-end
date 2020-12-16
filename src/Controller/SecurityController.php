<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Entity\User;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function login(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $passwordEncoder, EntityManagerInterface $entityManager)
    {

        $email = $request->request->get('email');
        $password = $request->request->get('password');

        if (null === $email || null === $password) {
            return $this->json(['message' => 'authentification failed']);
        }

        $user = $entityManager->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        if (null === $user || !$passwordEncoder->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'authentification failed']);
        }

        $json = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'auth_user']
        );

        return JsonResponse::fromJsonString($json);
    }
}