Feature: Search users

  Scenario: Searching users when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   | image         | education | experience | created                   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student | avatar.jpg    | la salle  | 10 years   | 2020-07-14T17:39:01+00:00 |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher | avatar123.jpg | la salle  | 20 years   | 2020-05-05T12:40:01+00:00 |
    When I send a GET request to "/api/v1/users"
    Then the response status code should be 200
    And the response content should be:
     """
    [
    {
      "id": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "firstName": "nikita",
      "lastName": "grichinenko",
      "roles": ["student"],
      "created": "2020-07-14T17:39:01+00:00",
      "image": "avatar.jpg",
      "education": "la salle",
      "experience": "10 years"
    },
    {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "firstName": "irving",
      "lastName": "cruz",
      "roles": ["teacher"],
      "created": "2020-05-05T12:40:01+00:00",
      "image": "avatar123.jpg",
      "education": "la salle",
      "experience": "20 years"
    }
    ]
    """

  Scenario: Searching users when I filter it by role
    Given there are users with the following details:
      | id                                   | firstName | lastName      | email             | password | roles   | image         | education | experience | created                   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko   | nikita@lasalle.es | 123456Aq | student | avatar.jpg    | la salle  | 10 years   | 2020-07-14T17:39:01+00:00 |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz          | irving@lasalle.es | qwertY12 | teacher | avatar123.jpg | la salle  | 20 years   | 2020-05-05T12:40:01+00:00 |
      | 02b799f2-9300-4e74-a82e-5e1ff8c35f78 | ruben     | cougil grande | ruben@lasalle.es  | zxcvb543 | teacher | avatar456.jpg | la salle  | 40 years   | 2020-06-05T12:40:01+00:00 |
    When I send a GET request to "/api/v1/users?role=teacher"
    Then the response status code should be 200
    And the response content should be:
     """
    [
     {
      "id": "cfe849f3-7832-435a-b484-83fabf530794",
      "firstName": "irving",
      "lastName": "cruz",
      "roles": ["teacher"],
      "created": "2020-05-05T12:40:01+00:00",
      "image": "avatar123.jpg",
      "education": "la salle",
      "experience": "20 years"
    },
    {
      "id": "02b799f2-9300-4e74-a82e-5e1ff8c35f78",
      "firstName": "ruben",
      "lastName": "cougil grande",
      "roles": ["teacher"],
      "created": "2020-06-05T12:40:01+00:00",
      "image": "avatar456.jpg",
      "education": "la salle",
      "experience": "40 years"
    }
    ]
    """

  Scenario: Searching users when users don't exist
    When I send a GET request to "/api/v1/users"
    Then the response status code should be 200
    And the response content should be:
    """
    []
    """
