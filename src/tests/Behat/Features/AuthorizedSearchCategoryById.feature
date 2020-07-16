Feature: Authorized search category by id

  Scenario: Searching category by id when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | language | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/panel/categories/cfe849f3-7832-435a-b484-83fabf530794"
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "name": "language",
      "status": "published"
    }
    """

  Scenario: Searching category by id when I don't have admin permissions
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | teacher |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | language | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/panel/categories/cfe849f3-7832-435a-b484-83fabf530794"
    Then the response status code should be 403
    And the response content should be:
    """
    {
      "code": 403,
      "message": "You do not have permission to perform this action"
    }
    """

  Scenario: Searching category by id when I'm not signed in
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | teacher |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | language | published |
    When I send a GET request to "/api/v1/panel/categories/cfe849f3-7832-435a-b484-83fabf530794"
    Then the response status code should be 401
    And the response content should be:
    """
    {
      "code": 401,
      "message": "JWT Token not found"
    }
    """

  Scenario: Searching category by id when all inputs are valid but category doesn't exist
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/panel/categories/cfe849f3-7832-435a-b484-83fabf530794"
    Then the response status code should be 404
    And the response content should be:
    """
    {
      "code": 404,
      "message": "Category not found"
    }
    """
