Feature: Regions API
  # GET /regions
  Scenario: Fetch regions list
    Given resource "/api/v1/regions"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/regions/list.json"
    And response data should be non empty array

  # GET /regions/{id}
  Scenario: Fetch region info
    Given resource "/api/v1/regions/1"
    When I make "GET" request
    Then response code should be "200"
    And response body should match schema "/api/v1/schemas/regions/one.json"
