
var untilsLinks = window.parent.document.getElementsByClassName('utilsLink');

for (var i=1; i<untilsLinks.length; i++)
         untilsLinks[i].style.display = "none";
     
function enableQuickSearch(noReload){
    Ext.onReady(function(){
        var qsFields=Ext.query('.sqsCustom');
        for(var qsField in qsFields){
            var loaded=false;
            if(isInteger(qsField)&&(qsFields[qsField].id&&!document.getElementById(qsFields[qsField].id).readOnly)&&typeof sqs_objects!='undefined'&&sqs_objects[qsFields[qsField].id]&&sqs_objects[qsFields[qsField].id]['disable']!=true){
                if(!loaded){
                    if(typeof QSFieldsArray[qsFields[qsField].id]=='undefined'){
                        var Arr=new Array(qsFields[qsField].parentNode,qsFields[qsField].nextSibling,qsFields[qsField]);
                        QSFieldsArray[qsFields[qsField].id]=Arr;
                    }
                    var sqs=sqs_objects[qsFields[qsField].id];
                    //console.log(sqs);
                    var display_field=sqs.field_list[0];
            
                    var ds=new Ext.data.Store({
                        storeId:"store_"+qsFields[qsField].id,
                        proxy:new Ext.data.HttpProxy({
                            url:'../index.php'
                        }),
                        remoteSort:true,
                        reader:new Ext.data.JsonReader({
                            root:'fields',
                            totalProperty:'totalCount',
                            id:'id'
                        },[{
                            name:display_field
                        },]),
                        baseParams:{
                            to_pdf:'true',
                            module:'Home',
                            action:'quicksearchQuery',
                            data:Ext.util.JSON.encode(sqs)
                        }
                    });
                    
                    var search=new Ext.form.ComboBox({
                        id:"combobox_"+qsFields[qsField].id,
                        store:ds,
                        queryDelay:700,
                        maxHeight:300,
                        minListWidth: 'auto',
                        displayField:display_field,
                        fieldClass:'',
                        listClsClass:typeof(Ext.version)!='undefined'?'x-sqs-list':'x-combo-list',
                        focusClass:'',
                        disabledClass:'',
                        emptyClass:'',
                        invalidClass:'',
                        selectedClass:typeof(Ext.version)!='undefined'?'x-sqs-selected':'x-combo-list',
                        typeAhead:true,
                        loadingText:'Searching...',
                        valueNotFoundText:sqs.no_match_text,
                        hideTrigger:true,
                        confirmed:false,
                        applyTo:typeof(Ext.version)!='undefined'?qsFields[qsField].id:Ext.form.ComboBox.prototype.applyTo,
                        minChars:1,
                        onSelect: function(record, index){
                            if(this.fireEvent('beforeselect', this, record, index) !== false){
                                this.setValue(record.data[this.valueField || this.displayField]);
                                this.fireEvent('select', this, record, index);
                            }  							
                        },
                        listeners:{
                            select:function(el,type, index){
                                setCustomFields(type,el,index,/account/);      
                            }
                        }    
                    });
                    if(typeof search.applyTo!='undefined'){
                        search.applyTo(qsFields[qsField].id);
                    }
                    search.wrap.applyStyles('display:inline');
                    qsFields[qsField].className=qsFields[qsField].className.replace('x-form-text','');
                    if(Ext.isMac&&Ext.isGecko){
                        document.getElementById(qsFields[qsField].id).addEventListener('keypress',preventDef,false);
                    }
                    if((qsFields[qsField].form&&typeof(qsFields[qsField].form)=='object'&&qsFields[qsField].form.name=='search_form')||(qsFields[qsField].className.match('sqsNoAutofill')!=null)){
                        search.events.autofill.listeners[0].fireFn=function(){};

                    }
                }
            }
        }
    });
}
function setCustomFields(type,el,index,filter){
    for(var field in type.json){
        for(var key in sqs_objects[el.el.id].field_list){
            if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                if(field=='name' && typeof type.json['expanded_name'] != "undefined"){ 
                    document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json['expanded_name'];
                }else{
                    document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];    
                }     
            }
        }
    }
    if($(el.innerList.dom.children[index]).hasClass("selected-accounts")){
        $(el.innerList.dom.children[index]).removeClass("selected-accounts");
        remove_account(el.innerList.dom.children[index].innerHTML);
    }else{
        $(el.innerList.dom.children[index]).addClass("selected-accounts");
        add_account(el.innerList.dom.children[index].innerHTML);    
    }
}

