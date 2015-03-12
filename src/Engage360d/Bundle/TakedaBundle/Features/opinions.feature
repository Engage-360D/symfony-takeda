Feature: Opinion API
  # POST /opinions
  Scenario: Create new opinion as anonymous user
    Given resource "/api/v1/opinions"
    When I make "POST" request
    Then response code should be "401"

  Scenario: Create new opinion as regular user
    Given resource "/api/v1/opinions"
    And I am logined as "regular@example.com" with password "regular"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new opinion as doctor
    Given resource "/api/v1/opinions"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new opinion as admin
    Given resource "/api/v1/opinions"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/opinions/post.json" matched by schema "/api/v1/schemas/opinions/post.json"
    When I make "POST" request
    Then response code should be "201"
    And response body should match schema "/api/v1/schemas/opinions/one.json"

  # PUT /opinions/{id}
  Scenario: Edit opinion as anonymous user
    Given resource "/api/v1/opinions/2"
    When I make "PUT" request
    Then response code should be "401"

  Scenario: Edit opinion as regular user
    Given resource "/api/v1/opinions/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit opinion as doctor
    Given resource "/api/v1/opinions/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit opinion as admin
    Given resource "/api/v1/opinions/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/opinions/put.json" matched by schema "/api/v1/schemas/opinions/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/opinions/one.json"

  # GET /opinions
  Scenario: Fetch all opinions as anonymous user
    Given resource "/api/v1/opinions"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/opinions/list.json"

  # GET /opinions/{id}
  Scenario: Fetch one opinion as anonymous user
    Given resource "/api/v1/opinions/2"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/opinions/one.json"

  # DELETE /opinions/{id}
  Scenario: Delete opinion as anonymous user
    Given resource "/api/v1/opinions/2"
    When I make "DELETE" request
    Then response code should be "401"

  Scenario: Delete opinion as regular user
    Given resource "/api/v1/opinions/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete opinion as doctor
    Given resource "/api/v1/opinions/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete opinion as admin
    Given resource "/api/v1/opinions/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/opinions/put.json" matched by schema "/api/v1/schemas/opinions/put.json"
    When I make "DELETE" request
    Then response code should be "200"
