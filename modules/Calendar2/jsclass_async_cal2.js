/******************************************************************************
OpensourceCRM End User License Agreement

INSTALLING OR USING THE OpensourceCRM's SOFTWARE THAT YOU HAVE SELECTED TO 
PURCHASE IN THE ORDERING PROCESS (THE "SOFTWARE"), YOU ARE AGREEING ON BEHALF OF
THE ENTITY LICENSING THE SOFTWARE ("COMPANY") THAT COMPANY WILL BE BOUND BY AND 
IS BECOMING A PARTY TO THIS END USER LICENSE AGREEMENT ("AGREEMENT") AND THAT 
YOU HAVE THE AUTHORITY TO BIND COMPANY.

IF COMPANY DOES NOT AGREE TO ALL OF THE TERMS OF THIS AGREEMENT, DO NOT SELECT 
THE "ACCEPT" BOX AND DO NOT INSTALL THE SOFTWARE. THE SOFTWARE IS PROTECTED BY 
COPYRIGHT LAWS AND INTERNATIONAL COPYRIGHT TREATIES, AS WELL AS OTHER 
INTELLECTUAL PROPERTY LAWS AND TREATIES. THE SOFTWARE IS LICENSED, NOT SOLD.

    *The COMPANY may not copy, deliver, distribute the SOFTWARE without written
     permit from OpensourceCRM.
    *The COMPANY may not reverse engineer, decompile, or disassemble the 
    SOFTWARE, except and only to the extent that such activity is expressly 
    permitted by applicable law notwithstanding this limitation.
    *The COMPANY may not sell, rent, or lease resell, or otherwise transfer for
     value, the SOFTWARE.
    *Termination. Without prejudice to any other rights, OpensourceCRM may 
    terminate this Agreement if the COMPANY fail to comply with the terms and 
    conditions of this Agreement. In such event, the COMPANY must destroy all 
    copies of the SOFTWARE and all of its component parts.
    *OpensourceCRM will give the COMPANY notice and 30 days to correct above 
    before the contract will be terminated.

The SOFTWARE is protected by copyright and other intellectual property laws and 
treaties. OpensourceCRM owns the title, copyright, and other intellectual 
property rights in the SOFTWARE.
*****************************************************************************/
/*********************************************************************************
* Portions created by SugarCRM are Copyright (C) SugarCRM, Inc. All Rights Reserved.
********************************************************************************/
//////////////////////////////////////////////////////////////////
// called on the return of a JSON-RPC async request,
// and calls the display() method on the widget registered
// in the registry at the request_id key returned by the server


//////////////////////////////////////////////////////////////////

function method_callback (request_id,rslt,e) {
	if(rslt == null) {
	    return;
	}

	if(typeof (global_request_registry[request_id]) != 'undefined') {	
	    widget = global_request_registry[request_id][0];
	    method_name = global_request_registry[request_id][1];
	    widget[method_name](rslt);
	}
}
                                                                                   

//////////////////////////////////////////////////
// class: SugarVCalClient
// async retrieval/parsing of vCal freebusy info 
// 
//////////////////////////////////////////////////

SugarClass.inherit("SugarVCalClient","SugarClass");

