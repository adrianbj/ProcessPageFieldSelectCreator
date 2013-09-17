ProcessPageFieldSelectCreator
=============================

This module provides a way to rapidly generate Page fields and the required templates and pages for use as a drop down select (or any other Page field type).

To use, run Page Field Select Creator from the Setup Menu

1. Enter a Field Title, eg: Room Types
2. Choose the parent where the page tree of options will be created, eg a hidden "Categories" parent page
3. Select a "Deference in API as" option depending on your needs
4. Select Options

    SIMPLE (title field only) - Enter required options for the select, one per line, eg:

    Single<br />
    Double<br />
    Suite

    ADVANCED (multiple fields) - Enter the field names on the first line and the values for each page on the subsequent lines, eg:

    Title, Number of Beds, Number of People, Kitchen Facilities
    Single, 1, 1, Fridge Only<br />
    Double, 2, 2, Fridge Only<br />
    Suite, 3, 6, Full Kitchen

5. Choose the input field type
6. Check whether "Allow new pages to be created from field?" should be enabled.

As an example, if you entered "Room Types" as the field title, you would end up with:

* a fully configured page field called: room_types
* ADVANCED OPTION - 3 additional fields - number_of_beds, number_of_people, kitchen
* a parent template called: room_types
* a child template called: room_types_items (with either just a title field, or with the 3 additional fields as well)
* a parent page called: Room Types
* a series of child pages named and titled based on the per line entries in the Select Options textarea

The templates are configured such that the "room_types_items" child template can only have the main "room_types" template as a parent, and vice versa.

This module will let you create a full page field setup in literally a few seconds :)

Then all you have to do is add the newly created page field to any template you want and you're ready to go!