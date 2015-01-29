Feature: Account API
  # GET /account
  Scenario: Get current user information
    Given resource "/api/v1/account"
    And I am logined as "regular@example.com" with password "regular"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"
