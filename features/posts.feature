Feature: Users posts
  In order to manage posts
  As a user
  I need to be able to create, delete, update and list posts

  Background:
    Given users exist in application:
      | name  | surname | email           |
      | Arlen | Bales   | arlen@thesa.com |

  Scenario: List all available posts
    Given I want to get the list of "posts"
    And posts exist in application:
      | title                | content                                           | user            | createdAt  |
      | Hello friends!       | Let's start an amazing adventure!                 | arlen@thesa.com | 2018-01-12 |
      | Hello again friends! | Let's start an amazing adventure again!           | arlen@thesa.com | 2018-01-12 |
      | Hello again friends! | Let's start an amazing adventure again and again! | arlen@thesa.com | 2018-01-12 |
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Successful"
    And the response should contain:
    """
      {
        "data": [
          {
            "id": 1,
            "title": "Hello friends!",
            "content": "Let's start an amazing adventure!",
            "created_at":"2018-01-12T00:00:00+01:00"
          },
          {
            "id": 2,
            "title": "Hello again friends!",
            "content": "Let's start an amazing adventure again!",
            "created_at":"2018-01-12T00:00:00+01:00"
          },
          {
            "id": 3,
            "title": "Hello again friends!",
            "content": "Let's start an amazing adventure again and again!",
            "created_at":"2018-01-12T00:00:00+01:00"
          }
        ]
      }
    """

  Scenario: Unable to create post as unauthorized user
    Given I want to create a "post"
    When I request resource
    Then the response status code should be "Unauthorized"

  Scenario: Create post
    Given I want to create a "post"
    And I provide data:
    """
      {
        "title": "Hello friends!",
        "content": "Let's start an amazing adventure!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Created"
    And the "Location" response header should be set to "/api/v1/posts/1"

  Scenario: Unable to create post for invalid data
    Given I want to create a "post"
    And I provide data:
    """
      {
        "title": "",
        "content": "Let's start an amazing adventure!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Bad Request"
    And the response should contain:
    """
    {"title":["not_blank"]}
    """

  Scenario: Unable to update post as unauthorized user
    Given I want to update a "post" with id "1"
    When I request resource
    Then the response status code should be "Unauthorized"

  Scenario: Update own post
    Given posts exist in application:
      | title          | content                           | user            |
      | Hello friends! | Let's start an amazing adventure! | arlen@thesa.com |
    Given I want to update a "post" with id "1"
    And I provide data:
    """
      {
        "title": "Hello again",
        "content": "This adventure was not that great!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "No Content"

  Scenario: Unable to update post with invalid data
    Given posts exist in application:
      | title          | content                           | user            |
      | Hello friends! | Let's start an amazing adventure! | arlen@thesa.com |
    Given I want to update a "post" with id "1"
    And I provide data:
    """
      {
        "title": "",
        "content": "This adventure was not that great!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Bad Request"
    And the response should contain:
    """
    {"title":["not_blank"]}
    """

  Scenario: Unable to update non-existing post
    Given users exist in application:
      | name  | surname | email           |
      | Renna | Bales   | renna@thesa.com |
    Given I want to update a "post" with id "1"
    And I provide data:
    """
      {
        "title": "Hello again",
        "content": "This adventure was not that great!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Not Found"

  Scenario: Forbidden to update posts of other users
    Given users exist in application:
      | name  | surname | email           |
      | Renna | Bales   | renna@thesa.com |
    And posts exist in application:
      | title          | content                           | user            |
      | Hello friends! | Let's start an amazing adventure! | renna@thesa.com |
    And I want to update a "post" with id "1"
    And I provide data:
    """
      {
        "title": "Hello again",
        "content": "This adventure was not that great!"
      }
    """
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Forbidden"

  Scenario: Delete own post
    Given I want to delete a "post" with id "1"
    And posts exist in application:
      | title          | content                           | user            |
      | Hello friends! | Let's start an amazing adventure! | arlen@thesa.com |
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "No Content"

  Scenario: Unable to delete non-existing post
    And I want to delete a "post" with id "1"
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Not Found"

  Scenario: Forbidden to delete posts of other users
    Given users exist in application:
      | name  | surname | email           |
      | Renna | Bales   | renna@thesa.com |
    And I want to delete a "post" with id "1"
    And posts exist in application:
      | title          | content                           | user            |
      | Hello friends! | Let's start an amazing adventure! | renna@thesa.com |
    And I am logged in as a user "arlen@thesa.com"
    When I request resource
    Then the response status code should be "Forbidden"
