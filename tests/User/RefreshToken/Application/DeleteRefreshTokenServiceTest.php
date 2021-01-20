<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\RefreshToken\Application;

use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\DeleteRefreshTokenRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\DeleteRefreshTokenService;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\RefreshToken\Domain\RefreshTokenBuilder;

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
        $request = new DeleteRefreshTokenRequest('token_string');

        $this->expectException(RefreshTokenNotFoundException::class);
        $this->refreshTokenRepository->expects(self::once())->method('ofToken')->willReturn(null);
        ($this->deleteRefreshTokenService)($request);
    }

    public function testWhenRequestIsValidThenDeleteRefreshToken()
    {
        $request = new DeleteRefreshTokenRequest('token_string');
        $refreshToken = (new RefreshTokenBuilder())
            ->withRefreshToken(new Token($request->getRefreshTokenValue()))
            ->build();
        $this->refreshTokenRepository
            ->expects(self::once())
            ->method('ofToken')
            ->with($refreshToken->getRefreshToken())
            ->willReturn($refreshToken);
        $this->refreshTokenRepository->expects(self::once())->method('delete')->with($refreshToken);

        ($this->deleteRefreshTokenService)($request);
    }


}