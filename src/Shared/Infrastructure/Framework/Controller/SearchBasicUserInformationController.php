<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Framework\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\SearchBasicUserInformationRequest;
use LaSalle\StudentTeacher\User\Application\Service\SearchBasicUserInformation;
use PhpParser\Node\Param;
use Symfony\Component\HttpFoundation\Response;

final class SearchBasicUserInformationController extends AbstractFOSRestController
{
    private SearchBasicUserInformation $searchUser;

    public function __construct(SearchBasicUserInformation $searchUser)
    {
        $this->searchUser = $searchUser;
    }

    /**
     * @Rest\Get("/api/account/{id}")
     */
    public function getAction(string $id)
    {
        $filters = [['field' => 'id', 'operator' => '=', 'value' => $id]];
        $orderBy = 'id';
        $order = 'desc';

        try {
            $userResponse = ($this->searchUser)(Filters::fromValues($filters), Order::fromValues($orderBy, $order), null, null);
        } catch (UserNotFoundException $e) {
            $view = $this->view(['message' => 'There\'s no user with such id'], Response::HTTP_NOT_FOUND);
            return $this->handleView($view);
        }

        //var_dump($userResponse);
        $view = $this->view($userResponse, Response::HTTP_OK);
        return $this->handleView($view);
    }
}