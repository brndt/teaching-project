Feature: Sign out user

  Scenario: Sign out user when all inputs are valid
    Given there are Users with the following details:
      | firstName | lastName    | email             | password |
      | nikita    | grichinenko | nikita@lasalle.es | 123456Aq |
    And there are RefreshTokens with the following details:
      | refreshToken |
      | 6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708 |
    And I am authenticated as "nikita@lasalle.es" with "123456Aq" password
    When I send a DELETE request to "/api/v1/users/sign_out" with body:
     """
    {
      "refresh_token": "6e4965317dd91ed1edee4af180b03775c84177c4ac82b63fe4e0c8e418e5832458036a89aa9f7c620ca51dc9f8606abc499ce018075662e4b4f4ad44ed6ed708"
    }
    """
    Then the response status code should be 200
    And the response content should be:
     """
    {
      "message": "You have successfully signed out"
    }
    """