QSFieldsArray=new Array();
if(typeof Ext=='object'){
    enableQuickSearch(true);
}
// End of File include/javascript/quicksearch.js
function add_account(index){
    var account_id = document.getElementById('account_id_filter').value;
    var custno = document.getElementById('account_name_custno_filter').value;    
    if(sessionStorage.getItem("accounts_521") != null && sessionStorage.getItem("custno_521") != null && account_id != '' && custno != ''){ 
        var accounts_arr = eval('(' + sessionStorage.getItem("accounts_521") + ')');
        var custno_arr = eval('(' + sessionStorage.getItem("custno_521") + ')');    
        accounts_arr[index] = account_id;  
        custno_arr[index] = custno;    
        sessionStorage.setItem("accounts_521", JSON.stringify(accounts_arr));
        sessionStorage.setItem("custno_521", JSON.stringify(custno_arr));
        var str_val = '';    
        for(var key in custno_arr) {
            str_val += custno_arr[key]+", ";
        } 
        str_val = str_val.slice(0,str_val.length-2);    
        $("#i521_customers").html(str_val);    
    }else if(account_id != '' && custno != ''){
        var accounts_arr = {};
        var custno_arr = {};    
        accounts_arr[index] = account_id;
        custno_arr[index] = custno;    
        sessionStorage.setItem("accounts_521", JSON.stringify(accounts_arr));
        sessionStorage.setItem("custno_521", JSON.stringify(custno_arr));    
        var str_val = '';    
        for(var key in custno_arr) {
            str_val += custno_arr[key]+", ";
        }   
        str_val = str_val.slice(0,str_val.length-2);    
        $("#i521_customers").html(str_val);    
    } 
}
function remove_account(index){
    if(sessionStorage.getItem("accounts_521") != null && sessionStorage.getItem("custno_521") != null){ 
        var accounts_arr = eval('(' + sessionStorage.getItem("accounts_521") + ')');
        var custno_arr = eval('(' + sessionStorage.getItem("custno_521") + ')');    
        delete accounts_arr[index];  
        delete custno_arr[index];    
        sessionStorage.setItem("accounts_521", JSON.stringify(accounts_arr));
        sessionStorage.setItem("custno_521", JSON.stringify(custno_arr));    
        var str_val = '';    
        for(var key in custno_arr) {
            str_val += custno_arr[key]+", ";
        }
        if(str_val != ''){
            str_val = str_val.slice(0,str_val.length-2);
            $("#i521_customers").html(str_val);
        }else{
            $("#i521_customers").html("All");
        } 
    }
}

