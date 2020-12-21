Feature: Email confirmation token

  Scenario: Email confirmation when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles | confirmationToken                                                                                                                | expirationDate |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin | 6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708 | +1 day         |
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/email_confirmation" with body:
    """
    {
      "token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 200
    And the response content should be:
    """
    {
      "message": "Your account has been successfully enabled"
    }
    """

  Scenario: Email confirmation when expiration date of confirmation token is invalid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles | confirmationToken                                                                                                                | expirationDate |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin | 6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708 | now            |
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/email_confirmation" with body:
    """
    {
      "token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 400
    And the response content should be:
    """
    {
      "code": 400,
      "message": "Confirmation token is expired"
    }
    """

  Scenario: Email confirmation when user doesn't have confirmation token generated
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles | confirmationToken | expirationDate |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |                   | now            |
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/email_confirmation" with body:
    """
    {
      "token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 404
    And the response content should be:
    """
    {
      "code": 404,
      "message": "Confirmation token not found"
    }
    """

  Scenario: Email confirmation when confirmation tokens are different
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles | confirmationToken                                                                                                                | expirationDate |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin | b715ac8bb7b455b16f4a9b79b23d338d0310f5c0633e9e34fc1a68a02f2a8025546e2c82d43aff060ba6a210f9e6e2e0508e2dcb6d985970682fc8b4cbb62b1e | +1 day         |
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/email_confirmation" with body:
    """
    {
      "token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 403
    And the response content should be:
    """
    {
      "code": 403,
      "message": "Confirmation token is incorrect"
    }
    """
