ProcessPageFieldSelectCreator
=============================

This module provides a way to rapidly generate Page fields and the required templates and pages for use as a drop down select (or any other Page field type).

To use, run Page Field Select Creator from the Setup Menu

1. Enter a Field Title, eg: Room Types
2. Choose the parent where the page tree of options will be created, eg a hidden "Categories" parent page
3. Select a "Deference in API as" option depending on your needs
4. Select Options - These will become the child pages that will populate the page field select options. There are two different options.

    Option 1. TITLE FIELD ONLY - enter one option per line, eg:

    Single<br />
    Double<br />
    Suite


    Option 2. MULTIPLE FIELDS - the first line is used for the field names and the first field must be 'Title'. Subsequent lines are the values for the fields, eg:

    Title, Number of Beds, Number of People, Kitchen Facilities<br />
    Single, 1, 1, Fridge Only<br />
    Double, 2, 2, Fridge Only<br />
    Suite, 3, 6, Full Kitchen

5. Choose the input field type
6. Check whether "Allow new pages to be created from field?" should be enabled.

As an example, if you entered "Room Types" as the field title, you would end up with:

* a fully configured page field called: room_types
* MULTIPLE FIELDS OPTION - 3 additional fields - number_of_beds, number_of_people, kitchen_facilities
* a parent template called: room_types
* a child template called: room_types_items (with either just a title field, or with the 3 additional fields as well)
* a parent page called: Room Types
* a series of child pages named and titled based on the per line entries in the Select Options textarea

The templates are configured such that the "room_types_items" child template can only have the main "room_types" template as a parent, and vice versa.

This module will let you create a full page field setup in literally a few seconds :)

Then all you have to do is add the newly created page field to any template you want and you're ready to go!


##How to install

Download and place the module folder named "ProcessPageFieldSelectCreator" in: /site/modules/

In the admin control panel, go to Modules. At the bottom of the screen, click the "Check for New Modules" button.

Now scroll to the ProcessPageFieldSelectCreator module and click "Install".


##Usage

Go to the Setup Page > Page Field Select Creator and follow the prompts.


##Support

http://processwire.com/talk/topic/4523-page-field-select-creator/