<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Token\Create;

use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Domain\TokenRepository;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\UserRepository;

final class CreateToken
{
    private TokenRepository $tokenRepository;
    private UserRepository $userRepository;

    public function __construct(TokenRepository $tokenRepository, UserRepository $userRepository)
    {
        $this->tokenRepository = $tokenRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(CreateTokenRequest $request): CreateTokenResponse
    {
        $user = $this->userRepository->searchById(Uuid::fromString($request->getUserId()));

        if (null === $user) {
            throw new UserNotFoundException();
        }

        $token = $this->tokenRepository->create($user);

        if (null === $token) {
            throw new TokenNotFoundException();
        }

        return new CreateTokenResponse($token->getToken());
    }
}