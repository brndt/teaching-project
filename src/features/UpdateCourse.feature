Feature: Update course

  Scenario: Updating course when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | b2c3532f-6629-435a-9908-63f9d3811ccd | language | published |
    And there are courses with the following details:
      | id                                   | categoryId                           | teacherId                            | name           | description      | level      | created                   | modified                  | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | spanish course | some description | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a PATCH request to "/api/v1/panel/courses/cfe849f3-7832-435a-b484-83fabf530794" with body:
    """
    {
      "teacherId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "categoryId": "b2c3532f-6629-435a-9908-63f9d3811ccd",
      "name": "some new title",
      "description": "some new description",
      "level": "advanced",
      "status": "unpublished"
    }
    """
    Then the response content should be:
    """
    {
      "message": "Course has been successfully updated"
    }
    """
    And the response status code should be 200

  Scenario: Updating course when I'm not such a teacher
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | b2c3532f-6629-435a-9908-63f9d3811ccd | language | published |
    And there are courses with the following details:
      | id                                   | categoryId                           | teacherId                            | name           | description      | level      | created                   | modified                  | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | spanish course | some description | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And I am authenticated as "irving@lasalle.es" with "qwertY12" password
    When I send a PATCH request to "/api/v1/panel/courses/cfe849f3-7832-435a-b484-83fabf530794" with body:
    """
    {
      "teacherId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "categoryId": "b2c3532f-6629-435a-9908-63f9d3811ccd",
      "name": "some new title",
      "description": "some new description",
      "level": "advanced",
      "status": "unpublished"
    }
    """
    Then the response status code should be 403
    And the response content should be:
    """
    {
      "code": 403,
      "message": "You do not have permission to perform this action"
    }
    """
