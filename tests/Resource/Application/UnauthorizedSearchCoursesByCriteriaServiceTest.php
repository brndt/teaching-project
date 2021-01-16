<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Resource\Application;

use LaSalle\StudentTeacher\Resource\Course\Application\CourseCollectionResponse;
use LaSalle\StudentTeacher\Resource\Course\Application\CourseResponse;
use LaSalle\StudentTeacher\Resource\Course\Application\UnauthorizedSearchCoursesByCriteriaRequest;
use LaSalle\StudentTeacher\Resource\Course\Application\UnauthorizedSearchCoursesByCriteriaService;
use LaSalle\StudentTeacher\Resource\Domain\Aggregate\Course;
use LaSalle\StudentTeacher\Resource\Domain\Repository\CourseRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\Resource\Builder\CourseBuilder;

final class UnauthorizedSearchCoursesByCriteriaServiceTest extends TestCase
{
    private UnauthorizedSearchCoursesByCriteriaService $searchCoursesByCriteriaService;
    private MockObject $courseRepository;

    public function setUp(): void
    {
        $this->courseRepository = $this->createMock(CourseRepository::class);
        $this->searchCoursesByCriteriaService = new UnauthorizedSearchCoursesByCriteriaService(
            $this->courseRepository,
        );
    }

    public function testWhenCoursesNotFoundThenReturnEmptyArray()
    {
        $request = new UnauthorizedSearchCoursesByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );
        $expectedCourseCollectionResponse = new CourseCollectionResponse(...$this->buildResponse(...[]));
        $this->courseRepository
            ->expects(self::once())
            ->method('matching')
            ->willReturn([]);
        $actualCourseCollectionResponse = ($this->searchCoursesByCriteriaService)($request);
        $this->assertEquals($expectedCourseCollectionResponse, $actualCourseCollectionResponse);
    }

    public function testWhenRequestIsValidThenReturnCourses()
    {
        $request = new UnauthorizedSearchCoursesByCriteriaRequest(
            [],
            null,
            null,
            null,
            null,
            null
        );

        $courses = [(new CourseBuilder())->build(), (new CourseBuilder())->build()];
        $expectedCourseCollectionResponse = new CourseCollectionResponse(...$this->buildResponse(...$courses));
        $this->courseRepository
            ->expects(self::once())
            ->method('matching')
            ->willReturn($courses);
        $actualCourseCollectionResponse = ($this->searchCoursesByCriteriaService)($request);
        $this->assertEquals($expectedCourseCollectionResponse, $actualCourseCollectionResponse);
    }

    private function buildResponse(Course ...$courses): array
    {
        return array_map(
            static function (Course $course) {
                return new CourseResponse(
                    $course->getId()->toString(),
                    $course->getTeacherId()->toString(),
                    $course->getCategoryId()->toString(),
                    $course->getName(),
                    $course->getDescription(),
                    $course->getLevel(),
                    $course->getCreated(),
                    $course->getModified(),
                    $course->getStatus()->value(),
                );
            },
            $courses
        );
    }
}
