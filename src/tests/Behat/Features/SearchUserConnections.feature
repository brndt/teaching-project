Feature: Search user connections

  Scenario: Searching user connections when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName      | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko   | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz          | irving@lasalle.es | qwertY12 | teacher |
      | 02b799f2-9300-4e74-a82e-5e1ff8c35f78 | ruben     | cougil grande | ruben@lasalle.es  | zxcvb543 | teacher |
    And there are user connections with the following details:
      | studentId                            | teacherId                            | state  | specifierId                          |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | cfe849f3-7832-435a-b484-83fabf530794 | pended | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | 02b799f2-9300-4e74-a82e-5e1ff8c35f78 | pended | 02b799f2-9300-4e74-a82e-5e1ff8c35f78 |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/connections"
    Then the response status code should be 200
    And the response content should be:
     """
    [
    {
      "userId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "friendId": "cfe849f3-7832-435a-b484-83fabf530794",
      "status": "pended",
      "specifierId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753"
    },
        {
      "userId": "16bf6c6a-c855-4a36-a3dd-5b9f6d92c753",
      "friendId": "02b799f2-9300-4e74-a82e-5e1ff8c35f78",
      "status": "pended",
      "specifierId": "02b799f2-9300-4e74-a82e-5e1ff8c35f78"
    }
    ]
    """

  Scenario: Searching user connections when i'm not specified user
    Given there are users with the following details:
      | id                                   | firstName | lastName      | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko   | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz          | irving@lasalle.es | qwertY12 | teacher |
      | 419b2c90-b52d-4c2d-8fb8-b6419dbd5b10 | ruben     | cougil grande | ruben@lasalle.es  | poiuyt99 | teacher |
    And there are user connections with the following details:
      | studentId                            | teacherId                            | state  | specifierId                          |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | cfe849f3-7832-435a-b484-83fabf530794 | pended | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 |
    And I am authenticated as "ruben@lasalle.es" with "poiuyt99" password
    When I send a GET request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/connections"
    Then the response status code should be 403
    And the response content should be:
     """
    {
      "code": 403,
      "message": "You do not have permission to perform this action"
    }
    """

  Scenario: Searching user connections when i'm not signed in
    Given there are users with the following details:
      | id                                   | firstName | lastName      | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko   | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz          | irving@lasalle.es | qwertY12 | teacher |
      | 419b2c90-b52d-4c2d-8fb8-b6419dbd5b10 | ruben     | cougil grande | ruben@lasalle.es  | poiuyt99 | teacher |
    And there are user connections with the following details:
      | studentId                            | teacherId                            | state  | specifierId                          |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | cfe849f3-7832-435a-b484-83fabf530794 | pended | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 |
    When I send a GET request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/connections"
    Then the response status code should be 401
    And the response content should be:
    """
    {
      "code": 401,
      "message": "JWT Token not found"
    }
    """

  Scenario: Searching user connections when user connections don't exist
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a GET request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/connections"
    Then the response status code should be 204
    And the response content should be:
     """
    null
    """
