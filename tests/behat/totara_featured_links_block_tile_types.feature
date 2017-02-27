@block @javascript @totara @block_totara_featured_links
Feature: Test that the tile types other than the default tile work as expected
  Makes sure that the other tile types work correctly

  Background:
    When I log in as "admin"
    And I am on site homepage
    And I follow "Turn editing on"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"

  @_file_upload
  Scenario: Test the label top tile
    When I set the following Totara form fields to these values:
      | URL | www.example.com  |
      | textbody | default description |
      | Heading location | Top         |
    #And I upload "blocks/totara_featured_links/tests/fixtures/Desert.jpg" file to "Background Image" filemanager  # There doenst seem to be a way of testing fileuploads in a totara form
    #And I set the "background_img" Totara form field to "blocks/totara_featured_links/tests/fixtures/Desert.jpg"  # As neither of these work.
    And I click on "Save changes" "button"
    Then I should see "default description"
    And ".block-totara-featured-links-content-top" "css_element" should exist

  Scenario: Test the multi image tile
    When I set the following fields to these values:
      | Tile type | Gallery Tile |
    And I wait "1" seconds
    And I set the following fields to these values:
      | URL | www.example.com  |
      | textbody | default description |
    And I click on "Save changes" "button"
    Then I should see "default description"
    And ".block-totara-featured-links-gallery-images" "css_element" should exist

  Scenario: Test the course tile
#    Given I click on "Cancel" "button"
#    And I am on site homepage
#    And I create a course with:
#      | Course full name | Course 1 |
#      | Course short name | C1 |
#    When I am on site homepage
#    And I click on "Add Tile" "link"
#    And I set the following fields to these values:
#      | Tile Type | Course Tile |
#    And I wait "1" seconds
#    And I set the following fields to these values:
#      | Name of the course | Course 1 |
#    And I click on "Save changes" "button"
#    Then I should see "Course 1"
