@block @javascript @totara @block_featured_links
Feature: Check that the visibility options for the tiles work correctly
  The User should be able to hide the tile based on whether other users are in an audience or they match some
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
    And I navigate to "Audiences" node in "Site administration > Users > Accounts"
    And I follow "Add new audience"
    And I set the following fields to these values:
      | Name         | Test_cohort_name        |
      | Context      | System                  |
      | Audiences ID | 222                     |
      | Description  | Test cohort description |
    And I press "Save changes"
    And I add "Admin User (moodle@example.com)" user to "222" cohort members
    And I add "First User (first@example.com)" user to "222" cohort members
    And I add "Second User (second@example.com)" user to "222" cohort members
    And I navigate to "Audiences" node in "Site administration > Users > Accounts"
    And I follow "Add new audience"
    And I set the following fields to these values:
      | Name         | Test_cohort_name2        |
      | Context      | System                  |
      | Audiences ID | 333                     |
      | Description  | Test cohort description |
    And I press "Save changes"
    And I add "Third User (third@example.com)" user to "333" cohort members
    And I add "Second User (second@example.com)" user to "333" cohort members
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Featured Links" block
    And I click on "a[type=\"add\"]" "css_element"
    And I set the field "url" to "www.google.com"
    And I set the field "textbody" to "default description"
    And I click on "Save changes" "button"

  Scenario: Test javascript with custom visibility rules works
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    Then I should see "Edit Visibility"
    When I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by audience rules" Totara form field to "1"
    And I set the "Define access by preset rules" Totara form field to "1"
    Then I should see "Presets"
    And I should see "Preset rule aggregation"
    And I should see "Audience"
    And I should see "Audience rule aggregation"
    And I should see "Ruleset aggregation logic"
    And I should see "Ruleset aggregation"
    When I click on "Add audiences" "button"
    Then I should see "Test_cohort_name"
    When I follow "Test_cohort_name"
    And I click on "OK" "button"
    And I wait "1" seconds
    Then I should see "Test_cohort_name"
    When I click on "Save changes" "button"
    Then ".block-featured-links-tile" "css_element" should exist

  Scenario: Test that setting hidden from everyone hides the tile
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I click on "Hidden from all" "text"
    And I click on "Save changes" "button"
    Then ".block-featured-links-disabled" "css_element" should exist
    And I follow "Turn editing off"
    And ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist

  Scenario: Test that the tile can be hidden by audience
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by audience rules" Totara form field to "1"
    And I click on "Add audiences" "button"
    And I follow "Test_cohort_name"
    And I click on "OK" "button"
    And I click on "Save changes" "button"
    Then ".block-featured-links-tile" "css_element" should exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should exist
    When I log out
    And I log in as "user3"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist

  Scenario: Test that that all aggregation on audiences hides the tile from people who aren't in all the audiences
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by audience rules" Totara form field to "1"
    And I click on "Add audiences" "button"
    And I follow "Test_cohort_name"
    And I follow "Test_cohort_name2"
    And I click on "OK" "button"
    And I set the "Audience rule aggregation" Totara form field to "All of the audiences above"
    And I click on "Save changes" "button"
    And I log out
      # In both the audiences.
    And I log in as "user2"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should exist
    When I log out
      # In one audienece but not both.
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user3"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
     # Not in any of the audiences.
    And I log in as "user4"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist

  Scenario: Check the the is site administrator preset rule works
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by preset rules" Totara form field to "1"
    And I set the "Condition required to view" Totara form field to "User is site administrator"
    And I click on "Save changes" "button"
    And I follow "Turn editing off"
    Then ".block-featured-links-tile" "css_element" should exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user2"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist

  Scenario: Test the aggregation between the presets work correctly
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by preset rules" Totara form field to "1"
    And I set the "Condition required to view" Totara form field to "User is site administrator,User is not logged in"
    And I set the "Preset rule aggregation" Totara form field to "All of the selected preset rules above"
    And I click on "Save changes" "button"
    And I follow "Turn editing off"
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user2"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist

  Scenario: Tests the aggregation between the audiences and the presets work correctly
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    And I click on "a[type=\"edit_vis\"]" "css_element"
    And I set the "Access" Totara form field to "Apply rules"
    And I click on "Expand all" "text"
    And I set the "Define access by preset rules" Totara form field to "1"
    And I set the "Condition required to view" Totara form field to "User is site administrator"
    And I set the "Define access by audience rules" Totara form field to "1"
    And I click on "Add audiences" "button"
    And I follow "Test_cohort_name"
    And I click on "OK" "button"
    And I set the "Ruleset aggregation" Totara form field to "Users matching all of the criteria above can view this feature link"
    And I click on "Save changes" "button"
    Then ".block-featured-links-tile" "css_element" should exist
    When I log out
    And I log in as "user1"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user2"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user3"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist
    When I log out
    And I log in as "user4"
    And I am on site homepage
    Then ".block-featured-links-tile" "css_element" should not exist