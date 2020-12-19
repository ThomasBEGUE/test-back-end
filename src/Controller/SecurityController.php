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

        // Update token validation
        $dateValidation = new \DateTime('now');
        $dateValidation->add(new \DateInterval('PT1H'));
        $user->setApiTokenDuration($dateValidation);
        // random_bytes() generate secure but not unique id and uniqid() generate 'unique' but not secure id
        // we concat both to have a token with unique part and secure part.
        // if 16 bytes is too week we can update this number or if the methods is too slow we can use only random_bytes()
        // with a big number and remove the uniqid() part.
        $randomPseudoUniqueToken = bin2hex(random_bytes(16)).uniqid().bin2hex(random_bytes(16));
        $user->setApiToken($randomPseudoUniqueToken);

        // persist in database
        $entityManager->persist($user);
        $entityManager->flush();

        $json = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'auth_user']
        );

        return JsonResponse::fromJsonString($json);
    }

    /**
     * @Route("/logout", name="logout", methods={"POST"})
     */
    public function logout(Request $request)
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }


}