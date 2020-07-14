Feature: Create course

  Scenario: Create course when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin   |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | b2c3532f-6629-435a-9908-63f9d3811ccd | language | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a POST request to "/api/v1/panel/courses" with body:
    """
    {
      "teacherId": "cfe849f3-7832-435a-b484-83fabf530794",
      "categoryId": "b2c3532f-6629-435a-9908-63f9d3811ccd",
      "name": "course name",
      "description": "course description",
      "level": "basic",
      "status": "published"
    }
    """
    Then the response content should be:
    """
    {
      "message": "Course has been successfully created"
    }
    """
    And the response status code should be 201
