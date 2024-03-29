<?php

/**
 * ProcessWire Page Field Select Creator
 * by Adrian Jones
 *
 * Allows automated creation of Page fields, along with the templates and page tree for their source.
 *
 * Copyright (C) 2020 by Adrian Jones
 * Licensed under GNU/GPL v2, see LICENSE.TXT
 *
 */

class ProcessPageFieldSelectCreator extends Process implements Module {

    public static function getModuleInfo() {
        return array(
            'title' => __('Page Field Select Creator'),
            'version' => '0.5.11',
            'summary' => __('Automated creation of Page fields, along with the templates and page tree for their source.'),
            'author' => 'Adrian Jones',
            'singular' => true,
            'autoload' => false,
            'icon'     => 'bars',
            'page' => array(
                'name' => 'page-field-select-creator',
                'parent' => "setup",
                'title' => "Page Field Select Creator"
            )
        );
    }


    public function init() {
        parent::init();
        $this->wire('config')->scripts->add($this->wire('config')->urls->{$this->className} . 'pluralize.js');
    }


    /**
     * Executed when root url for module is accessed
     *
     */
    public function ___execute() {

        $form = $this->buildForm1();
        if($this->wire('input')->post->submit) {
            return $this->processForm1($form);
        }
        else {
            return $form->render();
        }

    }


