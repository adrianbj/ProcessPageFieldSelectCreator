$(window).load(function() {
    $('#Inputfield_fieldTitle').bind('blur', function(){
        $("#Inputfield_parentTemplate").val($('#Inputfield_fieldTitle').val());
        $("#Inputfield_childTemplate").val($('#Inputfield_fieldTitle').val() + ' ' + $("#Inputfield_childTemplateSuffix").val());
    });
});