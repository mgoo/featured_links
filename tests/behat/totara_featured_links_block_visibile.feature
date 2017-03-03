@block @javascript @block_featured_links
Feature: Check that the visibility options for the tiles work correctly
  preset rules.
  The User should be able to set aggregation options for all of these so they can make sure they can hide the tile from
  the people they want while showing it to the people who need to see it.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | user1    | First     | User     | first@example.com  | T1       |
      | user2    | Second    | User     | second@example.com | T2       |
      | user3    | Third     | User     | third@example.com  | T3       |
      | user4    | Forth     | User     | forth@example.com  | T4       |
    And I log in as "admin"
    And I click on "nav > a:first-child" "css_element"
    And I click on "Customise this page" "button"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"
    And I set the field "url" to "www.example.com"
    And I set the field "textbody" to "default description"
    And I click on "Save changes" "button"

  Scenario: Test javascript with custom visibility rules works
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "Visibility" "link"
    Then I should see "Edit Visibility"
    When I set the field "Apply rules" to "1"
    And I click on "Expand all" "text"
    And I set the field "Define access by preset rules" to "1"
    Then I should see "Presets"
    And I should see "Preset rule aggregation"
    And I should see "Ruleset aggregation logic"
    And I should see "Ruleset aggregation"
    When I set the field "User is logged in" to "1"
    And I click on "Save changes" "button"
    Then "default description" "link" should exist

  Scenario: Test that setting hidden from everyone hides the tile
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "Visibility" "link"
    And I click on "Hidden from all" "text"
    And I click on "Save changes" "button"
    Then ".block-featured-links-disabled" "css_element" should exist
    And I click on "Stop customising this page" "button"
    And "default description" "link" should not exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then "default description" "link" should not exist

  Scenario: Check the the is site administrator preset rule works
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "Visibility" "link"
    And I set the field "Apply rules" to "1"
    And I click on "Expand all" "text"
    And I set the field "Define access by preset rules" to "1"
    And I set the field "User is site administrator" to "1"
    And I click on "Save changes" "button"
    And I click on "Stop customising this page" "button"
    Then "default description" "link" should exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then "default description" "link" should not exist
    When I log out
    And I log in as "user2"
    And I am on site homepage
    Then "default description" "link" should not exist

  Scenario: Test the aggregation between the presets work correctly
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "Visibility" "link"
    And I set the field "Apply rules" to "1"
    And I click on "Expand all" "text"
    And I set the field "Define access by preset rules" to "1"
    And I set the field "User is site administrator" to "1"
    And I set the field "User is not logged in" to "1"
    And I set the field "All of the selected preset rules above" to "1"
    And I click on "Save changes" "button"
    And I click on "Stop customising this page" "button"
    Then "default description" "link" should not exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then "default description" "link" should not exist
    When I log out
    And I log in as "user2"
    And I am on site homepage
    Then "default description" "link" should not exist