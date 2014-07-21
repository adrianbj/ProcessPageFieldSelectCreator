ProcessPageFieldSelectCreator
=============================

This module provides a way to rapidly generate Page fields and the required templates and pages for use as a drop down select (or any other Page field type).

This module will let you create a full page field setup in literally a few seconds :)

To use, run Page Field Select Creator from the Setup Menu

1. Enter a Field Title, eg: Room Types
2. Select Options - These will become the child pages that will populate the page field select options. There are two different options.

    Option 1. TITLE FIELD ONLY - enter one option per line, eg:

    Single<br />
    Double<br />
    Suite


    Option 2. MULTIPLE FIELDS - the first line is used for the field names and the first field must be 'Title'. Subsequent lines are the values for the fields, eg:

    Title, Number of Beds, Number of People, Kitchen Facilities<br />
    Single, 1, 1, Fridge Only<br />
    Double, 2, 2, Fridge Only<br />
    Suite, 3, 6, Full Kitchen

3. Choose the parent where the page tree of options will be created, eg a hidden "Options" parent page
4. Select a "Deference in API as" option depending on your needs
5. Choose the input field type
6. Check whether "Allow new pages to be created from field?" should be enabled.

As an example, if you entered "Room Types" as the field title, you would end up with all of the following automatically created:

* a fully configured page field called: room_types
* MULTIPLE FIELDS OPTION - 3 additional fields - number_of_beds, number_of_people, kitchen_facilities
* a parent template called: room_types
* a child template called: room_types_items (with either just a title field, or with the 3 additional fields as well)
* a parent page called: Room Types
* a series of child pages named and titled based on the per line entries in the Select Options textarea

The templates are configured such that the "room_types_items" child template can only have the main "room_types" template as a parent, and vice versa.

Then all you have to do is add the newly created page field to any template you want and you're ready to go!


##How to install

Download and place the module folder named "ProcessPageFieldSelectCreator" in: /site/modules/

In the admin control panel, go to Modules. At the bottom of the screen, click the "Check for New Modules" button.

Now scroll to the ProcessPageFieldSelectCreator module and click "Install".

Optional configuration option to set the suffix that you want to add to the end of the template name for the child pages. The default 'Items' will be converted to '_items' for the name.


##Usage

Go to the Setup Page > Page Field Select Creator and follow the prompts.


##Support

http://processwire.com/talk/topic/4523-page-field-select-creator/


## License

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

(See included LICENSE file for full license text.)