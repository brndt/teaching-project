Feature: Authorizated Search Categories By Criteria

  Scenario: Search categories by criteria when all inputs are valid
    Given there are Users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are categories with the following details:
      | id                                   | name        | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | language    | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/panel/categories/cfe849f3-7832-435a-b484-83fabf530794"
    Then the response content should be:
    """
    {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "name": "language",
      "status": "published"
    }
    """
    And the response status code should be 200
