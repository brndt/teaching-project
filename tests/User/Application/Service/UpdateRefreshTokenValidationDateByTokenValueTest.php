<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenValidationRequest;
use LaSalle\StudentTeacher\Token\Application\Service\UpdateRefreshTokenValidation;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateRefreshTokenValidationDateByTokenValueTest extends TestCase
{
    private MockObject $repository;
    private UpdateRefreshTokenValidation $updateRefreshTokenValidation;

    public function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepository::class);
        $this->updateRefreshTokenValidation = new UpdateRefreshTokenValidation($this->repository);
    }

    /**
     * @test
     */
    public function refreshTokenNotFoundShouldThrowAnException()
    {
        $this->expectException(RefreshTokenNotFoundException::class);
        $this->repository->method('searchByTokenValue')->willReturn(null);
        ($this->updateRefreshTokenValidation)($this->createRandomValidRefreshTokenRequest());
    }

    /**
     * @test
     */
    public function refreshTokenIsExpiredShouldThrowAnException()
    {
        $this->expectException(RefreshTokenIsExpiredException::class);
        $this->repository->method('searchByTokenValue')->willReturn($this->createRandomExpiredRefreshToken());
        ($this->updateRefreshTokenValidation)($this->createRandomValidRefreshTokenRequest());
    }

    private function createRandomExpiredRefreshToken()
    {
        return new RefreshToken(Uuid::generate(), RefreshTokenString::generate(), Uuid::generate(), new \DateTime());
    }

    private function createRandomValidRefreshTokenRequest()
    {
        return new UpdateRefreshTokenValidationRequest(
            new \DateTime('+1 day'),
            RefreshTokenString::generate()->toString()
        );
    }

}