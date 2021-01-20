<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\RefreshToken\Application;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\RefreshToken\Application\Service\UpdateRefreshTokenExpirationService;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\RefreshToken\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Test\LaSalle\StudentTeacher\User\RefreshToken\Domain\RefreshTokenBuilder;
use Test\LaSalle\StudentTeacher\User\User\Domain\UserBuilder;

final class UpdateRefreshTokenExpirationTest extends TestCase
{
    private MockObject $refreshTokenRepository;
    private MockObject $tokenManager;
    private MockObject $userRepository;
    private MockObject $randomStringGenerator;
    private UpdateRefreshTokenExpirationService $updateRefreshTokenExpirationService;

    public function setUp(): void
    {
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->updateRefreshTokenExpirationService = new UpdateRefreshTokenExpirationService(
            $this->refreshTokenRepository, $this->tokenManager, $this->userRepository, $this->randomStringGenerator
        );
    }

    public function testWhenRefreshTokenNotFoundThenThrowException()
    {
        $request = new UpdateRefreshTokenExpirationRequest('token_string', new DateTimeImmutable('+ 1 day'));

        $this->expectException(RefreshTokenNotFoundException::class);
        $this->refreshTokenRepository->expects(self::once())->method('ofToken')->willReturn(null);
        ($this->updateRefreshTokenExpirationService)($request);
    }

    public function testWhenRefreshTokenIsExpiredThenThrowException()
    {
        $request = new UpdateRefreshTokenExpirationRequest('token_string', new DateTimeImmutable('+ 1 day'));
        $refreshToken = (new RefreshTokenBuilder())
            ->withExpirationDate(new DateTimeImmutable())
            ->build();

        $this->expectException(RefreshTokenIsExpiredException::class);
        $this->refreshTokenRepository->expects(self::once())->method('ofToken')->willReturn($refreshToken);
        ($this->updateRefreshTokenExpirationService)($request);
    }

    public function testWhenRequestIsValidThenUpdateRefreshTokenExpiration()
    {
        $request = new UpdateRefreshTokenExpirationRequest('token_string', new DateTimeImmutable('+ 1 day'));
        $refreshToken = (new RefreshTokenBuilder())
            ->withRefreshToken(new Token($request->getRefreshToken()))
            ->withUserId(Uuid::generate())
            ->withExpirationDate(new DateTimeImmutable('+ 1 day'))
            ->build();
        $user = (new UserBuilder())
            ->withId($refreshToken->getUserId())
            ->build();
        $expectedTokensResponse = new TokensResponse(
            'token_string',
            $refreshToken->getRefreshToken()->toString(),
            $refreshToken->getUserId()->toString()
        );

        $this->refreshTokenRepository->expects(self::once())->method('ofToken')->willReturn($refreshToken);
        $this->refreshTokenRepository->expects(self::once())->method('save')->with(
            $this->callback($this->refreshTokenComparator($refreshToken))
        );
        $this->userRepository->expects(self::once())->method('ofId')->with(
            $refreshToken->getUserId()
        )->willReturn($user);
        $this->tokenManager->expects(self::once())->method('generate')->with(
            $this->callback($this->userComparator($user))
        )->willReturn('token_string');

        $tokensResponse = ($this->updateRefreshTokenExpirationService)($request);
        $this->assertTokensResponsesAreEqual($expectedTokensResponse, $tokensResponse);
    }

    private function assertTokensResponsesAreEqual(
        TokensResponse $firstTokenResponse,
        TokensResponse $secondTokenResponse
    ) {
        $this->assertEquals($firstTokenResponse->getRefreshToken(), $secondTokenResponse->getRefreshToken());
        $this->assertEqualsWithDelta(
            $firstTokenResponse->getRefreshToken(),
            $secondTokenResponse->getRefreshToken(),
            60
        );
    }

    private function refreshTokenComparator(RefreshToken $refreshTokenExpected): callable
    {
        return function (RefreshToken $refreshTokenActual) use ($refreshTokenExpected) {
            return $refreshTokenExpected->getRefreshToken()->toString() === $refreshTokenActual->getRefreshToken(
                )->toString()
                && $refreshTokenExpected->getExpirationDate()->diff($refreshTokenActual->getExpirationDate())->m < 1;
        };
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getExpirationDate()->diff($userActual->getExpirationDate())->m < 1;
        };
    }
}
