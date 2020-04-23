<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Token\Application\Service;

use LaSalle\StudentTeacher\Shared\Application\Exception\InvalidArgumentValidationException;
use LaSalle\StudentTeacher\Shared\Domain\Exception\InvalidUuidException;
use LaSalle\StudentTeacher\Shared\Domain\ValueObject\Uuid;
use LaSalle\StudentTeacher\Token\Application\Exception\TokenNotFoundException;
use LaSalle\StudentTeacher\Token\Application\Request\CreateTokenRequest;
use LaSalle\StudentTeacher\Token\Application\Response\CreateTokenResponse;
use LaSalle\StudentTeacher\Token\Domain\Repository\TokenRepository;
use LaSalle\StudentTeacher\User\Application\Exception\UserNotFoundException;
use LaSalle\StudentTeacher\User\Domain\Repository\UserRepository;

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
        try {
            $userId = Uuid::fromString($request->getUserId());
        } catch (InvalidUuidException $exception) {
            throw new InvalidArgumentValidationException($exception->getMessage());
        }

        $user = $this->userRepository->searchById($userId);

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