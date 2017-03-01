@block @totara @javascript @block_featured_links
Feature: Block edit test block
  In order to use the block
  The user must be able
    - to edit the content of the tiles
    - edit the config of the block

  Background:
    When I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"
    And I set the following fields to these values:
     | URL | www.example.com |
     | textbody | default description |
    And I click on "Save changes" "button"

  Scenario: Check that the tile can be created and that it contains the initial value
    Then ".block_featured_links" "css_element" should exist
    And I should see "default description"

  Scenario Outline: Editing the for actually changes the values in the tile
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    When I click on "Content" "link"
    And I set the following fields to these values:
      | Title       | <heading>  |
      | Description | <body>     |
      | URL         | <link>     |
    And I press "Save changes"
    Then I should see "<heading>"
    And I should see "<body>"
    And I should not see "default description"
    # Needs to be like this as the link could either have the heading or the body string in it
    When I click on ".block-featured-links-layout > div > a" "css_element"
    Then I should not see "totara"

    Examples:
      | heading | link | body |
      | | http://www.example.com | textbody |
      | Some Heading | http://www.example.com | some body |
      | heading  | http://www.example.com | |

  Scenario: Can the admin get to the edit form and cancel without effecting anything
    When I click on "div.block-featured-links-edit div.moodle-actionmenu" "css_element"
    When I click on "Content" "link"
    And I set the following fields to these values:
      | Title | Some Heading |
      | textbody | some body |
      | URL      | http://www.example.com |
    And I press "Cancel"
    And I am on site homepage
    Then I should see "default description"
    And I should not see "Some Heading"
    And I should not see "some body"
