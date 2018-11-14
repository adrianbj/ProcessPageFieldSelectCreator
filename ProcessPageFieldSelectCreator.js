$(window).load(function() {
    $('#Inputfield_fieldLabel').bind('keyup change', function() {
    	var field_label = $('#Inputfield_fieldLabel').val();
    	var plural = pluralize(field_label);
    	var singular = pluralize(field_label, 1);
	    $('#Inputfield_parentTemplate').val(plural);
	    $('#Inputfield_childTemplate').val(singular);
	    $('#Inputfield_parentPageTitle').val(plural);
    });
    $('#Inputfield_fieldLabel').bind('blur', function() {
    	if($('#Inputfield_parentTemplate').val() === $('#Inputfield_childTemplate').val()) {
    		$('#Inputfield_childTemplate').val('');
    	}
    });
});