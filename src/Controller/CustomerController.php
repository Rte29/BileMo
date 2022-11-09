<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\VarDumper\VarDumper;

class CustomerController extends AbstractController
{

    /**
     * @OA\Response(
     *      response= 200,
     *      description="Retourne la liste des customers",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     *     
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="La page que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     *
     * @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Le nombre d'éléments que l'on veut récupérer",
     *     @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers', name: 'app_customers', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function getAllCustomers(CustomerRepository $customerRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "app_customers" . $page . "-" . $limit;

        $jsonCustomerList = $cache->get($idCache, function (ItemInterface $item) use ($customerRepository, $page, $limit, $serializer) {
            echo ("pas en cache");
            $item->tag("customersCache");
            $context = SerializationContext::create()->setGroups(["getCustomers"]);
            $customerList = $customerRepository->findAllWithPagination($page, $limit);
            return $serializer->serialize($customerList, 'json', $context);
        });


        return new JsonResponse($jsonCustomerList, Response::HTTP_OK, [], true);
    }

    /**
     * @OA\Response(
     *      response= 200,
     *      description="Retourne le détail d'un customer",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     * @OA\Tag(name="Customers")
     */

    #[Route('/api/customers/{id}', name: 'detail_customer', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function getDetailCustomer(int $id, SerializerInterface $serializer, CustomerRepository $customerRepository): JsonResponse
    {

        $customer = $customerRepository->find($id);
        $context = SerializationContext::create()->setGroups(["getCustomers"]);
        if ($customer) {
            $jsonCustomer = $serializer->serialize($customer, 'json', $context);
            return new JsonResponse($jsonCustomer, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Response(
     *      response= 200,
     *      description="Supprime un customer",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers/{id}', name: 'delete_customer', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits pour supprimer un customer')]
    public function deleteCustomer(Customer $customer, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $cachePool->invalidateTags(["customersCache"]);
        $em->remove($customer);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * @OA\Response(
     *      response= 200,
     *      description="Création d'un customer",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers', name: 'create_customers', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function createCustomers(SerializerInterface $serializer, Request $request, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, TagAwareCacheInterface $cachePool, ValidatorInterface $validator): JsonResponse
    {

        $customer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $errors = $validator->validate($customer);
        if ($errors->count() > 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }

        $content = $request->toArray();
        $idUser = $content['idUser'] ?? -1;
        $customer->setRelation($userRepository->find($idUser));

        $em->persist($customer);
        $em->flush();

        $cachePool->invalidateTags(["customersCache"]);

        $context = SerializationContext::create()->setGroups(["getCustomers"]);
        $jsonCustomer = $serializer->serialize($customer, 'json', $context);

        $location = $urlGenerator->generate('detail_customer', ['id' => $customer->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonCustomer, Response::HTTP_CREATED, ["Location" => $location], true);
    }

    /**
     * @OA\Response(
     *      response= 200,
     *      description="Mise à jour d'un customer",
     *      @OA\JsonContent(
     *          type="array",
     *          @OA\Items(ref=@Model(type=Customer::class, groups={"getCustomers"}))
     *     )
     * )
     * @OA\Tag(name="Customers")
     */
    #[Route('/api/customers/{id}', name: "update_customer", methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'Vous n\'avez pas les droits')]
    public function updateCustomer(Int $id, Request $request, SerializerInterface $serializer, Customer $currentCustomer, EntityManagerInterface $em, UserRepository $userRepository, TagAwareCacheInterface $cachePool, ValidatorInterface $validator, CustomerRepository $customerRepository): JsonResponse
    {
        $newCustomer = $serializer->deserialize($request->getContent(), Customer::class, 'json');

        $currentCustomer->setFirstName($newCustomer->getFirstName());
        $currentCustomer->setLastName($newCustomer->getLastName());
        $currentCustomer->setEmail($newCustomer->getEmail());
        $currentCustomer->setRelation($newCustomer->getRelation());



        $errors = $validator->validate($currentCustomer);
        if ($errors->count() < 0) {
            return new JsonResponse($serializer->serialize($errors, 'json'), JsonResponse::HTTP_BAD_REQUEST, [], true);
        }


        $em->persist($currentCustomer);
        $em->flush();

        $cachePool->invalidateTags(["customersCache"]);

        $context = SerializationContext::create()->setGroups(["getCustomers"]);
        $jsonCustomer = $serializer->serialize($currentCustomer, 'json', $context);
        return new JsonResponse($jsonCustomer, JsonResponse::HTTP_OK, [], true);
    }
}
