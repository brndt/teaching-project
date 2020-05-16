<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use DateTimeImmutable;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Exception\RefreshTokenIsExpiredException;
use LaSalle\StudentTeacher\User\Application\Exception\RefreshTokenNotFoundException;
use LaSalle\StudentTeacher\User\Application\Request\UpdateRefreshTokenExpirationRequest;
use LaSalle\StudentTeacher\User\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\Application\Service\UpdateRefreshTokenExpirationService;
use LaSalle\StudentTeacher\User\Domain\Aggregate\RefreshToken;
use LaSalle\StudentTeacher\User\Domain\Aggregate\User;
use LaSalle\StudentTeacher\User\Domain\Repository\RefreshTokenRepository;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;
use LaSalle\StudentTeacher\User\Domain\TokenManager;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Email;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Name;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Password;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Roles;
use LaSalle\StudentTeacher\User\Domain\ValueObject\Token;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class UpdateRefreshTokenExpirationTest extends TestCase
{
    private MockObject $refreshTokenRepository;
    private MockObject $tokenManager;
    private MockObject $userRepository;
    private UpdateRefreshTokenExpirationService $updateRefreshTokenExpirationService;

    public function setUp(): void
    {
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->updateRefreshTokenExpirationService = new UpdateRefreshTokenExpirationService($this->refreshTokenRepository, $this->tokenManager, $this->userRepository);
    }

    public function testWhenRefreshTokenNotFoundThenThrowException()
    {
        $this->expectException(RefreshTokenNotFoundException::class);
        $this->refreshTokenRepository->expects($this->once())->method('ofToken')->willReturn(null);
        ($this->updateRefreshTokenExpirationService)($this->anyValidRefreshTokenRequest());
    }

    public function testWhenRefreshTokenIsExpiredThenThrowException()
    {
        $this->expectException(RefreshTokenIsExpiredException::class);
        $this->refreshTokenRepository->expects($this->once())->method('ofToken')->willReturn($this->anyExpiredRefreshToken());
        ($this->updateRefreshTokenExpirationService)($this->anyValidRefreshTokenRequest());
    }

    public function testWhenRequestIsValidThenUpdateRefreshTokenExpiration()
    {
        $this->refreshTokenRepository->expects($this->once())->method('ofToken')->willReturn($this->anyValidRefreshToken());
        $this->refreshTokenRepository->expects($this->once())->method('save')->with($this->equalTo($this->anyValidRefreshToken()));
        $this->userRepository->expects($this->once())->method('ofId')->with($this->equalTo($this->anyValidRefreshToken()->getUserId()))->willReturn($this->anyValidUser());
        $this->tokenManager->expects($this->once())->method('generate')->with($this->callback($this->userComparator($this->anyValidUser())))->willReturn('token_string');

        $tokensResponse = ($this->updateRefreshTokenExpirationService)($this->anyValidRefreshTokenRequest());
        $this->assertEquals($this->anyValidTokensResponse(), $tokensResponse);
    }

    private function anyValidRefreshToken(): RefreshToken
    {
        return new RefreshToken(new Token($this->anyValidRefreshTokenRequest()->getRefreshToken()), new Uuid('48d34c63-6bba-4c72-a461-8aac1fd7d138'), $this->anyValidRefreshTokenRequest()->getNewExpirationDate());
    }

    private function anyExpiredRefreshToken(): RefreshToken
    {
        return new RefreshToken(new Token($this->anyValidRefreshTokenRequest()->getRefreshToken()), new Uuid('48d34c63-6bba-4c72-a461-8aac1fd7d138'), new \DateTimeImmutable());
    }

    private function anyValidRefreshTokenRequest(): UpdateRefreshTokenExpirationRequest
    {
        return new UpdateRefreshTokenExpirationRequest(
            'token_string',
            (new DateTimeImmutable(date('Y-m-d H:m:s')))->modify('+ 1 day')
        );
    }

    private function userComparator(User $userExpected): callable
    {
        return function (User $userActual) use ($userExpected) {
            return $userExpected->getEmail()->toString() === $userActual->getEmail()->toString()
                && $userExpected->getFirstName()->toString() === $userActual->getFirstName()->toString()
                && $userExpected->getLastName()->toString() === $userActual->getLastName()->toString()
                && $userExpected->getRoles()->getArrayOfPrimitives() === $userActual->getRoles()->getArrayOfPrimitives()
                && $userExpected->getCreated() == $userActual->getCreated();
        };
    }

    private function anyValidUser(): User
    {
        return new User(
            new Uuid('cfe849f3-7832-435a-b484-83fabf530794'),
            new Email('user@example.com'),
            Password::fromPlainPassword('123456aa'),
            new Name('Alex'),
            new Name('Johnson'),
            Roles::fromArrayOfPrimitives(['teacher']),
            new \DateTimeImmutable('2020-04-27'),
            false
        );
    }

    private function anyValidTokensResponse(): TokensResponse
    {
        return new TokensResponse(
            'token_string',
            $this->anyValidRefreshToken()->getRefreshToken()->toString()
        );
    }
}