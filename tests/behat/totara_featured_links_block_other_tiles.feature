@block @totara @javascript @block_featured_links
  Feature: Tests adding the functionality of the other tiles packed with the block
    There are other tiles that come with the featured links block other than the static tile
      - test the content form
      - Test that it is displayed as expected

  Background:
    When I log in as "admin"
    And I follow "Dashboard"
    And I click on "Customise this page" "button"
    And I add the "Featured Links" block
    And I click on "Add Tile" "link"

    # This doesnt work due to the change event triggering twice
    # This causes the selenium driver to lose the elements and throw an error.

    Scenario: Course Tile content form
      #When I start watching to see if a new page loads
      #And I set the following Totara form fields to these values:
      #  | Tile Type | block_featured_links-course_tile |
      # | Tile Type | Course Tile |
      #Then a new page should have loaded since I started watching
      #When I set the following Totara form fields to these values:
      #  | Course Name | Not a Course |
      #And I click on "Save changes" "button"
      #Then I should see "Please enter the fullname of a course"

    Scenario: Course Tile content form
      #When I start watching to see if a new page loads
      #And I set the following Totara form fields to these values:
      #  | Tile Type | \block_featured_links\tile\gallery_tile |
      #  | Tile Type | Gallery Tile |
      #Then a new page should have loaded since I started watching
      #When I set the following Totara form fields to these values:
      #  | Interval (seconds) | 12 |
      #And I click on "Save changes" "button"




