Feature: Create category

  Scenario: Create category when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a POST request to "/api/v1/panel/categories" with body:
    """
    {
      "name": "language",
      "status": "published"
    }
    """
    Then the response content should be:
    """
    {
      "message": "Category has been successfully created"
    }
    """
    And the response status code should be 201
