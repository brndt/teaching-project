Feature: Creating user

  Scenario: Creating user when all inputs are valid
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "randomemail678678@localhost.com",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "message": "User has been created"
    }
    """
    And the response status code should be 201

  Scenario: Creating user when email already exists
    Given there are Users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles   |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | student |
      | cfe849f3-7832-435a-b484-83fabf530794 | irving    | cruz        | irving@lasalle.es | qwertY12 | teacher |
    When I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikita@lasalle.es",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "User is already exists"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when all email is invalid
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikitalasalle.es",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Email address is invalid"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when password doesn't contain any letter characters
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "12345678",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Password doesn't contain any letter characters"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when password doesn't contain any number characters
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "qwertyop",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Password doesn't contain any number characters"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when password is less than 8 characters
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "123456",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Password is less than 8 characters"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when first name contains invalid characters
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "123456Aq",
      "firstName": "Alex!",
      "lastName": "Johnson",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Name should only contain alpha characters or a hyphen or apostrophe"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when last name contains invalid characters
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson#",
      "roles": ["student"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Name should only contain alpha characters or a hyphen or apostrophe"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when role has invalid value
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["guest"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 400,
      "message": "Provided role value is invalid"
    }
    """
    And the response status code should be 400

  Scenario: Creating user when role has 'admin' value
    Given I send a POST request to "/api/v1/users" with body:
     """
    {
      "email": "nikital@asalle.es",
      "password": "123456Aq",
      "firstName": "Alex",
      "lastName": "Johnson",
      "roles": ["admin"]
    }
    """
    Then the response content should be:
     """
    {
      "code": 403,
      "message": "You do not have permission to perform this action"
    }
    """
    And the response status code should be 403