    /**
     * Build the form
     *
     */
    protected function buildForm1() {

        $form = $this->wire('modules')->get("InputfieldForm");
        $form->method = 'post';

        $fieldset = $this->wire('modules')->get("InputfieldFieldset");
        $fieldset->label = __('Field settings', __FILE__);
        $form->add($fieldset);

        $f = $this->wire('modules')->get("InputfieldText");
        $f->name = 'fieldLabel';
        $f->label = __('Field label', __FILE__);
        $f->required = true;
        $f->requiredAttr = true;
        $f->description = __('The label of the page field to be created. eg. Room Types', __FILE__);
        $f->notes = __('Use capitals, spaces etc as this is also used for the parent page name and various labels.', __FILE__);
        $fieldset->add($f);

        $f = $this->wire('modules')->get('InputfieldCheckbox');
        $f->name = 'noFieldCreation';
        $f->label = __('Don\'t actually create the page field?', __FILE__);
        $f->description = __('This prevents the actual page field from being created. However, the parent and child pages and templates will still be created. This is a little counter to the original purpose for this module but can still be useful in some scenarios as a quick way of setting up a page tree and the various connected templates and settings. The Field Label (above) is still used to automatically populate the page parent and template labels / names, so it is still required.', __FILE__);
        $f->notes = __('Unless you know what you are doing, don\'t use this :)', __FILE__);
        $f->collapsed = Inputfield::collapsedBlank;
        $f->attr('checked', $this->wire('session')->noFieldCreation == '1' ? 'checked' : '');
        if($this->wire('session')->noFieldCreation) $f->attr('value', $this->wire('session')->noFieldCreation);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldText");
        $f->name = 'fieldDescription';
        $f->label = __('Field description');
        $f->description = __('The description for the page field to be created.', __FILE__);
        $f->showIf="noFieldCreation!='1'";
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldRadios");
        $f->name = 'derefAsPage';
        $f->label = __('Page field value type', __FILE__);
        $f->description = __('If you want your field to support selection of multiple items (pages), then you should select the first option (PageArray). If your field only needs to support selection of a single item (page), then select one of the single Page options (if you are not sure which, select the last option).', __FILE__);
        $f->showIf="noFieldCreation!='1'";
        $f->required = true;
        $f->requiredAttr = true;
        $f->addOption('0', sprintf(__('Multiple pages%s(PageArray)', __FILE__), '&nbsp;'));
        $f->addOption('1', sprintf(__('Single page%s(Page) or boolean false when none selected', __FILE__), '&nbsp;'));
        $f->addOption('2', sprintf(__('Single page%s(Page) or empty page%s(NullPage) when none selected', __FILE__), '&nbsp;', '&nbsp;'));
        $this->wire('session')->derefAsPage ? $f->attr('value', $this->wire('session')->derefAsPage) : $f->attr('value', 0);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldSelect");
        $f->name = 'inputfield';
        $f->label = __('Input field type', __FILE__);
        $f->description = __('The type of field that will be used to select a page. Select one that is consistent with the single page vs. multi-page you chose in the "Page field value type" field.', __FILE__);
        $f->showIf="noFieldCreation!='1'";
        $f->required = true;
        $f->requiredAttr = true;

        $singles = array();
        $multiples = array();
        $sortables = array();
        $pageListTypes = array();

        foreach($this->wire('modules')->get("InputfieldPage")->data['inputfieldClasses'] as $class) {
            $module = $this->modules->getModule($class, array('noInit' => true));
            $info = $this->modules->getModuleInfo($module);
            $label = ucfirst($info['title']);
            if($module instanceof InputfieldPageListSelection) {
                $pageListTypes[] = $class;
            }
            if($module instanceof InputfieldHasSortableValue) {
                $sortables[$class] = $label;
            } else if($module instanceof InputfieldHasArrayValue) {
                $multiples[$class] = $label;
            } else {
                $singles[$class] = $label;
            }
            if($class == 'InputfieldPageAutocomplete') $singles["_$class"] = $label;
        }

        $multiLabel = $this->_('Multiple page selection');
        $f->addOption($this->_('Single page selection'), $singles);
        $f->addOption($multiLabel, $multiples);
        $f->addOption($multiLabel . ' (' . $this->_('sortable') . ')', $sortables);

        $this->wire('session')->inputfield ? $f->attr('value', $this->wire('session')->inputfield) : $f->attr('value', 'InputfieldAsmSelect');
        $fieldset->add($f);

        $fieldset = $this->wire('modules')->get("InputfieldFieldset");
        $fieldset->label = __('Pages settings', __FILE__);
        $form->add($fieldset);

        $f = $this->wire('modules')->get("InputfieldPageListSelect");
        $f->name = 'grandParent';
        $f->label = __('Grandparent page', __FILE__);
        $f->required = true;
        $f->requiredAttr = true;
        $f->description = __('Select the grandparent where the parent of the selectable pages will be created. For example you may want to choose a dedicated "Options" grandparent page.', __FILE__);
        if($this->wire('session')->grandParent) $f->attr('value', $this->wire('session')->grandParent);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldText");
        $f->name = 'parentPageTitle';
        $f->label = __('Parent page title', __FILE__);
        $f->description = __('The title for the parent page to be created', __FILE__);
        $f->notes = __('You can leave as is (generated from the Field Label entered above or change if you wish. Use capitals, spaces etc. The page name will be converted from the entered title.', __FILE__);
        $f->required = true;
        $f->requiredAttr = true;
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldTextarea");
        $f->name = 'childPages';
        $f->label = sprintf(__('Child pages%s(select options)', __FILE__), '&nbsp;');
        $f->description = __('These will become the child pages that will populate the page field select options. There are two different options.', __FILE__) . "\n\n" .
            __('Option 1. TITLE FIELD ONLY - enter one option per line, eg:', __FILE__) . "\n\n" .
            __('Single', __FILE__) . "\n" .
            __('Double', __FILE__) . "\n" .
            __('Suite', __FILE__) . "\n\n" .
            __('Option 2a. MULTIPLE FIELDS - the first line is used for the field names and the first field must be "Title". Subsequent lines are the values for the fields, eg:', __FILE__) . "\n\n" .
            __('Title, Number of Beds, Number of People, Kitchen Facilities', __FILE__) . "\n" .
            __('Single, 1, 1, Fridge Only', __FILE__) . "\n" .
            __('Double, 2, 2, Fridge Only', __FILE__) . "\n" .
            __('Suite, 3, 6, Full Kitchen', __FILE__) . "\n\n" .
            __('Option 2b. MULTIPLE FIELDS WITH SPECIFIED FIELD TYPES - same as 2a, except that you can specify the field types (Integer, Text, Textarea, TextLanguage, TextareaLanguage, Datetime, Email, Password), eg:', __FILE__) . "\n\n" .
            __('Title, Number of Beds>Integer, Number of People>Integer, Kitchen Facilities>Text', __FILE__) . "\n" .
            __('Single, 1, 1, Fridge Only', __FILE__) . "\n" .
            __('Double, 2, 2, Fridge Only', __FILE__) . "\n" .
            __('Suite, 3, 6, Full Kitchen', __FILE__);
        $f->notes = __('For Option 2a or 2b, "Title" must be the first option on the first line.', __FILE__) . "\n" .
            __('You can use CSV values with commas etc so long as you use double quote enclosures, eg: "Bolivia, Plurinational State of", BO, "BOLIVIA, PLURINATIONAL STATE OF", BOL, 68', __FILE__) . "\n" .
            __('For the multiple fields option, you can specify labels for new fields and they will be created automatically. Use capitals, spaces etc for new field names as these will be used for the field label and converted for the name.', __FILE__) . "\n" .
            __('Or you can choose from the following existing text fields:', __FILE__) . 'title, ' . implode(", ", $this->wire('fields')->find("type=FieldtypeText|FieldtypeInteger|FieldtypeTextarea|FieldtypeTextLanguage|FieldtypeTextareaLanguage|FieldtypeDatetime|FieldtypeEmail|FieldtypePassword")->getArray());
        $fieldset->add($f);

        $fieldset = $this->wire('modules')->get("InputfieldFieldset");
        $fieldset->label = __('Template settings', __FILE__);
        $form->add($fieldset);

        $f = $this->wire('modules')->get("InputfieldText");
        $f->name = 'parentTemplate';
        $f->label = __('Parent template label / name', __FILE__);
        $f->description = __('The label for the parent template to be created', __FILE__);
        $f->notes = __('You can leave as is (generated from the Field Label entered above) or change if you wish. Use capitals, spaces etc. The template name will be converted from the entered label.', __FILE__) . "\n\n" .
            __('Or you can choose from the following existing templates:', __FILE__) . "\n" . implode(", ", $this->wire('templates')->find("flags=0")->getArray());
        $f->required = true;
        $f->requiredAttr = true;
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldText");
        $f->name = 'childTemplate';
        $f->label = __('Child template label / name', __FILE__);
        $f->description = __('The label for the child template to be created', __FILE__);
        $f->notes = __('You can leave as is (generated from the Field Label entered above) or change if you wish. Use capitals, spaces etc. The template name will be converted from the entered label.', __FILE__) . "\n\n" .
            __('Or you can choose from the following existing templates:', __FILE__) . "\n" . implode(", ", $this->wire('templates')->find("flags=0")->getArray());
        $f->required = true;
        $f->requiredAttr = true;
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldRadios");
        $f->name = 'templateSpaceCharacter';
        $f->label = __('Template space character', __FILE__);
        $f->required = true;
        $f->description = __('The space replacement character used when creating the template name. eg. the underscore in "room_types" when the template label is "Room Types"', __FILE__);
        $f->addOption('_');
        $f->addOption('-');
        $f->defaultValue('_');
        $fieldset->add($f);

        $f = $this->wire('modules')->get('InputfieldCheckbox');
        $f->name = 'noChangeTemplate';
        $f->label = __('Don\'t allow pages to change their template?', __FILE__);
        $f->description = __('When checked, pages using these parent and child templates will be unable to be changed to another template.', __FILE__);
        $f->attr('checked', $this->wire('session')->noChangeTemplate == '0' ? '' : 'checked');
        if($this->wire('session')->noChangeTemplate) $f->attr('value', $this->wire('session')->noChangeTemplate);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldRadios");
        $f->name = 'noParents';
        $f->label = __('Can the parent template be used for new pages?', __FILE__);
        $f->notes = __('Note that "No" does not prevent the initial parent page being created.', __FILE__);
        $f->required = true;
        $f->requiredAttr = true;
        $f->addOption(0, __('Yes', __FILE__));
        $f->addOption(1, __('No', __FILE__));
        $f->addOption(-1, __('One', __FILE__));
        $this->wire('session')->noParents ? $f->attr('value', $this->wire('session')->noParents) : $f->attr('value', 1);
        $f->optionColumns = 1;
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldCheckbox");
        $f->name = 'addable';
        $f->label = __('Allow new pages to be created from field?', __FILE__);
        $f->description = __('If checked, an option to add new pages will also be present if the editing user has access to create/publish these pages.', __FILE__);
        $f->notes = __('All other requirements (parent & template selected and label-field set to title) for this feature to work are set automatically.', __FILE__);
        $f->attr('checked', $this->wire('session')->addable == '1' ? 'checked' : '' );
        if($this->wire('session')->addable) $f->attr('value', $this->wire('session')->addable);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldCheckbox");
        $f->name = 'preventaddnewshortcut';
        $f->label = __('Prevent template for select option pages from being added to the Add Page shortcut menu?', __FILE__);
        $f->description = __('If checked, the template for the select option pages won\'t be added to the "Add New Page" shortcut button/menu that appears on the main Pages screen.', __FILE__);
        $f->notes = __('This adds `noShortcut:1` to templates settings.', __FILE__);
        $f->attr('checked', $this->wire('session')->preventaddnewshortcut == '1' ? 'checked' : '' );
        if($this->wire('session')->preventaddnewshortcut) $f->attr('value', $this->wire('session')->preventaddnewshortcut);
        $fieldset->add($f);

        $f = $this->wire('modules')->get("InputfieldSubmit");
        $f->name = 'submit';
        $f->value = __('Create field, templates, and pages', __FILE__);
        $form->add($f);

        return $form;
    }


