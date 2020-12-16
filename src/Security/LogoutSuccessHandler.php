<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function onLogoutSuccess(Request $request)
    {
        // you can do anything here
        $authToken = $request->headers->get('X-AUTH-TOKEN');

        $user = $this->entityManager->getRepository(User::class)
            ->findOneBy(['apiToken' => $authToken]);

        if (null === $user) {
            return new JsonResponse(['message' => 'logout failed']);
        }

        $user->setApiTokenDuration(null);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse([]);
    }
}