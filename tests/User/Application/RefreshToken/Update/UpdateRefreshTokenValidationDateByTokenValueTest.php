<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\RefreshToken\Update;

use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Update\UpdateRefreshTokenValidationDateByTokenValue;
use LaSalle\StudentTeacher\Token\Application\RefreshToken\Update\UpdateRefreshTokenValidationDateByTokenValueRequest;
use LaSalle\StudentTeacher\Token\Domain\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Infrastructure\Framework\TokenGenerator\BasicRefreshTokenGenerating;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Test\LaSalle\StudentTeacher\User\Infrastructure\Persistence\InMemoryRefreshTokenRepository;

final class UpdateRefreshTokenValidationDateByTokenValueTest extends TestCase
{
    private RefreshTokenRepository $repository;
    private UpdateRefreshTokenValidationDateByTokenValue $updateRefreshTokenValidation;
    private BasicRefreshTokenGenerating $refreshTokenGenerating;

    public function setUp(): void
    {
        $this->repository = new InMemoryRefreshTokenRepository();
        $this->updateRefreshTokenValidation = new UpdateRefreshTokenValidationDateByTokenValue($this->repository);
        $this->refreshTokenGenerating = new BasicRefreshTokenGenerating();
    }

    private function executeUpdateRefreshToken()
    {
        return ($this->updateRefreshTokenValidation)(
            new UpdateRefreshTokenValidationDateByTokenValueRequest(
                new \DateTime(),
                'randomRefreshToken'
            )
        );
    }

    private function createRandomExpiredRefreshToken()
    {
        $dateTime = new \DateTime();
        return new RefreshToken(Uuid::uuid4()->toString(), 'randomRefreshToken', $dateTime);
    }

    private function createRandomValidRefreshToken()
    {
        $dateTime = new \DateTime();
        $dateTime->modify('+1 day');
        return new RefreshToken(Uuid::uuid4()->toString(), 'randomRefreshToken', $dateTime);
    }

    /**
     * @test
     */
    public function refreshTokenNotFoundShouldThrowAnException()
    {
        $this->expectException(RefreshTokenNotFoundException::class);
        $this->executeUpdateRefreshToken();
    }

    /**
     * @test
     */
    public function refreshTokenIsInvalidShouldThrowAnException()
    {
        $this->expectException(RefreshTokenIsExpiredException::class);
        $refreshToken = $this->createRandomExpiredRefreshToken();
        $this->repository->save($refreshToken);
        $this->executeUpdateRefreshToken();
    }

    /**
     * @test
     */
    public function afterRefreshTokenValidationItShouldHaveNewValue()
    {
        $refreshToken = $this->createRandomValidRefreshToken();
        $this->repository->save($refreshToken);

        $updatedRefreshToken = $this->executeUpdateRefreshToken();

        $refreshTokenInRepository = $this->repository->searchByTokenValue($refreshToken->getRefreshToken());
        $this->assertEquals($updatedRefreshToken->getValid(), $refreshTokenInRepository->getValid());
    }


}