Feature: User authentication
  In order to access restricted areas of application
  As a user
  I need to be able to authenticate

  Background:
    Given users exist in application:
      | name  | surname | email           |
      | Arlen | Bales   | arlen@thesa.com |

  Scenario: Getting authentication token
    Given I provide form data:
      | email    | arlen@thesa.com |
      | password | pass            |
    When I request "getToken" with "POST" method
    Then the response status code should be "Successful"

  Scenario: Authentication failed for wrong password
    Given I provide form data:
      | email    | arlen@thesa.com |
      | password | wrong_password  |
    When I request "getToken" with "POST" method
    Then the response status code should be "Unauthorized"
    And the response should contain:
    """
    {
      "code": 401,
      "message": "Bad credentials"
    }
    """
