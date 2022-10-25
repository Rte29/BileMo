<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CustomerController extends AbstractController
{
    #[Route('/api/customers', name: 'app_customers', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas les droits')]
    public function getAllCustomers(CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);
        $customerList = $customerRepository->findAllWithPagination($page, $limit);

        $jsonCustomerlist = $serializer->serialize($customerList, 'json', ['groups' => 'getCustomers']);
        return new JsonResponse($jsonCustomerlist, Response::HTTP_OK, [], true);
    }
    #[Route('/api/customers/{id}', name: 'detail_customer', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'Vous n\'avez pas les droits')]
    public function getDetailCustomer(int $id, SerializerInterface $serializer, CustomerRepository $customerRepository): JsonResponse
    {

        $customer = $customerRepository->find($id);
        if ($customer) {
            $jsonCustomer = $serializer->serialize($customer, 'json', ['groups' => 'getCustomers']);
            return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}
