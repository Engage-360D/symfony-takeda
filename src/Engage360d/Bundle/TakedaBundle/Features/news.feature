Feature: News API
  # POST /news
  Scenario: Create new news article as anonymous user
    Given resource "/api/v1/news"
    When I make "POST" request
    Then response code should be "401"

  Scenario: Create new news article as regular user
    Given resource "/api/v1/news"
    And I am logined as "regular@example.com" with password "regular"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new news article as doctor
    Given resource "/api/v1/news"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "POST" request
    Then response code should be "403"

  Scenario: Create new news article as admin
    Given resource "/api/v1/news"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/news/post.json" matched by schema "/api/v1/schemas/news/post.json"
    When I make "POST" request
    Then response code should be "201"
    And response body should match schema "/api/v1/schemas/news/one.json"

  # PUT /news/{id}
  Scenario: Edit news article as anonymous user
    Given resource "/api/v1/news/1"
    When I make "PUT" request
    Then response code should be "401"

  Scenario: Edit news article as regular user
    Given resource "/api/v1/news/1"
    And I am logined as "regular@example.com" with password "regular"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit news article as doctor
    Given resource "/api/v1/news/1"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "PUT" request
    Then response code should be "403"

  Scenario: Edit news article as admin
    Given resource "/api/v1/news/1"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/news/put.json" matched by schema "/api/v1/schemas/news/put.json"
    When I make "PUT" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/news/one.json"

  # GET /news
  Scenario: Fetch all news as anonymous user
    Given resource "/api/v1/news"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/news/list.json"

  # GET /news/{id}
  Scenario: Fetch one news article as anonymous user
    Given resource "/api/v1/news/1"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/news/one.json"

  # DELETE /news/{id}
  Scenario: Delete news article as anonymous user
    Given resource "/api/v1/news/1"
    When I make "DELETE" request
    Then response code should be "401"

  Scenario: Delete news article as regular user
    Given resource "/api/v1/news/1"
    And I am logined as "regular@example.com" with password "regular"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete news article as doctor
    Given resource "/api/v1/news/1"
    And I am logined as "doctor@example.com" with password "doctor"
    When I make "DELETE" request
    Then response code should be "403"

  Scenario: Delete news article as admin
    Given resource "/api/v1/news/1"
    And I am logined as "admin@example.com" with password "admin"
    And request body from example "/api/v1/examples/news/put.json" matched by schema "/api/v1/schemas/news/put.json"
    When I make "DELETE" request
    Then response code should be "200"
