<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\Token\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\RefreshTokensRequest;
use LaSalle\StudentTeacher\Token\Application\Service\UpdateRefreshTokensExpiration;
use LaSalle\StudentTeacher\Token\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\Token\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\Token\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateRefreshTokenExpirationTest extends TestCase
{
    private MockObject $repository;
    private UpdateRefreshTokensExpiration $updateRefreshTokenValidation;

    public function setUp(): void
    {
        $this->repository = $this->createMock(RefreshTokenRepository::class);
        $this->updateRefreshTokenValidation = new UpdateRefreshTokensExpiration($this->repository);
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
        return new RefreshToken(Token::generate(), Uuid::generate(), new \DateTime());
    }

    private function anyValidRefreshTokenRequest(): RefreshTokensRequest
    {
        return new RefreshTokensRequest(
            new \DateTime('+1 day'),
            Token::generate()->toString()
        );
    }

}