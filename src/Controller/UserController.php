<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    #[Route('/api/users', name: 'app_user', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un livre')]
    public function getAllUsers(UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $userList = $userRepository->findAll();

        $jsonUserlist = $serializer->serialize($userList, 'json');
        return new JsonResponse($jsonUserlist, Response::HTTP_OK, [], true);
    }
    #[Route('/api/users/{id}', name: 'detail_User', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits suffisants pour créer un livre')]
    public function getDetailProduct(int $id, SerializerInterface $serializer, UserRepository $userRepository): JsonResponse
    {

        $user = $userRepository->find($id);
        if ($user) {
            $jsonUser = $serializer->serialize($user, 'json');
            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
