$(window).load(function() {
    $('#Inputfield_fieldLabel').bind('keyup change', function() {
        var field_label = $('#Inputfield_fieldLabel').val();
        var plural = pluralize(field_label);
        var singular = pluralize(field_label, 1);
        $('#Inputfield_parentTemplate').val(plural);
        $('#Inputfield_childTemplate').val(singular);
        $('#Inputfield_parentPageTitle').val(plural);
    });
    // if parent and child template names are the same, set child to blank so user has to manually enter something different
    // this could occur if the field name is set to something like "sheep" where plural and singular is the same
    $('#Inputfield_fieldLabel').bind('blur', function() {
        if($('#Inputfield_parentTemplate').val() === $('#Inputfield_childTemplate').val()) {
            $('#Inputfield_childTemplate').val('');
        }
    });
});