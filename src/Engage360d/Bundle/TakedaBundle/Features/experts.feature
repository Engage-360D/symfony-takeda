Feature: Expert API
  # POST /experts
  Scenario: Create new expert as anonymous user
    Given resource "/api/v1/experts"
    When I make "POST" request
    Then response code should be "401"

  Scenario: Create new expert as regular user
    Given resource "/api/v1/experts"
    And I am logined as "regular@example.com" with password "regular"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new expert as doctor
    Given resource "/api/v1/experts"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new expert as admin
    Given resource "/api/v1/experts"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/experts/post.json" matched by schema "/api/v1/schemas/experts/post.json"
    When I make "POST" request
    Then response code should be "201"
    And response body should match schema "/api/v1/schemas/experts/one.json"

  # PUT /experts/{id}
  Scenario: Edit expert as anonymous user
    Given resource "/api/v1/experts/2"
    When I make "PUT" request
    Then response code should be "401"

  Scenario: Edit expert as regular user
    Given resource "/api/v1/experts/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit expert as doctor
    Given resource "/api/v1/experts/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit expert as admin
    Given resource "/api/v1/experts/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/experts/put.json" matched by schema "/api/v1/schemas/experts/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/experts/one.json"

  # GET /experts
  Scenario: Fetch all experts as anonymous user
    Given resource "/api/v1/experts"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/experts/list.json"

  # GET /experts/{id}
  Scenario: Fetch one expert as anonymous user
    Given resource "/api/v1/experts/2"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/experts/one.json"

  # DELETE /experts/{id}
  Scenario: Delete expert as anonymous user
    Given resource "/api/v1/experts/2"
    When I make "DELETE" request
    Then response code should be "401"

  Scenario: Delete expert as regular user
    Given resource "/api/v1/experts/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete expert as doctor
    Given resource "/api/v1/experts/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete expert as admin
    Given resource "/api/v1/experts/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/experts/put.json" matched by schema "/api/v1/schemas/experts/put.json"
    When I make "DELETE" request
    Then response code should be "200"
