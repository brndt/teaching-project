<?php

declare(strict_types=1);

namespace LaSalle\StudentTeacher\Shared\Infrastructure\Behat;

use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\DomCrawler\Crawler;

final class MinkSessionRequestHelper
{
    private MinkHelper $sessionHelper;

    public function __construct($sessionHelper)
    {
        $this->sessionHelper = $sessionHelper;
    }

    public function sendRequest($method, $url, array $optionalParams = []): void
    {
        $this->request($method, $url, $optionalParams);
    }

    public function sendRequestWithPyStringNode($method, $url, PyStringNode $body): void
    {
        $this->request($method, $url, ['content' => $body->getRaw()]);
    }

    public function request($method, $url, array $optionalParams = []): Crawler
    {
        return $this->sessionHelper->sendRequest($method, $url, $optionalParams);
    }
}
