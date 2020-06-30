Feature: Using SymfonyExtension

  Scenario: Checking the application's kernel environment
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "randomemail678678@localhost.com",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "message": "User has been created"
    }
    """