function clear_account(){
    document.getElementById('account_name_custno_filter').value = ''; 
    document.getElementById('account_name_filter').value = ''; 
    document.getElementById('account_id_filter').value = '';       
}       
 function reset_account_filter(){    
    sessionStorage.removeItem("accounts_521");
    sessionStorage.removeItem("custno_521");
    $("#i521_customers").html("All");
    get_date_for_i521('');
}          
function i521_fmp_slsm_list_quick_search(input_val){
    if(input_val.length != 0){
        $("#i521_box_for_slsm_first").hide();
        var new_select = "";
        new_select += '<select id="i521_fmprep_slsm_tree_search" onchange="javaScript:get_date_for_i521(\'\')" size="15" multiple="multiple" style="width: 340px;">';
        new_select += '<option value="all" style="border-bottom: 2px solid grey;">ALL</option>';
        $.each($("#i521_fmprep_slsm_tree option"), function(){
            var option_val = this.text;
            if(option_val.indexOf(input_val.toUpperCase()) + 1) {
                new_select += '<option value="'+this.value+'">'+this.text+'</option>';
            }
        });
        new_select += '</select>';
        $("#i521_box_for_slsm_second").show();
        $("#i521_box_for_slsm_second").html(new_select);
    }else{
        $("#i521_box_for_slsm_second").hide();
        $("#i521_box_for_slsm_first").show();
    }
    $("#i521_fmprep_slsm_tree_search").click(function(){
        var fmprep_slsm_tree_search = $("#i521_fmprep_slsm_tree_search option:selected").val();
        if (fmprep_slsm_tree_search != 'all'){
            $("#i521_lslsm").html("SLSM "+fmprep_slsm_tree_search);
        }
        else{
            $("#i521_lslsm").html("");
        }
    });
}

