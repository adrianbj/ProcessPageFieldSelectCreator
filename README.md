ProcessPageFieldSelectCreator
=============================

Automated creation of Page fields for Select drop downs, along with the templates and page tree for their source.

To use, run Page Field Select Creator from the Setup Menu
Enter a Field Title
1. Choose the parent where the page tree of options will be created, eg a hidden "Categories" parent page
2. Select the "Deference in API as" option
3. Enter all the required options for the select, one per line, eg: single, double, penthouse, honeymoon, suite
4. Choose the input field type
5. Check whether "Allow new pages to be created from field?" should be enabled.

As an example, if you entered "Room Types" as the field title, you would end up with:
* a field called: room_types
*a parent template called: room_types
* a child template called: room_types_items
* a parent page called: Room Types
* a series of child pages named based on the per line entries in the Select Options textarea

The templates are configured such that the "room_types_items" child template can only have the main "room_types" template as a parent, and vice versa.

This module will let you create a full page field setup in literally a few seconds :)
