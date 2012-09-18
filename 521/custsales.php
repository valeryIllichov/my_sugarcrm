<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>


    <meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Server-side Pagination and Sorting for Dynamic Data</title>

<style type="text/css">
/*margin and padding on body element
  can introduce errors in determining
  element position and are not recommended;
  we turn them off as a foundation for YUI
  CSS treatments. */
body {
	margin:0;
	padding:0;
}
</style>

<link rel="stylesheet" type="text/css" href="yui/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="yui/build/paginator/assets/skins/sam/paginator.css" />
<link rel="stylesheet" type="text/css" href="yui/build/datatable/assets/skins/sam/datatable.css" />
<link rel="stylesheet" type="text/css" href="yui/build/menu/assets/skins/sam/menu.css" />
<link rel="stylesheet" type="text/css" href="yui/build/button/assets/skins/sam/button.css" />


<script type="text/javascript" src="yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="yui/build/json/json-min.js"></script>
<script type="text/javascript" src="yui/build/element/element-min.js"></script>
<script type="text/javascript" src="yui/build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="yui/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="yui/build/datatable/datatable.js"></script>

<script type="text/javascript" src="yui/build/container/container_core-min.js"></script>
<script type="text/javascript" src="yui/build/menu/menu-min.js"></script>
<script type="text/javascript" src="yui/build/element/element-min.js"></script>
<script type="text/javascript" src="yui/build/button/button-min.js"></script>



<!--there is no custom header content for this example-->

</head>

<body class="yui-skin-sam">

<fieldset id="selectionbuttons">
	<legend>Criteria</legend>
</fieldset>


<h1>Cust sales demo</h1>

<div class="exampleIntro">
	<p>This example enables server-side sorting and pagination for data that is
dynamic in nature.</p>
			
</div>

<!--BEGIN SOURCE CODE FOR EXAMPLE =============================== -->

<div id="dynamicdata"></div>

<script type="text/javascript">
YAHOO.example.DynamicData = function() {
    // Column definitions
    var myColumnDefs = [ // sortable:true enables sorting
        {key:"custno", label:"CustNo", sortable:true},
        {key:"custname", label:"Name", sortable:true},
        {key:"mtd_sales", label:"Mtd Sales", sortable:true, formatter:"currency"},
        {key:"mtd_gp", label:"Mtd GP", sortable:true, formatter:"currency"},
        {key:"ytd_sales", label:"Ytd Sales", sortable:true, formatter:"currency"},
        {key:"ytd_gp", label:"Ytd GP", sortable:true, formatter:"currency"},
        {key:"ly_sales", label:"Ly Sales", sortable:true, formatter:"currency"},
        {key:"ly_gp", label:"Ly GP", sortable:true, formatter:"currency"}
    ];

    // Custom parser
    var stringToDate = function(sData) {
        var array = sData.split("-");
        return new Date(array[1] + " " + array[0] + ", " + array[2]);
    };
    
    // DataSource instance
    var myDataSource = new YAHOO.util.DataSource("json_proxy_customer.php?");
    myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    myDataSource.responseSchema = {
        resultsList: "records",
        fields: [
            {key:"custno"},
            {key:"custname"},
            {key:"mtd_sales",parser:"number"},
            {key:"mtd_gp",parser:"number"},
            {key:"ytd_sales",parser:"number"},
            {key:"ytd_gp",parser:"number"},
            {key:"ly_sales",parser:"number"},
            {key:"ly_gp",parser:"number"}
        ],
        metaFields: {
            totalRecords: "totalRecords" // Access to value in the server response
        }
    };
    
    // DataTable configuration
    var myConfigs = {
        initialRequest: "location=1&sort=mtd_sales&dir=desc&startIndex=0&results=20", // Initial request for first page of data
        dynamicData: true, // Enables dynamic server-driven data
        sortedBy : {key:"mtd_sales", dir:YAHOO.widget.DataTable.CLASS_DESC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({ rowsPerPage:20 }) // Enables pagination 
    };
    
    // DataTable instance
    var myDataTable = new YAHOO.widget.DataTable("dynamicdata", myColumnDefs, myDataSource, myConfigs);
    // Update totalRecords on the fly with value from server
    myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalRecords;
        return oPayload;
    }


    return {
        ds: myDataSource,
        dt: myDataTable
    };

}();

</script>

<script type="text/javascript">

YAHOO.util.Event.onContentReady("selectionbuttons", function () {
		var changeCustomerTypeClick = function (p_sType, p_aArgs, p_oItem) {
			
			var sText = p_oItem.cfg.getProperty("text");
			
			alert("[MenuItem Properties] text: " + sText + ", value: " + p_oItem.value);

			YAHOO.example.DynamicData.dt.configs.initialRequest = "location=1&sort=mtd_sales&dir=desc&startIndex=0&results=20";

//			YAHOO.example.DynamicData.dt.getRecordSet().reset();
			YAHOO.example.DynamicData.dt.refreshView();
		};

		//	Create an array of YAHOO.widget.MenuItem configuration properties
   var aCustomerTypeButtonMenu = [

			{ text: "All", value: null, onclick: { fn: changeCustomerTypeClick } },
			{ text: "Ford Dealers", value: "Ford", onclick: { fn: changeCustomerTypeClick } },
			{ text: "GM Dealers", value: "GM", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Other Dealer", value: "Other Dealer", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Asian/European", value: "European", onclick: { fn: changeCustomerTypeClick } },
			{ text: "All Day Parts", value: "All Day Parts", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Jobber/Distributor/Retailer", value: "Jobber", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Independent Service Center", value: "Installer", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Independent Service Center", value: "Fleet", onclick: { fn: changeCustomerTypeClick } },
			{ text: "Cash/COD/Other", value: "Other", onclick: { fn: changeCustomerTypeClick } }
		];

		//	Instantiate a Split Button using the array of YAHOO.widget.MenuItem 
		//	configuration properties as the value for the "menu" 
		//	configuration attribute.
	var oCustomerTypeButton = new YAHOO.widget.Button({ type: "split",  label: "Customer Type", name: "splitbutton5", menu: aCustomerTypeButtonMenu, container: this });

    

	});
		//	"click" event handler for each item in the Button's Menu

    



</script>

</body>
</html>
<!-- presentbright.corp.yahoo.com uncompressed/chunked Thu Feb 19 10:53:12 PST 2009 -->