    /**
     * Process the form and populate session variables with the results
     *
     */
    protected function processForm1(InputfieldForm $form) {

        $form->processInput($this->wire('input')->post);
        if(count($form->getErrors()) || $form->get('grandParent')->value == '') {
            $this->error("Missing required field(s)");
            return $form->render();
        }

        $fieldLabel = $form->get('fieldLabel')->value;
        $fieldName = $this->wire('sanitizer')->fieldName(strtolower($fieldLabel));

        $parentTemplateLabel = $form->get('parentTemplate')->value;
        $parentTemplateName = $this->wire('sanitizer')->name(strtolower($parentTemplateLabel), true, 191, $form->get('templateSpaceCharacter')->value);

        $this->wire('session')->noFieldCreation = (int) $this->wire('input')->noFieldCreation;
        $this->wire('session')->grandParent = (int) $this->wire('input')->grandParent;
        $this->wire('session')->noChangeTemplate = (int) $this->wire('input')->noChangeTemplate;
        $this->wire('session')->noParents = (int) $this->wire('input')->noParents;
        $this->wire('session')->derefAsPage = (int) $this->wire('input')->derefAsPage;
        $this->wire('session')->inputfield = $this->wire('input')->inputfield;
        $this->wire('session')->addable = (int) $this->wire('input')->addable;
        $this->wire('session')->preventaddnewshortcut = (int) $this->wire('input')->preventaddnewshortcut;

        if($form->get('noFieldCreation')->value !== 1 && $this->wire('fields')->$fieldName) {
            return "<h2>{$this->_('Field already exists')}</h2><p>{$this->_('The page field you are trying to create already exists.')}</p><p>{$this->_('You can either')} <a href='./'>{$this->_('try again')}</a> {$this->_('with a different Field Label, or')} <a href='{$this->config->urls->admin}setup/field/edit?id={$this->wire('fields')->$fieldName->id}'>{$this->_('edit/delete the existing field')}</a>.</p>";
        }


        // Templates - first iteration to create fieldgroups and templates
        // Parent template
        if(!$this->wire('fieldgroups')->$parentTemplateName) {
            $fg = new Fieldgroup();
            $fg->name = $parentTemplateName;
            $fg->add("title");
            $fg->save();
        }
        else {
            $fg = $this->wire('fieldgroups')->$parentTemplateName;
        }


        if(!$this->wire('templates')->$parentTemplateName) {
            $parentTemplate = new Template();
            $parentTemplate->name = $parentTemplateName;
            $parentTemplate->label = $parentTemplateLabel;
            $parentTemplate->fieldgroup = $fg;
            $parentTemplate->noChangeTemplate = $form->get('noChangeTemplate')->value;
            $parentTemplate->save();
        }
        else {
            $parentTemplate = $this->wire('templates')->$parentTemplateName;
        }


        // Child template
        $childTemplateLabel = $form->get('childTemplate')->value;
        $childTemplateName = $this->wire('sanitizer')->name(strtolower($childTemplateLabel), true, 191, $form->get('templateSpaceCharacter')->value);

        if(!$this->wire('fieldgroups')->$childTemplateName) {
            $fg = new Fieldgroup();
            $fg->name = $childTemplateName;
            $fg->add("title");
            $fg->save();
        }
        else {
            $fg = $this->wire('fieldgroups')->$childTemplateName;
        }

        if(!$this->wire('templates')->$childTemplateName) {
            $childTemplate = new Template();
            $childTemplate->name = $childTemplateName;
            $childTemplate->label = $childTemplateLabel;
            $childTemplate->noShortcut = $form->get('preventaddnewshortcut')->value == 1 ? '1' : '';
            $childTemplate->fieldgroup = $fg;
            $childTemplate->noChangeTemplate = $form->get('noChangeTemplate')->value;
            $childTemplate->save();
        }
        else {
            $childTemplate = $this->wire('templates')->$childTemplateName;
        }


        // Templates - second iteration to save settings.

        // Parent Template
        $parentTemplate->noParents = $form->get('noParents')->value;
        $parentTemplate->childTemplates = array($childTemplate->id);
        $parentTemplate->save();

        // Child template
        $childTemplate->noChildren = 1;
        $childTemplate->parentTemplates = array($parentTemplate->id);
        $childTemplate->save();


        // Pages
        // Parent
        $grandParent = $this->wire('pages')->get($form->get('grandParent')->value);
        $parentPageTitle = $form->get('parentPageTitle')->value;
        $parentPageName = $this->wire('sanitizer')->pageName($parentPageTitle, true);
        if(!$this->wire('pages')->get("parent=$grandParent, name=$parentPageName")->id) {
            $parentPage = new Page();
            $parentPage->parent = $grandParent;
            $parentPage->template = $parentTemplate;
            $parentPage->name = $parentPageName;
            $parentPage->title = $parentPageTitle;
            $parentPage->of;
            $parentPage->save();
        }

        // Child pages - ie select options
        $i=0;

        if($form->get('childPages')->value != '') {
            $childPages = trim(preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $form->get('childPages')->value),"\n"); // remove any blank lines from the textarea

            if(strpos($childPages,',') !== false) $childPages = stristr($childPages, "Title"); // if Option 2, then need to remove anything from before "Title"

            foreach(explode("\n",$childPages) as $selectTitle) {

                if(strpos($selectTitle,',') !== false) { // creation of child pages using Option 2 configuration options - comma should be a good enough indicator to check for

                    $x=0;

                    if($i==0) {
                        $selectTitle = trim($selectTitle,", \t\0\x0B"); // prevent potential creation of blank fields and pages if leading or trailing comma etc provided
                    }
                    else {
                        $selectTitle = ltrim($selectTitle,", \t\0\x0B"); // not first row so only want to remove leading commas etc, because trailing ones could be blank field values
                    }

                    foreach(str_getcsv($selectTitle) as $selectFieldTitle) {

                        if(strpos($selectFieldTitle,'>') !== false) {
                            $selectFieldTitleName = strstr($selectFieldTitle, '>', true);
                            $selectFieldType = 'Fieldtype' . str_replace($selectFieldTitleName . '>', '', $selectFieldTitle);
                        }
                        else {
                            $selectFieldTitleName = $selectFieldTitle;
                            $selectFieldType = 'FieldtypeText';
                        }
                        $selectFieldName = $this->wire('sanitizer')->fieldName(strtolower(trim($selectFieldTitleName)));


                        if($i==0) { // first loop/line is for field creation. Remaining are for child pages

                            if($selectFieldName != 'title' && $selectFieldName != '') { // no need to create title field, and don't want to create a field with no name - possible if trailing comma left in place

                                $sub_field[$x] = $selectFieldName; // store name of fields for later population of values in child page

                                if(!$this->wire('fields')->$selectFieldName) {
                                    $field = new Field();
                                    $field->type = $this->wire('modules')->get($selectFieldType);
                                    $field->name = $selectFieldName;
                                    $field->label = $selectFieldTitleName;
                                    $field->save();

                                    $childTemplate->fieldgroup->append($field);
                                }
                                else {
                                    $childTemplate->fieldgroup->append($this->wire('fields')->$selectFieldName); // add existing field to template - NB: this does not change any of the attributes of a field if it already exists - this could be problematic
                                }

                                $childTemplate->fieldgroup->save();
                            }

                        }
                        else { // all but first loop - for creating child pages from the rest of the lines in the select options textarea

                            if($x==0 && $selectFieldName != '') { // first value in comma separated row should be the title, so create the child page with this
                                $child_page = new Page();
                                $child_page->parent = $parentPage;
                                $child_page->template = $childTemplate;
                                $child_page->name = $this->wire('sanitizer')->pageName($selectFieldTitleName, true);
                                $child_page->title = $selectFieldTitle;
                                $child_page->of;
                                $child_page->save();
                            }
                            else { // add values for the other fields to the child page
                                if(isset($child_page) && $child_page->id && isset($sub_field) && isset($sub_field[$x])) {
                                    $child_page->{$sub_field[$x]} = $selectFieldTitle;
                                    $child_page->save();
                                }
                            }

                        }

                        $x++;
                    }
                }
                else { // creation of child pages using the SIMPLE configuration option
                    $child_page = new Page();
                    $child_page->parent = $parentPage;
                    $child_page->template = $childTemplate;
                    $child_page->name = $this->wire('sanitizer')->pageName($selectTitle, true);
                    $child_page->title = $selectTitle;
                    $child_page->of;
                    $child_page->save();
                }

                $i++;
            }
        }


