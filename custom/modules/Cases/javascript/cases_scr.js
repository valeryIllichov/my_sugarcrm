
$(document).ready(function () { 
    var number = $("#case_number_label").next()[0].innerHTML;
    var url = "index.php?module=Cases&action=getCasesCustomFields";
    var response = {};
    if(typeof(number)!='undefined' && number != '' && number != '\n'){
        $.post(url, {case_number: number}, function(data){
            response = eval('('+data+')');
//            var psc = $("#connection_type_c option:selected").val();
            var sms = $("#subject_c option:selected").val();
//            if(psc == 'yes'){
//                var id = $("#connection_type_c")[0].id;
//                $("#connection_type_c").parent().append("<input type='text' class='cust-val' onChange='changeFieldValue("+id+")' value='"+response['psc']+"' >");
//            }
            if(sms == 'yes'){
                var id = $("#subject_c")[0].id;
                $("#subject_c").parent().append("<span class='cust-val' style='margin-left: 20px;'><label style='padding-right: 10px;'>Which system?:</label><input type='text' class='inp-val' onChange='changeFieldValue("+id+")' value='"+response['sms']+"' ></span>");
            }
        });
    }
    $("#subject_c").change(function () {
        var id = this.id;
        if(typeof(number)!='undefined' && number != '' && number != '\n'){
//            if(id == 'connection_type_c'){
//                var field_val = response['psc'];
//            }
            if(id == 'subject_c'){
                var field_val = response['sms'];
            }
        }
        if($(this).val() == 'yes' && !$(this).next().hasClass("cust-val")){
            if(typeof(number)!='undefined' && number != '' && field_val != 'no' && number != '\n'){
               $(this).parent().append("<span class='cust-val' style='margin-left: 20px;'><label style='padding-right: 10px;'>Which system?:</label><input type='text' class='inp-val' onChange='changeFieldValue("+id+")' value='"+field_val+"' ></span>");  
            }else{
               $(this).parent().append("<span class='cust-val' style='margin-left: 20px;'><label style='padding-right: 10px;'>Which system?:</label><input type='text' class='inp-val' onChange='changeFieldValue("+id+")' ></span>");
            }
            $(this).append("<option class='cust-opt' selected='selected'>Yes</option>"); 
            $(this).find(':last').css('display', 'none');
        }else if($(this).val() == 'no' && $(this).next().hasClass("cust-val")){
            $(this).next().remove();
            $(this).find('.cust-opt').remove();
        }
    });
});


function changeFieldValue(elem) {
    var input_val =  $(elem).next().find('.inp-val').val();
    $(elem).find(':last').attr("value",input_val);
}
