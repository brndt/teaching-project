Feature: Unauthorized search categories by criteria

  Scenario: Unauthorized searching categories by criteria when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are categories with the following details:
      | id                                   | name        | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | language    | published |
      | f132f80d-439b-4105-9f43-87f82c75f8be | music       | published |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | programming | archive   |
    When I send a GET request to "/api/v1/categories" with body:
     """
    {
      "limit": "10"
    }
    """
    Then the response status code should be 200
    And the response content should be:
    """
    [
    {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "name": "language",
      "status": "published"
    },
    {
      "id": "f132f80d-439b-4105-9f43-87f82c75f8be",
      "name": "music",
      "status": "published"
    }
    ]
    """

  Scenario: Unauthorized searching categories by criteria when they don't exist
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    When I send a GET request to "/api/v1/categories" with body:
     """
    {
      "limit": "10"
    }
    """
    And the response status code should be 204
    Then the response content should be:
    """
    null
    """
