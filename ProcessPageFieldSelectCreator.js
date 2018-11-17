$(window).load(function() {
    $('#Inputfield_fieldLabel').bind('keyup change', function() {
        var field_label = $('#Inputfield_fieldLabel').val();
        var plural = pluralize.plural(field_label);
        var singular = pluralize.singular(field_label);
        $('#Inputfield_parentTemplate').val(plural);
        $('#Inputfield_childTemplate').val(singular);
        $('#Inputfield_parentPageTitle').val(plural);
    });
    // if parent and child template names are the same, force parent to have 's' suffix
    // this could occur if the field name is set to something like "sheep" where plural and singular is the same
    $('#Inputfield_fieldLabel').bind('blur', function() {
        if($('#Inputfield_parentTemplate').val() !== '' && $('#Inputfield_parentTemplate').val() === $('#Inputfield_childTemplate').val()) {
            $('#Inputfield_parentTemplate').val($('#Inputfield_fieldLabel').val() + 's');
        }
    });
});