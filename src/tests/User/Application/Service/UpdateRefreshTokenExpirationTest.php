<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\Token\Application\Service\UpdateRefreshTokenExpiration;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\RefreshTokenString;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateRefreshTokenExpirationTest extends TestCase
{
    private MockObject $repository;
    private UpdateRefreshTokenExpiration $updateRefreshTokenValidation;

    public function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepository::class);
        $this->updateRefreshTokenValidation = new UpdateRefreshTokenExpiration($this->repository);
    }

    public function testWhenRefreshTokenNotFoundThenThrowException()
    {
        $this->expectException(RefreshTokenNotFoundException::class);
        $this->repository->method('ofRefreshTokenString')->willReturn(null);
        ($this->updateRefreshTokenValidation)($this->anyValidRefreshTokenRequest());
    }

    public function testWhenRefreshTokenIsExpiredThenThrowException()
    {
        $this->expectException(RefreshTokenIsExpiredException::class);
        $this->repository->method('ofRefreshTokenString')->willReturn($this->anyExpiredRefreshToken());
        ($this->updateRefreshTokenValidation)($this->anyValidRefreshTokenRequest());
    }

    private function anyExpiredRefreshToken(): RefreshToken
    {
        return new RefreshToken(RefreshTokenString::generate(), Uuid::generate(), new \DateTime());
    }

    private function anyValidRefreshTokenRequest(): UpdateRefreshTokenExpirationRequest
    {
        return new UpdateRefreshTokenExpirationRequest(
            new \DateTime('+1 day'),
            RefreshTokenString::generate()->toString()
        );
    }

}