function SugarVCalClient() {
	this.init();
}
  
    SugarVCalClient.prototype.init = function(){
      //this.urllib = importModule("urllib");
    }

    SugarVCalClient.prototype.load = function(user_id,request_id){

      this.user_id = user_id;

      // get content at url and declare the callback using anon function:
      urllib.getURL('./vcal_server_cal2.php?type=vfb&source=outlook&user_id='+user_id,[["Content-Type", "text/plain"]], function (result) { 
                  if (typeof GLOBAL_REGISTRY.freebusy == 'undefined')
                  {
                     GLOBAL_REGISTRY.freebusy = new Object();
                  }
   		          if (typeof GLOBAL_REGISTRY.freebusy_adjusted == 'undefined')
                  {
                     GLOBAL_REGISTRY.freebusy_adjusted = new Object();
                  }
                  // parse vCal and put it in the registry using the user_id as a key:
                  GLOBAL_REGISTRY.freebusy[user_id] = SugarVCalClient.parseResults(result.responseText, false);                  // parse for current user adjusted vCal
                  GLOBAL_REGISTRY.freebusy_adjusted[user_id] = SugarVCalClient.parseResults(result.responseText, true);
                  // now call the display() on the widget registered at request_id:
                  global_request_registry[request_id][0].display();
                  })
    }

    // parse vCal freebusy info and return object
    SugarVCalClient.prototype.parseResults = function(textResult, adjusted){
      var match = /FREEBUSY.*?\:([\w]+)\/([\w]+)/g;
    //  datetime = new SugarDateTime();
      var result;
      var timehash = new Object();
      var dst_start;
      var dst_end;

	  if(GLOBAL_REGISTRY.current_user.fields.dst_start == null) 
	  	dst_start = '19700101T000000Z';
	  else 
		dst_start = GLOBAL_REGISTRY.current_user.fields.dst_start.replace(/ /gi, 'T').replace(/:/gi,'').replace(/-/gi,'') + 'Z';
		
	  if(GLOBAL_REGISTRY.current_user.fields.dst_end == null) 		
	  	dst_end = '19700101T000000Z';
      else 
   		dst_end = GLOBAL_REGISTRY.current_user.fields.dst_end.replace(/ /gi, 'T').replace(/:/gi,'').replace(/-/gi,'') + 'Z';
   		
      gmt_offset_secs = GLOBAL_REGISTRY.current_user.fields.gmt_offset * 60;
      // loop thru all FREEBUSY matches
      while(((result= match.exec(textResult))) != null)
      {
        var startdate;
        var enddate;
      	if(adjusted) {// send back adjusted for current_user
		  startdate = SugarDateTime.parseAdjustedDate(result[1], dst_start, dst_end, gmt_offset_secs);
          enddate = SugarDateTime.parseAdjustedDate(result[2], dst_start, dst_end, gmt_offset_secs);
      	}
      	else { // GMT
	      startdate = SugarVCalClient.formatDate(result[1]);
          enddate = SugarVCalClient.formatDate(result[2]);
	      //startdate = SugarDateTime.parseUTCDate(result[1]);
          //enddate = SugarDateTime.parseUTCDate(result[2]);
	    }
        var startmins = startdate.getUTCMinutes();

        // pick the start slot based on the minutes
        if ( startmins >= 0 && startmins < 15) {
          startdate.setUTCMinutes(0);
        }
        else if ( startmins >= 15 && startmins < 30) {
          startdate.setUTCMinutes(15);
        }
        else if ( startmins >= 30 && startmins < 45) {
          startdate.setUTCMinutes(30);
        }
        else {
          startdate.setUTCMinutes(45);
        }
 
        // starting at startdate, create hash of each busy 15 min 
        // timeslot and store as a key
        for(var i=0;i<100;i++)
        {
          if (startdate.valueOf() < enddate.valueOf())
          {
            var hash = SugarDateTime.getUTCHash(startdate);
            if (typeof (timehash[hash]) == 'undefined')
            {
              timehash[hash] = 0;            
            }
            timehash[hash] += 1;            
            startdate = new Date(startdate.valueOf()+(15*60*1000));

          }
          else
          {
            break;
          }
        }
      }
      return timehash;  
    }
    SugarVCalClient.parseResults = SugarVCalClient.prototype.parseResults;
    
    // return the javascript Date object given a vCal UTC string
	SugarVCalClient.prototype.formatDate = function(date_string) {
		var match = /(\d{4})(\d{2})(\d{2})T(\d{2})(\d{2})(\d{2})Z/;
		if(((result= match.exec(date_string))) != null)
		{
			//var new_date = new Date(Date.UTC(result[1],result[2] - 1,result[3],result[4],result[5],parseInt(result[6])));
			var new_date = new Date(result[1],result[2] - 1,result[3],result[4],result[5],parseInt(result[6]));
			return new_date;
		}
	}
	SugarVCalClient.formatDate = SugarVCalClient.prototype.formatDate;

//////////////////////////////////////////////////
// class: SugarRPCClient
// wrapper around async JSON-RPC client class
// 
//////////////////////////////////////////////////
SugarRPCClient.allowed_methods = ['retrieve','query','save','set_accept_status','get_objects_from_module', 'email', 'get_user_array', 'get_full_list'];

SugarClass.inherit("SugarRPCClient","SugarClass");

function SugarRPCClient() {
	this.init();
}

/*
 * PUT NEW METHODS IN THIS ARRAY:
 */
SugarRPCClient.prototype.allowed_methods = ['retrieve','query','save','set_accept_status', 'get_objects_from_module', 'email', 'get_user_array', 'get_full_list'];

SugarRPCClient.prototype.init = function() {
	this._serviceProxy;
	this._showError= function (e){ 
		alert("ERROR CONNECTING to: ./index.php?entryPoint=json_server, ERROR:"+e); 
	}
	this.serviceURL = './index.php?entryPoint=json_server';
	this._serviceProxy = new jsonrpc.ServiceProxy(this.serviceURL,this.allowed_methods);
}

// send a 3rd argument of value 'true' to make the call synchronous.
// in synchronous mode, the return will be the result.
// in asynchronous mode, the return will be the request_id to map the call-back function to.
SugarRPCClient.prototype.call_method = function(method,args) {
	var self=this;
	try {
	  	var the_result;
  	
		if(arguments.length == 3 && arguments[2] == true) {
	  		// aha! fooled you! this function can be called synchronous!
			the_result = this._serviceProxy[method](args);
		} else {
	  		// make the call asynchronous
  			this._serviceProxy[method](args, method_callback);
  			the_result = this._serviceProxy.httpConn.request_id;
		}
		return the_result;
	} catch(e) {//error before calling server
		this._showError(e);
	}
}


var global_rpcClient =  new SugarRPCClient();