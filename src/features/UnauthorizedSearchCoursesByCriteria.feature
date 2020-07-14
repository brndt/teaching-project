Feature: Unauthorized search courses By Criteria

  Scenario: Unauthorized search courses by criteria when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are courses with the following details:
      | id                                   | categoryId                           | teacherId                            | name           | description      | level       | created                   | modified                  | status    |
      | cfe849f3-7832-435a-b484-83fabf530794 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | spanish course | some description | some level  | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
      | 48d34c63-6bba-4c72-a461-8aac1fd7d138 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | french course  | some description | some level  | 2020-07-13T13:54:13+00:00 | 2020-07-13T13:54:13+00:00 | published |
      | 62b3d03d-eb61-4aff-8ba7-ce6d71119803 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | italian course | some description | basic level | 2020-07-12T13:54:13+00:00 | 2020-07-13T13:54:13+00:00 | archive   |
    When I send a GET request to "/api/v1/courses" with body:
     """
    {
      "limit": "10"
    }
    """
    Then the response content should be:
    """
    [
    {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "teacherId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "categoryId": "b2c3532f-6629-435a-9908-63f9d3811ccd",
      "name": "spanish course",
      "description": "some description",
      "level": "some level",
      "created": "2020-07-14T13:54:13+00:00",
      "modified": "2020-07-14T13:54:13+00:00",
      "status": "published"
    },
    {
      "id": "48d34c63-6bba-4c72-a461-8aac1fd7d138",
      "teacherId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "categoryId": "b2c3532f-6629-435a-9908-63f9d3811ccd",
      "name": "french course",
      "description": "some description",
      "level": "some level",
      "created": "2020-07-13T13:54:13+00:00",
      "modified": "2020-07-13T13:54:13+00:00",
      "status": "published"
    }
    ]
    """
    And the response status code should be 200
