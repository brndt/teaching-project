<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\User\Application\Service;

use InvalidArgumentException;
use LaSalle\StudentTeacher\Shared\Domain\RandomStringGenerator;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\User\Application\Request\GenerateTokensRequest;
use LaSalle\StudentTeacher\User\Application\Response\TokensResponse;
use LaSalle\StudentTeacher\User\Application\Service\GenerateTokensService;
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

final class GenerateTokensServiceTest extends TestCase
{
    private GenerateTokensService $generateTokensService;
    private MockObject $refreshTokenRepository;
    private MockObject $tokenManager;
    private MockObject $userRepository;
    private MockObject $randomStringGenerator;

    public function setUp(): void
    {
        $this->refreshTokenRepository = $this->createMock(RefreshTokenRepository::class);
        $this->tokenManager = $this->createMock(TokenManager::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->randomStringGenerator = $this->createMock(RandomStringGenerator::class);
        $this->generateTokensService = new GenerateTokensService(
            $this->refreshTokenRepository,
            $this->tokenManager,
            $this->userRepository,
            $this->randomStringGenerator
        );
    }

    public function testWhenUserIdIsInvalidThenThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        ($this->generateTokensService)($this->anyGenerateTokensRequestWithInvalidUserId());
    }

    public function testWhenRequestIsValidThenGenerateTokens()
    {
        $this->randomStringGenerator->method('generate')->willReturn('random_token');
        $this->refreshTokenRepository->expects($this->once())->method('save')->with(
            $this->equalTo($this->anyValidRefreshToken())
        );
        $this->userRepository->expects($this->once())->method('ofId')->with($this->equalTo($this->anyValidRefreshToken()->getUserId()))->willReturn($this->anyValidUser());
        $this->tokenManager->expects($this->once())->method('generate')->with($this->callback($this->userComparator($this->anyValidUser())))->willReturn('token_string');

        $tokensResponse = ($this->generateTokensService)($this->anyValidGenerateTokensRequest());
        $this->assertEquals($this->anyValidTokensResponse(),$tokensResponse);
    }

    private function anyValidGenerateTokensRequest(): GenerateTokensRequest
    {
        return new GenerateTokensRequest(
            'cfe849f3-7832-435a-b484-83fabf530794',
            new \DateTimeImmutable('2020-04-28 15:00:00')
        );
    }

    private function anyGenerateTokensRequestWithInvalidUserId(): GenerateTokensRequest
    {
        return new GenerateTokensRequest(
            '16bf6c6a-c855-4a36-a3dd-5b9f6d92c753-invalid',
            new \DateTimeImmutable('2020-04-28 15:00:00')
        );
    }

    private function anyValidRefreshToken(): RefreshToken
    {
        return new RefreshToken(
            new Token('random_token'),
            new Uuid('cfe849f3-7832-435a-b484-83fabf530794'),
            new \DateTimeImmutable('2020-04-28 15:00:00')
        );
    }

    private function anyValidTokensResponse(): TokensResponse
    {
        return new TokensResponse(
            'token_string',
            $this->anyValidRefreshToken()->getRefreshToken()->toString()
        );
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
}