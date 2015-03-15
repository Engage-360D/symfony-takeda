Feature: Records API
  # GET /records
  Scenario: Fetch all records as anonymous user
  Given resource "/api/v1/records"
  When I make "GET" request
  Then response code should be "200"
  And response body should match schema "/api/v1/schemas/records/list.json"

    # GET /records/{id}
  Scenario: Fetch one record as anonymous user
  Given resource "/api/v1/records/1"
  When I make "GET" request
  Then response code should be "200"
  And response body should match schema "/api/v1/schemas/records/one.json"
