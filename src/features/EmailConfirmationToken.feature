Feature: Email confirmation token

  Scenario: Confirm email when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles | confirmationToken                                                                                                                | expirationDate |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin | 6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708 | +1 day         |
    When I send a POST request to "/api/v1/users/16bf6c6a-c855-4a36-a3dd-5b9f6d92c753/email_confirmation" with body:
    """
    {
      "token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response content should be:
    """
    {
      "message": "Your account has been successfully enabled"
    }
    """
    And the response status code should be 200
