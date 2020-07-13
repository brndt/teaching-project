<?php

declare(strict_types=1);

namespace Test\LaSalle\StudentTeacher\Behat\Context;

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use LaSalle\StudentTeacher\Shared\Infrastructure\Behat\MinkHelper;
use LaSalle\StudentTeacher\Shared\Infrastructure\Behat\MinkSessionRequestHelper;
use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

final class ApiContext extends RawMinkContext implements Context
{
    private KernelInterface $kernel;
    private MinkHelper $sessionHelper;
    private Session $minkSession;
    private MinkSessionRequestHelper $request;
    private string $token;
    private string $refreshToken;

    public function __construct(Session $minkSession, KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        $this->minkSession = $minkSession;
        $this->sessionHelper = new MinkHelper($this->minkSession);
        $this->request = new MinkSessionRequestHelper(new MinkHelper($minkSession));
    }

    /**
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, PyStringNode $body): void
    {
        $this->request->sendRequestWithPyStringNode($method, $this->locatePath($url), $body);
    }

    /**
     * @Given /^I am authenticated as "([^"]*)" with "([^"]*)" password$/
     */
    public function iAmAuthenticatedAs($email, $password)
    {
        $credentials = ['email' => $email, 'password' => $password];

        $this->request->sendRequest(
            'POST',
            $this->locatePath('/api/v1/users/sign_in'),
            ['content' => json_encode($credentials)]
        );

        $response = json_decode($this->sessionHelper->getResponse(), true);

        $this->sessionHelper->setServerParameter('HTTP_Authorization', sprintf('Bearer %s', $response['token']));
    }

    /**
     * @Then the response content should be:
     */
    public function theResponseContentShouldBe(PyStringNode $expectedResponse): void
    {
        $expected = $this->sanitizeOutput($expectedResponse->getRaw());
        $actual = $this->sanitizeOutput($this->sessionHelper->getResponse());

        if ($expected !== $actual) {
            throw new RuntimeException(
                sprintf("The outputs does not match!\n\n-- Expected:\n%s\n\n-- Actual:\n%s", $expected, $actual)
            );
        }
    }

    /**
     * @Then the response status code should be :expectedResponseCode
     */
    public function theResponseStatusCodeShouldBe($expectedResponseCode): void
    {
        if ($this->minkSession->getStatusCode() !== (int)$expectedResponseCode) {
            throw new RuntimeException(
                sprintf(
                    'The status code <%s> does not match the expected <%s>',
                    $this->minkSession->getStatusCode(),
                    $expectedResponseCode
                )
            );
        }
    }

    /**
     * @BeforeScenario
     */
    public function beforeScenario()
    {
        StaticDriver::beginTransaction();
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        StaticDriver::rollBack();
    }

    /**
     * @BeforeSuite
     */
    public static function beforeSuite()
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    /**
     * @AfterSuite
     */
    public static function afterSuite()
    {
        StaticDriver::setKeepStaticConnections(false);
    }

    private function sanitizeOutput(string $output)
    {
        return json_encode(json_decode(trim($output), true));
    }

}