$(document).ready(function(){
    $(window.parent.document).find(".utilsLink").each(function(i,v){
        var curr_link = $(v);
        if(curr_link.children().attr('alt') == 'Print' || curr_link.html() ==  'Print' ){
            curr_link.addClass('custom-521-print');
            curr_link.css("display","inline-block");
        }
        if(curr_link.html() ==  'Help' ){
            curr_link.after("<a href='javascript:document.getElementById(\"SUGARIFRAME\").contentWindow.send_export_form();' class='utilsLink'><img src='themes/Sugar/images/Icon_csv.gif' width='16' height='16' alt='Print' border='0' align='absmiddle'></a>&nbsp;<a  href='javascript:void window.document.getElementById(\"SUGARIFRAME\").contentWindow.send_export_form()' class='utilsLink'>Export</a>");
        }
    });
   $(window.parent.document).find(".custom-521-print").click(function(e){
        e.preventDefault();
        var activ_tab = $("#crm521 ul.yui-nav li.selected a").attr('href').substr(1);
         if(activ_tab == 'customersalestabs'){
            activ_tab = $("#customersalestabs ul.yui-nav li.selected a").attr("href").substr(1);
        }
        printTable(activ_tab);
    });
    $("#i521_slsm_list_show").hover(
        function(){
            $("#i521_slsm_list_show").find("#i521_slsm_panel").stop(true, true);
            $("#i521_slsm_list_show").find("#i521_slsm_panel").slideDown("slow");
        },
        function() {
            $("#i521_slsm_list_show").find("#i521_slsm_panel").slideUp("slow");
        });
    $("#i521_area_list_show").hover(
        function(){
            $("#i521_area_list_show").find("#i521_area_panel").stop(true, true);
            $("#i521_area_list_show").find("#i521_area_panel").slideDown("slow");
        },
        function() {
            $("#i521_area_list_show").find("#i521_area_panel").slideUp("slow");
        });
    $("#i521_dealer_list_show").hover(
        function(){
            $("#i521_dealer_list_show").find("#i521_dealer_panel").stop(true, true);
            $("#i521_dealer_list_show").find("#i521_dealer_panel").slideDown("slow");
        },
        function() {
            $("#i521_dealer_list_show").find("#i521_dealer_panel").slideUp("slow");
        });
    $("#i521_customer_list_show").hover(
        function(){
            $("#i521_customer_list_show").find("#i521_customer_panel").stop(true, true);
            $("#i521_customer_list_show").find("#i521_customer_panel").slideDown("slow");
        },
        function() {
            if ( $(".x-shadow").css("display") == "none" || $(".x-shadow").length == 0) { 
                $("#i521_customer_list_show").find("#i521_customer_panel").delay(1000).slideUp("slow");
            } 
        });  
    $("#filter_list_show").click(function(){ 
        if($("#filter_panel").css("display") == "none"){
            $("#filter_list_show").find("#filter_panel").stop(true, true);
            $("#filter_list_show").find("#filter_panel").slideDown();
        }else{
            $("#filter_list_show").find("#filter_panel").delay(1000).slideUp("fast");
        }
    }); 
    
    $("#crm521 ul.yui-nav li a").click(function(){
     
       var current_tab =  $(this).attr('href').substr(1);
        if(current_tab == 'customersalestabs'){
            current_tab = 'customersales';
        }
       var active_tab = $("#crm521 ul.yui-nav li.selected a").attr("href").substr(1);
       var current_title = $('#title-list .divtitle label[for="#'+current_tab+'"]').html();
       var active_title = $('#title-list .active-tab label').html();
        
       $(".active-tab").removeClass('active-tab');
       $('#title-list .divtitle label[for="#'+current_tab+'"]').parent().addClass('active-tab');
       
       var paginator = 0;
       var paginator_before = 0;
       if( typeof localStorage.tablePaginator != "undefited" && localStorage.tablePaginator != null ){
           paginator = localStorage.tablePaginator;
       }
       if( typeof localStorage.tablePaginatorBefore != "undefited" && localStorage.tablePaginatorBefore != null ){
           paginator_before = localStorage.tablePaginatorBefore;
       }
       if(current_title != active_title && current_title != ''){
            get_date_for_i521(current_tab);
       }else if(current_title == ''){
            get_date_for_i521_dashletFilter(current_tab);
       }
       if(paginator != paginator_before){
           localStorage.tablePaginatorBefore = localStorage.tablePaginator;
           $("#crm521 ul.yui-nav li a").each(function(k,v){
               var tab = $(v).attr("href").substr(1);
               if(tab == 'customersalestabs'){
                   tab = 'customersales';
               }
               if(tab != active_tab || tab != 'salessummary'){
                   get_date_for_i521(tab);
               }
               
           });
       }
       if(current_tab != 'customerar2'){
           var frameWidth = document.getElementById("crm521").offsetWidth;
            var tableWidth = $("#"+current_tab).find("table").width();
            if(tableWidth > frameWidth){
                $("#menu-scrolling-"+current_tab).css("display","block");
                initScroll(current_tab);
            }else{
                $("#menu-scrolling-"+current_tab).css("display","none");
                    destroyScroll(current_tab);
            }
       }
    });
    $("#current_user_filter").change(function(){
        var checkboxelem = $(this);   
        var name = "current_user_keep_filters";
        var expires = new Date();
        if (checkboxelem.is(":checked")) {
           expires.setTime(expires.getTime() + (1000 * 86400 * 365)); 
            set_cookie(name, 1, expires);
        }else{
            set_cookie(name, 0, expires);
        }
    });
    
});