        // Page Field
        if($form->get('noFieldCreation')->value !== 1 && !$this->wire('fields')->$fieldName && $fieldName != '') {
            $field = new Field();
            $field->type = $this->wire('modules')->get('FieldtypePage');
            $field->name = $fieldName;
            $field->label = $fieldLabel;
            $field->description = $form->get('fieldDescription')->value;
            $field->derefAsPage = $form->get('derefAsPage')->value;
            $field->parent_id = $parentPage->id;
            $field->template_id = $childTemplate->id;
            $field->inputfield = $form->get('inputfield')->value;
            $field->labelFieldName = 'title';
            $field->addable = $form->get('addable')->value;
            $field->save();
            return $this->processFormMarkup($field, $parentTemplate, $childTemplate, $parentPage);
        }
        else {
            return $this->processFormMarkup(null, $parentTemplate, $childTemplate, $parentPage);
        }


    }


    /**
     * Provide the completion output markup for processImportForm1
     *
     */
    protected function processFormMarkup($field, $parentTemplate, $childTemplate, $parentPage) {

        $out = '';
        $out .= "<h2>Created all required templates and pages";
        if($field) {
            $out .= "
             for the $field->label field</h2>
             <p><a href='{$this->config->urls->admin}setup/field/edit?id={$field->id}'>{$this->_('View the created page reference field')}</a></p>";
        }
        else {
            $out .= '</h2>';
        }
        $out .= "
            <p><a href='{$this->config->urls->admin}setup/template/edit?id={$parentTemplate->id}'>{$this->_('View the parent template')}</a></p>
            <p><a href='{$this->config->urls->admin}setup/template/edit?id={$childTemplate->id}'>{$this->_('View the child template')}</a></p>
            <p><a href='{$this->config->urls->admin}page/list/?open={$parentPage->id}'>{$this->_('Open the page tree of created options')}</a></p>
            <p><a href='./'>Create another page field</a></p>";

        return $out;
    }

}
