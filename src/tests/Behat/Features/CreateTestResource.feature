Feature: Create video resource

  Scenario: Creating unit when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin   |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    And there are categories with the following details:
      | id                                   | name     | status    |
      | b2c3532f-6629-435a-9908-63f9d3811ccd | language | published |
    And there are courses with the following details:
      | id                                   | categoryId                           | teacherId                            | name           | description      | level      | created                   | modified                  | status    |
      | ea4f0311-4ffc-4fd0-9b4a-f2743565d851 | b2c3532f-6629-435a-9908-63f9d3811ccd | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | spanish course | some description | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And there are units with the following details:
      | id                                   | courseId                             | name              | description       | level      | created                   | modified                  | status    |
      | 5fd6fb25-a2a9-4376-af45-3c48508acd16 | ea4f0311-4ffc-4fd0-9b4a-f2743565d851 | random unit       | some description  | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
      | 18282c2f-6539-41f9-89ea-11f0c1d48069 | ea4f0311-4ffc-4fd0-9b4a-f2743565d851 | other random unit | other description | some level | 2020-07-14T13:54:13+00:00 | 2020-07-14T13:54:13+00:00 | published |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a POST request to "/api/v1/panel/courses/ea4f0311-4ffc-4fd0-9b4a-f2743565d851/units/5fd6fb25-a2a9-4376-af45-3c48508acd16/test_resources" with body:
    """
    {
      "unitId": "64f2a0db-c96c-4807-ba6f-e2408e4dff8c",
      "name": "super test123678",
      "content": "some content",
      "status": "published",
      "questions": {
        "0": {
          "question": "hola como estas",
          "answers": {
            "0": {
              "answer" : "bien",
              "is_correct": true
            },
            "1": {
              "answer" : "mal",
              "is_correct": false
            }
           }
          },
          "1": {
            "question": "hola que tal",
            "answers": {
              "0": {
                "answer" : "perfecto",
                "is_correct": true
              },
              "1": {
                "answer" : "fatal",
                "is_correct": false
              }
            }
          }
        }
    }
    """
    Then the response status code should be 201
    And the response content should be:
    """
    {
      "message": "Test resource has been successfully created"
    }
    """
