Feature: Follow user
  In order to follow other users
  As a user
  I need to be able to see all available users and follow them

  Background:
    Given users exist in application:
      | name    | surname | email            |
      | Arlen   | Bales   | arlen@thesa.com  |
      | Renna   | Bales   | renna@thesa.com  |
      | Rojer   | Inn     | rojer@thesa.com  |
      | Leesha  | Paper   | leesha@thesa.com |
      | Ahmann  | Jardir  | ahmann@thesa.com |
      | Inevera | Damajah | inevra@thesa.com |

  Scenario: List all available users
    Given I want to get the list of "users"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Successful"
    And the response should contain:
    """
      {
        "data": [
          {
            "id": 1,
            "name": "Arlen",
            "surname": "Bales",
            "email": "arlen@thesa.com"
          },
          {
            "id": 2,
            "name": "Renna",
            "surname": "Bales",
            "email": "renna@thesa.com"
          },
          {
            "id": 3,
            "name": "Rojer",
            "surname": "Inn",
            "email": "rojer@thesa.com"
          },
          {
            "id": 4,
            "name": "Leesha",
            "surname": "Paper",
            "email": "leesha@thesa.com"
          },
          {
            "id": 5,
            "name": "Ahmann",
            "surname": "Jardir",
            "email": "ahmann@thesa.com"
          },
          {
            "id": 6,
            "name": "Inevera",
            "surname": "Damajah",
            "email": "inevra@thesa.com"
          }
        ]
      }
    """

  Scenario: Filter users by name
    Given I want to get the list of "users"
    And I set "name" filter to "Arlen"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Successful"
    And the response should contain:
    """
      {
        "data": [
          {
            "id": 1,
            "name": "Arlen",
            "surname": "Bales",
            "email": "arlen@thesa.com"
          }
        ]
      }
    """

  Scenario: Filter users by surname
    Given I want to get the list of "users"
    And I set "surname" filter to "Bales"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Successful"
    And the response should contain:
    """
      {
        "data": [
          {
            "id": 1,
            "name": "Arlen",
            "surname": "Bales",
            "email": "arlen@thesa.com"
          },
          {
            "id": 2,
            "name": "Renna",
            "surname": "Bales",
            "email": "renna@thesa.com"
          }
        ]
      }
    """

  Scenario: Unable for user to follow himself
    Given I want to follow the user with id "1"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Bad Request"
    And the response should contain:
    """
    {"error":"User can't follow himself"}
    """

  Scenario: Unable to follow the user twice
    Given User "arlen@thesa.com" is following user "renna@thesa.com"
    And I want to follow the user with id "2"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Bad Request"
    And the response should contain:
    """
    {"error":"User renna@thesa.com is already followed by arlen@thesa.com"}
    """

  Scenario: Follow user
    Given I want to follow the user with id "2"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "No Content"

