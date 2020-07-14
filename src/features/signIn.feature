Feature: Sign in user

  Scenario: Sign in user when all inputs are valid
    Given there are Users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
    When I send a POST request to "/api/v1/users/sign_in" with body:
     """
    {
      "email": "nikita@lasalle.es",
      "password": "123456Aq"
    }
    """
    Then the response status code should be 200
