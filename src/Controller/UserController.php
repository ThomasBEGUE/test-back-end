<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Require ROLE_USER for *every* controller method in this class.
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/user", name="user_index")
     */
    public function index(SerializerInterface $serializer)
    {

        $user  = $this->getUser();

        $json = $serializer->serialize(
            $user,
            'json',
            ['groups' => 'show_user']
        );

        return JsonResponse::fromJsonString($json);
    }
}