function set_cookie(name, value, expires){
    if (!expires){
        expires = new Date();
    }
    document.cookie = name + "=" + escape(value) + "; expires=" + expires.toGMTString() +  "; path=/";
}  
function get_date_for_i521(current_tab){
    /* Slsm button */
    var input_value = $("#i521_fmp_slsm_input").val();
    if(input_value.length == 0){
        var select_slsm = $("#i521_fmprep_slsm_tree option:selected").val();
    }else{
        var select_slsm = $("#i521_fmprep_slsm_tree_search option:selected").val();
    }
    var select_slsm_title = '';
    if(select_slsm == null){
        // select_slsm = 'allin';
        select_slsm = '';
        select_slsm_title = '';
    } else if (select_slsm != 'all') {
        var selected_slsm = new Array();
        var selected_slsm_title = new Array();
        if (input_value.length == 0) {
            $("#i521_fmprep_slsm_tree option:selected").each(function(k, v) {
                selected_slsm[k] = $(this).val();
                var    indx = $(this).val().indexOf(';');
                 if(indx > -1) {
                    selected_slsm_title[k] = $(this).val().substring(0,indx);
                }else{
                     selected_slsm_title[k] = $(this).val();
                }
            });
        }else{
            $("#i521_fmprep_slsm_tree_search option:selected").each(function(k, v) {
                selected_slsm[k] = $(this).val();
                var    indx = $(this).val().indexOf(';');
                 if(indx > -1) {
                    selected_slsm_title[k] = $(this).val().substring(0,indx);
                }else{
                     selected_slsm_title[k] = $(this).val();
                }
            });

        }
        select_slsm = selected_slsm.join(";");
        select_slsm_title = selected_slsm_title.join(";");
    } else if (select_slsm == 'all') {
        select_slsm = '';
        select_slsm_title = '';
    }
    /* Area Button */
    var select_reg_loc = $("#i521_fmp_reg_loc option:selected").val();
    var select_reg = '';
    var select_loc = '';
    if(select_reg_loc == null){
        // select_reg_loc = 'allin';
        select_reg = '';
        select_loc = '';
    }else if(select_reg_loc != 'all') {
        var selected_reg = new Array();
        var selected_loc = new Array();
        $("#i521_fmp_reg_loc option:selected").each(function(k, v) {
            if($(this).val().indexOf('r') + 1) {
                selected_reg[selected_reg.length] = $(this).val().substr(1);
            }else{
                selected_loc[selected_loc.length] = $(this).val();
            }
        });
        select_reg = selected_reg.join(";");
        select_loc = selected_loc.join(";");
    } else if(select_reg_loc == 'all'){
        select_reg = '';
        select_loc = '';
    }
    /* Dealer Button */
    var select_dealer = $("#i521_fmp_dealer_type option:selected").val();
    if(select_dealer == null) {
        //select_dealer = 'allin';
        select_dealer = '';
    }else if(select_dealer != 'all') {
        var selected_dealer = new Array();
        $("#i521_fmp_dealer_type option:selected").each(function(k, v) {
            selected_dealer[k] = $(this).val();
        });
        select_dealer = selected_dealer.join(";");
    }else {
        select_dealer = '';
    }
                
    if(sessionStorage.getItem("accounts_521") != null && sessionStorage.getItem("custno_521") != null){
        var accounts_arr = eval('(' + sessionStorage.getItem("accounts_521") + ')');
        var custno_arr = eval('(' + sessionStorage.getItem("custno_521") + ')'); 
        var account_id = '';
        for(var key in accounts_arr) {
            account_id += accounts_arr[key]+";";
        } 
        account_id = account_id.slice(0,account_id.length-1);    
        var custno_name = '';    
        for(var key in custno_arr) {
            custno_name += custno_arr[key]+";";
        } 
        custno_name = custno_name.slice(0,custno_name.length-1);     
    } else{
        account_id  = "";
        custno_name = "";
    }  
    if(current_tab == ''){
        current_tab = $("#crm521 ul.yui-nav li.selected a").attr("href").substr(1);
    }
    if(current_tab == 'customersalestabs'){
        current_tab = $("#customersalestabs ul.yui-nav li.selected a").attr("href").substr(1);
    }
    updateDivTitle(current_tab,select_reg,select_loc,select_slsm_title,select_dealer,custno_name);
    localStorage.currentRegion=select_reg;
    localStorage.currentLocation=select_loc;
    localStorage.currentSlsmTitle=select_slsm_title;
    localStorage.currentSlsm=select_slsm;
    localStorage.currentDealerType=select_dealer;
    localStorage.currentCustomerCustno=custno_name;
    localStorage.currentCustomerId=account_id;
    var slsmList="";
    var regionList="";
    var locationList="";
    var selectMethod="";
    $( ".container-close" ).remove();
    slsmList=select_slsm;
    regionList=select_reg;
    locationList=select_loc;
    YAHOO.FMP.SalesSummary = createSalesSummaryDataTable(current_tab, selectMethod, slsmList,  regionList, locationList,select_dealer,account_id)();
    YAHOO.FMP.CustomerAR = createCustomerARDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerSales = createCustomerSalesDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'',account_id)();
    YAHOO.FMP.CustomerSalesNonOE = createCustomerSalesNonoeDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'nonoe',account_id)();
    YAHOO.FMP.CustomerSalesUnderCar = createCustomerSalesUndercarDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'undercar',account_id)();
    YAHOO.FMP.CustomerSalesComparison = createCustomerSalesComparisonDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerBudgetComparison = createCustomerBudgetComparisonDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerReturns = createCustomerReturnsDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerTransactions = createCustomerTransactionsDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerBudget = createCustomerBudgetDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
}
function get_date_for_i521_dashletFilter(current_tab){
    if(current_tab == ''){
        current_tab = $("#crm521 ul.yui-nav li.selected a").attr("href").substr(1);
    }
    if(current_tab == 'customersalestabs'){
        current_tab = $("#customersalestabs ul.yui-nav li.selected a").attr("href").substr(1);
    }
    
    select_reg=localStorage.currentRegion;
    select_loc=localStorage.currentLocation;
    select_slsm_title=localStorage.currentSlsmTitle;
    select_slsm=localStorage.currentSlsm;
    select_dealer=localStorage.currentDealerType;
    var custno_name= "";
    var account_id="";
    updateDivTitle(current_tab,select_reg,select_loc,select_slsm_title,select_dealer,custno_name);
    var slsmList="";
    var regionList="";
    var locationList="";
    var selectMethod=""; 
    $( ".container-close" ).remove();
    slsmList=select_slsm;
    regionList=select_reg;
    locationList=select_loc;
    YAHOO.FMP.SalesSummary = createSalesSummaryDataTable(current_tab, selectMethod, slsmList,  regionList, locationList,select_dealer,account_id)();
    YAHOO.FMP.CustomerAR = createCustomerARDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerSales = createCustomerSalesDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'',account_id)();
    YAHOO.FMP.CustomerSalesNonOE = createCustomerSalesNonoeDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'nonoe',account_id)();
    YAHOO.FMP.CustomerSalesUnderCar = createCustomerSalesUndercarDataTable(current_tab, selectMethod, slsmList, regionList, locationList,select_dealer,'undercar',account_id)();
    YAHOO.FMP.CustomerSalesComparison = createCustomerSalesComparisonDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerBudgetComparison = createCustomerBudgetComparisonDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerReturns = createCustomerReturnsDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerTransactions = createCustomerTransactionsDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();
    YAHOO.FMP.CustomerBudget = createCustomerBudgetDataTable(current_tab, selectMethod, slsmList, regionList, locationList, select_dealer,account_id)();

}
function updateDivTitle(currentTab,currentRegion,currentLocation,currentSlsm,currentDealerType,currentCustomer) {
    var s = "";
    var slash = false;
    
    if(currentRegion != "") {
        s += " Region " + currentRegion;
        slash = true;
    }
    
    if(currentLocation != "") {
        if(slash) s += " / ";
        s += " Location " + currentLocation;
        slash = true;
    }
    
    if(currentSlsm != "") {
        if(slash) s += " / ";
        s += " Slsm " + currentSlsm;
        slash = true;
    }
    
    if(currentDealerType != "") {
        if(slash) s += " / ";
         s += " Customer Type " + currentDealerType;
         slash = true;
    }
    
     if(currentCustomer != "") {
        if(slash) s += " / ";
         s += " Customers " + currentCustomer;
    }
    
    if(s == "") {
        s = "All Sales";
    }
    $('#title-list .divtitle label[for="#'+currentTab+'"]').html(s);
}

