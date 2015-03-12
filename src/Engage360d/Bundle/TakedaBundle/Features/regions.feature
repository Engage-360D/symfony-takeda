Feature: Regions API
  # POST /regions
  Scenario: Create new region as anonymous user
    Given resource "/api/v1/regions"
    When I make "POST" request
    Then response code should be "401"

  Scenario: Create new region as regular user
    Given resource "/api/v1/regions"
    And I am logined as "regular@example.com" with password "regular"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new region as doctor
    Given resource "/api/v1/regions"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new region as admin
    Given resource "/api/v1/regions"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/regions/post.json" matched by schema "/api/v1/schemas/regions/post.json"
    When I make "POST" request
    Then response code should be "201"
    And response body should match schema "/api/v1/schemas/regions/one.json"

  # PUT /regions/{id}
  Scenario: Edit region as anonymous user
    Given resource "/api/v1/regions/2"
    When I make "PUT" request
    Then response code should be "401"

  Scenario: Edit region as regular user
    Given resource "/api/v1/regions/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit region as doctor
    Given resource "/api/v1/regions/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit region as admin
    Given resource "/api/v1/regions/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/regions/put.json" matched by schema "/api/v1/schemas/regions/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/regions/one.json"

  # GET /regions
  Scenario: Fetch all regions as anonymous user
    Given resource "/api/v1/regions"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/regions/list.json"

  # GET /regions/{id}
  Scenario: Fetch one region as anonymous user
    Given resource "/api/v1/regions/2"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/regions/one.json"

  # DELETE /regions/{id}
  Scenario: Delete region as anonymous user
    Given resource "/api/v1/regions/2"
    When I make "DELETE" request
    Then response code should be "401"

  Scenario: Delete region as regular user
    Given resource "/api/v1/regions/2"
    And I am logined as "regular@example.com" with password "regular"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete region as doctor
    Given resource "/api/v1/regions/2"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete region as admin
    Given resource "/api/v1/regions/2"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/regions/put.json" matched by schema "/api/v1/schemas/regions/put.json"
    When I make "DELETE" request
    Then response code should be "200"

