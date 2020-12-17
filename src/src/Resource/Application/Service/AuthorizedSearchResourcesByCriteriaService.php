<?php


namespace LaSalle\StudentTeacher\Resource\Application\Service;


use LaSalle\StudentTeacher\Resource\Application\Request\AuthorizedSearchResourcesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Application\Response\ResourceCollectionResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\ResourceResponse;
use LaSalle\StudentTeacher\Resource\Application\Response\UnitResponse;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Resource;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\ResourceRepository;
use LaSalle\StudentTeacher\Resource\Domain\Repository\UnitRepository;
use LaSalle\StudentTeacher\Resource\Domain\Service\CourseService;
use LaSalle\StudentTeacher\Resource\Domain\Service\ResourceService;
use LaSalle\StudentTeacher\Resource\Domain\Service\UnitService;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Criteria;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Filters;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Operator;
use LaSalle\StudentTeacher\Shared\Domain\Criteria\Order;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\Service\AuthorizationService;
use LaSalle\StudentTeacher\User\Domain\Service\UserService;

class AuthorizedSearchResourcesByCriteriaService
{
    private CourseRepository $courseRepository;
    private CourseService $courseService;
    private ResourceRepository $resourceRepository;
    private ResourceService $resourceService;
    private UserService $userService;
    private UnitRepository $unitRepository;
    private UnitService $unitService;
    private AuthorizationService $authorizationService;

    public function __construct(CourseRepository $courseRepository, ResourceRepository $resourceRepository, UnitRepository $unitRepository, UserRepository $userRepository, AuthorizationService $authorizationService)
    {
        $this->courseRepository = $courseRepository;
        $this->courseService = new CourseService($courseRepository);
        $this->resourceRepository = $resourceRepository;
        $this->unitRepository = $unitRepository;
        $this->unitService = new UnitService($unitRepository);
        $this->resourceService = new ResourceService($resourceRepository);
        $this->userService = new UserService($userRepository);
        $this->authorizationService = $authorizationService;
    }

    public function __invoke(AuthorizedSearchResourcesByCriteriaRequest $request): ResourceCollectionResponse
    {
        $requestAuthorId = new Uuid($request->getRequestAuthorId());
        $requestAuthor = $this->userService->findUser($requestAuthorId);

        $courseId = new Uuid($request->getCourseId());
        $course = $this->courseService->findCourse($courseId);

        $unitId = new Uuid($request->getUnitId());
        $unit = $this->unitService->findUnit($unitId);


        $filters = Filters::fromValues([['field' => 'unitId', 'operator' => '=', 'value' => $unitId->toString()]]);

        $criteria = new Criteria(
            $filters,
            Order::fromValues($request->getOrderBy(), $request->getOrder()),
            Operator::fromValue($request->getOperator()),
            $request->getOffset(),
            $request->getLimit()
        );

        $resources = $this->resourceRepository->matching($criteria);
        //var_dump($resources);
        //die();
        return new ResourceCollectionResponse(...$this->buildResponse(...$resources));
    }

    private function buildResponse(Resource ...$resources): array
    {
        return array_map(
            static function (Resource $resource) {
                return new ResourceResponse(
                    $resource->getId()->toString(),
                    $resource->getUnitId()->toString(),
                    $resource->getName(),
                    $resource->getDescription(),
                    $resource->getContent(),
                    $resource->getCreated(),
                    $resource->getModified(),
                    $resource->getStatus()->value(),
                );
            },
            $resources
        );
    }

}
