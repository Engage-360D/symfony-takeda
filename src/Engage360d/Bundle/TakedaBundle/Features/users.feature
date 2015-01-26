Feature: User API
  # POST /users
  Scenario: Register new user
    Given resource "/api/v1/users"
    And request body from example "/api/v1/examples/users/post.json" matched by schema "/api/v1/schemas/users/post.json"
    When I make "POST" request
    Then response code should be "201"
    And response body should match schema "/api/v1/schemas/users/one.json"

  # GET /users
  Scenario: Get users list as anonymous
    Given resource "/api/v1/users"
    When I make "GET" request
    Then response code should be "401"

  Scenario: Get users list as regular user
    Given resource "/api/v1/users"
    And I am logined as "regular@example.com" with password "regular"
    When I make "GET" request
    Then response code should be "403"
    
  Scenario: Get users list as doctor
    Given resource "/api/v1/users"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "GET" request
    Then response code should be "403"

  Scenario: Get users list as admin
    Given resource "/api/v1/users"
    And I am logined as "admin@example.com" with password "admin"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/list.json"

  # GET /users/{id}
  Scenario: Get user info as anonymous
    Given resource "/api/v1/users/1"
    When I make "GET" request
    Then response code should be "401"

  Scenario: Get user info as regular user
    Given resource "/api/v1/users/1"
    And I am logined as "regular@example.com" with password "regular"
    When I make "GET" request
    Then response code should be "403"
    
  Scenario: Get user info as doctor
    Given resource "/api/v1/users/1"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "GET" request
    Then response code should be "403"

  Scenario: Get user info as admin
    Given resource "/api/v1/users/1"
    And I am logined as "admin@example.com" with password "admin"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"

  # PUT /users/{id}
  Scenario: Update user info as anonymous
    Given resource "/api/v1/users/4"
    When I make "PUT" request
    Then response code should be "401"

  Scenario: Update user info as regular user
    Given resource "/api/v1/users/4"
    And I am logined as "regular@example.com" with password "regular"
    When I make "PUT" request
    Then response code should be "403"
    
  Scenario: Update user info as doctor
    Given resource "/api/v1/users/4"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Update user info as admin
    Given resource "/api/v1/users/4"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/users/put.json" matched by schema "/api/v1/schemas/users/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/users/one.json"

  # DELETE /users/{id}
  Scenario: Delete user as anonymous
    Given resource "/api/v1/users/4"
    When I make "DELETE" request
    Then response code should be "401"

  Scenario: Delete user as regular user
    Given resource "/api/v1/users/4"
    And I am logined as "regular@example.com" with password "regular"
    When I make "DELETE" request
    Then response code should be "403"
    
  Scenario: Delete user as doctor
    Given resource "/api/v1/users/4"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete user as admin
    Given resource "/api/v1/users/4"
    And I am logined as "admin@example.com" with password "admin"
    When I make "DELETE" request
    Then response code should be "200"
