@block @javascript @totara @block_totara_featured_links
Feature: Test the reordering tiles using drag and drop
  This is meant to test the drag and drop reordering however I coulnt seem to get this to work

  Background:
    When I log in as "admin"
    And I follow "Dashboard"
    And I click on "Customise this page" "button"
    And I add the "Featured Links" block

    #This doesnt work
  Scenario: Drag a tile to reorder it. doesnt work
    When I click on "Add Tile" "link"
    And I set the following fields to these values:
      | URL | www.example.com |
      | Description | tile1 |
    And I press "Save changes"
    And I click on "Add Tile" "link"
    And I set the following fields to these values:
      | URL | www.example.com |
      | Description | tile2 |
    And I press "Save changes"
    #And I drag "#block-totara-featured-links-tile-1" "css_element" and I drop it in "#block-totara-featured-links-tile-2" "css_element"
