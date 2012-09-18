<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>


    <meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>Factory Motor Parts 5-21 Screen</title>

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
<script type="text/javascript" src="yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="yui/build/connection/connection-min.js"></script>
<script type="text/javascript" src="yui/build/json/json-min.js"></script>
<script type="text/javascript" src="yui/build/element/element-min.js"></script>
<script type="text/javascript" src="yui/build/paginator/paginator-min.js"></script>
<script type="text/javascript" src="yui/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="yui/build/datatable/datatable.js"></script>

</head>

<body class="yui-skin-sam">


<!--BEGIN SOURCE CODE FOR EXAMPLE =============================== -->

<h1>5-21 data</h1>

<div id="salessummary"></div>
<div id="customerinquiry"></div>

<script type="text/javascript">
YAHOO.example.DynamicData = function() {
    // Column definitions
    var summaryColumnDefs = [ // sortable:true enables sorting
	{label:"Corporate Data", children:[
        {key:"label1", label:""},
        {key:"amount1", label:"", formatter: "currency"},
        {key:"label2", label:""},
        {key:"amount2", label:"", formatter: "currency"}
	]}
    ];

    // Custom parser
    var stringToDate = function(sData) {
        var array = sData.split("-");
        return new Date(array[1] + " " + array[0] + ", " + array[2]);
    };
    
    // DataSource instance
    var summaryDataSource = new YAHOO.util.DataSource("json_proxy_sales.php?");
    summaryDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
    summaryDataSource.responseSchema = {
        resultsList: "records",
        fields: [
            {key:"label1"},
            {key:"amount1", parser:"number"},
            {key:"label2"},
            {key:"amount2",parser:"number"}
        ],
        metaFields: {
            totalRecords: "totalRecords" // Access to value in the server response
        }
    };
    
    // DataTable configuration
    var summaryConfigs = {
        initialRequest: "", //"sort=id&dir=asc&startIndex=0&results=25", // Initial request for first page of data
        dynamicData: true // Enables dynamic server-driven data
        /*sortedBy : {key:"id", dir:YAHOO.widget.DataTable.CLASS_ASC}, // Sets UI initial sort arrow
        paginator: new YAHOO.widget.Paginator({ rowsPerPage:25 }) // Enables pagination  */
    };
    
    // DataTable instance
    var summaryDataTable = new YAHOO.widget.DataTable("salessummary", summaryColumnDefs, summaryDataSource, summaryConfigs);
    // Update totalRecords on the fly with value from server
    summaryDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
        oPayload.totalRecords = oResponse.meta.totalRecords;
        return oPayload;
    }
    
    return {
        ds: summaryDataSource,
        dt: summaryDataTable
    };
        
}();
</script>

<!--END SOURCE CODE FOR EXAMPLE =============================== -->

</body>
</html>
<!-- presentbright.corp.yahoo.com uncompressed/chunked Thu Feb 19 10:53:12 PST 2009 -->
