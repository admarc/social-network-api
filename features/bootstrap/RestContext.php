<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\ClientInterface;
use Behat\Gherkin\Node\PyStringNode;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class RestContext implements Context
{
    const API_VERSION = 'v1';

    private $client;
    private $method;
    private $resource;
    private $body;
    private $formParams;
    private $filters = [];
    private $response;
    private $token;
    private $jwtTokenManager;
    private $entityManager;

    public function __construct(
        ClientInterface $client,
        JWTTokenManagerInterface $jwtTokenManager,
        EntityManagerInterface $entityManager
    ) {
        $this->client = $client;
        $this->jwtTokenManager = $jwtTokenManager;
        $this->entityManager = $entityManager;
    }

    /**
     * @Given I want to get the list of :url
     */
    public function iWantToGetTheListOf($resource)
    {
        $this->resource = $resource;
        $this->method = 'get';
    }

    /**
     * @Given I want to create a :resource
     */
    public function iWantToCreateA(string $resource)
    {
        $this->resource = sprintf('%ss', $resource);
        $this->method = 'post';
    }

    /**
     * @Given I want to update a :resource with id :id
     */
    public function iWantToUpdateAWithId(string $resource, int $id)
    {
        $this->resource = sprintf('%ss/%d', $resource, $id);
        $this->method = 'put';
    }

    /**
     * @Given I want to delete a :resource with id :id
     */
    public function iWantToDeleteAWithId(string $resource, int $id)
    {
        $this->resource = sprintf('%ss/%d', $resource, $id);
        $this->method = 'delete';
    }

    /**
     * @Given I want to follow the user with id :id
     */
    public function iWantToFollowTheUserWithId(int $id)
    {
        $this->resource = sprintf('users/%d/followers', $id);
        $this->method = 'put';
    }

    /**
     * @Given I provide form data:
     */
    public function iProvideFormData(\Behat\Gherkin\Node\TableNode $table)
    {
        $this->formParams = $table->getRowsHash();
    }

    /**
     * @Given I provide data:
     */
    public function iProvideData(PyStringNode $body)
    {
        $this->body = $body->getRaw();
    }

    /**
     * @When I request :url with :method method
     */
    public function iRequestWithMethod($url, $method)
    {
        $url = sprintf('/api/%s/%s', self::API_VERSION, $url);

        $options = ['http_errors' => false];

        if (null !== $this->body) {
            $options['body'] = $this->body;
        }

        if (null !== $this->formParams) {
            $options['form_params'] = $this->formParams;
        }

        $this->response = $this->client->request(
            $method,
            $url,
            $options
        );
    }

    /**
     * @When I request resource
     */
    public function iRequestResource()
    {
        if (!$this->resource) {
            throw new \Exception('Resource needs to be set to perform request');
        }

        $url = sprintf('/api/%s/%s', self::API_VERSION, $this->resource);

        $options = ['http_errors' => false];

        if (null !== $this->token) {
            $options['headers'] = ['Authorization' => "Bearer {$this->token}"];
        }

        if (!empty($this->filters)) {
            $url = sprintf('%s?%s', $url, http_build_query($this->filters));
        }

        if (null !== $this->body) {
            $options['body'] = $this->body;
        }

        $this->response = $this->client->request(
            $this->method,
            $url,
            $options
        );
    }

    /**
     * @Then the response status code should be :status
     */
    public function theResponseStatusIs(string $status)
    {
        $statusesMap = [
            'Successful' => 200,
            'Created' => 201,
            'No Content' => 204,
            'Bad Request' => 400,
            'Unauthorized' => 401,
            'Forbidden' => 403,
            'Not Found' => 404,
        ];

        $responseStatus = $this->response->getStatusCode();

        if ($responseStatus !== $statusesMap[$status]) {
            throw new \RestResponseException(
                sprintf('Given response status does not match actual one: %s', $responseStatus)
            );
        }
    }

    /**
     * @Then the :headerName response header should be set to :headerValue
     */
    public function theResponseHeaderShouldBeSetTo($headerName, $headerValue)
    {
        $responseHeader = $this->response->getHeader($headerName)[0];
        if ($responseHeader !== $headerValue) {
            throw new \RestResponseException(
                sprintf('Response header for "%s" does not match actual one: %s', $headerName, $responseHeader)
            );
        }
    }

    /**
     * @Then the response should contain:
     */
    public function theResponseShouldContain(PyStringNode $expectedResponse)
    {
        $rawExpectedResponse = $this->cleanUpJsonResponse($expectedResponse->getRaw());

        $response = $this->cleanUpJsonResponse($this->response->getBody()->getContents());

        if ($rawExpectedResponse !== $response) {
            throw new \RestResponseException(
                sprintf('Given response does not match actual one: %s', print_r($response, true))
            );
        }
    }

    private function cleanUpJsonResponse(string $json): string
    {
        return json_encode(json_decode($json));
    }

    /**
     * @Given I set :name filter to :value
     */
    public function iSetFilterTo(string $name, string $value)
    {
        $this->filters[$name] = $value;
    }

    /**
     * @Given I am logged in as a user :email
     */
    public function iAmLoggedInUser(string $email)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        $this->token = $this->jwtTokenManager->create($user);
    }
}
