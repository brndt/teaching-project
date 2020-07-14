Feature: Create user connection

  Scenario: Create user connection when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/connections" with body:
     """
    {
      "friendId": "cfe849f3-7832-435a-b484-83fabf530794"
    }
    """
    Then the response status code should be 201
    And the response content should be:
     """
    {
      "message": "Request has been successfully sent to this user"
    }
    """