function isInteger(s){
    if(typeof num_grp_sep!='undefined'&&typeof dec_sep!='undefined')
        s=unformatNumberNoParse(s,num_grp_sep,dec_sep).toString();
    var i;
    for(i=0;i<s.length;i++){
        var c=s.charAt(i);
        if(((c<"0")||(c>"9"))){
            if(i==0&&c=="-"){}else
                return false;
        }
    }
    return true;
}
function unformatNumberNoParse(n,num_grp_sep,dec_sep){
    if(typeof num_grp_sep=='undefined'||typeof dec_sep=='undefined')return n;
    n=n?n.toString():'';
    if(n.length>0){
        n=n.replace(new RegExp(RegExp.escape(num_grp_sep),'g'),'').replace(new RegExp(RegExp.escape(dec_sep)),'.');
        return n;
    }
    return'';
}

 function printTable(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    Popup(printContents); 
}

function Popup(data) 
{
	var mywindow = window.open('', 'Print', 'menubar=1,status=0,resizable=1,scrollbars=1,toolbar=0,location=1');
	mywindow.document.write('<html><head><title>5-21 Sales Summary</title>');
	/*optional stylesheet*/ //
	mywindow.document.write('<link rel="stylesheet" type="text/css" href="521/style/skin.css">');
	mywindow.document.write('<link rel="stylesheet" type="text/css" href="521/style/print.css">');
                  mywindow.document.write('<link rel="stylesheet" type="text/css" href="style/skin.css">');
	mywindow.document.write('<link rel="stylesheet" type="text/css" href="style/print.css">');
	mywindow.document.write('</head><body class="yui-skin-sam"><div class="yui-dt">');
	mywindow.document.write(data);
	mywindow.document.write('</div></body></html>');
	mywindow.document.close();
	mywindow.print();
	return true;
}

  
function send_export_form(){
    var currentTab =  $("#crm521 ul.yui-nav li.selected a").attr("href").substr(1);
    if(currentTab == 'customersalestabs'){
        currentTab = 'customersales';
    }
    var action = "export.php";
    var newForm=document.createElement('form');
    newForm.method='post';
    newForm.action=action;
    newForm.name='newForm';
    newForm.id='newForm';
    var filtersTa=document.createElement('textarea');
    filtersTa.name='filters';
    filtersTa.style.display='none';
    var currentRegion = typeof localStorage.currentRegion != "undefited" ? localStorage.currentRegion :"";
    var currentLocation = typeof localStorage.currentLocation != "undefited" ? localStorage.currentLocation :"";
    var currentSlsm = typeof localStorage.currentSlsm != "undefited" ? localStorage.currentSlsm :"";
    var currentDealerType = typeof localStorage.currentDealerType != "undefited" ? localStorage.currentDealerType :"";
    var currentCustomerId = typeof localStorage.currentCustomerId != "undefited" ? localStorage.currentCustomerId :"";
    filtersTa.value="region="+currentRegion+
                                  "&location="+currentLocation+
                                  "&slsm="+currentSlsm+
                                   "&dealertype="+currentDealerType+
                                   "&account="+currentCustomerId;
    newForm.appendChild(filtersTa);
    var tabInput=document.createElement('input');
    tabInput.name='tab';
    tabInput.type='hidden';
    tabInput.value=currentTab;
    newForm.appendChild(tabInput);
    var fieldsInput=document.createElement('input');
    fieldsInput.name='fields';
    fieldsInput.type='hidden';
    var field_str = '';
    $('#'+currentTab+'-dlg-picker .yui-buttongroup').each(function(k,v){
        if(typeof v != "undefined"){
            if($(v).find(".yui-radio-button-checked span.first-child button").html() == 'Show'){
                field_str += $(v).parent().parent().find(':first-child').html()+'&'+v.id+'|';
            } 
        }
    });
    fieldsInput.value=field_str;
    newForm.appendChild(fieldsInput);
    document.getElementById(currentTab).parentNode.appendChild(newForm);
    newForm.submit();
    return false;
}
