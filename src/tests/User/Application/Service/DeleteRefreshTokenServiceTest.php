<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\User\Application\Service\DeleteRefreshTokenService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteRefreshTokenServiceTest extends TestCase
{
    private MockObject $refreshTokenRepository;
    private MockObject $tokenManager;
    private MockObject $userRepository;
    private MockObject $randomStringGenerator;
    private DeleteRefreshTokenService $deleteRefreshTokenService;

    public function setUp(): void
    {
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->deleteRefreshTokenService = new DeleteRefreshTokenService(
            $this->refreshTokenRepository,
            $this->tokenManager,
            $this->userRepository,
            $this->randomStringGenerator
        );
    }

    public function testWhenRefreshTokenNotFoundThenThrowException()
    {
        $this->expectException(RefreshTokenNotFoundException::class);
        $this->refreshTokenRepository->expects($this->once())->method('ofToken')->willReturn(null);
        ($this->deleteRefreshTokenService)($this->anyValidRefreshTokenRequest());
    }

    public function testWhenRequestIsValidThenDeleteRefreshToken()
    {
        $this->refreshTokenRepository->expects($this->once())->method('ofToken')->with(
            $this->anyValidRefreshToken()->getRefreshToken()
        )->willReturn($this->anyValidRefreshToken());
        $this->refreshTokenRepository->expects($this->once())->method('delete')->with($this->anyValidRefreshToken());

        ($this->deleteRefreshTokenService)($this->anyValidRefreshTokenRequest());
    }

    private function anyValidRefreshTokenRequest(): DeleteRefreshTokenRequest
    {
        return new DeleteRefreshTokenRequest('token_string');
    }

    private function anyValidRefreshToken(): RefreshToken
    {
        return new RefreshToken(
            new Token($this->anyValidRefreshTokenRequest()->getRefreshTokenValue()),
            new Uuid('48d34c63-6bba-4c72-a461-8aac1fd7d138'),
            new \DateTimeImmutable('2020-05-17 18:27:00')
        );
    }

}