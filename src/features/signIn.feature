Feature: Sign in user

  Scenario: Sign in user when all inputs are valid
    Given there are Users with the following details:
      | firstName | lastName    | email             | password |
      | nikita    | grichinenko | nikita@lasalle.es | 123456Aq |
    When I send a POST request to "/api/v1/users/sign_in" with body:
     """
    {
      "email": "nikita@lasalle.es",
      "password": "123456Aq"
    }
    """
    Then the response status code should be 200
