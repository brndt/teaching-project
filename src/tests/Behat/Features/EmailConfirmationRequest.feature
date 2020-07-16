Feature: Email confirmation request

  Scenario: Sending email confirmation when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    When I send a POST request to "/api/v1/users/email_confirmation" with body:
    """
    {
      "email": "nikita@lasalle.es"
    }
    """
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "message": "Confirmation has been successfully sent to your email"
    }
    """

  Scenario: Sending email confirmation when I pass email that doesn't exist
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    When I send a POST request to "/api/v1/users/email_confirmation" with body:
    """
    {
      "email": "irving@lasalle.es"
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
