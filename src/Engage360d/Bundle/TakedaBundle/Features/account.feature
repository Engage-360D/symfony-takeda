Feature: Account API
  # GET /account
  Scenario: Get current user information
    Given resource "/api/v1/account"
    And I am logined as "regular@example.com" with password "regular"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"

  # PUT /account
  Scenario: Update current user information
    Given resource "/api/v1/account"
    And I am logined as "regular@example.com" with password "regular"
    And request body from example "/api/v1/examples/users/put.json" matched by schema "/api/v1/schemas/users/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"
    And response data value under key "email" should be "vslinko@yahoo.com"

  Scenario: Return user information
    Given resource "/api/v1/account"
    And I am logined as "regular@example.com" with password "regular"
    And request body is:
      """
      {"data":{"email":"regular@example.com"}}
      """
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"
    And response data value under key "email" should be "regular@example.com"
