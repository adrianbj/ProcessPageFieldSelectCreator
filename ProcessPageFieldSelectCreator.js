$(window).load(function() {
    $('#Inputfield_fieldLabel').bind('keyup change', function(){
        $("#Inputfield_parentTemplate").val($('#Inputfield_fieldLabel').val());
        $("#Inputfield_childTemplate").val($('#Inputfield_fieldLabel').val() + ' ' + $("#Inputfield_childTemplateSuffix").val());
        $("#Inputfield_parentPageTitle").val($('#Inputfield_fieldLabel').val());
    });
});