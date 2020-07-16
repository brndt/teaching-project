Feature: Sign in user

  Scenario: Sign in user when all inputs are valid
    Given there are users with the following details:
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

  Scenario: Sign in user when user doesn't exist
    When I send a POST request to "/api/v1/users/sign_in" with body:
     """
    {
      "email": "nikita@lasalle.es",
      "password": "123456Aq"
    }
    """
    Then the response status code should be 404
    And the response content should be:
     """
    {
      "code": 404,
      "message": "User was not found"
    }
    """

  Scenario: Sign in user when password is incorrect
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
    When I send a POST request to "/api/v1/users/sign_in" with body:
     """
    {
      "email": "nikita@lasalle.es",
      "password": "123456AqOtherPassword"
    }
    """
    Then the response status code should be 400
    And the response content should be:
     """
    {
      "code": 400,
      "message": "Your password is incorrect"
    }
    """
