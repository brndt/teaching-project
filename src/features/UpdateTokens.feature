Feature: Update tokens

  Scenario: Updating tokens when all inputs are valid
    Given there are users with the following details:
      | id                                   | firstName | lastName    | email             | password | roles |
      | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | nikita    | grichinenko | nikita@lasalle.es | 123456Aq | admin |
    And there are refresh tokens with the following details:
      | refreshToken                                                                                                                     | userId                               | expirationDate |
      | 6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708 | 16bf6c6a-c855-4a36-a3dd-5b9f6d92c753 | +1 day         |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a POST request to "/api/v1/users/token_refresh" with body:
    """
    {
      "refresh_token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 200
