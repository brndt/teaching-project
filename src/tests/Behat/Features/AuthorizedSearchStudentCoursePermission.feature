Feature: Search student course permission

  Scenario: Search student course permission when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin   |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
      | ab0642f0-ac7f-406a-903e-1c5480e7b7f8 | student    | luis        | student@lasalle.es | qwertY12 | student |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | b2c3532f-6629-435a-9908-63f9d3811ccd | language | published |
    And there are courses with the following details:
      | id                                   | categoryId                           | teacherId                            | name           | description      | level      | created                   | modified                  | status    |
      | ea4f0311-4ffc-4fd0-9b4a-f2743565d851 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | spanish course | some description | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And there are course permissions with the following details:
      | id                                   | courseId                             | studentId                            | created                   | modified                  | status    |
      | 5a79de58-3cd0-4c4d-8d33-cb5aee5eb84a | ea4f0311-4ffc-4fd0-9b4a-f2743565d851 | ab0642f0-ac7f-406a-903e-1c5480e7b7f8 | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/panel/course_permission?courseId=ea4f0311-4ffc-4fd0-9b4a-f2743565d851&studentId=ab0642f0-ac7f-406a-903e-1c5480e7b7f8"
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "id": "5a79de58-3cd0-4c4d-8d33-cb5aee5eb84a",
      "courseId": "ea4f0311-4ffc-4fd0-9b4a-f2743565d851",
      "studentId": "ab0642f0-ac7f-406a-903e-1c5480e7b7f8",
      "created": "2020-07-14T13:54:13+00:00",
      "modified": "2020-07-14T13:54:13+00:00",
      "status": "published"
    }
    """
