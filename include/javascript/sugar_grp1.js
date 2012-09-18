/**
 * Javascript file for Sugar
 *
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 */
if(typeof(SUGAR)=="undefined"){
    var SUGAR={};
    
    SUGAR.themes={};
    
}
SUGAR.sugarHome={};
    
SUGAR.subpanelUtils={};
    
SUGAR.ajaxStatusClass={};
    
SUGAR.tabChooser={};
    
SUGAR.util={};
    
SUGAR.savedViews={};
    
SUGAR.dashlets={};
    
SUGAR.unifiedSearchAdvanced={};
    
SUGAR.searchForm={};
    
SUGAR.language={};
    
SUGAR.Studio={};
    
SUGAR.contextMenu={};
    
var dtCh="-";
var minYear=1900;
var maxYear=2100;
var nameIndex=0;
var typeIndex=1;
var requiredIndex=2;
var msgIndex=3;
var jstypeIndex=5;
var minIndex=10;
var maxIndex=11;
var compareToIndex=7;
var arrIndex=12;
var operatorIndex=13;
var allowblank=8;
var validate=new Array();
var maxHours=24;
var requiredTxt='Missing Required Field:'
var invalidTxt='Invalid Value:'
var secondsSinceLoad=0;
var inputsWithErrors=new Array();
var lastSubmitTime=0;
var alertList=new Array();
var oldStartsWith='';
function isSupportedIE(){
    var userAgent=navigator.userAgent.toLowerCase();
    if(userAgent.indexOf("msie")!=-1&&userAgent.indexOf("mac")==-1&&userAgent.indexOf("opera")==-1){
        var version=navigator.appVersion.match(/MSIE (.\..)/)[1];
        if(version>=5.5){
            return true;
        }else{
            return false;
        }
    }
}
var isIE=isSupportedIE();
var isSafari=(navigator.userAgent.toLowerCase().indexOf('safari')!=-1);
RegExp.escape=function(text){
    if(!arguments.callee.sRE){
        var specials=['/','.','*','+','?','|','(',')','[',']','{','}','\\'];
        arguments.callee.sRE=new RegExp('(\\'+specials.join('|\\')+')','g');
    }
    return text.replace(arguments.callee.sRE,'\\$1');
}
function addAlert(type,name,subtitle,description,time,redirect){
    var addIndex=alertList.length;
    alertList[addIndex]=new Array();
    alertList[addIndex]['name']=name;
    alertList[addIndex]['type']=type;
    alertList[addIndex]['subtitle']=subtitle;
    alertList[addIndex]['description']=description.replace(/<br>/gi,"\n").replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
    alertList[addIndex]['time']=time;
    alertList[addIndex]['done']=0;
    alertList[addIndex]['redirect']=redirect;
}
function checkAlerts(){
    secondsSinceLoad+=1;
    var mj=0;
    var alertmsg='';
    for(mj=0;mj<alertList.length;mj++){
        if(alertList[mj]['done']==0){
            if(alertList[mj]['time']<secondsSinceLoad&&alertList[mj]['time']>-1){
                alertmsg=alertList[mj]['type']+":"+alertList[mj]['name']+"\n"+alertList[mj]['subtitle']+"\n"+alertList[mj]['description']+"\n\n";
                alertList[mj]['done']=1;
                if(alertList[mj]['redirect']==''){
                    alert(alertmsg);
                }
                else if(confirm(alertmsg)){
                    window.location=alertList[mj]['redirect'];
                }
            }
        }
    }
    setTimeout("checkAlerts()",1000);
}
function toggleDisplay(id){
    if(this.document.getElementById(id).style.display=='none'){
        this.document.getElementById(id).style.display='';
        if(this.document.getElementById(id+"link")!=undefined){
            this.document.getElementById(id+"link").style.display='none';
        }
        if(this.document.getElementById(id+"_anchor")!=undefined)
            this.document.getElementById(id+"_anchor").innerHTML='[ - ]';
    }
    else{
        this.document.getElementById(id).style.display='none'
        if(this.document.getElementById(id+"link")!=undefined){
            this.document.getElementById(id+"link").style.display='';
        }
        if(this.document.getElementById(id+"_anchor")!=undefined)
            this.document.getElementById(id+"_anchor").innerHTML='[+]';
    }
}
function checkAll(form,field,value){
    for(i=0;i<form.elements.length;i++){
        if(form.elements[i].name==field)
            form.elements[i].checked=value;
    }
}
function replaceAll(text,src,rep){
    offset=text.toLowerCase().indexOf(src.toLowerCase());
    while(offset!=-1){
        text=text.substring(0,offset)+rep+text.substring(offset+src.length,text.length);
        offset=text.indexOf(src,offset+rep.length+1);
    }
    return text;
}
function addForm(formname){
    validate[formname]=new Array();
}
function addToValidate(formname,name,type,required,msg){
    if(typeof validate[formname]=='undefined'){
        addForm(formname);
    }
    validate[formname][validate[formname].length]=new Array(name,type,required,msg);
}
function addToValidateRange(formname,name,type,required,msg,min,max){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='range'
    validate[formname][validate[formname].length-1][minIndex]=min;
    validate[formname][validate[formname].length-1][maxIndex]=max;
}
function addToValidateIsValidDate(formname,name,type,required,msg){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='date'
}
function addToValidateIsValidTime(formname,name,type,required,msg){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='time'
}
function addToValidateDateBefore(formname,name,type,required,msg,compareTo){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='isbefore'
    validate[formname][validate[formname].length-1][compareToIndex]=compareTo;
}
function addToValidateDateBeforeAllowBlank(formname,name,type,required,msg,compareTo,allowBlank){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='isbefore'
    validate[formname][validate[formname].length-1][compareToIndex]=compareTo;
    validate[formname][validate[formname].length-1][allowblank]=allowBlank;
}
function addToValidateBinaryDependency(formname,name,type,required,msg,compareTo){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='binarydep';
    validate[formname][validate[formname].length-1][compareToIndex]=compareTo;
}
function addToValidateComparison(formname,name,type,required,msg,compareTo){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='comparison';
    validate[formname][validate[formname].length-1][compareToIndex]=compareTo;
}
function addToValidateIsInArray(formname,name,type,required,msg,arr,operator){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='in_array';
    validate[formname][validate[formname].length-1][arrIndex]=arr;
    validate[formname][validate[formname].length-1][operatorIndex]=operator;
}
function addToValidateVerified(formname,name,type,required,msg,arr,operator){
    addToValidate(formname,name,type,required,msg);
    validate[formname][validate[formname].length-1][jstypeIndex]='verified';
}
function removeFromValidate(formname,name){
    for(i=0;i<validate[formname].length;i++){
        if(validate[formname][i][nameIndex]==name){
            validate[formname].splice(i,1);
        }
    }
}
function checkValidate(formname,name){
    if(validate[formname]){
        for(i=0;i<validate[formname].length;i++){
            if(validate[formname][i][nameIndex]==name){
                return true;
            }
        }
    }
    return false;
}
var formsWithFieldLogic=null;
var formWithPrecision=null;
function addToValidateFieldLogic(formId,minFieldId,maxFieldId,defaultFieldId,lenFieldId,type,msg){
    this.formId=document.getElementById(formId);
    this.min=document.getElementById(minFieldId);
    this.max=document.getElementById(maxFieldId);
    this._default=document.getElementById(defaultFieldId);
    this.len=document.getElementById(lenFieldId);
    this.msg=msg;
    this.type=type;
}
function addToValidatePrecision(formId,valueId,precisionId){
    this.form=document.getElementById(formId);
    this.float=document.getElementById(valueId);
    this.precision=document.getElementById(precisionId);
}
function isValidPrecision(value,precision){
    value=trim(value.toString());
    if(precision=='')
        return true;
    if(value=='')
        return true;
    if((precision=="0")){
        if(value.indexOf(".")==-1){
            return true;
        }else{
            return false;
        }
    }
    var actualPrecision=value.substr(value.indexOf(".")+1,value.length).length;
    return actualPrecision==precision;
}
function toDecimal(original,precision){
    precision=(precision==null)?2:precision;
    num=Math.pow(10,precision);
    temp=Math.round(original*num)/num;
    if((temp*100)%100==0)
        return temp+'.00';
    if((temp*10)%10==0)
        return temp+'0';
    return temp
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
function isNumeric(s){
    if(!/^-*[0-9\.]+$/.test(s)){
        return false
    }
    else{
        return true;
    }
}
function stripCharsInBag(s,bag){
    var i;
    var returnString="";
    for(i=0;i<s.length;i++){
        var c=s.charAt(i);
        if(bag.indexOf(c)==-1)returnString+=c;
    }
    return returnString;
}
function daysInFebruary(year){
    return(((year%4==0)&&((!(year%100==0))||(year%400==0)))?29:28);
}
function DaysArray(n){
    for(var i=1;i<=n;i++){
        this[i]=31
        if(i==4||i==6||i==9||i==11){
            this[i]=30
        }
        if(i==2){
            this[i]=29
        }
    }
    return this
}
var date_reg_positions={
    'Y':1,
    'm':2,
    'd':3
};

var date_reg_format='([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})'
function isDate(dtStr){
    if(dtStr.length==0){
        return true;
    }
    myregexp=new RegExp(date_reg_format)
    if(!myregexp.test(dtStr))
        return false
    m='';
    d='';
    y='';
    var dateParts=dtStr.match(date_reg_format);
    for(key in date_reg_positions){
        index=date_reg_positions[key];
        if(key=='m'){
            m=dateParts[index];
        }
        else if(key=='d'){
            d=dateParts[index];
        }
        else{
            y=dateParts[index];
        }
    }
    var dd=new Date(y,m,0);
    if(y<1)
        return false;
    if(m>12||m<1)
        return false;
    if(d<1||d>dd.getDate())
        return false;
    return true;
}
function getDateObject(dtStr){
    if(dtStr.length==0){
        return true;
    }
    myregexp=new RegExp(date_reg_format)
    if(myregexp.exec(dtStr))var dt=myregexp.exec(dtStr)
    else return false;
    var yr=dt[date_reg_positions['Y']];
    var mh=dt[date_reg_positions['m']];
    var dy=dt[date_reg_positions['d']];
    var date1=new Date();
    date1.setFullYear(yr);
    date1.setMonth(mh-1);
    date1.setDate(dy);
    return date1;
}
function isBefore(value1,value2){
    var d1=getDateObject(value1);
    var d2=getDateObject(value2);
    return d2>=d1;
}
function isValidEmail(emailStr){
    if(emailStr.length==0){
        return true;
    }
    var lastChar=emailStr.charAt(emailStr.length-1);
    if(!lastChar.match(/[^\.]/i)){
        return false;
    }
    var emailArr=emailStr.split(/[,;]/);
    for(var i=0;i<emailArr.length;i++){
        emailAddress=emailArr[i];
        if(trim(emailAddress)!=''){
            if(!/^\s*[\w.%+\-&']+@([A-Z0-9-]+\.)*[A-Z0-9-]+\.[A-Z]{2,}\s*$/i.test(emailAddress) &&
                !/^.*<[A-Z0-9._%+\-&']+?@([A-Z0-9-]+\.)*[A-Z0-9-]+\.[A-Z]{2,}>\s*$/i.test(emailAddress)){
                return false;
            }
        }
    }
    return true;
}
function isValidPhone(phoneStr){
    if(phoneStr.length==0){
        return true;
    }
    if(!/^[0-9\-\(\)]+$/.test(phoneStr))
        return false
    return true
}
function isFloat(floatStr){
    if(floatStr.length==0){
        return true;
    }
    if(!(typeof(num_grp_sep)=='undefined'||typeof(dec_sep)=='undefined'))
        floatStr=unformatNumber(floatStr,num_grp_sep,dec_sep).toString();
    return/^(-)?[0-9\.]+$/.test(floatStr);
}
function isDBName(str){
    if(str.length==0){
        return true;
    }
    if(!/^[a-zA-Z][a-zA-Z\_0-9]+$/.test(str))
        return false
    return true
}
var time_reg_format="[0-9]{1,2}\:[0-9]{2}";
function isTime(timeStr){
    time_reg_format=time_reg_format.replace('([ap]m)','');
    time_reg_format=time_reg_format.replace('([AP]M)','');
    if(timeStr.length==0){
        return true;
    }
    myregexp=new RegExp(time_reg_format)
    if(!myregexp.test(timeStr))
        return false
    return true
}
function inRange(value,min,max){
    if(typeof num_grp_sep!='undefined'&&typeof dec_sep!='undefined')
        value=unformatNumberNoParse(value,num_grp_sep,dec_sep).toString();
    return value>=min&&value<=max;
}
function bothExist(item1,item2){
    if(typeof item1=='undefined'){
        return false;
    }
    if(typeof item2=='undefined'){
        return false;
    }
    if((item1==''&&item2!='')||(item1!=''&&item2=='')){
        return false;
    }
    return true;
}
function trim(s){
    if(typeof(s)=='undefined')
        return s;
    while(s.substring(0,1)==" "){
        s=s.substring(1,s.length);
    }
    while(s.substring(s.length-1,s.length)==' '){
        s=s.substring(0,s.length-1);
    }
    return s;
}
function check_form(formname){
    if(typeof(siw)!='undefined'&&siw&&typeof(siw.selectingSomething)!='undefined'&&siw.selectingSomething)
        return false;
    return validate_form(formname,'');
}
function add_error_style(formname,input,txt){
    try{
        inputHandle=document.forms[formname][input];
        style=get_current_bgcolor(inputHandle);
        if(inputHandle.parentNode.innerHTML.search(txt)==-1){
            errorTextNode=document.createElement('span');
            errorTextNode.className='required';
            errorTextNode.innerHTML='<br />'+txt;
            if(inputHandle.parentNode.className.indexOf('x-form-field-wrap')!=-1){
                inputHandle.parentNode.parentNode.appendChild(errorTextNode);
            }
            else{
                inputHandle.parentNode.appendChild(errorTextNode);
            }
            inputHandle.style.backgroundColor="#FF0000";
            inputsWithErrors.push(inputHandle);
        }
        if(inputsWithErrors.length==1){
            for(wp=1;wp<=10;wp++){
                window.setTimeout('fade_error_style(style, '+wp*10+')',1000+(wp*50));
            }
        }
    }catch(e){}
}
function clear_all_errors(){
    for(var wp=0;wp<inputsWithErrors.length;wp++){
        if(typeof(inputsWithErrors[wp])!='undefined'&&typeof inputsWithErrors[wp].parentNode!='undefined'){
            if(inputsWithErrors[wp].parentNode.className.indexOf('x-form-field-wrap')!=-1){
                inputsWithErrors[wp].parentNode.parentNode.removeChild(inputsWithErrors[wp].parentNode.parentNode.lastChild);
            }
            else{
                inputsWithErrors[wp].parentNode.removeChild(inputsWithErrors[wp].parentNode.lastChild);
            }
        }
    }
}
function get_current_bgcolor(input){
    if(input.currentStyle){
        style=input.currentStyle.backgroundColor;
        return style.substring(1,7);
    }
    else{
        style='';
        styleRGB=document.defaultView.getComputedStyle(input,'').getPropertyValue("background-color");
        comma=styleRGB.indexOf(',');
        style+=dec2hex(styleRGB.substring(4,comma));
        commaPrevious=comma;
        comma=styleRGB.indexOf(',',commaPrevious+1);
        style+=dec2hex(styleRGB.substring(commaPrevious+2,comma));
        style+=dec2hex(styleRGB.substring(comma+2,styleRGB.lastIndexOf(')')));
        return style;
    }
}
function hex2dec(hex){
    return(parseInt(hex,16));
}
var hexDigit=new Array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F");
function dec2hex(dec){
    return(hexDigit[dec>>4]+hexDigit[dec&15]);
}
function fade_error_style(normalStyle,percent){
    errorStyle='c60c30';
    var r1=hex2dec(errorStyle.slice(0,2));
    var g1=hex2dec(errorStyle.slice(2,4));
    var b1=hex2dec(errorStyle.slice(4,6));
    var r2=hex2dec(normalStyle.slice(0,2));
    var g2=hex2dec(normalStyle.slice(2,4));
    var b2=hex2dec(normalStyle.slice(4,6));
    var pc=percent/100;
    r=Math.floor(r1+(pc*(r2-r1))+.5);
    g=Math.floor(g1+(pc*(g2-g1))+.5);
    b=Math.floor(b1+(pc*(b2-b1))+.5);
    for(var wp=0;wp<inputsWithErrors.length;wp++){
        inputsWithErrors[wp].style.backgroundColor="#"+dec2hex(r)+dec2hex(g)+dec2hex(b);
    }
}
function validate_form(formname,startsWith){
    requiredTxt=SUGAR.language.get('app_strings','ERR_MISSING_REQUIRED_FIELDS');
    invalidTxt=SUGAR.language.get('app_strings','ERR_INVALID_VALUE');
    if(typeof(formname)=='undefined')

    {
        return false;
    }
    if(typeof(validate[formname])=='undefined')
    {
        return true;
    }
    var form=document.forms[formname];
    var isError=false;
    var errorMsg="";
    var _date=new Date();
    if(_date.getTime()<(lastSubmitTime+2000)&&startsWith==oldStartsWith){
        return false;
    }
    lastSubmitTime=_date.getTime();
    oldStartsWith=startsWith;
    clear_all_errors();
    inputsWithErrors=new Array();
    for(var i=0;i<validate[formname].length;i++){
        if(validate[formname][i][nameIndex].indexOf(startsWith)==0){
            if(typeof form[validate[formname][i][nameIndex]]!='undefined'){
                var bail=false;
                if(validate[formname][i][requiredIndex]&&validate[formname][i][typeIndex]!='bool'){
                    if(typeof form[validate[formname][i][nameIndex]]=='undefined'||trim(form[validate[formname][i][nameIndex]].value)==""){
                        add_error_style(formname,validate[formname][i][nameIndex],requiredTxt+' '+validate[formname][i][msgIndex]);
                        isError=true;
                    }
                }
                if(!bail){
                    switch(validate[formname][i][typeIndex]){
                        case'email':
                            if(!isValidEmail(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'time':
                            if(!isTime(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'date':
                            if(!isDate(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'alpha':
                            break;
                        case'DBName':
                            if(!isDBName(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'alphanumeric':
                            break;
                        case'int':
                            if(!isInteger(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'currency':case'float':
                            if(!isFloat(trim(form[validate[formname][i][nameIndex]].value))){
                                isError=true;
                                add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                            }
                            break;
                        case'error':
                            isError=true;
                            add_error_style(formname,validate[formname][i][nameIndex],validate[formname][i][msgIndex]);
                            break;
                    }
                    if(typeof validate[formname][i][jstypeIndex]!='undefined'){
                        switch(validate[formname][i][jstypeIndex]){
                            case'range':
                                if(!inRange(trim(form[validate[formname][i][nameIndex]].value),validate[formname][i][minIndex],validate[formname][i][maxIndex])){
                                    isError=true;
                                    var lbl_validate_range=SUGAR.language.get('app_strings','LBL_VALIDATE_RANGE');
                                    add_error_style(formname,validate[formname][i][nameIndex],validate[formname][i][msgIndex]+" value "+form[validate[formname][i][nameIndex]].value+" "+lbl_validate_range+" ("+validate[formname][i][minIndex]+" - "+validate[formname][i][maxIndex]+") ");
                                }
                                break;
                            case'isbefore':
                                compareTo=form[validate[formname][i][compareToIndex]];
                                if(typeof compareTo!='undefined'){
                                    if(trim(compareTo.value)!=''||(validate[formname][i][allowblank]!='true')){
                                        date2=trim(compareTo.value);
                                        date1=trim(form[validate[formname][i][nameIndex]].value);
                                        if(trim(date1).length!=0&&!isBefore(date1,date2)){
                                            isError=true;
                                            add_error_style(formname,validate[formname][i][nameIndex],validate[formname][i][msgIndex]+"("+date1+") "+SUGAR.language.get('app_strings','MSG_IS_NOT_BEFORE')+' '+date2);
                                        }
                                    }
                                }
                                break;
                            case'binarydep':
                                compareTo=form[validate[formname][i][compareToIndex]];
                                if(typeof compareTo!='undefined'){
                                    item1=trim(form[validate[formname][i][nameIndex]].value);
                                    item2=trim(compareTo.value);
                                    if(!bothExist(item1,item2)){
                                        isError=true;
                                        add_error_style(formname,validate[formname][i][nameIndex],validate[formname][i][msgIndex]);
                                    }
                                }
                                break;
                            case'comparison':
                                compareTo=form[validate[formname][i][compareToIndex]];
                                if(typeof compareTo!='undefined'){
                                    item1=trim(form[validate[formname][i][nameIndex]].value);
                                    item2=trim(compareTo.value);
                                    if(!bothExist(item1,item2)||item1!=item2){
                                        isError=true;
                                        add_error_style(formname,validate[formname][i][nameIndex],validate[formname][i][msgIndex]);
                                    }
                                }
                                break;
                            case'in_array':
                                arr=eval(validate[formname][i][arrIndex]);
                                operator=validate[formname][i][operatorIndex];
                                item1=trim(form[validate[formname][i][nameIndex]].value);
                                if(operator.charAt(0)=='u'){
                                    item1=item1.toUpperCase();
                                    operator=operator.substring(1);
                                }
                                else if(operator.charAt(0)=='l'){
                                    item1=item1.toLowerCase();
                                    operator=operator.substring(1);
                                }
                                for(j=0;j<arr.length;j++){
                                    val=arr[j];
                                    if((operator=="=="&&val==item1)||(operator=="!="&&val!=item1)){
                                        isError=true;
                                        add_error_style(formname,validate[formname][i][nameIndex],invalidTxt+" "+validate[formname][i][msgIndex]);
                                    }
                                }
                                break;
                            case'verified':
                                if(trim(form[validate[formname][i][nameIndex]].value)=='false'){
                                    isError=true;
                                }
                                break;
                        }
                    }
                }
            }
        }
    }
    if(formsWithFieldLogic){
        var invalidLogic=false;
        if(formsWithFieldLogic.min&&formsWithFieldLogic.max&&formsWithFieldLogic._default){
            var showErrorsOn={
                min:{
                    value:'min',
                    show:false,
                    obj:formsWithFieldLogic.min.value
                },
                max:{
                    value:'max',
                    show:false,
                    obj:formsWithFieldLogic.max.value
                },
                _default:{
                    value:'default',
                    show:false,
                    obj:formsWithFieldLogic._default.value
                },
                len:{
                    value:'len',
                    show:false,
                    obj:parseInt(formsWithFieldLogic.len.value)
                }
            };
        
            var min=(formsWithFieldLogic.min.value!='')?parseInt(formsWithFieldLogic.min.value):'undef';
            var max=(formsWithFieldLogic.max.value!='')?parseInt(formsWithFieldLogic.max.value):'undef';
            var _default=(formsWithFieldLogic._default.value!='')?parseInt(formsWithFieldLogic._default.value):'undef';
            for(var i in showErrorsOn){
                if(showErrorsOn[i].value!='len'&&showErrorsOn[i].obj.length>showErrorsOn.len.obj){
                    invalidLogic=true;
                    showErrorsOn[i].show=true;
                    showErrorsOn.len.show=true;
                }
            }
            if(min!='undef'&&max!='undef'&&_default!='undef'){
                if(!inRange(_default,min,max)){
                    invalidLogic=true;
                    showErrorsOn.min.show=true;
                    showErrorsOn.max.show=true;
                    showErrorsOn._default.show=true;
                }
            }
            if(min!='undef'&&max!='undef'&&min>max){
                invalidLogic=true;
                showErrorsOn.min.show=true;
                showErrorsOn.max.show=true;
            }
            if(min!='undef'&&_default!='undef'&&_default<min){
                invalidLogic=true;
                showErrorsOn.min.show=true;
                showErrorsOn._default.show=true;
            }
            if(max!='undef'&&_default!='undef'&&_default>max){
                invalidLogic=true;
                showErrorsOn.max.show=true;
                showErrorsOn._default.show=true;
            }
            if(invalidLogic){
                isError=true;
                for(var error in showErrorsOn)
                    if(showErrorsOn[error].show)
                        add_error_style(formname,showErrorsOn[error].value,formsWithFieldLogic.msg);
            }
            else if(!isError)
                formsWithFieldLogic=null;
        }
    }
    if(formWithPrecision){
        if(!isValidPrecision(formWithPrecision.float.value,formWithPrecision.precision.value)){
            isError=true;
            add_error_style(formname,'default',SUGAR.language.get('app_strings','ERR_COMPATIBLE_PRECISION_VALUE'));
        }else if(!isError){
            isError=false;
        }
    }
    if(isError==true){
        var nw,ne,sw,se;
        if(self.pageYOffset)

        {
            nwX=self.pageXOffset;
            seX=self.innerWidth;
            nwY=self.pageYOffset;
            seY=self.innerHeight;
        }
        else if(document.documentElement&&document.documentElement.scrollTop)
        {
            nwX=document.documentElement.scrollLeft;
            seX=document.documentElement.clientWidth;
            nwY=document.documentElement.scrollTop;
            seY=document.documentElement.clientHeight;
        }
        else if(document.body)
        {
            nwX=document.body.scrollLeft;
            seX=document.body.clientWidth;
            nwY=document.body.scrollTop;
            seY=document.body.clientHeight;
        }
        var inView=true;
        for(var wp=0;wp<inputsWithErrors.length;wp++){
            var elementCoor=findElementPos(inputsWithErrors[wp]);
            if(!(elementCoor.x>=nwX&&elementCoor.y>=nwY&&elementCoor.x<=seX&&elementCoor.y<=seY)){
                inView=false;
                scrollToTop=elementCoor.y-75;
                scrollToLeft=elementCoor.x-75;
            }
            else{
                break;
            }
        }
        if(!inView)window.scrollTo(scrollToTop,scrollToLeft);
        return false;
    }
    return true;
}
var marked_row=new Array;
function setPointer(theRow,theRowNum,theAction,theDefaultColor,thePointerColor,theMarkColor){
    var theCells=null;
    if((thePointerColor==''&&theMarkColor=='')||typeof(theRow.style)=='undefined'){
        return false;
    }
    if(typeof(document.getElementsByTagName)!='undefined'){
        theCells=theRow.getElementsByTagName('td');
    }
    else if(typeof(theRow.cells)!='undefined'){
        theCells=theRow.cells;
    }
    else{
        return false;
    }
    var rowCellsCnt=theCells.length;
    var domDetect=null;
    var currentColor=null;
    var newColor=null;
    if(typeof(window.opera)=='undefined'&&typeof(theCells[0].getAttribute)!='undefined'){
        currentColor=theCells[0].getAttribute('bgcolor');
        domDetect=true;
    }
    else{
        currentColor=theCells[0].style.backgroundColor;
        domDetect=false;
    }
    if(currentColor==''||(currentColor!=null&&(currentColor.toLowerCase()==theDefaultColor.toLowerCase()))){
        if(theAction=='over'&&thePointerColor!=''){
            newColor=thePointerColor;
        }
        else if(theAction=='click'&&theMarkColor!=''){
            newColor=theMarkColor;
            marked_row[theRowNum]=true;
        }
    }
    else if(currentColor!=null&&(currentColor.toLowerCase()==thePointerColor.toLowerCase())&&(typeof(marked_row[theRowNum])=='undefined'||!marked_row[theRowNum])){
        if(theAction=='out'){
            newColor=theDefaultColor;
        }
        else if(theAction=='click'&&theMarkColor!=''){
            newColor=theMarkColor;
            marked_row[theRowNum]=true;
        }
    }
    else if(currentColor!=null&&(currentColor.toLowerCase()==theMarkColor.toLowerCase())){
        if(theAction=='click'){
            newColor=(thePointerColor!='')?thePointerColor:theDefaultColor;
            marked_row[theRowNum]=(typeof(marked_row[theRowNum])=='undefined'||!marked_row[theRowNum])?true:null;
        }
    }
    if(newColor){
        var c=null;
        if(domDetect){
            for(c=0;c<rowCellsCnt;c++){
                theCells[c].setAttribute('bgcolor',newColor,0);
            }
        }
        else{
            for(c=0;c<rowCellsCnt;c++){
                theCells[c].style.backgroundColor=newColor;
            }
        }
    }
    return true;
}
function goToUrl(selObj,goToLocation){
    eval("document.location.href = '"+goToLocation+"pos="+selObj.options[selObj.selectedIndex].value+"'");
}
var json_objects=new Object();
function getXMLHTTPinstance(){
    var xmlhttp=false;
    var userAgent=navigator.userAgent.toLowerCase();
    if(userAgent.indexOf("msie")!=-1&&userAgent.indexOf("mac")==-1&&userAgent.indexOf("opera")==-1){
        var version=navigator.appVersion.match(/MSIE (.\..)/)[1];
        if(version>=5.5){
            try{
                xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch(e){
                try{
                    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch(E){
                    xmlhttp=false;
                }
            }
        }
    }
    if(!xmlhttp&&typeof XMLHttpRequest!='undefined'){
        xmlhttp=new XMLHttpRequest();
    }
    return xmlhttp;
}
var global_xmlhttp=getXMLHTTPinstance();
function http_fetch_sync(url,post_data){
    global_xmlhttp=getXMLHTTPinstance();
    var method='GET';
    if(typeof(post_data)!='undefined')method='POST';
    try{
        global_xmlhttp.open(method,url,false);
    }
    catch(e){
        alert('message:'+e.message+":url:"+url);
    }
    if(method=='POST'){
        global_xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    }
    global_xmlhttp.send(post_data);
    var args={
        "responseText":global_xmlhttp.responseText,
        "responseXML":global_xmlhttp.responseXML,
        "request_id":request_id
    };
    
    return args;
}
function http_fetch_async(url,callback,request_id,post_data){
    var method='GET';
    if(typeof(post_data)!='undefined'){
        method='POST';
    }
    try{
        global_xmlhttp.open(method,url,true);
    }
    catch(e){
        alert('message:'+e.message+":url:"+url);
    }
    if(method=='POST'){
        global_xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
    }
    global_xmlhttp.onreadystatechange=function(){
        if(global_xmlhttp.readyState==4){
            if(global_xmlhttp.status==200){
                var args={
                    "responseText":global_xmlhttp.responseText,
                    "responseXML":global_xmlhttp.responseXML,
                    "request_id":request_id
                };
                
                callback.call(document,args);
            }
            else{
                alert("There was a problem retrieving the XML data:\n"+global_xmlhttp.statusText);
            }
        }
    }
    global_xmlhttp.send(post_data);
}
function call_json_method(module,action,vars,variable_name,callback){
    global_xmlhttp.open("GET","index.php?entryPoint=json&module="+module+"&action="+action+"&"+vars,true);
    global_xmlhttp.onreadystatechange=function(){
        if(global_xmlhttp.readyState==4){
            if(global_xmlhttp.status==200){
                json_objects[variable_name]=JSON.parse(global_xmlhttp.responseText);
                var respText=JSON.parseNoSecurity(global_xmlhttp.responseText);
                var args={
                    responseText:respText,
                    responseXML:global_xmlhttp.responseXML
                };
                    
                callback.call(document,args);
            }
            else{
                alert("There was a problem retrieving the XML data:\n"+global_xmlhttp.statusText);
            }
        }
    }
    global_xmlhttp.send(null);
}
function insert_at_cursor(field,value){
    if(document.selection){
        field.focus();
        sel=document.selection.createRange();
        sel.text=value;
    }
    else if(field.selectionStart||field.selectionStart=='0'){
        var start_pos=field.selectionStart;
        var end_pos=field.selectionEnd;
        field.value=field.value.substring(0,start_pos)+value+field.value.substring(end_pos,field.value.length);
    }
    else{
        field.value+=value;
    }
}
function checkParentType(type,button){
    if(button==null){
        return;
    }
    if(typeof disabledModules!='undefined'&&typeof(disabledModules[type])!='undefined'){
        button.disabled='disabled';
    }
    else{
        button.disabled=false;
    }
}
function parseDate(input,format){
    date=input.value;
    format=format.replace(/%/g,'');
    sep=format.charAt(1);
    yAt=format.indexOf('Y')
    if(date.match(/^\d{1,2}[\/-]\d{1,2}[\/-]\d{2,4}$/)&&yAt==4){
        if(date.match(/^\d{1}[\/-].*$/))date='0'+date;
        if(date.match(/^\d{2}[\/-]\d{1}[\/-].*$/))date=date.substring(0,3)+'0'+date.substring(3,date.length);
        if(date.match(/^\d{2}[\/-]\d{2}[\/-]\d{2}$/))date=date.substring(0,6)+'20'+date.substring(6,date.length);
    }
    else if(date.match(/^\d{2,4}[\/-]\d{1,2}[\/-]\d{1,2}$/)){
        if(date.match(/^\d{2}[\/-].*$/))date='20'+date;
        if(date.match(/^\d{4}[\/-]\d{1}[\/-].*$/))date=date.substring(0,5)+'0'+date.substring(5,date.length);
        if(date.match(/^\d{4}[\/-]\d{2}[\/-]\d{1}$/))date=date.substring(0,8)+'0'+date.substring(8,date.length);
    }
    else if(date.match(/^\d{4,8}$/)){
        digits=0;
        if(date.match(/^\d{8}$/))digits=8;
        else if(date.match(/\d{6}/))digits=6;
        else if(date.match(/\d{4}/))digits=4;
        else if(date.match(/\d{5}/))digits=5;
        switch(yAt){
            case 0:
                switch(digits){
                    case 4:
                        date='20'+date.substring(0,2)+sep+'0'+date.substring(2,3)+sep+'0'+date.substring(3,4);
                        break;
                    case 5:
                        date='20'+date.substring(0,2)+sep+date.substring(2,4)+sep+'0'+date.substring(4,5);
                        break;
                    case 6:
                        date='20'+date.substring(0,2)+sep+date.substring(2,4)+sep+date.substring(4,6);
                        break;
                    case 8:
                        date=date.substring(0,4)+sep+date.substring(4,6)+sep+date.substring(6,8);
                        break;
                }
                break;
            case 2:
                switch(digits){
                    case 4:
                        date='0'+date.substring(0,1)+sep+'20'+date.substring(1,3)+sep+'0'+date.substring(3,4);
                        break;
                    case 5:
                        date=date.substring(0,2)+sep+'20'+date.substring(2,4)+sep+'0'+date.substring(4,5);
                        break;
                    case 6:
                        date=date.substring(0,2)+sep+'20'+date.substring(2,4)+sep+date.substring(4,6);
                        break;
                    case 8:
                        date=date.substring(0,2)+sep+date.substring(2,6)+sep+date.substring(6,8);
                        break;
                }
            case 4:
                switch(digits){
                    case 4:
                        date='0'+date.substring(0,1)+sep+'0'+date.substring(1,2)+sep+'20'+date.substring(2,4);
                        break;
                    case 5:
                        date='0'+date.substring(0,1)+sep+date.substring(1,3)+sep+'20'+date.substring(3,5);
                        break;
                    case 6:
                        date=date.substring(0,2)+sep+date.substring(2,4)+sep+'20'+date.substring(4,6);
                        break;
                    case 8:
                        date=date.substring(0,2)+sep+date.substring(2,4)+sep+date.substring(4,8);
                        break;
                }
                break;
        }
    }
    date=date.replace(/[\/-]/g,sep);
    input.value=date;
}
function findElementPos(obj){
    var x=0;
    var y=0;
    if(obj.offsetParent){
        while(obj.offsetParent){
            x+=obj.offsetLeft;
            y+=obj.offsetTop;
            obj=obj.offsetParent;
        }
    }
    else if(obj.x&&obj.y){
        y+=obj.y
        x+=obj.x
    }
    return new coordinate(x,y);
}
function getClientDim(){
    var nwX,nwY,seX,seY;
    if(self.pageYOffset)

    {
        nwX=self.pageXOffset;
        seX=self.innerWidth+nwX;
        nwY=self.pageYOffset;
        seY=self.innerHeight+nwY;
    }
    else if(document.documentElement&&document.documentElement.scrollTop)
    {
        nwX=document.documentElement.scrollLeft;
        seX=document.documentElement.clientWidth+nwX;
        nwY=document.documentElement.scrollTop;
        seY=document.documentElement.clientHeight+nwY;
    }
    else if(document.body)
    {
        nwX=document.body.scrollLeft;
        seX=document.body.clientWidth+nwX;
        nwY=document.body.scrollTop;
        seY=document.body.clientHeight+nwY;
    }
    return{
        'nw':new coordinate(nwX,nwY),
        'se':new coordinate(seX,seY)
    };
    
}
function freezeEvent(e){
    if(e){
        if(e.preventDefault)e.preventDefault();
        e.returnValue=false;
        e.cancelBubble=true;
        if(e.stopPropagation)e.stopPropagation();
        return false;
    }
}
function coordinate(_x,_y){
    var x=_x;
    var y=_y;
    this.add=add;
    this.sub=sub;
    this.x=x;
    this.y=y;
    function add(rh){
        return new position(this.x+rh.x,this.y+rh.y);
    }
    function sub(rh){
        return new position(this.x+rh.x,this.y+rh.y);
    }
}
function sendAndRetrieve(theForm,theDiv,loadingStr){
    function success(data){
        document.getElementById(theDiv).innerHTML=data.responseText;
        ajaxStatus.hideStatus();
    }
    if(typeof loadingStr=='undefined')SUGAR.language.get('app_strings','LBL_LOADING');
    ajaxStatus.showStatus(loadingStr);
    YAHOO.util.Connect.setForm(theForm);
    var cObj=YAHOO.util.Connect.asyncRequest('POST','index.php',{
        success:success,
        failure:success
    });
    return false;
}
function sendAndRedirect(theForm,loadingStr,redirect_location){
    function success(data){
        if(redirect_location){
            location.href=redirect_location;
        }
        ajaxStatus.hideStatus();
    }
    if(typeof loadingStr=='undefined')SUGAR.language.get('app_strings','LBL_LOADING');
    ajaxStatus.showStatus(loadingStr);
    YAHOO.util.Connect.setForm(theForm);
    var cObj=YAHOO.util.Connect.asyncRequest('POST','index.php',{
        success:success,
        failure:success
    });
    return false;
}
function saveForm(theForm,theDiv,loadingStr){
    if(check_form(theForm)){
        for(i=0;i<ajaxFormArray.length;i++){
            if(ajaxFormArray[i]==theForm){
                ajaxFormArray.splice(i,1);
            }
        }
        return sendAndRetrieve(theForm,loadingStr,theDiv);
    }
    else
        return false;
}
function saveForms(savingStr,completeStr){
    index=0;
    theForms=ajaxFormArray;
    function success(data){
        var theForm=document.getElementById(ajaxFormArray[0]);
        document.getElementById('multiedit_'+theForm.id).innerHTML=data.responseText;
        var saveAllButton=document.getElementById('ajaxsaveall');
        ajaxFormArray.splice(index,1);
        if(saveAllButton&&ajaxFormArray.length<=1){
            saveAllButton.style.visibility='hidden';
        }
        index++;
        if(index==theForms.length){
            ajaxStatus.showStatus(completeStr);
            window.setTimeout('ajaxStatus.hideStatus();',2000);
            if(saveAllButton)
                saveAllButton.style.visibility='hidden';
        }
    }
    if(typeof savingStr=='undefined')SUGAR.language.get('app_strings','LBL_LOADING');
    ajaxStatus.showStatus(savingStr);
    for(i=0;i<theForms.length;i++){
        var theForm=document.getElementById(theForms[i]);
        if(check_form(theForm.id)){
            theForm.action.value='AjaxFormSave';
            YAHOO.util.Connect.setForm(theForm);
            var cObj=YAHOO.util.Connect.asyncRequest('POST','index.php',{
                success:success,
                failure:success
            });
        }else{
            ajaxStatus.hideStatus();
        }
        lastSubmitTime=lastSubmitTime-2000;
    }
    return false;
}
function sugarListView(){}
sugarListView.prototype.confirm_action=function(del){
    if(del==1){
        return confirm(SUGAR.language.get('app_strings','NTC_DELETE_CONFIRMATION_NUM')+sugarListView.get_num_selected()+SUGAR.language.get('app_strings','NTC_DELETE_SELECTED_RECORDS'));
    }
    else{
        return confirm(SUGAR.language.get('app_strings','NTC_UPDATE_CONFIRMATION_NUM')+sugarListView.get_num_selected()+SUGAR.language.get('app_strings','NTC_DELETE_SELECTED_RECORDS'));
    }
}
sugarListView.get_num_selected=function(){
    if(typeof document.MassUpdate!='undefined'){
        the_form=document.MassUpdate;
        for(wp=0;wp<the_form.elements.length;wp++){
            if(typeof the_form.elements[wp].name!='undefined'&&the_form.elements[wp].name=='selectCount[]'){
                return the_form.elements[wp].value;
            }
        }
    }
    return 0;
}
sugarListView.update_count=function(count,add){
    if(typeof document.MassUpdate!='undefined'){
        the_form=document.MassUpdate;
        for(wp=0;wp<the_form.elements.length;wp++){
            if(typeof the_form.elements[wp].name!='undefined'&&the_form.elements[wp].name=='selectCount[]'){
                if(add){
                    the_form.elements[wp].value=parseInt(the_form.elements[wp].value)+count;
                }
                else the_form.elements[wp].value=count;
            }
        }
    }
}
sugarListView.prototype.use_external_mail_client=function(no_record_txt){
    selected_records=sugarListView.get_checks_count();
    if(selected_records<1){
        alert(no_record_txt);
    }else{
        location.href='mailto:';
    }
    return false;
}
sugarListView.prototype.send_form_for_emails=function(select,currentModule,action,no_record_txt,action_module,totalCount,totalCountError){
    if(document.MassUpdate.select_entire_list.value==1){
        if(totalCount>10){
            alert(totalCountError);
            return;
        }
        select=false;
    }
    else if(document.MassUpdate.massall.checked==true)
        select=false;else
        select=true;
    sugarListView.get_checks();
    var newForm=document.createElement('form');
    newForm.method='post';
    newForm.action=action;
    newForm.name='newForm';
    newForm.id='newForm';
    var uidTa=document.createElement('textarea');
    uidTa.name='uid';
    uidTa.style.display='none';
    if(select){
        uidTa.value=document.MassUpdate.uid.value;
    }
    else{
        inputs=document.MassUpdate.elements;
        ar=new Array();
        for(i=0;i<inputs.length;i++){
            if(inputs[i].name=='mass[]'&&inputs[i].checked&&typeof(inputs[i].value)!='function'){
                ar.push(inputs[i].value);
            }
        }
        uidTa.value=ar.join(',');
    }
    if(uidTa.value==''){
        alert(no_record_txt);
        return false;
    }
    var selectedArray=uidTa.value.split(",");
    if(selectedArray.length>10){
        alert(totalCountError);
        return;
    }
    newForm.appendChild(uidTa);
    var moduleInput=document.createElement('input');
    moduleInput.name='module';
    moduleInput.type='hidden';
    moduleInput.value=currentModule;
    newForm.appendChild(moduleInput);
    var actionInput=document.createElement('input');
    actionInput.name='action';
    actionInput.type='hidden';
    actionInput.value='Compose';
    newForm.appendChild(actionInput);
    if(typeof action_module!='undefined'&&action_module!=''){
        var actionModule=document.createElement('input');
        actionModule.name='action_module';
        actionModule.type='hidden';
        actionModule.value=action_module;
        newForm.appendChild(actionModule);
    }
    if(typeof return_info!='undefined'&&return_info!=''){
        var params=return_info.split('&');
        if(params.length>0){
            for(var i=0;i<params.length;i++){
                if(params[i].length>0){
                    var param_nv=params[i].split('=');
                    if(param_nv.length==2){
                        returnModule=document.createElement('input');
                        returnModule.name=param_nv[0];
                        returnModule.type='hidden';
                        returnModule.value=param_nv[1];
                        newForm.appendChild(returnModule);
                    }
                }
            }
        }
    }
    document.MassUpdate.parentNode.appendChild(newForm);
    newForm.submit();
    document.MassUpdate.uid.value='';
    return false;
}
sugarListView.prototype.send_form=function(select,currentModule,action,no_record_txt,action_module,return_info){
    if(document.MassUpdate.select_entire_list.value==1){
        var href=action;
        if(action.indexOf('?')!=-1)
            href+='&module='+currentModule;else
            href+='?module='+currentModule;
        if(return_info)
            href+=return_info;
        var newForm=document.createElement('form');
        newForm.method='post';
        newForm.action=href;
        newForm.name='newForm';
        newForm.id='newForm';
        var postTa=document.createElement('textarea');
        postTa.name='current_post';
        postTa.value=document.MassUpdate.current_query_by_page.value;
        postTa.style.display='none';
        newForm.appendChild(postTa);
        document.MassUpdate.parentNode.appendChild(newForm);
        newForm.submit();
        return;
    }
    else if(document.MassUpdate.massall.checked==true)
        select=false;else
        select=true;
    sugarListView.get_checks();
    var newForm=document.createElement('form');
    newForm.method='post';
    newForm.action=action;
    newForm.name='newForm';
    newForm.id='newForm';
    var uidTa=document.createElement('textarea');
    uidTa.name='uid';
    uidTa.style.display='none';
    if(select){
        uidTa.value=document.MassUpdate.uid.value;
    }
    else{
        inputs=document.MassUpdate.elements;
        ar=new Array();
        for(i=0;i<inputs.length;i++){
            if(inputs[i].name=='mass[]'&&inputs[i].checked&&typeof(inputs[i].value)!='function'){
                ar.push(inputs[i].value);
            }
        }
        uidTa.value=ar.join(',');
    }
    if(uidTa.value==''){
        alert(no_record_txt);
        return false;
    }
    newForm.appendChild(uidTa);
    var moduleInput=document.createElement('input');
    moduleInput.name='module';
    moduleInput.type='hidden';
    moduleInput.value=currentModule;
    newForm.appendChild(moduleInput);
    var actionInput=document.createElement('input');
    actionInput.name='action';
    actionInput.type='hidden';
    actionInput.value='index';
    newForm.appendChild(actionInput);
    if(typeof action_module!='undefined'&&action_module!=''){
        var actionModule=document.createElement('input');
        actionModule.name='action_module';
        actionModule.type='hidden';
        actionModule.value=action_module;
        newForm.appendChild(actionModule);
    }
    if(typeof return_info!='undefined'&&return_info!=''){
        var params=return_info.split('&');
        if(params.length>0){
            for(var i=0;i<params.length;i++){
                if(params[i].length>0){
                    var param_nv=params[i].split('=');
                    if(param_nv.length==2){
                        returnModule=document.createElement('input');
                        returnModule.name=param_nv[0];
                        returnModule.type='hidden';
                        returnModule.value=param_nv[1];
                        newForm.appendChild(returnModule);
                    }
                }
            }
        }
    }
    document.MassUpdate.parentNode.appendChild(newForm);
    newForm.submit();
    document.MassUpdate.uid.value='';
    return false;
}
sugarListView.get_checks_count=function(){
    ar=new Array();
    inputs=document.MassUpdate.elements;
    for(i=0;i<inputs.length;i++){
        if(inputs[i].name=='mass[]'){
            ar[inputs[i].value]=(inputs[i].checked)?1:0;
        }
    }
    uids=new Array();
    for(i in ar){
        if((typeof(ar[i])!='function')&&ar[i]==1){
            uids.push(i);
        }
    }
    return uids.length;
}
sugarListView.get_checks=function(){
    ar=new Array();
    if(document.MassUpdate.uid.value!=''){
        oldUids=document.MassUpdate.uid.value.split(',');
        for(uid in oldUids){
            if(typeof(oldUids[uid])!='function'){
                ar[oldUids[uid]]=1;
            }
        }
    }
    inputs=document.MassUpdate.elements;
    for(i=0;i<inputs.length;i++){
        if(inputs[i].name=='mass[]'){
            ar[inputs[i].value]=(inputs[i].checked)?1:0;
        }
    }
    uids=new Array();
    for(i in ar){
        if(typeof(ar[i])!='function'&&ar[i]==1){
            uids.push(i);
        }
    }
    document.MassUpdate.uid.value=uids.join(',');
    if(uids.length==0)return false;
    return true;
}
sugarListView.prototype.order_checks=function(order,orderBy,moduleString){
    checks=sugarListView.get_checks();
    eval('document.MassUpdate.'+moduleString+'.value = orderBy');
    document.MassUpdate.lvso.value=order;
    if(typeof document.MassUpdate.massupdate!='undefined'){
        document.MassUpdate.massupdate.value='false';
    }
    document.MassUpdate.action.value=document.MassUpdate.return_action.value;
    document.MassUpdate.return_module.value='';
    document.MassUpdate.return_action.value='';
    document.MassUpdate.submit();
    return!checks;
}
sugarListView.prototype.save_checks=function(offset,moduleString){
    checks=sugarListView.get_checks();
    eval('document.MassUpdate.'+moduleString+'.value = offset');
    if(typeof document.MassUpdate.massupdate!='undefined'){
        document.MassUpdate.massupdate.value='false';
    }
    document.MassUpdate.action.value=document.MassUpdate.return_action.value;
    document.MassUpdate.return_module.value='';
    document.MassUpdate.return_action.value='';
    document.MassUpdate.submit();
    return!checks;
}
sugarListView.prototype.check_item=function(cb,form){
    if(cb.checked){
        sugarListView.update_count(1,true);
    }else{
        sugarListView.update_count(-1,true);
        if(typeof form!='undefined'&&form!=null){
            sugarListView.prototype.updateUid(cb,form);
        }
    }
}
sugarListView.prototype.updateUid=function(cb,form){
    if(form.name=='MassUpdate'&&form.uid&&form.uid.value&&cb.value&&form.uid.value.indexOf(cb.value)!=-1){
        if(form.uid.value.indexOf(','+cb.value)!=-1){
            form.uid.value=form.uid.value.replace(','+cb.value,'');
        }else if(form.uid.value.indexOf(cb.value+',')!=-1){
            form.uid.value=form.uid.value.replace(cb.value+',','');
        }else if(form.uid.value.indexOf(cb.value)!=-1){
            form.uid.value=form.uid.value.replace(cb.value,'');
        }
    }
}
sugarListView.prototype.check_entire_list=function(form,field,value,list_count){
    count=0;
    document.MassUpdate.massall.checked=true;
    document.MassUpdate.massall.disabled=true;
    for(i=0;i<form.elements.length;i++){
        if(form.elements[i].name==field){
            if(form.elements[i].checked!=value)count++;
            form.elements[i].checked=value;
            form.elements[i].disabled=true;
        }
    }
    document.MassUpdate.select_entire_list.value=1;
    sugarListView.update_count(list_count,false);
}
sugarListView.prototype.check_all=function(form,field,value,pageTotal){
    count=0;
    document.MassUpdate.massall.checked=value;
    if(document.MassUpdate.select_entire_list&&document.MassUpdate.select_entire_list.value==1)
        document.MassUpdate.massall.disabled=true;else
        document.MassUpdate.massall.disabled=false;
    for(i=0;i<form.elements.length;i++){
        if(form.elements[i].name==field){
            form.elements[i].disabled=false;
            if(form.elements[i].checked!=value)
                count++;
            form.elements[i].checked=value;
            if(!value){
                sugarListView.prototype.updateUid(form.elements[i],form);
            }
        }
    }
    if(pageTotal>=0)
        sugarListView.update_count(pageTotal);
    else if(value)
        sugarListView.update_count(count,true);else
        sugarListView.update_count(-1*count,true);
}
sugarListView.check_all=sugarListView.prototype.check_all;
sugarListView.confirm_action=sugarListView.prototype.confirm_action;
sugarListView.prototype.check_boxes=function(){
    var inputsCount=0;
    var checkedCount=0;
    var existing_onload=window.onload;
    var theForm=document.MassUpdate;
    inputs_array=theForm.elements;
    if(typeof theForm.uid.value!='undefined'&&theForm.uid.value!=""){
        checked_items=theForm.uid.value.split(",");
        if(theForm.select_entire_list.value==1)
            document.MassUpdate.massall.disabled=true;
        for(wp=0;wp<inputs_array.length;wp++){
            if(inputs_array[wp].name=="mass[]"){
                inputsCount++;
                if(theForm.select_entire_list.value==1){
                    inputs_array[wp].checked=true;
                    inputs_array[wp].disabled=true;
                    checkedCount++;
                }
                else{
                    for(i in checked_items){
                        if(inputs_array[wp].value==checked_items[i]){
                            checkedCount++;
                            inputs_array[wp].checked=true;
                        }
                    }
                }
            }
        }
        if(theForm.select_entire_list.value==0)
            sugarListView.update_count(checked_items.length);else
            sugarListView.update_count(0,true);
    }
    else{
        for(wp=0;wp<inputs_array.length;wp++){
            if(inputs_array[wp].name=="mass[]"){
                inputs_array[wp].checked=false;
                inputs_array[wp].disabled=false;
            }
        }
        if(document.MassUpdate.massall){
            document.MassUpdate.massall.checked=false;
            document.MassUpdate.massall.disabled=false;
        }
        sugarListView.update_count(0)
    }
    if(checkedCount>0&&checkedCount==inputsCount)
        document.MassUpdate.massall.checked=true;
}
function check_used_email_templates(){
    var ids=document.MassUpdate.uid.value;
    var call_back={
        success:function(r){
            if(r.responseText!=''){
                if(!confirm(SUGAR.language.get('app_strings','NTC_TEMPLATES_IS_USED')+r.responseText)){
                    return false;
                }
            }
            document.MassUpdate.submit();
            return false;
        }
    };

    url="index.php?module=EmailTemplates&action=CheckDeletable&from=ListView&to_pdf=1&records="+ids;
    YAHOO.util.Connect.asyncRequest('POST',url,call_back,null);
}
sugarListView.prototype.send_mass_update=function(mode,no_record_txt,del){
    formValid=check_form('MassUpdate');
    if(!formValid)return false;
    if(document.MassUpdate.select_entire_list&&document.MassUpdate.select_entire_list.value==1)
        mode='entire';else
        mode='selected';
    var ar=new Array();
    switch(mode){
        case'selected':
            for(wp=0;wp<document.MassUpdate.elements.length;wp++){
                if(typeof document.MassUpdate.elements[wp].name!='undefined'&&document.MassUpdate.elements[wp].name=='mass[]'&&document.MassUpdate.elements[wp].checked){
                    ar.push(document.MassUpdate.elements[wp].value);
                }
            }
            if(document.MassUpdate.uid.value!='')document.MassUpdate.uid.value+=',';
            document.MassUpdate.uid.value+=ar.join(',');
            if(document.MassUpdate.uid.value==''){
                alert(no_record_txt);
                return false;
            }
            break;
        case'entire':
            var entireInput=document.createElement('input');
            entireInput.name='entire';
            entireInput.type='hidden';
            entireInput.value='index';
            document.MassUpdate.appendChild(entireInput);
            break;
    }
    if(!sugarListView.confirm_action(del))
        return false;
    if(del==1){
        var deleteInput=document.createElement('input');
        deleteInput.name='Delete';
        deleteInput.type='hidden';
        deleteInput.value=true;
        document.MassUpdate.appendChild(deleteInput);
        if(document.MassUpdate.module!='undefined'&&document.MassUpdate.module.value=='EmailTemplates'){
            check_used_email_templates();
            return false;
        }
    }
    document.MassUpdate.submit();
    return false;
}
sugarListView.prototype.clear_all=function(){
    document.MassUpdate.uid.value='';
    document.MassUpdate.select_entire_list.value=0;
    sugarListView.check_all(document.MassUpdate,'mass[]',false);
    document.MassUpdate.massall.checked=false;
    document.MassUpdate.massall.disabled=false;
    sugarListView.update_count(0);
}
sListView=new sugarListView();
function unformatNumber(n,num_grp_sep,dec_sep){
    var x=unformatNumberNoParse(n,num_grp_sep,dec_sep);
    x=x.toString();
    if(x.length>0){
        return parseFloat(x);
    }
    return'';
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
function formatNumber(n,num_grp_sep,dec_sep,round,precision){
    if(typeof num_grp_sep=='undefined'||typeof dec_sep=='undefined')return n;
    n=n?n.toString():'';
    if(n.split)n=n.split('.');else return n;
    if(n.length>2)return n.join('.');
    if(typeof round!='undefined'){
        if(round>0&&n.length>1){
            n[1]=parseFloat('0.'+n[1]);
            n[1]=Math.round(n[1]*Math.pow(10,round))/Math.pow(10,round);
            n[1]=n[1].toString().split('.')[1];
        }
        if(round<=0){
            n[0]=Math.round(parseInt(n[0])*Math.pow(10,round))/Math.pow(10,round);
            n[1]='';
        }
    }
    if(typeof precision!='undefined'&&precision>=0){
        if(n.length>1&&typeof n[1]!='undefined')n[1]=n[1].substring(0,precision);else n[1]='';
        if(n[1].length<precision){
            for(var wp=n[1].length;wp<precision;wp++)n[1]+='0';
        }
    }
    regex=/(\d+)(\d{3})/;
    while(num_grp_sep!=''&&regex.test(n[0]))n[0]=n[0].replace(regex,'$1'+num_grp_sep+'$2');
    return n[0]+(n.length>1&&n[1]!=''?dec_sep+n[1]:'');
}
SUGAR.ajaxStatusClass=function(){};
    
SUGAR.ajaxStatusClass.prototype.statusDiv=null;
SUGAR.ajaxStatusClass.prototype.oldOnScroll=null;
SUGAR.ajaxStatusClass.prototype.shown=false;
SUGAR.ajaxStatusClass.prototype.positionStatus=function(){
    this.statusDiv.style.top=document.body.scrollTop+8+'px';
    statusDivRegion=YAHOO.util.Dom.getRegion(this.statusDiv);
    statusDivWidth=statusDivRegion.right-statusDivRegion.left;
    this.statusDiv.style.left=YAHOO.util.Dom.getViewportWidth()/2-statusDivWidth/2+'px';
}
SUGAR.ajaxStatusClass.prototype.createStatus=function(text){
    statusDiv=document.createElement('div');
    statusDiv.className='dataLabel';
    statusDiv.style.background='#ffffff';
    statusDiv.style.color='#c60c30';
    statusDiv.style.position='absolute';
    statusDiv.style.opacity=.8;
    statusDiv.style.filter='alpha(opacity=80)';
    statusDiv.id='ajaxStatusDiv';
    document.body.appendChild(statusDiv);
    this.statusDiv=document.getElementById('ajaxStatusDiv');
}
SUGAR.ajaxStatusClass.prototype.showStatus=function(text){
    if(!this.statusDiv){
        this.createStatus(text);
    }
    else{
        this.statusDiv.style.display='';
    }
    this.statusDiv.innerHTML='&nbsp;<b>'+text+'</b>&nbsp;';
    this.positionStatus();
    if(!this.shown){
        this.shown=true;
        this.statusDiv.style.display='';
        if(window.onscroll)this.oldOnScroll=window.onscroll;
        window.onscroll=this.positionStatus;
    }
}
SUGAR.ajaxStatusClass.prototype.hideStatus=function(text){
    if(!this.shown)return;
    this.shown=false;
    if(this.oldOnScroll)window.onscroll=this.oldOnScroll;else window.onscroll='';
    this.statusDiv.style.display='none';
}
SUGAR.ajaxStatusClass.prototype.flashStatus=function(text,time){
    this.showStatus(text);
    window.setTimeout('ajaxStatus.hideStatus();',time);
}
var ajaxStatus=new SUGAR.ajaxStatusClass();
SUGAR.unifiedSearchAdvanced=function(){
    var usa_div;
    var usa_img;
    var usa_open;
    var usa_content;
    var anim_open;
    var anim_close;
    return{
        init:function(){
            SUGAR.unifiedSearchAdvanced.usa_div=document.getElementById('unified_search_advanced_div');
            SUGAR.unifiedSearchAdvanced.usa_img=document.getElementById('unified_search_advanced_img');
            if(!SUGAR.unifiedSearchAdvanced.usa_div||!SUGAR.unifiedSearchAdvanced.usa_img)return;
            SUGAR.unifiedSearchAdvanced.anim_open=new YAHOO.util.Anim('unified_search_advanced_div',{
                height:{
                    to:300
                }
            });
            SUGAR.unifiedSearchAdvanced.anim_open.duration=0.75;
            SUGAR.unifiedSearchAdvanced.anim_close=new YAHOO.util.Anim('unified_search_advanced_div',{
                height:{
                    to:1
                }
            });
            SUGAR.unifiedSearchAdvanced.anim_close.duration=0.75;
            SUGAR.unifiedSearchAdvanced.anim_close.onComplete.subscribe(function(){
                SUGAR.unifiedSearchAdvanced.usa_div.style.display='none'
            });
            SUGAR.unifiedSearchAdvanced.usa_img._x=YAHOO.util.Dom.getX(SUGAR.unifiedSearchAdvanced.usa_img);
            SUGAR.unifiedSearchAdvanced.usa_img._y=YAHOO.util.Dom.getY(SUGAR.unifiedSearchAdvanced.usa_img);
            SUGAR.unifiedSearchAdvanced.usa_open=false;
            SUGAR.unifiedSearchAdvanced.usa_content=null;
            YAHOO.util.Event.addListener('unified_search_advanced_img','click',SUGAR.unifiedSearchAdvanced.get_content);
        },
        get_content:function(e){
            if(SUGAR.unifiedSearchAdvanced.usa_content==null){
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
                var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=1&module=Home&action=UnifiedSearch&usa_form=true',{
                    success:SUGAR.unifiedSearchAdvanced.animate,
                    failure:SUGAR.unifiedSearchAdvanced.animate
                },null);
            }
            else SUGAR.unifiedSearchAdvanced.animate();
        },
        animate:function(data){
            ajaxStatus.hideStatus();
            if(data){
                SUGAR.unifiedSearchAdvanced.usa_content=data.responseText;
                SUGAR.unifiedSearchAdvanced.usa_div.innerHTML=SUGAR.unifiedSearchAdvanced.usa_content;
            }
            if(SUGAR.unifiedSearchAdvanced.usa_open){
                document.UnifiedSearch.advanced.value='false';
                SUGAR.unifiedSearchAdvanced.anim_close.animate();
            }
            else{
                document.UnifiedSearch.advanced.value='true';
                SUGAR.unifiedSearchAdvanced.usa_div.style.display='';
                YAHOO.util.Dom.setX(SUGAR.unifiedSearchAdvanced.usa_div,SUGAR.unifiedSearchAdvanced.usa_img._x-90);
                YAHOO.util.Dom.setY(SUGAR.unifiedSearchAdvanced.usa_div,SUGAR.unifiedSearchAdvanced.usa_img._y+15);
                SUGAR.unifiedSearchAdvanced.anim_open.animate();
            }
            SUGAR.unifiedSearchAdvanced.usa_open=!SUGAR.unifiedSearchAdvanced.usa_open;
            return false;
        },
        checkUsaAdvanced:function(){
            if(document.UnifiedSearch.advanced.value=='true'){
                document.UnifiedSearchAdvanced.query_string.value=document.UnifiedSearch.query_string.value;
                document.UnifiedSearchAdvanced.submit();
                return false;
            }
            return true;
        }
    };

}();
if(typeof YAHOO!='undefined')YAHOO.util.Event.addListener(window,'load',SUGAR.unifiedSearchAdvanced.init);
SUGAR.ui={
    toggleHeader:function(){
        var h=document.getElementById('header');
        if(h!=null){
            if(h!=null){
                if(h.style.display=='none'){
                    h.style.display='';
                }else{
                    h.style.display='none';
                }
            }
        }else{
            alert(SUGAR.language.get("app_strings","ERR_NO_HEADER_ID"));
        }
    }
};

SUGAR.util=function(){
    var additionalDetailsCache;
    var additionalDetailsCalls;
    var additionalDetailsRpcCall;
    return{
        evalScript:function(text){
            
            if(isSafari){
                var waitUntilLoaded=function(){
                    SUGAR.evalScript_waitCount--;
                    if(SUGAR.evalScript_waitCount==0){
                        var headElem=document.getElementsByTagName('head')[0];
                        for(var i in SUGAR.evalScript_evalElem){
                            var tmpElem=document.createElement('script');
                            tmpElem.type='text/javascript';
                            tmpElem.text=SUGAR.evalScript_evalElem[i];
                            headElem.appendChild(tmpElem);
                        }
                    }
                };
            
                var tmpElem=document.createElement('div');
                tmpElem.innerHTML=text;
                var results=tmpElem.getElementsByTagName('script');
                if(results==null){
                    return;
                }
                var headElem=document.getElementsByTagName('head')[0];
                var tmpElem=null;
                SUGAR.evalScript_waitCount=0;
                SUGAR.evalScript_evalElem=new Array();
                for(var i in results){
                    if(typeof(results[i])!='object'){
                        continue;
                    };
            
                    tmpElem=document.createElement('script');
                    tmpElem.type='text/javascript';
                    if(results[i].src!=null&&results[i].src!=''){
                        tmpElem.src=results[i].src;
                    }else{
                        SUGAR.evalScript_evalElem[SUGAR.evalScript_evalElem.length]=results[i].text;
                        continue;
                    }
                    tmpElem.addEventListener('load',waitUntilLoaded);
                    SUGAR.evalScript_waitCount++;
                    headElem.appendChild(tmpElem);
                }
                SUGAR.evalScript_waitCount++;
                waitUntilLoaded();
                return;
            }
            var objRegex=/<\s*script([^>]*)>((.|\s|\v|\0)*?)<\s*\/script\s*>/igm;
            var lastIndex=-1;
            var result=objRegex.exec(text);
            
            //console.log(text);
            while(result&&result.index>lastIndex){
                
                lastIndex=result.index
                try{
                    var script=document.createElement('script');
                    script.type='text/javascript';
                    if(result[1].indexOf("src=")>-1){
                        var srcRegex=/.*src=['"]([a-zA-Z0-9\&\/\//:\.\?=]*)['"].*/igm;
                        var srcResult=result[1].replace(srcRegex,'$1');
                        
                        script.src=srcResult;
                    }else{
                        script.text=result[2];
                    }
                    
                    document.body.appendChild(script)
                }
                catch(e){}
                result=objRegex.exec(text);
            }
        },
        getLeftColObj:function(){
            leftColObj=document.getElementById('leftCol');
            while(leftColObj.nodeName!='TABLE'){
                leftColObj=leftColObj.firstChild;
            }
            leftColTable=leftColObj;
            leftColTd=leftColTable.getElementsByTagName('td')[0];
            leftColTdRegion=YAHOO.util.Dom.getRegion(leftColTd);
            leftColTd.style.width=(leftColTdRegion.right-leftColTdRegion.left)+'px';
            return leftColTd;
        },
        fillShortcuts:function(e,shortcutContent){
            return;
        },
        retrieveAndFill:function(url,theDiv,postForm,callback,callbackParam,appendMode){
            if(typeof theDiv=='string'){
                try{
                    theDiv=document.getElementById(theDiv);
                }
                catch(e){
                    return;
                }
            }
            var success=function(data){
                if(typeof theDiv!='undefined'&&theDiv!=null)

                {
                    try{
                        if(typeof appendMode!='undefined'&&appendMode)

                        {
                            theDiv.innerHTML+=data.responseText;
                        }
                        else
                        {
                            theDiv.innerHTML=data.responseText;
                        }
                    }
                    catch(e){
                        return;
                    }
                }
                if(typeof callback!='undefined'&&callback!=null)callback(callbackParam);
            }
            if(typeof postForm=='undefined'||postForm==null){
                var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                    success:success,
                    failure:success
                });
            }
            else{
                YAHOO.util.Connect.setForm(postForm);
                var cObj=YAHOO.util.Connect.asyncRequest('POST',url,{
                    success:success,
                    failure:success
                });
            }
        },
        checkMaxLength:function(){
            var maxLength=this.getAttribute('maxlength');
            var currentLength=this.value.length;
            if(currentLength>maxLength){
                this.value=this.value.substring(0,maxLength);
            }
        },
        setMaxLength:function(){
            var x=document.getElementsByTagName('textarea');
            for(var i=0;i<x.length;i++){
                if(x[i].getAttribute('maxlength')){
                    x[i].onkeyup=x[i].onchange=SUGAR.util.checkMaxLength;
                    x[i].onkeyup();
                }
            }
        },
        getAdditionalDetails:function(bean,id,spanId){
            go=function(){
                oReturn=function(body,caption,width,theme){
                    return overlib(body,CAPTION,caption,STICKY,MOUSEOFF,1000,WIDTH,width,CLOSETEXT,('<img border=0 style="margin-left:2px; margin-right: 2px;" src=themes/'+theme+'/images/close.gif>'),CLOSETITLE,SUGAR.language.get('app_strings','LBL_ADDITIONAL_DETAILS_CLOSE_TITLE'),CLOSECLICK,FGCLASS,'olFgClass',CGCLASS,'olCgClass',BGCLASS,'olBgClass',TEXTFONTCLASS,'olFontClass',CAPTIONFONTCLASS,'olCapFontClass',CLOSEFONTCLASS,'olCloseFontClass',REF,spanId,REFC,'LL',REFX,13);
                }
                success=function(data){
                    eval(data.responseText);
                    SUGAR.util.additionalDetailsCache[spanId]=new Array();
                    SUGAR.util.additionalDetailsCache[spanId]['body']=result['body'];
                    SUGAR.util.additionalDetailsCache[spanId]['caption']=result['caption'];
                    SUGAR.util.additionalDetailsCache[spanId]['width']=result['width'];
                    SUGAR.util.additionalDetailsCache[spanId]['theme']=result['theme'];
                    ajaxStatus.hideStatus();
                    return oReturn(SUGAR.util.additionalDetailsCache[spanId]['body'],SUGAR.util.additionalDetailsCache[spanId]['caption'],SUGAR.util.additionalDetailsCache[spanId]['width'],SUGAR.util.additionalDetailsCache[spanId]['theme']);
                }
                if(typeof SUGAR.util.additionalDetailsCache[spanId]!='undefined')
                    return oReturn(SUGAR.util.additionalDetailsCache[spanId]['body'],SUGAR.util.additionalDetailsCache[spanId]['caption'],SUGAR.util.additionalDetailsCache[spanId]['width'],SUGAR.util.additionalDetailsCache[spanId]['theme']);
                if(typeof SUGAR.util.additionalDetailsCalls[spanId]!='undefined')
                    return;
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
                url='index.php?to_pdf=1&module=Home&action=AdditionalDetailsRetrieve&bean='+bean+'&id='+id;
                SUGAR.util.additionalDetailsCalls[spanId]=YAHOO.util.Connect.asyncRequest('GET',url,{
                    success:success,
                    failure:success
                });
                return false;
            }
            SUGAR.util.additionalDetailsRpcCall=window.setTimeout('go()',250);
        },
        clearAdditionalDetailsCall:function(){
            if(typeof SUGAR.util.additionalDetailsRpcCall=='number')window.clearTimeout(SUGAR.util.additionalDetailsRpcCall);
        }
    };

}();
SUGAR.util.additionalDetailsCache=new Array();
SUGAR.util.additionalDetailsCalls=new Array();
if(typeof YAHOO!='undefined')YAHOO.util.Event.addListener(window,'load',SUGAR.util.setMaxLength);
SUGAR.savedViews=function(){
    var selectedOrderBy;
    var selectedSortOrder;
    var displayColumns;
    var hideTabs;
    var columnsMeta;
    return{
        setChooser:function(){
            var displayColumnsDef=new Array();
            var hideTabsDef=new Array();
            var left_td=document.getElementById('display_tabs_td');
            if(typeof left_td=='undefined'||left_td==null)return;
            var right_td=document.getElementById('hide_tabs_td');
            var displayTabs=left_td.getElementsByTagName('select')[0];
            var hideTabs=right_td.getElementsByTagName('select')[0];
            for(i=0;i<displayTabs.options.length;i++){
                displayColumnsDef.push(displayTabs.options[i].value);
            }
            if(typeof hideTabs!='undefined'){
                for(i=0;i<hideTabs.options.length;i++){
                    hideTabsDef.push(hideTabs.options[i].value);
                }
            }
            document.getElementById('displayColumnsDef').value=displayColumnsDef.join('|');
            document.getElementById('hideTabsDef').value=hideTabsDef.join('|');
        },
        select:function(saved_search_select){
            for(var wp=0;wp<document.search_form.saved_search_select.options.length;wp++){
                if(typeof document.search_form.saved_search_select.options[wp].value!='undefined'&&document.search_form.saved_search_select.options[wp].value==saved_search_select){
                    document.search_form.saved_search_select.selectedIndex=wp;
                    document.search_form.ss_delete.style.display='';
                    document.search_form.ss_update.style.display='';
                }
            }
        },
        saved_search_action:function(action,delete_lang){
            if(action=='delete'){
                if(!confirm(delete_lang))return;
            }
            if(action=='save'){
                if(document.search_form.saved_search_name.value.replace(/^\s*|\s*$/g,'')==''){
                    alert(SUGAR.language.get('app_strings','LBL_SAVED_SEARCH_ERROR'));
                    return;
                }
            }
            if(document.search_form.saved_search_action)
            {
                document.search_form.saved_search_action.value=action;
                document.search_form.search_module.value=document.search_form.module.value;
                document.search_form.module.value='SavedSearch';
            }
            document.search_form.submit();
        },
        shortcut_select:function(selectBox,module){
            selecturl='index.php?module=SavedSearch&search_module='+module+'&action=index&saved_search_select='+selectBox.options[selectBox.selectedIndex].value
            if(typeof(document.getElementById('searchFormTab'))!='undefined'){
                selecturl=selecturl+'&searchFormTab='+document.search_form.searchFormTab.value;
            }
            if(document.getElementById('showSSDIV')&&typeof(document.getElementById('showSSDIV')!='undefined')){
                selecturl=selecturl+'&showSSDIV='+document.getElementById('showSSDIV').value;
            }
            document.location.href=selecturl;
        },
        handleForm:function(){
            SUGAR.tabChooser.movementCallback=function(left_side,right_side){
                while(document.getElementById('orderBySelect').childNodes.length!=0){
                    document.getElementById('orderBySelect').removeChild(document.getElementById('orderBySelect').lastChild);
                }
                var selectedIndex=0;
                var nodeCount=-1;
                for(i in left_side.childNodes){
                    if(typeof left_side.childNodes[i].nodeName!='undefined'&&left_side.childNodes[i].nodeName.toLowerCase()=='option'&&typeof SUGAR.savedViews.columnsMeta[left_side.childNodes[i].value]!='undefined'&&typeof SUGAR.savedViews.columnsMeta[left_side.childNodes[i].value]['sortable']=='undefined'&&SUGAR.savedViews.columnsMeta[left_side.childNodes[i].value]['sortable']!=false){
                        nodeCount++;
                        optionNode=document.createElement('option');
                        optionNode.value=left_side.childNodes[i].value;
                        optionNode.innerHTML=left_side.childNodes[i].innerHTML;
                        document.getElementById('orderBySelect').appendChild(optionNode);
                        if(optionNode.value==SUGAR.savedViews.selectedOrderBy)
                            selectedIndex=nodeCount;
                    }
                }
                document.getElementById('orderBySelect').selectedIndex=selectedIndex;
            };

            SUGAR.tabChooser.movementCallback(document.getElementById('display_tabs_td').getElementsByTagName('select')[0]);
            if(document.search_form.orderBy)
                document.search_form.orderBy.options.value=SUGAR.savedViews.selectedOrderBy;
            if(SUGAR.savedViews.selectedSortOrder=='DESC')document.getElementById('sort_order_desc_radio').checked=true;else document.getElementById('sort_order_asc_radio').checked=true;
        }
    };

}();
SUGAR.searchForm=function(){
    var url;
    return{
        searchFormSelect:function(view,previousView){
            var module=view.split('|')[0];
            var theView=view.split('|')[1];
            var handleDisplay=function(){
                document.search_form.searchFormTab.value=theView;
                patt=module+"(.*)SearchForm$";
                divId=document.search_form.getElementsByTagName('div');
                for(i=0;i<divId.length;i++){
                    if(divId[i].id.match(module)==module){
                        if(divId[i].id.match('SearchForm')=='SearchForm'){
                            if(document.getElementById(divId[i].id).style.display==''){
                                previousTab=divId[i].id.match(patt)[1];
                            }
                            document.getElementById(divId[i].id).style.display='none';
                        }
                    }
                }
                document.getElementById(module+theView+'SearchForm').style.display='';
                if(previousView){
                    thepreviousView=previousView.split('|')[1];
                }
                else{
                    thepreviousView=previousTab;
                }
                thepreviousView=thepreviousView.replace(/_search/,"");
                for(num in document.search_form.elements){
                    if(document.search_form.elements[num]){
                        el=document.search_form.elements[num];
                        pattern="^(.*)_"+thepreviousView+"$";
                        if(typeof el.type!='undefined'&&typeof el.name!='undefined'&&el.name.match(pattern)){
                            advanced_input_name=el.name.match(pattern)[1];
                            advanced_input_name=advanced_input_name+"_"+theView.replace(/_search/,"");
                            if(typeof document.search_form[advanced_input_name]!='undefined')
                                SUGAR.searchForm.copyElement(advanced_input_name,el);
                        }
                    }
                }
            }
            if(document.getElementById(module+theView+'SearchForm').innerHTML==''){
                ajaxStatus.showStatus(SUGAR.language.get('app_strings','LBL_LOADING'));
                var success=function(data){
                    document.getElementById(module+theView+'SearchForm').innerHTML=data.responseText;
                    SUGAR.util.evalScript(data.responseText);
                    if(theView=='saved_views'){
                        if(typeof columnsMeta!='undefined')SUGAR.savedViews.columnsMeta=columnsMeta;
                        if(typeof selectedOrderBy!='undefined')SUGAR.savedViews.selectedOrderBy=selectedOrderBy;
                        if(typeof selectedSortOrder!='undefined')SUGAR.savedViews.selectedSortOrder=selectedSortOrder;
                    }
                    handleDisplay();
                    enableQS(true);
                    ajaxStatus.hideStatus();
                }
                url='index.php?module='+module+'&action=ListView&search_form_only=true&to_pdf=true&search_form_view='+theView;
                var tpl='';
                if(document.getElementById('search_tpl')!=null&&typeof(document.getElementById('search_tpl'))!='undefined'){
                    tpl=document.getElementById('search_tpl').value;
                    if(tpl!=''){
                        url+='&search_tpl='+tpl;
                    }
                }
                if(theView=='saved_views')
                    url+='&displayColumns='+SUGAR.savedViews.displayColumns+'&hideTabs='+SUGAR.savedViews.hideTabs+'&orderBy='+SUGAR.savedViews.selectedOrderBy+'&sortOrder='+SUGAR.savedViews.selectedSortOrder;
                var cObj=YAHOO.util.Connect.asyncRequest('GET',url,{
                    success:success,
                    failure:success
                });
            }
            else{
                handleDisplay();
            }
        },
        copyElement:function(inputName,copyFromElement){
            switch(copyFromElement.type){
                case'select-one':case'text':
                    document.search_form[inputName].value=copyFromElement.value;
                    break;
            }
        },
        clear_form:function(form){
            var newLoc='index.php?action='+form.action.value+'&module='+form.module.value+'&query=true&clear_query=true';
            if(typeof(form.searchFormTab)!='undefined'){
                newLoc+='&searchFormTab='+form.searchFormTab.value;
            }
            var tpl='';
            if(document.getElementById('search_tpl')!=null&&typeof(document.getElementById('search_tpl'))!='undefined'){
                tpl=document.getElementById('search_tpl').value;
                if(tpl!=''){
                    newLoc+='&search_tpl='+tpl;
                }
            }
            if(document.getElementById('saved_search_select').value!='_none'){
                newLoc='index.php?module=SavedSearch&search_module='+form.module.value+'&action=index&saved_search_select=_none';
                if(typeof(document.getElementById('searchFormTab'))!='undefined'){
                    newLoc=newLoc+'&searchFormTab='+document.search_form.searchFormTab.value;
                }
                if(document.getElementById('showSSDIV')&&typeof(document.getElementById('showSSDIV')!='undefined')){
                    newLoc=newLoc+'&showSSDIV='+document.getElementById('showSSDIV').value;
                }
            }
            document.location.href=newLoc;
        }
    };

}();
SUGAR.tabChooser=function(){
    var object_refs=new Array();
    return{
        frozenOptions:[],
        movementCallback:function(left_side,right_side){},
        orderCallback:function(left_side,right_side){},
        freezeOptions:function(left_name,right_name,target){
            if(!SUGAR.tabChooser.frozenOptions){
                SUGAR.tabChooser.frozenOptions=[];
            }
            if(!SUGAR.tabChooser.frozenOptions[left_name]){
                SUGAR.tabChooser.frozenOptions[left_name]=[];
            }
            if(!SUGAR.tabChooser.frozenOptions[left_name][right_name]){
                SUGAR.tabChooser.frozenOptions[left_name][right_name]=[];
            }
            if(typeof target=='array'){
                for(var i in target){
                    SUGAR.tabChooser.frozenOptions[left_name][right_name][target[i]]=true;
                }
            }else{
                SUGAR.tabChooser.frozenOptions[left_name][right_name][target]=true;
            }
        },
        buildSelectHTML:function(info){
            var text="<select";
            if(typeof(info['select']['size'])!='undefined'){
                text+=" size=\""+info['select']['size']+"\"";
            }
            if(typeof(info['select']['name'])!='undefined'){
                text+=" name=\""+info['select']['name']+"\"";
            }
            if(typeof(info['select']['style'])!='undefined'){
                text+=" style=\""+info['select']['style']+"\"";
            }
            if(typeof(info['select']['onchange'])!='undefined'){
                text+=" onChange=\""+info['select']['onchange']+"\"";
            }
            if(typeof(info['select']['multiple'])!='undefined'){
                text+=" multiple";
            }
            text+=">";
            for(i=0;i<info['options'].length;i++){
                option=info['options'][i];
                text+="<option value=\""+option['value']+"\" ";
                if(typeof(option['selected'])!='undefined'&&option['selected']==true){
                    text+="SELECTED";
                }
                text+=">"+option['text']+"</option>";
            }
            text+="</select>";
            return text;
        },
        left_to_right:function(left_name,right_name,left_size,right_size){
            var left_td=document.getElementById(left_name+'_td');
            var right_td=document.getElementById(right_name+'_td');
            var display_columns_ref=left_td.getElementsByTagName('select')[0];
            var hidden_columns_ref=right_td.getElementsByTagName('select')[0];
            var selected_left=new Array();
            var notselected_left=new Array();
            var notselected_right=new Array();
            var left_array=new Array();
            var frozen_options=SUGAR.tabChooser.frozenOptions;
            frozen_options=frozen_options&&frozen_options[left_name]&&frozen_options[left_name][right_name]?frozen_options[left_name][right_name]:[];
            for(i=0;i<display_columns_ref.options.length;i++)

            {
                    if(display_columns_ref.options[i].selected==true&&!frozen_options[display_columns_ref.options[i].value])

                    {
                        selected_left[selected_left.length]={
                            text:display_columns_ref.options[i].text,
                            value:display_columns_ref.options[i].value
                        };
            
                    }
                    else
                    {
                        notselected_left[notselected_left.length]={
                            text:display_columns_ref.options[i].text,
                            value:display_columns_ref.options[i].value
                        };
        
                    }
                }
            for(i=0;i<hidden_columns_ref.options.length;i++)
            {
                notselected_right[notselected_right.length]={
                    text:hidden_columns_ref.options[i].text,
                    value:hidden_columns_ref.options[i].value
                };
    
            }
            var left_select_html_info=new Object();
            var left_options=new Array();
            var left_select=new Object();
            left_select['name']=left_name+'[]';
            left_select['id']=left_name;
            left_select['size']=left_size;
            left_select['multiple']='true';
            var right_select_html_info=new Object();
            var right_options=new Array();
            var right_select=new Object();
            right_select['name']=right_name+'[]';
            right_select['id']=right_name;
            right_select['size']=right_size;
            right_select['multiple']='true';
            for(i=0;i<notselected_right.length;i++){
                right_options[right_options.length]=notselected_right[i];
            }
            for(i=0;i<selected_left.length;i++){
                right_options[right_options.length]=selected_left[i];
            }
            for(i=0;i<notselected_left.length;i++){
                left_options[left_options.length]=notselected_left[i];
            }
            left_select_html_info['options']=left_options;
            left_select_html_info['select']=left_select;
            right_select_html_info['options']=right_options;
            right_select_html_info['select']=right_select;
            right_select_html_info['style']='background: lightgrey';
            var left_html=this.buildSelectHTML(left_select_html_info);
            var right_html=this.buildSelectHTML(right_select_html_info);
            left_td.innerHTML=left_html;
            right_td.innerHTML=right_html;
            object_refs[left_name]=left_td.getElementsByTagName('select')[0];
            object_refs[right_name]=right_td.getElementsByTagName('select')[0];
            this.movementCallback(object_refs[left_name],object_refs[right_name]);
            return false;
        },
        right_to_left:function(left_name,right_name,left_size,right_size,max_left){
            var left_td=document.getElementById(left_name+'_td');
            var right_td=document.getElementById(right_name+'_td');
            var display_columns_ref=left_td.getElementsByTagName('select')[0];
            var hidden_columns_ref=right_td.getElementsByTagName('select')[0];
            var selected_right=new Array();
            var notselected_right=new Array();
            var notselected_left=new Array();
            var frozen_options=SUGAR.tabChooser.frozenOptions;
            frozen_options=SUGAR.tabChooser.frozenOptions&&SUGAR.tabChooser.frozenOptions[right_name]&&SUGAR.tabChooser.frozenOptions[right_name][left_name]?SUGAR.tabChooser.frozenOptions[right_name][left_name]:[];
            for(i=0;i<hidden_columns_ref.options.length;i++)

            {
                    if(hidden_columns_ref.options[i].selected==true&&!frozen_options[hidden_columns_ref.options[i].value])

                    {
                        selected_right[selected_right.length]={
                            text:hidden_columns_ref.options[i].text,
                            value:hidden_columns_ref.options[i].value
                        };
            
                    }
                    else
                    {
                        notselected_right[notselected_right.length]={
                            text:hidden_columns_ref.options[i].text,
                            value:hidden_columns_ref.options[i].value
                        };
        
                    }
                }
            if(max_left!=''&&(display_columns_ref.length+selected_right.length)>max_left){
                alert('Maximum of '+max_left+' columns can be displayed.');
                return;
            }
            for(i=0;i<display_columns_ref.options.length;i++)
            {
                notselected_left[notselected_left.length]={
                    text:display_columns_ref.options[i].text,
                    value:display_columns_ref.options[i].value
                };
    
            }
            var left_select_html_info=new Object();
            var left_options=new Array();
            var left_select=new Object();
            left_select['name']=left_name+'[]';
            left_select['id']=left_name;
            left_select['multiple']='true';
            left_select['size']=left_size;
            var right_select_html_info=new Object();
            var right_options=new Array();
            var right_select=new Object();
            right_select['name']=right_name+'[]';
            right_select['id']=right_name;
            right_select['multiple']='true';
            right_select['size']=right_size;
            for(i=0;i<notselected_left.length;i++){
                left_options[left_options.length]=notselected_left[i];
            }
            for(i=0;i<selected_right.length;i++){
                left_options[left_options.length]=selected_right[i];
            }
            for(i=0;i<notselected_right.length;i++){
                right_options[right_options.length]=notselected_right[i];
            }
            left_select_html_info['options']=left_options;
            left_select_html_info['select']=left_select;
            right_select_html_info['options']=right_options;
            right_select_html_info['select']=right_select;
            right_select_html_info['style']='background: lightgrey';
            var left_html=this.buildSelectHTML(left_select_html_info);
            var right_html=this.buildSelectHTML(right_select_html_info);
            left_td.innerHTML=left_html;
            right_td.innerHTML=right_html;
            object_refs[left_name]=left_td.getElementsByTagName('select')[0];
            object_refs[right_name]=right_td.getElementsByTagName('select')[0];
            this.movementCallback(object_refs[left_name],object_refs[right_name]);
            return false;
        },
        up:function(name,left_name,right_name){
            var left_td=document.getElementById(left_name+'_td');
            var right_td=document.getElementById(right_name+'_td');
            var td=document.getElementById(name+'_td');
            var obj=td.getElementsByTagName('select')[0];
            obj=(typeof obj=="string")?document.getElementById(obj):obj;
            if(obj.tagName.toLowerCase()!="select"&&obj.length<2)
                return false;
            var sel=new Array();
            for(i=0;i<obj.length;i++){
                if(obj[i].selected==true){
                    sel[sel.length]=i;
                }
            }
            for(i=0;i<sel.length;i++){
                if(sel[i]!=0&&!obj[sel[i]-1].selected){
                    var tmp=new Array(obj[sel[i]-1].text,obj[sel[i]-1].value);
                    obj[sel[i]-1].text=obj[sel[i]].text;
                    obj[sel[i]-1].value=obj[sel[i]].value;
                    obj[sel[i]].text=tmp[0];
                    obj[sel[i]].value=tmp[1];
                    obj[sel[i]-1].selected=true;
                    obj[sel[i]].selected=false;
                }
            }
            object_refs[left_name]=left_td.getElementsByTagName('select')[0];
            object_refs[right_name]=right_td.getElementsByTagName('select')[0];
            this.orderCallback(object_refs[left_name],object_refs[right_name]);
            return false;
        },
        down:function(name,left_name,right_name){
            var left_td=document.getElementById(left_name+'_td');
            var right_td=document.getElementById(right_name+'_td');
            var td=document.getElementById(name+'_td');
            var obj=td.getElementsByTagName('select')[0];
            if(obj.tagName.toLowerCase()!="select"&&obj.length<2)
                return false;
            var sel=new Array();
            for(i=obj.length-1;i>-1;i--){
                if(obj[i].selected==true){
                    sel[sel.length]=i;
                }
            }
            for(i=0;i<sel.length;i++){
                if(sel[i]!=obj.length-1&&!obj[sel[i]+1].selected){
                    var tmp=new Array(obj[sel[i]+1].text,obj[sel[i]+1].value);
                    obj[sel[i]+1].text=obj[sel[i]].text;
                    obj[sel[i]+1].value=obj[sel[i]].value;
                    obj[sel[i]].text=tmp[0];
                    obj[sel[i]].value=tmp[1];
                    obj[sel[i]+1].selected=true;
                    obj[sel[i]].selected=false;
                }
            }
            object_refs[left_name]=left_td.getElementsByTagName('select')[0];
            object_refs[right_name]=right_td.getElementsByTagName('select')[0];
            this.orderCallback(object_refs[left_name],object_refs[right_name]);
            return false;
        }
    };

}();
SUGAR.language=function(){
    return{
        languages:new Array(),
        setLanguage:function(module,data){
            if(!SUGAR.language.languages){}
            SUGAR.language.languages[module]=data;
        },
        get:function(module,str){
            if(typeof SUGAR.language.languages[module]=='undefined'||typeof SUGAR.language.languages[module][str]=='undefined')
                return'undefined';
            return SUGAR.language.languages[module][str];
        }
    };

}();
SUGAR.contextMenu=function(){
    return{
        objects:new Object(),
        objectTypes:new Object(),
        registerObject:function(objectType,id,metaData){
            SUGAR.contextMenu.objects[id]=new Object();
            SUGAR.contextMenu.objects[id]={
                'objectType':objectType,
                'metaData':metaData
            };
        
        },
        registerObjectType:function(name,menuItems){
            SUGAR.contextMenu.objectTypes[name]=new Object();
            SUGAR.contextMenu.objectTypes[name]={
                'menuItems':menuItems,
                'objects':new Array()
            };
        
        },
        getListItemFromEventTarget:function(p_oNode){
            var oLI;
            if(p_oNode.tagName=="LI"){
                oLI=p_oNode;
            }
            else{
                do{
                    if(p_oNode.tagName=="LI"){
                        oLI=p_oNode;
                        break;
                    }
                }while((p_oNode=p_oNode.parentNode));
            }
            return oLI;
        },
        onContextMenuMove:function(){
            var oNode=this.contextEventTarget;
            var bDisabled=(oNode.tagName=="UL");
            var i=this.getItemGroups()[0].length-1;
            do{
                this.getItem(i).cfg.setProperty("disabled",bDisabled);
            }
            while(i--);
        },
        onContextMenuItemClick:function(p_sType,p_aArguments,p_oItem){
            var oLI=SUGAR.contextMenu.getListItemFromEventTarget(this.parent.contextEventTarget);
            id=this.parent.contextEventTarget.parentNode.id;
            funct=eval(SUGAR.contextMenu.objectTypes[SUGAR.contextMenu.objects[id]['objectType']]['menuItems'][this.index]['action']);
            funct(this.parent.contextEventTarget,SUGAR.contextMenu.objects[id]['metaData']);
        },
        init:function(){
            for(var i in SUGAR.contextMenu.objects){
                if(typeof SUGAR.contextMenu.objectTypes[SUGAR.contextMenu.objects[i]['objectType']]['objects']=='undefined')
                    SUGAR.contextMenu.objectTypes[SUGAR.contextMenu.objects[i]['objectType']]['objects']=new Array();
                SUGAR.contextMenu.objectTypes[SUGAR.contextMenu.objects[i]['objectType']]['objects'].push(document.getElementById(i));
            }
            for(var i in SUGAR.contextMenu.objectTypes){
                var oContextMenu=new YAHOO.widget.ContextMenu(i,{
                    'trigger':SUGAR.contextMenu.objectTypes[i]['objects']
                });
                var aMainMenuItems=SUGAR.contextMenu.objectTypes[i]['menuItems'];
                var nMainMenuItems=aMainMenuItems.length;
                var oMenuItem;
                for(var j=0;j<nMainMenuItems;j++){
                    oMenuItem=new YAHOO.widget.ContextMenuItem(aMainMenuItems[j].text,{
                        helptext:aMainMenuItems[j].helptext
                    });
                    oMenuItem.clickEvent.subscribe(SUGAR.contextMenu.onContextMenuItemClick,oMenuItem,true);
                    oContextMenu.addItem(oMenuItem);
                }
                oContextMenu.moveEvent.subscribe(SUGAR.contextMenu.onContextMenuMove,oContextMenu,true);
                oContextMenu.keyDownEvent.subscribe(SUGAR.contextMenu.onContextMenuItemClick,oContextMenu,true);
                oContextMenu.render(document.body);
            }
        }
    };

}();
SUGAR.contextMenu.actions=function(){
    return{
        createNote:function(itemClicked,metaData){
            loc='index.php?module=Notes&action=EditView';
            for(i in metaData){
                if(i=='notes_parent_type')loc+='&parent_type='+metaData[i];
                else if(i!='module'&&i!='parent_type')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        scheduleMeeting:function(itemClicked,metaData){
            loc='index.php?module=Meetings&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        scheduleCall:function(itemClicked,metaData){
            loc='index.php?module=Calls&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        createContact:function(itemClicked,metaData){
            loc='index.php?module=Contacts&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        createTask:function(itemClicked,metaData){
            loc='index.php?module=Tasks&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        createOpportunity:function(itemClicked,metaData){
            loc='index.php?module=Opportunities&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        createCase:function(itemClicked,metaData){
            loc='index.php?module=Cases&action=EditView';
            for(i in metaData){
                if(i!='module')loc+='&'+i+'='+metaData[i];
            }
            document.location=loc;
        },
        addToFavorites:function(itemClicked,metaData){
            success=function(data){}
            var cObj=YAHOO.util.Connect.asyncRequest('GET','index.php?to_pdf=true&module=Home&action=AddToFavorites&target_id='+metaData['id']+'&target_module='+metaData['module'],{
                success:success,
                failure:success
            });
        }
    };

}();
var popup_request_data;
var close_popup;
function get_popup_request_data()
{
    return window.document.popup_request_data;
}
function get_close_popup()
{
    return window.document.close_popup;
}
function open_popup(module_name,width,height,initial_filter,close_popup,hide_clear_button,popup_request_data,popup_mode,create,metadata)
{
    window.document.popup_request_data=popup_request_data;
    window.document.close_popup=close_popup;
    URL='index.php?'
    +'module='+module_name
    +'&action=Popup';
    if(initial_filter!='')

    {
        URL+='&query=true'+initial_filter;
    }
    if(hide_clear_button)
    {
        URL+='&hide_clear_button=true';
    }
    windowName='popup_window';
    windowFeatures='width='+width
    +',height='+height
    +',resizable=1,scrollbars=1';
    if(popup_mode==''&&popup_mode=='undefined'){
        popup_mode='single';
    }
    URL+='&mode='+popup_mode;
    if(create==''&&create=='undefined'){
        create='false';
    }
    URL+='&create='+create;
    if(metadata!=''&&metadata!='undefined'){
        URL+='&metadata='+metadata;
    }
    win=window.open(URL,windowName,windowFeatures);
    if(window.focus)

    {
        win.focus();
    }
    return win;
}
var from_popup_return=false;
function set_return_basic(popup_reply_data,filter)
{
    var form_name=popup_reply_data.form_name;
    var name_to_value_array=popup_reply_data.name_to_value_array;
    for(var the_key in name_to_value_array)

    {
            if(the_key=='toJSON')

            {}
            else if(the_key.match(filter))
            {
                var displayValue=name_to_value_array[the_key].replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
                ;
                if(window.document.forms[form_name]&&window.document.forms[form_name].elements[the_key]){
                    if(window.document.forms[form_name].elements[the_key].tagName=='SELECT'){
                        var selectField=window.document.forms[form_name].elements[the_key];
                        for(var i=0;i<selectField.options.length;i++){
                            if(selectField.options[i].text==displayValue){
                                selectField.options[i].selected=true;
                                break;
                            }
                        }
                    }else{
                        window.document.forms[form_name].elements[the_key].value=displayValue;
                    }
                }
            }
        }
}
function set_return(popup_reply_data)
{
    from_popup_return=true;
    var form_name=popup_reply_data.form_name;
    var name_to_value_array=popup_reply_data.name_to_value_array;
    if(name_to_value_array['account_id'])

    {
        var label_str='';
        var label_data_str='';
        var current_label_data_str='';
        for(var the_key in name_to_value_array)

        {
                if(the_key=='toJSON')

                {}
                else
                {
                    var displayValue=name_to_value_array[the_key].replace(/&amp;/gi,'&').replace(/&lt;/gi,'<').replace(/&gt;/gi,'>').replace(/&#039;/gi,'\'').replace(/&quot;/gi,'"');
                    if(window.document.forms[form_name]&&document.getElementById(the_key+'_label')&&!the_key.match(/account/)){
                        var data_label=document.getElementById(the_key+'_label').innerHTML.replace(/\n/gi,'');
                        label_str+=data_label+' \n';
                        label_data_str+=data_label+' '+displayValue+'\n';
                        if(window.document.forms[form_name].elements[the_key]){
                            current_label_data_str+=data_label+' '+window.document.forms[form_name].elements[the_key].value+'\n';
                        }
                    }
                }
            }
        if(label_data_str!=label_str&&current_label_data_str!=label_str){
            if(confirm(SUGAR.language.get('app_strings','NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM')+'\n\n'+label_data_str))

            {
                set_return_basic(popup_reply_data,/\S/);
            }else{
                set_return_basic(popup_reply_data,/account/);
            }
        }else if(label_data_str!=label_str&&current_label_data_str==label_str){
            set_return_basic(popup_reply_data,/\S/);
        }else if(label_data_str==label_str){
            set_return_basic(popup_reply_data,/account/);
        }
    }else{
        set_return_basic(popup_reply_data,/\S/);
    }
}
function set_return_and_save(popup_reply_data)
{
    var form_name=popup_reply_data.form_name;
    var name_to_value_array=popup_reply_data.name_to_value_array;
    for(var the_key in name_to_value_array)

    {
            if(the_key=='toJSON')

            {}
            else
            {
                window.document.forms[form_name].elements[the_key].value=name_to_value_array[the_key];
            }
        }
    window.document.forms[form_name].return_module.value=window.document.forms[form_name].module.value;
    window.document.forms[form_name].return_action.value='DetailView';
    window.document.forms[form_name].return_id.value=window.document.forms[form_name].record.value;
    window.document.forms[form_name].action.value='Save';
    window.document.forms[form_name].submit();
}
function get_initial_filter_by_account(form_name)
{
    var account_id=window.document.forms[form_name].account_id.value;
    var account_name=escape(window.document.forms[form_name].account_name.value);
    var initial_filter="&account_id="+account_id+"&account_name="+account_name;
    return initial_filter;
}
function copyAddress(form,fromKey,toKey){
    var elems=new Array("address_street","address_city","address_state","address_postalcode","address_country");
    var checkbox=document.getElementById(toKey+"_checkbox");
    if(typeof checkbox!="undefined"){
        if(!checkbox.checked){
            for(x in elems){
                t=toKey+"_"+elems[x];
                document.getElementById(t).removeAttribute('readonly');
            }
        }else{
            for(x in elems){
                f=fromKey+"_"+elems[x];
                t=toKey+"_"+elems[x];
                document.getElementById(t).value=document.getElementById(f).value;
                document.getElementById(t).setAttribute('readonly',true);
            }
        }
    }
    return true;
}
function check_deletable_EmailTemplate(){
    id=document.getElementsByName('record')[0].value;
    currentForm=document.getElementById('form');
    var call_back={
        success:function(r){
            if(r.responseText=='true'){
                if(!confirm(SUGAR.language.get('app_strings','NTC_TEMPLATE_IS_USED'))){
                    return false;
                }
            }else{
                if(!confirm(SUGAR.language.get('app_strings','NTC_DELETE_CONFIRMATION'))){
                    return false;
                }
            }
            currentForm.return_module.value='EmailTemplates';
            currentForm.return_action.value='ListView';
            currentForm.action.value='Delete';
            currentForm.submit();
        }
    };

    url="index.php?module=EmailTemplates&action=CheckDeletable&from=DetailView&to_pdf=1&record="+id;
    YAHOO.util.Connect.asyncRequest('POST',url,call_back,null);
}// End of File include/javascript/sugar_3.js
                                
/**
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 */
function Get_Cookie(name){
    var start=document.cookie.indexOf(name+'=');
    var len=start+name.length+1;
    if((!start)&&(name!=document.cookie.substring(0,name.length)))
        return null;
    if(start==-1)
        return null;
    var end=document.cookie.indexOf(';',len);
    if(end==-1)end=document.cookie.length;
    if(end==start){
        return'';
    }
    return unescape(document.cookie.substring(len,end));
}
function Set_Cookie(name,value,expires,path,domain,secure)
{
    var today=new Date();
    today.setTime(today.getTime());
    if(expires)

    {
        expires=expires*1000*60*60*24;
    }
    var expires_date=new Date(today.getTime()+(expires));
    document.cookie=name+"="+escape(value)+
    ((expires)?";expires="+expires_date.toGMTString():"")+
    ((path)?";path="+path:"")+
    ((domain)?";domain="+domain:"")+
    ((secure)?";secure":"");
}
function Delete_Cookie(name,path,domain){
    if(Get_Cookie(name))
        document.cookie=name+'='+
        ((path)?';path='+path:'')+
        ((domain)?';domain='+domain:'')+';expires=Thu, 01-Jan-1970 00:00:01 GMT';
}
function get_sub_cookies(cookie){
    var cookies=new Array();
    var end='';
    if(cookie&&cookie!=''){
        end=cookie.indexOf('#')
        while(end>-1){
            var cur=cookie.substring(0,end);
            cookie=cookie.substring(end+1,cookie.length);
            var name=cur.substring(0,cur.indexOf('='));
            var value=cur.substring(cur.indexOf('=')+1,cur.length);
            cookies[name]=value;
            end=cookie.indexOf('#')
        }
    }
    return cookies;
}
function subs_to_cookie(cookies){
    var cookie='';
    for(var i in cookies)

    {
            if(typeof(cookies[i])!="function"){
                cookie+=i+'='+cookies[i]+'#';
            }
        }
    return cookie;
}// End of File include/javascript/cookie.js
                                
/*  Copyright Mihai Bazon, 2002, 2003  |  http://dynarch.com/mishoo/
 * ------------------------------------------------------------------
 *
 * The DHTML Calendar, version 0.9.6 "Keep cool but don't freeze"
 *
 * Details and latest version at:
 * http://dynarch.com/mishoo/calendar.epl
 *
 * This script is distributed under the GNU Lesser General Public License.
 * Read the entire license text here: http://www.gnu.org/licenses/lgpl.html
 */
Calendar=function(firstDayOfWeek,dateStr,onSelected,onClose,inputField){
    this.activeDiv=null;
    this.currentDateEl=null;
    this.getDateStatus=null;
    this.timeout=null;
    this.onSelected=onSelected||null;
    this.onClose=onClose||null;
    this.dragging=false;
    this.hidden=false;
    this.minYear=1970;
    this.maxYear=2050;
    this.dateFormat=Calendar._TT["DEF_DATE_FORMAT"];
    this.ttDateFormat=Calendar._TT["TT_DATE_FORMAT"];
    this.isPopup=true;
    this.weekNumbers=true;
    this.firstDayOfWeek=firstDayOfWeek;
    this.showsOtherMonths=false;
    this.dateStr=dateStr;
    this.ar_days=null;
    this.showsTime=false;
    this.time24=true;
    this.yearStep=2;
    this.table=null;
    this.element=null;
    this.tbody=null;
    this.firstdayname=null;
    this.monthsCombo=null;
    this.yearsCombo=null;
    this.hilitedMonth=null;
    this.activeMonth=null;
    this.hilitedYear=null;
    this.activeYear=null;
    this.dateClicked=false;
    this.inputField=inputField||null;
    if(typeof Calendar._SDN=="undefined"){
        if(typeof Calendar._SDN_len=="undefined")
            Calendar._SDN_len=3;
        var ar=new Array();
        for(var i=8;i>0;){
            ar[--i]=Calendar._DN[i].substr(0,Calendar._SDN_len);
        }
        Calendar._SDN=ar;
        if(typeof Calendar._SMN_len=="undefined")
            Calendar._SMN_len=3;
        ar=new Array();
        for(var i=12;i>0;){
            ar[--i]=Calendar._MN[i].substr(0,Calendar._SMN_len);
        }
        Calendar._SMN=ar;
    }
};

Calendar._C=null;
if(typeof jscal_today!='undefined'){
    ;
    Calendar.dateToday=jscal_today;
}
Calendar.is_ie=(/msie/i.test(navigator.userAgent)&&!/opera/i.test(navigator.userAgent));
Calendar.is_ie5=(Calendar.is_ie&&/msie 5\.0/i.test(navigator.userAgent));
Calendar.is_opera=/opera/i.test(navigator.userAgent);
Calendar.is_khtml=/Konqueror|Safari|KHTML/i.test(navigator.userAgent);
Calendar.getAbsolutePos=function(el){
    var SL=0,ST=0;
    var is_div=/^div$/i.test(el.tagName);
    if(is_div&&el.scrollLeft)
        SL=el.scrollLeft;
    if(is_div&&el.scrollTop)
        ST=el.scrollTop;
    var r={
        x:el.offsetLeft-SL,
        y:el.offsetTop-ST
    };
        
    if(el.offsetParent){
        var tmp=this.getAbsolutePos(el.offsetParent);
        r.x+=tmp.x;
        r.y+=tmp.y;
    }
    return r;
};

Calendar.isRelated=function(el,evt){
    var related=evt.relatedTarget;
    if(!related){
        var type=evt.type;
        if(type=="mouseover"){
            related=evt.fromElement;
        }else if(type=="mouseout"){
            related=evt.toElement;
        }
    }
    while(related){
        if(related==el){
            return true;
        }
        related=related.parentNode;
    }
    return false;
};

Calendar.removeClass=function(el,className){
    if(!(el&&el.className)){
        return;
    }
    var cls=el.className.split(" ");
    var ar=new Array();
    for(var i=cls.length;i>0;){
        if(cls[--i]!=className){
            ar[ar.length]=cls[i];
        }
    }
    el.className=ar.join(" ");
};

Calendar.addClass=function(el,className){
    Calendar.removeClass(el,className);
    el.className+=" "+className;
};

Calendar.getElement=function(ev){
    if(Calendar.is_ie){
        return window.event.srcElement;
    }else{
        return ev.currentTarget;
    }
};

Calendar.getTargetElement=function(ev){
    if(Calendar.is_ie){
        return window.event.srcElement;
    }else{
        return ev.target;
    }
};

Calendar.stopEvent=function(ev){
    ev||(ev=window.event);
    if(Calendar.is_ie){
        ev.cancelBubble=true;
        ev.returnValue=false;
    }else{
        ev.preventDefault();
        ev.stopPropagation();
    }
    return false;
};

Calendar.addEvent=function(el,evname,func){
    if(el.attachEvent){
        el.attachEvent("on"+evname,func);
    }else if(el.addEventListener){
        el.addEventListener(evname,func,true);
    }else{
        el["on"+evname]=func;
    }
};

Calendar.removeEvent=function(el,evname,func){
    if(el.detachEvent){
        el.detachEvent("on"+evname,func);
    }else if(el.removeEventListener){
        el.removeEventListener(evname,func,true);
    }else{
        el["on"+evname]=null;
    }
};

Calendar.createElement=function(type,parent){
    var el=null;
    if(document.createElementNS){
        el=document.createElementNS("http://www.w3.org/1999/xhtml",type);
    }else{
        el=document.createElement(type);
    }
    if(typeof parent!="undefined"){
        parent.appendChild(el);
    }
    return el;
};

Calendar._add_evs=function(el){
    with(Calendar){
        addEvent(el,"mouseover",dayMouseOver);
        addEvent(el,"mousedown",dayMouseDown);
        addEvent(el,"mouseout",dayMouseOut);
        if(is_ie){
            addEvent(el,"dblclick",dayMouseDblClick);
            el.setAttribute("unselectable",true);
        }
        }
};

Calendar.findMonth=function(el){
    if(typeof el.month!="undefined"){
        return el;
    }
    else if(typeof el.parentNode.month!="undefined"){
        return el.parentNode;
    }
    return null;
};

Calendar.findYear=function(el){
    if(typeof el.year!="undefined"){
        return el;
    }else if(typeof el.parentNode.year!="undefined"){
        return el.parentNode;
    }
    return null;
};

Calendar.showMonthsCombo=function(){
    var cal=Calendar._C;
    if(!cal){
        return false;
    }
    var cal=cal;
    var cd=cal.activeDiv;
    var mc=cal.monthsCombo;
    if(cal.hilitedMonth){
        Calendar.removeClass(cal.hilitedMonth,"hilite");
    }
    if(cal.activeMonth){
        Calendar.removeClass(cal.activeMonth,"active");
    }
    var mon=cal.monthsCombo.getElementsByTagName("div")[cal.date.getMonth()];
    Calendar.addClass(mon,"active");
    cal.activeMonth=mon;
    var s=mc.style;
    s.display="block";
    if(cd.navtype<0)
        s.left=cd.offsetLeft+"px";
    else{
        var mcw=mc.offsetWidth;
        if(typeof mcw=="undefined")
            mcw=50;
        s.left=(cd.offsetLeft+cd.offsetWidth-mcw)+"px";
    }
    s.top=(cd.offsetTop+cd.offsetHeight)+"px";
};

Calendar.showYearsCombo=function(fwd){
    var cal=Calendar._C;
    if(!cal){
        return false;
    }
    var cal=cal;
    var cd=cal.activeDiv;
    var yc=cal.yearsCombo;
    if(cal.hilitedYear){
        Calendar.removeClass(cal.hilitedYear,"hilite");
    }
    if(cal.activeYear){
        Calendar.removeClass(cal.activeYear,"active");
    }
    cal.activeYear=null;
    var Y=cal.date.getFullYear()+(fwd?1:-1);
    var yr=yc.firstChild;
    var show=false;
    for(var i=12;i>0;--i){
        if(Y>=cal.minYear&&Y<=cal.maxYear){
            yr.firstChild.data=Y;
            yr.year=Y;
            yr.style.display="block";
            show=true;
        }else{
            yr.style.display="none";
        }
        yr=yr.nextSibling;
        Y+=fwd?cal.yearStep:-cal.yearStep;
    }
    if(show){
        var s=yc.style;
        s.display="block";
        if(cd.navtype<0)
            s.left=cd.offsetLeft+"px";
        else{
            var ycw=yc.offsetWidth;
            if(typeof ycw=="undefined")
                ycw=50;
            s.left=(cd.offsetLeft+cd.offsetWidth-ycw)+"px";
        }
        s.top=(cd.offsetTop+cd.offsetHeight)+"px";
    }
};

Calendar.tableMouseUp=function(ev){
    var cal=Calendar._C;
    if(!cal){
        return false;
    }
    if(cal.timeout){
        clearTimeout(cal.timeout);
    }
    var el=cal.activeDiv;
    if(!el){
        return false;
    }
    var target=Calendar.getTargetElement(ev);
    ev||(ev=window.event);
    Calendar.removeClass(el,"active");
    if(target==el||target.parentNode==el){
        Calendar.cellClick(el,ev);
    }
    var mon=Calendar.findMonth(target);
    var date=null;
    if(mon){
        date=new Date(cal.date);
        if(mon.month!=date.getMonth()){
            date.setMonth(mon.month);
            cal.setDate(date);
            cal.dateClicked=false;
            cal.callHandler();
        }
    }else{
        var year=Calendar.findYear(target);
        if(year){
            date=new Date(cal.date);
            if(year.year!=date.getFullYear()){
                date.setFullYear(year.year);
                cal.setDate(date);
                cal.dateClicked=false;
                cal.callHandler();
            }
        }
    }
    with(Calendar){
        removeEvent(document,"mouseup",tableMouseUp);
        removeEvent(document,"mouseover",tableMouseOver);
        removeEvent(document,"mousemove",tableMouseOver);
        cal._hideCombos();
        _C=null;
        return stopEvent(ev);
        }
};

Calendar.tableMouseOver=function(ev){
    var cal=Calendar._C;
    if(!cal){
        return;
    }
    var el=cal.activeDiv;
    var target=Calendar.getTargetElement(ev);
    if(target==el||target.parentNode==el){
        Calendar.addClass(el,"hilite active");
        Calendar.addClass(el.parentNode,"rowhilite");
    }else{
        if(typeof el.navtype=="undefined"||(el.navtype!=50&&(el.navtype==0||Math.abs(el.navtype)>2)))
            Calendar.removeClass(el,"active");
        Calendar.removeClass(el,"hilite");
        Calendar.removeClass(el.parentNode,"rowhilite");
    }
    ev||(ev=window.event);
    if(el.navtype==50&&target!=el){
        var pos=Calendar.getAbsolutePos(el);
        var w=el.offsetWidth;
        var x=ev.clientX;
        var dx;
        var decrease=true;
        if(x>pos.x+w){
            dx=x-pos.x-w;
            decrease=false;
        }else
            dx=pos.x-x;
        if(dx<0)dx=0;
        var range=el._range;
        var current=el._current;
        var count=Math.floor(dx/10)%range.length;
        for(var i=range.length;--i>=0;)
            if(range[i]==current)
                break;while(count-->0)
            if(decrease){
                if(--i<0)
                    i=range.length-1;
            }else if(++i>=range.length)
                i=0;
        var newval=range[i];
        el.firstChild.data=newval;
        cal.onUpdateTime();
    }
    var mon=Calendar.findMonth(target);
    if(mon){
        if(mon.month!=cal.date.getMonth()){
            if(cal.hilitedMonth){
                Calendar.removeClass(cal.hilitedMonth,"hilite");
            }
            Calendar.addClass(mon,"hilite");
            cal.hilitedMonth=mon;
        }else if(cal.hilitedMonth){
            Calendar.removeClass(cal.hilitedMonth,"hilite");
        }
    }else{
        if(cal.hilitedMonth){
            Calendar.removeClass(cal.hilitedMonth,"hilite");
        }
        var year=Calendar.findYear(target);
        if(year){
            if(year.year!=cal.date.getFullYear()){
                if(cal.hilitedYear){
                    Calendar.removeClass(cal.hilitedYear,"hilite");
                }
                Calendar.addClass(year,"hilite");
                cal.hilitedYear=year;
            }else if(cal.hilitedYear){
                Calendar.removeClass(cal.hilitedYear,"hilite");
            }
        }else if(cal.hilitedYear){
            Calendar.removeClass(cal.hilitedYear,"hilite");
        }
    }
    return Calendar.stopEvent(ev);
};

Calendar.tableMouseDown=function(ev){
    if(Calendar.getTargetElement(ev)==Calendar.getElement(ev)){
        return Calendar.stopEvent(ev);
    }
};

Calendar.calDragIt=function(ev){
    var cal=Calendar._C;
    if(!(cal&&cal.dragging)){
        return false;
    }
    var posX;
    var posY;
    if(Calendar.is_ie){
        posY=window.event.clientY+document.body.scrollTop;
        posX=window.event.clientX+document.body.scrollLeft;
    }else{
        posX=ev.pageX;
        posY=ev.pageY;
    }
    cal.hideShowCovered();
    var st=cal.element.style;
    st.left=(posX-cal.xOffs)+"px";
    st.top=(posY-cal.yOffs)+"px";
    return Calendar.stopEvent(ev);
};

Calendar.calDragEnd=function(ev){
    var cal=Calendar._C;
    if(!cal){
        return false;
    }
    cal.dragging=false;
    with(Calendar){
        removeEvent(document,"mousemove",calDragIt);
        removeEvent(document,"mouseup",calDragEnd);
        tableMouseUp(ev);
        }
    cal.hideShowCovered();
};

Calendar.dayMouseDown=function(ev){
    var el=Calendar.getElement(ev);
    if(el.disabled){
        return false;
    }
    var cal=el.calendar;
    cal.activeDiv=el;
    Calendar._C=cal;
    if(el.navtype!=300)with(Calendar){
        if(el.navtype==50){
            el._current=el.firstChild.data;
            addEvent(document,"mousemove",tableMouseOver);
        }else
            addEvent(document,Calendar.is_ie5?"mousemove":"mouseover",tableMouseOver);
        addClass(el,"hilite active");
        addEvent(document,"mouseup",tableMouseUp);
        }else if(cal.isPopup){
        cal._dragStart(ev);
    }
    if(el.navtype==-1||el.navtype==1){
        if(cal.timeout)clearTimeout(cal.timeout);
        cal.timeout=setTimeout("Calendar.showMonthsCombo()",250);
    }else if(el.navtype==-2||el.navtype==2){
        if(cal.timeout)clearTimeout(cal.timeout);
        cal.timeout=setTimeout((el.navtype>0)?"Calendar.showYearsCombo(true)":"Calendar.showYearsCombo(false)",250);
    }else{
        cal.timeout=null;
    }
    return Calendar.stopEvent(ev);
};

Calendar.dayMouseDblClick=function(ev){
    Calendar.cellClick(Calendar.getElement(ev),ev||window.event);
    if(Calendar.is_ie){
        document.selection.empty();
    }
};

Calendar.dayMouseOver=function(ev){
    var el=Calendar.getElement(ev);
    if(Calendar.isRelated(el,ev)||Calendar._C||el.disabled){
        return false;
    }
    if(el.ttip){
        if(el.ttip.substr(0,1)=="_"){
            el.ttip=el.caldate.print(el.calendar.ttDateFormat)+el.ttip.substr(1);
        }
        el.calendar.tooltips.firstChild.data=el.ttip;
    }
    if(el.navtype!=300){
        Calendar.addClass(el,"hilite");
        if(el.caldate){
            Calendar.addClass(el.parentNode,"rowhilite");
        }
    }
    return Calendar.stopEvent(ev);
};

Calendar.dayMouseOut=function(ev){
    with(Calendar){
        var el=getElement(ev);
        if(isRelated(el,ev)||_C||el.disabled){
            return false;
        }
        removeClass(el,"hilite");
        if(el.caldate){
            removeClass(el.parentNode,"rowhilite");
        }
        el.calendar.tooltips.firstChild.data=_TT["SEL_DATE"];
        return stopEvent(ev);
        }
};
    
Calendar.cellClick=function(el,ev){
    var cal=el.calendar;
    var closing=false;
    var newdate=false;
    var date=null;
    if(typeof el.navtype=="undefined"){
        Calendar.removeClass(cal.currentDateEl,"selected");
        Calendar.addClass(el,"selected");
        closing=(cal.currentDateEl==el);
        if(!closing){
            cal.currentDateEl=el;
        }
        cal.date=new Date(el.caldate);
        date=cal.date;
        newdate=true;
        if(!(cal.dateClicked=!el.otherMonth))
            cal._init(cal.firstDayOfWeek,date);
    }else{
        if(el.navtype==200){
            Calendar.removeClass(el,"hilite");
            cal.callCloseHandler();
            return;
        }
        if(el.navtype==0){
            if(typeof Calendar.dateToday!='undefined')date=new Date(parseFloat(Calendar.dateToday));else date=new Date();
        }
        else{
            date=new Date(cal.date);
        }
        cal.dateClicked=false;
        var year=date.getFullYear();
        var mon=date.getMonth();
        function setMonth(m){
            var day=date.getDate();
            var max=date.getMonthDays(m);
            if(day>max){
                date.setDate(max);
            }
            date.setMonth(m);
        };
        
        switch(el.navtype){
            case 400:
                Calendar.removeClass(el,"hilite");
                var text=Calendar._TT["ABOUT"];
                if(typeof text!="undefined"){
                    text+=cal.showsTime?Calendar._TT["ABOUT_TIME"]:"";
                }else{
                    text="Help and about box text is not translated into this language.\n"+"If you know this language and you feel generous please update\n"+"the corresponding file in \"lang\" subdir to match calendar-en.js\n"+"and send it back to <mishoo@infoiasi.ro> to get it into the distribution  ;-)\n\n"+"Thank you!\n"+"http://dynarch.com/mishoo/calendar.epl\n";
                }
                alert(text);
                return;
            case-2:
                if(year>cal.minYear){
                    date.setFullYear(year-1);
                }
                break;
            case-1:
                if(mon>0){
                    setMonth(mon-1);
                }else if(year-->cal.minYear){
                    date.setFullYear(year);
                    setMonth(11);
                }
                break;
            case 1:
                if(mon<11){
                    setMonth(mon+1);
                }else if(year<cal.maxYear){
                    date.setFullYear(year+1);
                    setMonth(0);
                }
                break;
            case 2:
                if(year<cal.maxYear){
                    date.setFullYear(year+1);
                }
                break;
            case 100:
                cal.setFirstDayOfWeek(el.fdow);
                return;
            case 50:
                var range=el._range;
                var current=el.firstChild.data;
                for(var i=range.length;--i>=0;)
                    if(range[i]==current)
                        break;if(ev&&ev.shiftKey){
                    if(--i<0)
                        i=range.length-1;
                }else if(++i>=range.length)
                    i=0;
                var newval=range[i];
                el.firstChild.data=newval;
                cal.onUpdateTime();
                return;
            case 0:
                if((typeof cal.getDateStatus=="function")&&cal.getDateStatus(date,date.getFullYear(),date.getMonth(),date.getDate())){
                    return false;
                }
                break;
        }
        if(!date.equalsTo(cal.date)){
            cal.setDate(date);
            newdate=true;
        }
    }
    if(newdate){
        cal.callHandler();
    }
    if(closing){
        Calendar.removeClass(el,"hilite");
        cal.callCloseHandler();
    }
};

Calendar.prototype.create=function(_par){
    var parent=null;
    if(!_par){
        parent=document.getElementsByTagName("body")[0];
        this.isPopup=true;
    }else{
        parent=_par;
        this.isPopup=false;
    }
    if(this.dateStr)this.date=new Date(this.dateStr)
    else if(typeof Calendar.dateToday=='undefined')this.date=new Date();else this.date=new Date(Calendar.dateToday);
    var table=Calendar.createElement("table");
    this.table=table;
    table.cellSpacing=0;
    table.cellPadding=0;
    table.calendar=this;
    Calendar.addEvent(table,"mousedown",Calendar.tableMouseDown);
    var div=Calendar.createElement("div");
    this.element=div;
    div.className="calendar";
    if(this.isPopup){
        div.style.position="absolute";
        div.style.display="none";
        div.style.zIndex=12000;
    }
    div.appendChild(table);
    var thead=Calendar.createElement("thead",table);
    var cell=null;
    var row=null;
    var cal=this;
    var hh=function(text,cs,navtype){
        cell=Calendar.createElement("td",row);
        cell.colSpan=cs;
        cell.className="button";
        if(navtype!=0&&Math.abs(navtype)<=2)
            cell.className+=" nav";
        Calendar._add_evs(cell);
        cell.calendar=cal;
        cell.navtype=navtype;
        if(text.substr(0,1)!="&"){
            cell.appendChild(document.createTextNode(text));
        }
        else{
            cell.innerHTML=text;
        }
        return cell;
    };
    
    row=Calendar.createElement("tr",thead);
    var title_length=6;
    (this.isPopup)&&--title_length;
    (this.weekNumbers)&&++title_length;
    hh("?",1,400).ttip=Calendar._TT["INFO"];
    this.title=hh("",title_length,300);
    this.title.className="title";
    if(this.isPopup){
        this.title.ttip=Calendar._TT["DRAG_TO_MOVE"];
        this.title.style.cursor="move";
        hh("&#x00d7;",1,200).ttip=Calendar._TT["CLOSE"];
    }
    row=Calendar.createElement("tr",thead);
    row.className="headrow";
    this._nav_py=hh("&#x00ab;",1,-2);
    this._nav_py.ttip=Calendar._TT["PREV_YEAR"];
    this._nav_pm=hh("&#x2039;",1,-1);
    this._nav_pm.ttip=Calendar._TT["PREV_MONTH"];
    this._nav_now=hh(Calendar._TT["TODAY"],this.weekNumbers?4:3,0);
    this._nav_now.ttip=Calendar._TT["GO_TODAY"];
    this._nav_nm=hh("&#x203a;",1,1);
    this._nav_nm.ttip=Calendar._TT["NEXT_MONTH"];
    this._nav_ny=hh("&#x00bb;",1,2);
    this._nav_ny.ttip=Calendar._TT["NEXT_YEAR"];
    row=Calendar.createElement("tr",thead);
    row.className="daynames";
    if(this.weekNumbers){
        cell=Calendar.createElement("td",row);
        cell.className="name wn";
        cell.appendChild(document.createTextNode(Calendar._TT["WK"]));
    }
    for(var i=7;i>0;--i){
        cell=Calendar.createElement("td",row);
        cell.appendChild(document.createTextNode(""));
        if(!i){
            cell.navtype=100;
            cell.calendar=this;
            Calendar._add_evs(cell);
        }
    }
    this.firstdayname=(this.weekNumbers)?row.firstChild.nextSibling:row.firstChild;
    this._displayWeekdays();
    var tbody=Calendar.createElement("tbody",table);
    this.tbody=tbody;
    for(i=6;i>0;--i){
        row=Calendar.createElement("tr",tbody);
        if(this.weekNumbers){
            cell=Calendar.createElement("td",row);
            cell.appendChild(document.createTextNode(""));
        }
        for(var j=7;j>0;--j){
            cell=Calendar.createElement("td",row);
            cell.appendChild(document.createTextNode(""));
            cell.calendar=this;
            Calendar._add_evs(cell);
        }
    }
    if(this.showsTime){
        row=Calendar.createElement("tr",tbody);
        row.className="time";
        cell=Calendar.createElement("td",row);
        cell.className="time";
        cell.colSpan=2;
        cell.innerHTML=Calendar._TT["TIME"]||"&nbsp;";
        cell=Calendar.createElement("td",row);
        cell.className="time";
        cell.colSpan=this.weekNumbers?4:3;
        (function(){
            function makeTimePart(className,init,range_start,range_end){
                var part=Calendar.createElement("span",cell);
                part.className=className;
                part.appendChild(document.createTextNode(init));
                part.calendar=cal;
                part.ttip=Calendar._TT["TIME_PART"];
                part.navtype=50;
                part._range=[];
                if(typeof range_start!="number")
                    part._range=range_start;
                else{
                    for(var i=range_start;i<=range_end;++i){
                        var txt;
                        if(i<10&&range_end>=10)txt='0'+i;else txt=''+i;
                        part._range[part._range.length]=txt;
                    }
                }
                Calendar._add_evs(part);
                return part;
            };
    
            var hrs=cal.date.getHours();
            var mins=cal.date.getMinutes();
            var t12=!cal.time24;
            var pm=(hrs>12);
            if(t12&&pm)hrs-=12;
            var H=makeTimePart("hour",hrs,t12?1:0,t12?12:23);
            var span=Calendar.createElement("span",cell);
            span.appendChild(document.createTextNode(":"));
            span.className="colon";
            var M=makeTimePart("minute",mins,0,59);
            var AP=null;
            cell=Calendar.createElement("td",row);
            cell.className="time";
            cell.colSpan=2;
            if(t12)
                AP=makeTimePart("ampm",pm?"pm":"am",["am","pm"]);else
                cell.innerHTML="&nbsp;";
            cal.onSetTime=function(){
                var hrs=this.date.getHours();
                var mins=this.date.getMinutes();
                var pm=(hrs>12);
                if(pm&&t12)hrs-=12;
                H.firstChild.data=(hrs<10)?("0"+hrs):hrs;
                M.firstChild.data=(mins<10)?("0"+mins):mins;
                if(t12)
                    AP.firstChild.data=pm?"pm":"am";
            };
    
            cal.onUpdateTime=function(){
                var date=this.date;
                var h=parseInt(H.firstChild.data,10);
                if(t12){
                    if(/pm/i.test(AP.firstChild.data)&&h<12)
                        h+=12;
                    else if(/am/i.test(AP.firstChild.data)&&h==12)
                        h=0;
                }
                var d=date.getDate();
                var m=date.getMonth();
                var y=date.getFullYear();
                date.setHours(h);
                date.setMinutes(parseInt(M.firstChild.data,10));
                date.setFullYear(y);
                date.setMonth(m);
                date.setDate(d);
                this.dateClicked=false;
                this.callHandler();
            };
    
        })();
    }else{
        this.onSetTime=this.onUpdateTime=function(){};

    }
    var tfoot=Calendar.createElement("tfoot",table);
    row=Calendar.createElement("tr",tfoot);
    row.className="footrow";
    cell=hh(Calendar._TT["SEL_DATE"],this.weekNumbers?8:7,300);
    cell.className="ttip";
    if(this.isPopup){
        cell.ttip=Calendar._TT["DRAG_TO_MOVE"];
        cell.style.cursor="move";
    }
    this.tooltips=cell;
    div=Calendar.createElement("div",this.element);
    this.monthsCombo=div;
    div.className="combo";
    for(i=0;i<Calendar._MN.length;++i){
        var mn=Calendar.createElement("div");
        mn.className=Calendar.is_ie?"label-IEfix":"label";
        mn.month=i;
        mn.appendChild(document.createTextNode(Calendar._SMN[i]));
        div.appendChild(mn);
    }
    div=Calendar.createElement("div",this.element);
    this.yearsCombo=div;
    div.className="combo";
    for(i=12;i>0;--i){
        var yr=Calendar.createElement("div");
        yr.className=Calendar.is_ie?"label-IEfix":"label";
        yr.appendChild(document.createTextNode(""));
        div.appendChild(yr);
    }
    this._init(this.firstDayOfWeek,this.date);
    parent.appendChild(this.element);
};

Calendar._keyEvent=function(ev){
    if(!window.calendar){
        return false;
    }
    (Calendar.is_ie)&&(ev=window.event);
    var cal=window.calendar;
    var act=(Calendar.is_ie||ev.type=="keypress");
    if(ev.ctrlKey){
        switch(ev.keyCode){
            case 37:
                act&&Calendar.cellClick(cal._nav_pm);
                break;
            case 38:
                act&&Calendar.cellClick(cal._nav_py);
                break;
            case 39:
                act&&Calendar.cellClick(cal._nav_nm);
                break;
            case 40:
                act&&Calendar.cellClick(cal._nav_ny);
                break;
            default:
                return false;
        }
    }else switch(ev.keyCode){
        case 32:
            Calendar.cellClick(cal._nav_now);
            break;
        case 27:
            act&&cal.callCloseHandler();
            break;
        case 37:case 38:case 39:case 40:
            if(act){
                var date=cal.date.getDate()-1;
                var el=cal.currentDateEl;
                var ne=null;
                var prev=(ev.keyCode==37)||(ev.keyCode==38);
                switch(ev.keyCode){
                    case 37:
                        (--date>=0)&&(ne=cal.ar_days[date]);
                        break;
                    case 38:
                        date-=7;
                        (date>=0)&&(ne=cal.ar_days[date]);
                        break;
                    case 39:
                        (++date<cal.ar_days.length)&&(ne=cal.ar_days[date]);
                        break;
                    case 40:
                        date+=7;
                        (date<cal.ar_days.length)&&(ne=cal.ar_days[date]);
                        break;
                }
                if(!ne){
                    if(prev){
                        Calendar.cellClick(cal._nav_pm);
                    }else{
                        Calendar.cellClick(cal._nav_nm);
                    }
                    date=(prev)?cal.date.getMonthDays():1;
                    el=cal.currentDateEl;
                    ne=cal.ar_days[date-1];
                }
                Calendar.removeClass(el,"selected");
                Calendar.addClass(ne,"selected");
                cal.date=new Date(ne.caldate);
                cal.callHandler();
                cal.currentDateEl=ne;
            }
            break;
        case 13:
            if(act){
                cal.callHandler();
                cal.hide();
            }
            break;
        default:
            return false;
    }
    return Calendar.stopEvent(ev);
};

Calendar.prototype._init=function(firstDayOfWeek,date){
    if(typeof Calendar.dateToday=='undefined')var today=new Date();else var today=new Date(parseFloat(Calendar.dateToday));
    this.table.style.visibility="hidden";
    var year=date.getFullYear();
    if(year<this.minYear){
        year=this.minYear;
        date.setFullYear(year);
    }else if(year>this.maxYear){
        year=this.maxYear;
        date.setFullYear(year);
    }
    this.firstDayOfWeek=firstDayOfWeek;
    this.date=new Date(date);
    var month=date.getMonth();
    var mday=date.getDate();
    var no_days=date.getMonthDays();
    date.setDate(1);
    var day1=(date.getDay()-this.firstDayOfWeek)%7;
    if(day1<0)
        day1+=7;
    date.setDate(-day1);
    date.setDate(date.getDate()+1);
    var row=this.tbody.firstChild;
    var MN=Calendar._SMN[month];
    var ar_days=new Array();
    var weekend=Calendar._TT["WEEKEND"];
    for(var i=0;i<6;++i,row=row.nextSibling){
        var cell=row.firstChild;
        if(this.weekNumbers){
            cell.className="day wn";
            cell.firstChild.data=date.getWeekNumber();
            cell=cell.nextSibling;
        }
        row.className="daysrow";
        var hasdays=false;
        for(var j=0;j<7;++j,cell=cell.nextSibling,date.setDate(date.getDate()+1)){
            var iday=date.getDate();
            var wday=date.getDay();
            cell.className="day";
            var current_month=(date.getMonth()==month);
            if(!current_month){
                if(this.showsOtherMonths){
                    cell.className+=" othermonth";
                    cell.otherMonth=true;
                }else{
                    cell.className="emptycell";
                    cell.innerHTML="&nbsp;";
                    cell.disabled=true;
                    continue;
                }
            }else{
                cell.otherMonth=false;
                hasdays=true;
            }
            cell.disabled=false;
            cell.firstChild.data=iday;
            if(typeof this.getDateStatus=="function"){
                var status=this.getDateStatus(date,year,month,iday);
                if(status===true){
                    cell.className+=" disabled";
                    cell.disabled=true;
                }else{
                    if(/disabled/i.test(status))
                        cell.disabled=true;
                    cell.className+=" "+status;
                }
            }
            if(!cell.disabled){
                ar_days[ar_days.length]=cell;
                cell.caldate=new Date(date);
                cell.ttip="_";
                if(current_month&&iday==mday){
                    cell.className+=" selected";
                    this.currentDateEl=cell;
                }
                if(date.getFullYear()==today.getFullYear()&&date.getMonth()==today.getMonth()&&iday==today.getDate()){
                    cell.className+=" today";
                    cell.ttip+=Calendar._TT["PART_TODAY"];
                }
                if(weekend.indexOf(wday.toString())!=-1){
                    cell.className+=cell.otherMonth?" oweekend":" weekend";
                }
            }
        }
        if(!(hasdays||this.showsOtherMonths))
            row.className="emptyrow";
    }
    this.ar_days=ar_days;
    this.title.firstChild.data=Calendar._MN[month]+", "+year;
    this.onSetTime();
    this.table.style.visibility="visible";
};

Calendar.prototype.setDate=function(date){
    if(!date.equalsTo(this.date)){
        this._init(this.firstDayOfWeek,date);
    }
};

Calendar.prototype.refresh=function(){
    this._init(this.firstDayOfWeek,this.date);
};

Calendar.prototype.setFirstDayOfWeek=function(firstDayOfWeek){
    this._init(firstDayOfWeek,this.date);
    this._displayWeekdays();
};

Calendar.prototype.setDateStatusHandler=Calendar.prototype.setDisabledHandler=function(unaryFunction){
    this.getDateStatus=unaryFunction;
};

Calendar.prototype.setRange=function(a,z){
    this.minYear=a;
    this.maxYear=z;
};

Calendar.prototype.callHandler=function(){
    if(this.onSelected){
        this.onSelected(this,this.date.print(this.dateFormat));
    }
};

Calendar.prototype.callCloseHandler=function(){
    if(this.onClose){
        this.onClose(this);
    }
    this.hideShowCovered();
};

Calendar.prototype.destroy=function(){
    var el=this.element.parentNode;
    el.removeChild(this.element);
    Calendar._C=null;
    window.calendar=null;
};

Calendar.prototype.reparent=function(new_parent){
    var el=this.element;
    el.parentNode.removeChild(el);
    new_parent.appendChild(el);
};

Calendar._checkCalendar=function(ev){
    if(!window.calendar){
        return false;
    }
    var el=Calendar.is_ie?Calendar.getElement(ev):Calendar.getTargetElement(ev);
    for(;el!=null&&el!=calendar.element;el=el.parentNode);
    if(el==null){
        window.calendar.callCloseHandler();
        return Calendar.stopEvent(ev);
    }
};

Calendar.prototype.show=function(){
    if(this.inputField!=null&&!this.inputField.readOnly)

    {
        var rows=this.table.getElementsByTagName("tr");
        for(var i=rows.length;i>0;){
            var row=rows[--i];
            Calendar.removeClass(row,"rowhilite");
            var cells=row.getElementsByTagName("td");
            for(var j=cells.length;j>0;){
                var cell=cells[--j];
                Calendar.removeClass(cell,"hilite");
                Calendar.removeClass(cell,"active");
            }
        }
        this.element.style.display="block";
        this.hidden=false;
        if(this.isPopup){
            window.calendar=this;
            Calendar.addEvent(document,"keydown",Calendar._keyEvent);
            Calendar.addEvent(document,"keypress",Calendar._keyEvent);
            Calendar.addEvent(document,"mousedown",Calendar._checkCalendar);
        }
        this.hideShowCovered();
    }
};

Calendar.prototype.hide=function(){
    if(this.isPopup){
        Calendar.removeEvent(document,"keydown",Calendar._keyEvent);
        Calendar.removeEvent(document,"keypress",Calendar._keyEvent);
        Calendar.removeEvent(document,"mousedown",Calendar._checkCalendar);
    }
    this.element.style.display="none";
    this.hidden=true;
    this.hideShowCovered();
};

Calendar.prototype.showAt=function(x,y){
    var s=this.element.style;
    s.left=x+"px";
    s.top=y+"px";
    this.show();
};

Calendar.prototype.showAtElement=function(el,opts){
    var self=this;
    var p=Calendar.getAbsolutePos(el);
    if(!opts||typeof opts!="string"){
        this.showAt(p.x,p.y+el.offsetHeight);
        return true;
    }
    function fixPosition(box){
        if(box.x<0)
            box.x=0;
        if(box.y<0)
            box.y=0;
        var cp=document.createElement("div");
        var s=cp.style;
        s.position="absolute";
        s.right=s.bottom=s.width=s.height="0px";
        document.body.appendChild(cp);
        var br=Calendar.getAbsolutePos(cp);
        document.body.removeChild(cp);
        if(Calendar.is_ie){
            br.y+=document.body.scrollTop;
            br.x+=document.body.scrollLeft;
        }else{
            br.y+=window.scrollY;
            br.x+=window.scrollX;
        }
        var tmp=box.x+box.width-br.x;
        if(tmp>0)box.x-=tmp;
        tmp=box.y+box.height-br.y;
        if(tmp>0)box.y-=tmp;
    };
    
    this.element.style.display="block";
    Calendar.continuation_for_the_khtml_browser=function(){
        var w=self.element.offsetWidth;
        var h=self.element.offsetHeight;
        self.element.style.display="none";
        var valign=opts.substr(0,1);
        var halign="l";
        if(opts.length>1){
            halign=opts.substr(1,1);
        }
        switch(valign){
            case"T":
                p.y-=h;
                break;
            case"B":
                p.y+=el.offsetHeight;
                break;
            case"C":
                p.y+=(el.offsetHeight-h)/2;
                break;
            case"t":
                p.y+=el.offsetHeight-h;
                break;
            case"b":
                break;
        }
        switch(halign){
            case"L":
                p.x-=w;
                break;
            case"R":
                p.x+=el.offsetWidth;
                break;
            case"C":
                p.x+=(el.offsetWidth-w)/2;
                break;
            case"r":
                p.x+=el.offsetWidth-w;
                break;
            case"l":
                break;
        }
        p.width=w;
        p.height=h+40;
        self.monthsCombo.style.display="none";
        fixPosition(p);
        self.showAt(p.x,p.y);
    };
    
    if(Calendar.is_khtml)
        setTimeout("Calendar.continuation_for_the_khtml_browser()",10);else
        Calendar.continuation_for_the_khtml_browser();
};

Calendar.prototype.setDateFormat=function(str){
    this.dateFormat=str;
};

Calendar.prototype.setTtDateFormat=function(str){
    this.ttDateFormat=str;
};

Calendar.prototype.parseDate=function(str,fmt){
    var y=0;
    var m=-1;
    var d=0;
    var a=str.split(/\W+/);
    if(!fmt){
        fmt=this.dateFormat;
    }
    var b=fmt.match(/%./g);
    var i=0,j=0;
    var hr=0;
    var min=0;
    for(i=0;i<a.length;++i){
        if(!a[i])
            continue;
        switch(b[i]){
            case"%d":case"%e":
                d=parseInt(a[i],10);
                break;
            case"%m":
                m=parseInt(a[i],10)-1;
                break;
            case"%Y":case"%y":
                y=parseInt(a[i],10);
                (y<100)&&(y+=(y>29)?1900:2000);
                break;
            case"%b":case"%B":
                for(j=0;j<12;++j){
                    if(Calendar._MN[j].substr(0,a[i].length).toLowerCase()==a[i].toLowerCase()){
                        m=j;
                        break;
                    }
                }
                break;
            case"%H":case"%I":case"%k":case"%l":
                hr=parseInt(a[i],10);
                break;
            case"%P":case"%p":
                if(/pm/i.test(a[i])&&hr<12)
                    hr+=12;
                break;
            case"%M":
                min=parseInt(a[i],10);
                break;
        }
    }
    if(y!=0&&m!=-1&&d!=0){
        this.setDate(new Date(y,m,d,hr,min,0));
        return;
    }
    y=0;
    m=-1;
    d=0;
    for(i=0;i<a.length;++i){
        if(a[i].search(/[a-zA-Z]+/)!=-1){
            var t=-1;
            for(j=0;j<12;++j){
                if(Calendar._MN[j].substr(0,a[i].length).toLowerCase()==a[i].toLowerCase()){
                    t=j;
                    break;
                }
            }
            if(t!=-1){
                if(m!=-1){
                    d=m+1;
                }
                m=t;
            }
        }else if(parseInt(a[i],10)<=12&&m==-1){
            m=a[i]-1;
        }else if(parseInt(a[i],10)>31&&y==0){
            y=parseInt(a[i],10);
            (y<100)&&(y+=(y>29)?1900:2000);
        }else if(d==0){
            d=a[i];
        }
    }
    if(y==0){
        var today=new Date();
        y=today.getFullYear();
    }
    if(m!=-1&&d!=0){
        this.setDate(new Date(y,m,d,hr,min,0));
    }
};

Calendar.prototype.hideShowCovered=function(){
    if(!Calendar.is_ie){
        return;
    }
    var self=this;
    Calendar.continuation_for_the_khtml_browser=function(){
        function getVisib(obj){
            var value=obj.style.visibility;
            if(!value){
                if(document.defaultView&&typeof(document.defaultView.getComputedStyle)=="function"){
                    if(!Calendar.is_khtml)
                        value=document.defaultView.getComputedStyle(obj,"").getPropertyValue("visibility");else
                        value='';
                }else if(obj.currentStyle){
                    value=obj.currentStyle.visibility;
                }else
                    value='';
            }
            return value;
        };
        
        var tags=new Array("applet","iframe","select");
        var el=self.element;
        var p=Calendar.getAbsolutePos(el);
        var EX1=p.x;
        var EX2=el.offsetWidth+EX1;
        var EY1=p.y;
        var EY2=el.offsetHeight+EY1;
        for(var k=tags.length;k>0;){
            var ar=document.getElementsByTagName(tags[--k]);
            var cc=null;
            for(var i=ar.length;i>0;){
                cc=ar[--i];
                p=Calendar.getAbsolutePos(cc);
                var CX1=p.x;
                var CX2=cc.offsetWidth+CX1;
                var CY1=p.y;
                var CY2=cc.offsetHeight+CY1;
                if(self.hidden||(CX1>EX2)||(CX2<EX1)||(CY1>EY2)||(CY2<EY1)){
                    if(!cc.__msh_save_visibility){
                        cc.__msh_save_visibility=getVisib(cc);
                    }
                    cc.style.visibility=cc.__msh_save_visibility;
                }else{
                    if(!cc.__msh_save_visibility){
                        cc.__msh_save_visibility=getVisib(cc);
                    }
                    cc.style.visibility="hidden";
                }
            }
        }
    };

    if(Calendar.is_khtml)
        setTimeout("Calendar.continuation_for_the_khtml_browser()",10);else
        Calendar.continuation_for_the_khtml_browser();
};

Calendar.prototype._displayWeekdays=function(){
    var fdow=this.firstDayOfWeek;
    var cell=this.firstdayname;
    var weekend=Calendar._TT["WEEKEND"];
    for(var i=0;i<7;++i){
        cell.className="day name";
        var realday=(i+fdow)%7;
        if(i){
            cell.ttip=Calendar._TT["DAY_FIRST"].replace("%s",Calendar._DN[realday]);
            cell.navtype=100;
            cell.calendar=this;
            cell.fdow=realday;
            Calendar._add_evs(cell);
        }
        if(weekend.indexOf(realday.toString())!=-1){
            Calendar.addClass(cell,"weekend");
        }
        cell.firstChild.data=Calendar._SDN[(i+fdow)%7];
        cell=cell.nextSibling;
    }
};
    
Calendar.prototype._hideCombos=function(){
    this.monthsCombo.style.display="none";
    this.yearsCombo.style.display="none";
};

Calendar.prototype._dragStart=function(ev){
    if(this.dragging){
        return;
    }
    this.dragging=true;
    var posX;
    var posY;
    if(Calendar.is_ie){
        posY=window.event.clientY+document.body.scrollTop;
        posX=window.event.clientX+document.body.scrollLeft;
    }else{
        posY=ev.clientY+window.scrollY;
        posX=ev.clientX+window.scrollX;
    }
    var st=this.element.style;
    this.xOffs=posX-parseInt(st.left);
    this.yOffs=posY-parseInt(st.top);
    with(Calendar){
        addEvent(document,"mousemove",calDragIt);
        addEvent(document,"mouseup",calDragEnd);
        }
};
    
Date._MD=new Array(31,28,31,30,31,30,31,31,30,31,30,31);
Date.SECOND=1000;
Date.MINUTE=60*Date.SECOND;
Date.HOUR=60*Date.MINUTE;
Date.DAY=24*Date.HOUR;
Date.WEEK=7*Date.DAY;
Date.prototype.getMonthDays=function(month){
    var year=this.getFullYear();
    if(typeof month=="undefined"){
        month=this.getMonth();
    }
    if(((0==(year%4))&&((0!=(year%100))||(0==(year%400))))&&month==1){
        return 29;
    }else{
        return Date._MD[month];
    }
};

Date.prototype.getDayOfYear=function(){
    var now=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0);
    var then=new Date(this.getFullYear(),0,0,0,0,0);
    var time=now-then;
    return Math.floor(time/Date.DAY);
};

Date.prototype.getWeekNumber=function(){
    var d=new Date(this.getFullYear(),this.getMonth(),this.getDate(),0,0,0);
    var DoW=d.getDay();
    d.setDate(d.getDate()-(DoW+6)%7+3);
    var ms=d.valueOf();
    d.setMonth(0);
    d.setDate(4);
    return Math.round((ms-d.valueOf())/(7*864e5))+1;
};

Date.prototype.equalsTo=function(date){
    return((this.getFullYear()==date.getFullYear())&&(this.getMonth()==date.getMonth())&&(this.getDate()==date.getDate())&&(this.getHours()==date.getHours())&&(this.getMinutes()==date.getMinutes()));
};

Date.prototype.print=function(str){
    var m=this.getMonth();
    var d=this.getDate();
    var y=this.getFullYear();
    var wn=this.getWeekNumber();
    var w=this.getDay();
    var s={};
    
    var hr=this.getHours();
    var pm=(hr>=12);
    var ir=(pm)?(hr-12):hr;
    var dy=this.getDayOfYear();
    if(ir==0)
        ir=12;
    var min=this.getMinutes();
    var sec=this.getSeconds();
    s["%a"]=Calendar._SDN[w];
    s["%A"]=Calendar._DN[w];
    s["%b"]=Calendar._SMN[m];
    s["%B"]=Calendar._MN[m];
    s["%C"]=1+Math.floor(y/100);
    s["%d"]=(d<10)?("0"+d):d;
    s["%e"]=d;
    s["%H"]=(hr<10)?("0"+hr):hr;
    s["%I"]=(ir<10)?("0"+ir):ir;
    s["%j"]=(dy<100)?((dy<10)?("00"+dy):("0"+dy)):dy;
    s["%k"]=hr;
    s["%l"]=ir;
    s["%m"]=(m<9)?("0"+(1+m)):(1+m);
    s["%M"]=(min<10)?("0"+min):min;
    s["%n"]="\n";
    s["%p"]=pm?"PM":"AM";
    s["%P"]=pm?"pm":"am";
    s["%s"]=Math.floor(this.getTime()/1000);
    s["%S"]=(sec<10)?("0"+sec):sec;
    s["%t"]="\t";
    s["%U"]=s["%W"]=s["%V"]=(wn<10)?("0"+wn):wn;
    s["%u"]=w+1;
    s["%w"]=w;
    s["%y"]=(''+y).substr(2,2);
    s["%Y"]=y;
    s["%%"]="%";
    var re=/%./g;
    var isSafari=navigator.userAgent.toLowerCase().indexOf("safari")!=-1;
    if(!Calendar.is_ie5&&!isSafari)
        return str.replace(re,function(par){
            return s[par]||par;
        })
    var a=str.match(re);
    for(var i=0;i<a.length;i++){
        var tmp=s[a[i]];
        if(tmp){
            re=new RegExp(a[i],'g');
            str=str.replace(re,tmp);
        }
    }
    return str;
};

Date.prototype.__msh_oldSetFullYear=Date.prototype.setFullYear;
Date.prototype.setFullYear=function(y){
    var d=new Date(this);
    d.__msh_oldSetFullYear(y);
    if(d.getMonth()!=this.getMonth())
        this.setDate(28);
    this.__msh_oldSetFullYear(y);
};

window.calendar=null;// End of File jscalendar/calendar.js
                                
/*  Copyright Mihai Bazon, 2002, 2003  |  http://dynarch.com/mishoo/
 * ---------------------------------------------------------------------------
 *
 * The DHTML Calendar
 *
 * Details and latest version at:
 * http://dynarch.com/mishoo/calendar.epl
 *
 * This script is distributed under the GNU Lesser General Public License.
 * Read the entire license text here: http://www.gnu.org/licenses/lgpl.html
 *
 * This file defines helper functions for setting up the calendar.  They are
 * intended to help non-programmers get a working calendar on their site
 * quickly.  This script should not be seen as part of the calendar.  It just
 * shows you what one can do with the calendar, while in the same time
 * providing a quick and simple method for setting it up.  If you need
 * exhaustive customization of the calendar creation process feel free to
 * modify this code to suit your needs (this is recommended and much better
 * than modifying calendar.js itself).
 */
Calendar.setup=function(params){
    function param_default(pname,def){
        if(typeof params[pname]=="undefined"){
            params[pname]=def;
        }
    };
    
    param_default("inputFieldObj",null);
    param_default("displayAreaObj",null);
    param_default("buttonObj",null);
    param_default("inputField",null);
    param_default("displayArea",null);
    param_default("button",null);
    param_default("eventName","click");
    param_default("ifFormat","%Y/%m/%d");
    param_default("daFormat","%Y/%m/%d");
    param_default("singleClick",true);
    param_default("disableFunc",null);
    param_default("dateStatusFunc",params["disableFunc"]);
    param_default("firstDay",isNaN(Calendar._FD)?0:Calendar._FD);
    param_default("align","Br");
    param_default("range",[1900,2999]);
    param_default("weekNumbers",true);
    param_default("flat",null);
    param_default("flatCallback",null);
    param_default("onSelect",null);
    param_default("onClose",null);
    param_default("onOpen",null);
    param_default("onUpdate",null);
    param_default("date",null);
    param_default("showsTime",false);
    param_default("timeFormat","24");
    param_default("electric",true);
    param_default("step",2);
    param_default("position",null);
    param_default("cache",false);
    param_default("showOthers",false);
    var tmp=["inputField","displayArea","button"];
    for(var i in tmp)

    {
            if(params[tmp[i]+'Obj']==null&&typeof params[tmp[i]]=="string")

            {
                params[tmp[i]]=document.getElementById(params[tmp[i]]);
            }
            else
            {
                params[tmp[i]]=params[tmp[i]+'Obj'];
            }
        }
    if(!(params.flat||params.inputField||params.displayArea||params.button)){
        return false;
    }
    function onSelect(cal){
        var p=cal.params;
        var update=(cal.dateClicked||p.electric);
        if(update&&p.flat){
            if(typeof p.flatCallback=="function")
                p.flatCallback(cal);else
                alert("No flatCallback given -- doing nothing.");
            return false;
        }
        if(update&&p.inputField){
            val=cal.date.print(p.daFormat);
            val=val.substring(0,10);
            p.inputField.value=val;
            if(typeof p.inputField.onchange=="function")
                p.inputField.onchange();
        }
        if(update&&p.displayArea)
            p.displayArea.innerHTML=cal.date.print(p.daFormat);
        if(update&&p.singleClick&&cal.dateClicked)
            cal.callCloseHandler();
        if(update&&typeof p.onUpdate=="function")
            p.onUpdate(cal);
    };

    if(params.flat!=null){
        if(typeof params.flat=="string")
            params.flat=document.getElementById(params.flat);
        if(!params.flat){
            alert("Calendar.setup:\n  Flat specified but can't find parent.");
            return false;
        }
        var cal=new Calendar(params.firstDay,params.date,params.onSelect||onSelect);
        cal.showsTime=params.showsTime;
        cal.time24=(params.timeFormat=="24");
        cal.params=params;
        cal.weekNumbers=params.weekNumbers;
        cal.setRange(params.range[0],params.range[1]);
        cal.setDateStatusHandler(params.dateStatusFunc);
        cal.create(params.flat);
        cal.show();
        return false;
    }
    var triggerEl=params.button||params.displayArea||params.inputField;
    triggerEl["on"+params.eventName]=function(){
        if(params.onOpen){
            params.onOpen();
        }
        var dateEl=params.inputField||params.displayArea;
        var dateFmt=((typeof params.ifFormat!="undefined")&&params.ifFormat!="%Y/%m/%d")?params.ifFormat:params.daFormat;
        params.daFormat=dateFmt;
        if(dateFmt.indexOf(" ")>-1){
            dateFmt=dateFmt.substring(0,dateFmt.indexOf(" "));
        }
        var mustCreate=false;
        var cal=window.calendar;
        if(!(cal&&params.cache)){
            window.calendar=cal=new Calendar(params.firstDay,params.date,params.onSelect||onSelect,params.onClose||function(cal){
                cal.hide();
            },params.inputField);
            cal.showsTime=params.showsTime;
            cal.time24=(params.timeFormat=="24");
            cal.weekNumbers=params.weekNumbers;
            mustCreate=true;
        }else{
            if(params.date)
                cal.setDate(params.date);
            cal.hide();
        }
        cal.showsOtherMonths=params.showOthers;
        cal.yearStep=params.step;
        cal.setRange(params.range[0],params.range[1]);
        cal.params=params;
        cal.setDateStatusHandler(params.dateStatusFunc);
        cal.setDateFormat(dateFmt);
        if(mustCreate)
            cal.create();
        cal.parseDate(dateEl.value||dateEl.innerHTML);
        cal.refresh();
        if(!params.position)
            cal.showAtElement(params.button||params.displayArea||params.inputField,params.align);else
            cal.showAt(params.position[0],params.position[1]);
        return false;
    };

};// End of File jscalendar/calendar-setup_3.js
                                
/*
 * Ext JS Library 2.0.2
 * Copyright(c) 2006-2008, Ext JS, LLC.
 * licensing@extjs.com
 *
 * http://extjs.com/license
 *
 * Custom build : ComboBox + Data JSON + data Core with Multi-store + DataView + Core Components
 * Added : SugarCRM customization for quickSearch - listClsClass - bsoufflet
 * Bug 24132 - Modified : Ext.EventManager using the Doug Hendricks fix find in the Extjs forum at this url : http://extjs.com/forum/showthread.php?p=120384#post120384
 */
if((typeof(module_sugar_grp1)!='undefined'&&(module_sugar_grp1!="Emails"||(module_sugar_grp1=="Emails"&&(typeof(action_sugar_grp1)!='undefined')&&(action_sugar_grp1=='ListViewAll'||action_sugar_grp1=="ListView"||action_sugar_grp1=="EditView"))))||(typeof(module_sugar_grp1)=='undefined')){
    Ext={
        version:"2.0.1"
    };
    
    window["undefined"]=window["undefined"];
    Ext.apply=function(C,D,B){
        if(B){
            Ext.apply(C,B)
        }
        if(C&&D&&typeof D=="object"){
            for(var A in D){
                C[A]=D[A]
            }
        }
        return C
    };
    (function(){
        var idSeed=0;
        var ua=navigator.userAgent.toLowerCase();
        var isStrict=document.compatMode=="CSS1Compat",isOpera=ua.indexOf("opera")>-1,isSafari=(/webkit|khtml/).test(ua),isSafari3=isSafari&&ua.indexOf("webkit/5")!=-1,isIE=!isOpera&&ua.indexOf("msie")>-1,isIE7=!isOpera&&ua.indexOf("msie 7")>-1,isGecko=!isSafari&&ua.indexOf("gecko")>-1,isBorderBox=isIE&&!isStrict,isWindows=(ua.indexOf("windows")!=-1||ua.indexOf("win32")!=-1),isMac=(ua.indexOf("macintosh")!=-1||ua.indexOf("mac os x")!=-1),isAir=(ua.indexOf("adobeair")!=-1),isLinux=(ua.indexOf("linux")!=-1),isSecure=window.location.href.toLowerCase().indexOf("https")===0;
        if(isIE&&!isIE7){
            try{
                document.execCommand("BackgroundImageCache",false,true)
            }catch(e){}
        }
        Ext.apply(Ext,{
            isStrict:isStrict,
            isSecure:isSecure,
            isReady:false,
            enableGarbageCollector:true,
            enableListenerCollection:false,
            SSL_SECURE_URL:"javascript:false",
            BLANK_IMAGE_URL:"themes/default/images/blank.gif",
            emptyFn:function(){},
            applyIf:function(o,c){
                if(o&&c){
                    for(var p in c){
                        if(typeof o[p]=="undefined"){
                            o[p]=c[p]
                        }
                    }
                }
                return o
            },
            addBehaviors:function(o){
                if(!Ext.isReady){
                    Ext.onReady(function(){
                        Ext.addBehaviors(o)
                    });
                    return
                }
                var cache={};
    
                for(var b in o){
                    var parts=b.split("@");
                    if(parts[1]){
                        var s=parts[0];
                        if(!cache[s]){
                            cache[s]=Ext.select(s)
                        }
                        cache[s].on(parts[1],o[b])
                    }
                }
                cache=null
            },
            id:function(el,prefix){
                prefix=prefix||"ext-gen";
                el=Ext.getDom(el);
                var id=prefix+(++idSeed);
                return el?(el.id?el.id:(el.id=id)):id
            },
            extend:function(){
                var io=function(o){
                    for(var m in o){
                        this[m]=o[m]
                    }
                };
        
                var oc=Object.prototype.constructor;
                return function(sb,sp,overrides){
                    if(typeof sp=="object"){
                        overrides=sp;
                        sp=sb;
                        sb=overrides.constructor!=oc?overrides.constructor:function(){
                            sp.apply(this,arguments)
                        }
                    }
                    var F=function(){},sbp,spp=sp.prototype;
                    F.prototype=spp;
                    sbp=sb.prototype=new F();
                    sbp.constructor=sb;
                    sb.superclass=spp;
                    if(spp.constructor==oc){
                        spp.constructor=sp
                    }
                    sb.override=function(o){
                        Ext.override(sb,o)
                    };
    
                    sbp.override=io;
                    Ext.override(sb,overrides);
                    sb.extend=function(o){
                        Ext.extend(sb,o)
                    };
    
                    return sb
                }
            }(),
            override:function(origclass,overrides){
                if(overrides){
                    var p=origclass.prototype;
                    for(var method in overrides){
                        p[method]=overrides[method]
                    }
                }
            },
            namespace:function(){
                var a=arguments,o=null,i,j,d,rt;
                for(i=0;i<a.length;++i){
                    d=a[i].split(".");
                    rt=d[0];
                    eval("if (typeof "+rt+" == \"undefined\"){"+rt+" = {};} o = "+rt+";");
                    for(j=1;j<d.length;++j){
                        o[d[j]]=o[d[j]]||{};
            
                        o=o[d[j]]
                    }
                }
            },
            urlEncode:function(o){
                if(!o){
                    return""
                }
                var buf=[];
                for(var key in o){
                    var ov=o[key],k=encodeURIComponent(key);
                    var type=typeof ov;
                    if(type=="undefined"){
                        buf.push(k,"=&")
                    }else{
                        if(type!="function"&&type!="object"){
                            buf.push(k,"=",encodeURIComponent(ov),"&")
                        }else{
                            if(Ext.isArray(ov)){
                                if(ov.length){
                                    for(var i=0,len=ov.length;i<len;i++){
                                        buf.push(k,"=",encodeURIComponent(ov[i]===undefined?"":ov[i]),"&")
                                    }
                                }else{
                                    buf.push(k,"=&")
                                }
                            }
                        }
                    }
                }
                buf.pop();
                return buf.join("")
            },
            urlDecode:function(string,overwrite){
                if(!string||!string.length){
                    return{}
                }
                var obj={};

                var pairs=string.split("&");
                var pair,name,value;
                for(var i=0,len=pairs.length;i<len;i++){
                    pair=pairs[i].split("=");
                    name=decodeURIComponent(pair[0]);
                    value=decodeURIComponent(pair[1]);
                    if(overwrite!==true){
                        if(typeof obj[name]=="undefined"){
                            obj[name]=value
                        }else{
                            if(typeof obj[name]=="string"){
                                obj[name]=[obj[name]];
                                obj[name].push(value)
                            }else{
                                obj[name].push(value)
                            }
                        }
                    }else{
                        obj[name]=value
                    }
                }
                return obj
            },
            each:function(array,fn,scope){
                if(typeof array.length=="undefined"||typeof array=="string"){
                    array=[array]
                }
                for(var i=0,len=array.length;i<len;i++){
                    if(fn.call(scope||array[i],array[i],i,array)===false){
                        return i
                    }
                }
            },
            combine:function(){
                var as=arguments,l=as.length,r=[];
                for(var i=0;i<l;i++){
                    var a=as[i];
                    if(Ext.isArray(a)){
                        r=r.concat(a)
                    }else{
                        if(a.length!==undefined&&!a.substr){
                            r=r.concat(Array.prototype.slice.call(a,0))
                        }else{
                            r.push(a)
                        }
                    }
                }
                return r
            },
            escapeRe:function(s){
                return s.replace(/([.*+?^${}()|[\]\/\\])/g,"\\$1")
            },
            callback:function(cb,scope,args,delay){
                if(typeof cb=="function"){
                    if(delay){
                        cb.defer(delay,scope,args||[])
                    }else{
                        cb.apply(scope,args||[])
                    }
                }
            },
            getDom:function(el){
                if(!el||!document){
                    return null
                }
                return el.dom?el.dom:(typeof el=="string"?document.getElementById(el):el)
            },
            getDoc:function(){
                return Ext.get(document)
            },
            getBody:function(){
                return Ext.get(document.body||document.documentElement)
            },
            getCmp:function(id){
                return Ext.ComponentMgr.get(id)
            },
            num:function(v,defaultValue){
                if(typeof v!="number"){
                    return defaultValue
                }
                return v
            },
            destroy:function(){
                for(var i=0,a=arguments,len=a.length;i<len;i++){
                    var as=a[i];
                    if(as){
                        if(typeof as.destroy=="function"){
                            as.destroy()
                        }else{
                            if(as.dom){
                                as.removeAllListeners();
                                as.remove()
                            }
                        }
                    }
                }
            },
            removeNode:isIE?function(){
                var d;
                return function(n){
                    if(n&&n.tagName!="BODY"){
                        d=d||document.createElement("div");
                        d.appendChild(n);
                        d.innerHTML=""
                    }
                }
            }():function(n){
                if(n&&n.parentNode&&n.tagName!="BODY"){
                    n.parentNode.removeChild(n)
                }
            },
            type:function(o){
                if(o===undefined||o===null){
                    return false
                }
                if(o.htmlElement){
                    return"element"
                }
                var t=typeof o;
                if(t=="object"&&o.nodeName){
                    switch(o.nodeType){
                        case 1:
                            return"element";
                        case 3:
                            return(/\S/).test(o.nodeValue)?"textnode":"whitespace"
                    }
                }
                if(t=="object"||t=="function"){
                    switch(o.constructor){
                        case Array:
                            return"array";
                        case RegExp:
                            return"regexp"
                    }
                    if(typeof o.length=="number"&&typeof o.item=="function"){
                        return"nodelist"
                    }
                }
                return t
            },
            isEmpty:function(v,allowBlank){
                return v===null||v===undefined||(!allowBlank?v==="":false)
            },
            value:function(v,defaultValue,allowBlank){
                return Ext.isEmpty(v,allowBlank)?defaultValue:v
            },
            isArray:function(v){
                return v&&typeof v.pop=="function"
            },
            isDate:function(v){
                return v&&typeof v.getFullYear=="function"
            },
            isOpera:isOpera,
            isSafari:isSafari,
            isSafari3:isSafari3,
            isSafari2:isSafari&&!isSafari3,
            isIE:isIE,
            isIE6:isIE&&!isIE7,
            isIE7:isIE7,
            isGecko:isGecko,
            isBorderBox:isBorderBox,
            isLinux:isLinux,
            isWindows:isWindows,
            isMac:isMac,
            isAir:isAir,
            useShims:((isIE&&!isIE7)||(isGecko&&isMac))
        });
        Ext.ns=Ext.namespace
    })();
    Ext.ns("Ext","Ext.util","Ext.grid","Ext.dd","Ext.tree","Ext.data","Ext.form","Ext.menu","Ext.state","Ext.lib","Ext.layout","Ext.app","Ext.ux");
    Ext.apply(Function.prototype,{
        createCallback:function(){
            var A=arguments;
            var B=this;
            return function(){
                return B.apply(window,A)
            }
        },
        createDelegate:function(C,B,A){
            var D=this;
            return function(){
                var F=B||arguments;
                if(A===true){
                    F=Array.prototype.slice.call(arguments,0);
                    F=F.concat(B)
                }else{
                    if(typeof A=="number"){
                        F=Array.prototype.slice.call(arguments,0);
                        var E=[A,0].concat(B);
                        Array.prototype.splice.apply(F,E)
                    }
                }
                return D.apply(C||window,F)
            }
        },
        defer:function(C,E,B,A){
            var D=this.createDelegate(E,B,A);
            if(C){
                return setTimeout(D,C)
            }
            D();
            return 0
        },
        createSequence:function(B,A){
            if(typeof B!="function"){
                return this
            }
            var C=this;
            return function(){
                var D=C.apply(this||window,arguments);
                B.apply(A||this||window,arguments);
                return D
            }
        },
        createInterceptor:function(B,A){
            if(typeof B!="function"){
                return this
            }
            var C=this;
            return function(){
                B.target=this;
                B.method=C;
                if(B.apply(A||this||window,arguments)===false){
                    return
                }
                return C.apply(this||window,arguments)
            }
        }
    });
    Ext.applyIf(String,{
        escape:function(A){
            return A.replace(/('|\\)/g,"\\$1")
        },
        leftPad:function(D,B,C){
            var A=new String(D);
            if(!C){
                C=" "
            }while(A.length<B){
                A=C+A
            }
            return A.toString()
        },
        format:function(B){
            var A=Array.prototype.slice.call(arguments,1);
            return B.replace(/\{(\d+)\}/g,function(C,D){
                return A[D]
            })
        }
    });
    String.prototype.toggle=function(B,A){
        return this==B?A:B
    };
    
    String.prototype.trim=function(){
        var A=/^\s+|\s+$/g;
        return function(){
            return this.replace(A,"")
        }
    }();
    Ext.applyIf(Number.prototype,{
        constrain:function(B,A){
            return Math.min(Math.max(this,B),A)
        }
    });
    Ext.applyIf(Array.prototype,{
        indexOf:function(C){
            for(var B=0,A=this.length;B<A;B++){
                if(this[B]==C){
                    return B
                }
            }
            return-1
        }
    });
    Date.prototype.getElapsed=function(A){
        return Math.abs((A||new Date()).getTime()-this.getTime())
    };
    (function(){
        var B;
        Ext.lib.Dom={
            getViewWidth:function(E){
                return E?this.getDocumentWidth():this.getViewportWidth()
            },
            getViewHeight:function(E){
                return E?this.getDocumentHeight():this.getViewportHeight()
            },
            getDocumentHeight:function(){
                var E=(document.compatMode!="CSS1Compat")?document.body.scrollHeight:document.documentElement.scrollHeight;
                return Math.max(E,this.getViewportHeight())
            },
            getDocumentWidth:function(){
                var E=(document.compatMode!="CSS1Compat")?document.body.scrollWidth:document.documentElement.scrollWidth;
                return Math.max(E,this.getViewportWidth())
            },
            getViewportHeight:function(){
                if(Ext.isIE){
                    return Ext.isStrict?document.documentElement.clientHeight:document.body.clientHeight
                }else{
                    return self.innerHeight
                }
            },
            getViewportWidth:function(){
                if(Ext.isIE){
                    return Ext.isStrict?document.documentElement.clientWidth:document.body.clientWidth
                }else{
                    return self.innerWidth
                }
            },
            isAncestor:function(F,G){
                F=Ext.getDom(F);
                G=Ext.getDom(G);
                if(!F||!G){
                    return false
                }
                if(F.contains&&!Ext.isSafari){
                    return F.contains(G)
                }else{
                    if(F.compareDocumentPosition){
                        return!!(F.compareDocumentPosition(G)&16)
                    }else{
                        var E=G.parentNode;
                        while(E){
                            if(E==F){
                                return true
                            }else{
                                if(!E.tagName||E.tagName.toUpperCase()=="HTML"){
                                    return false
                                }
                            }
                            E=E.parentNode
                        }
                        return false
                    }
                }
            },
            getRegion:function(E){
                return Ext.lib.Region.getRegion(E)
            },
            getY:function(E){
                return this.getXY(E)[1]
            },
            getX:function(E){
                return this.getXY(E)[0]
            },
            getXY:function(G){
                var F,K,M,N,J=(document.body||document.documentElement);
                G=Ext.getDom(G);
                if(G==J){
                    return[0,0]
                }
                if(G.getBoundingClientRect){
                    M=G.getBoundingClientRect();
                    N=C(document).getScroll();
                    return[M.left+N.left,M.top+N.top]
                }
                var O=0,L=0;
                F=G;
                var E=C(G).getStyle("position")=="absolute";
                while(F){
                    O+=F.offsetLeft;
                    L+=F.offsetTop;
                    if(!E&&C(F).getStyle("position")=="absolute"){
                        E=true
                    }
                    if(Ext.isGecko){
                        K=C(F);
                        var P=parseInt(K.getStyle("borderTopWidth"),10)||0;
                        var H=parseInt(K.getStyle("borderLeftWidth"),10)||0;
                        O+=H;
                        L+=P;
                        if(F!=G&&K.getStyle("overflow")!="visible"){
                            O+=H;
                            L+=P
                        }
                    }
                    F=F.offsetParent
                }
                if(Ext.isSafari&&E){
                    O-=J.offsetLeft;
                    L-=J.offsetTop
                }
                if(Ext.isGecko&&!E){
                    var I=C(J);
                    O+=parseInt(I.getStyle("borderLeftWidth"),10)||0;
                    L+=parseInt(I.getStyle("borderTopWidth"),10)||0
                }
                F=G.parentNode;
                while(F&&F!=J){
                    if(!Ext.isOpera||(F.tagName!="TR"&&C(F).getStyle("display")!="inline")){
                        O-=F.scrollLeft;
                        L-=F.scrollTop
                    }
                    F=F.parentNode
                }
                return[O,L]
            },
            setXY:function(E,F){
                E=Ext.fly(E,"_setXY");
                E.position();
                var G=E.translatePoints(F);
                if(F[0]!==false){
                    E.dom.style.left=G.left+"px"
                }
                if(F[1]!==false){
                    E.dom.style.top=G.top+"px"
                }
            },
            setX:function(F,E){
                this.setXY(F,[E,false])
            },
            setY:function(E,F){
                this.setXY(E,[false,F])
            }
        };

        Ext.lib.Event=function(){
            var F=false;
            var G=[];
            var K=[];
            var I=0;
            var H=[];
            var E=0;
            var J=null;
            return{
                POLL_RETRYS:200,
                POLL_INTERVAL:20,
                EL:0,
                TYPE:1,
                FN:2,
                WFN:3,
                OBJ:3,
                ADJ_SCOPE:4,
                _interval:null,
                startInterval:function(){
                    if(!this._interval){
                        var L=this;
                        var M=function(){
                            L._tryPreloadAttach()
                        };
                    
                        this._interval=setInterval(M,this.POLL_INTERVAL)
                    }
                },
                onAvailable:function(N,L,O,M){
                    H.push({
                        id:N,
                        fn:L,
                        obj:O,
                        override:M,
                        checkReady:false
                    });
                    I=this.POLL_RETRYS;
                    this.startInterval()
                },
                addListener:function(Q,M,P){
                    Q=Ext.getDom(Q);
                    if(!Q||!P){
                        return false
                    }
                    if("unload"==M){
                        K[K.length]=[Q,M,P];
                        return true
                    }
                    var O=function(R){
                        return typeof Ext!="undefined"?P(Ext.lib.Event.getEvent(R)):false
                    };
            
                    var L=[Q,M,P,O];
                    var N=G.length;
                    G[N]=L;
                    this.doAdd(Q,M,O,false);
                    return true
                },
                removeListener:function(S,O,R){
                    var Q,N;
                    S=Ext.getDom(S);
                    if(!R){
                        return this.purgeElement(S,false,O)
                    }
                    if("unload"==O){
                        for(Q=0,N=K.length;Q<N;Q++){
                            var M=K[Q];
                            if(M&&M[0]==S&&M[1]==O&&M[2]==R){
                                K.splice(Q,1);
                                return true
                            }
                        }
                        return false
                    }
                    var L=null;
                    var P=arguments[3];
                    if("undefined"==typeof P){
                        P=this._getCacheIndex(S,O,R)
                    }
                    if(P>=0){
                        L=G[P]
                    }
                    if(!S||!L){
                        return false
                    }
                    this.doRemove(S,O,L[this.WFN],false);
                    delete G[P][this.WFN];
                    delete G[P][this.FN];
                    G.splice(P,1);
                    return true
                },
                getTarget:function(N,M){
                    N=N.browserEvent||N;
                    var L=N.target||N.srcElement;
                    return this.resolveTextNode(L)
                },
                resolveTextNode:function(L){
                    if(Ext.isSafari&&L&&3==L.nodeType){
                        return L.parentNode
                    }else{
                        return L
                    }
                },
                getPageX:function(M){
                    M=M.browserEvent||M;
                    var L=M.pageX;
                    if(!L&&0!==L){
                        L=M.clientX||0;
                        if(Ext.isIE){
                            L+=this.getScroll()[1]
                        }
                    }
                    return L
                },
                getPageY:function(L){
                    L=L.browserEvent||L;
                    var M=L.pageY;
                    if(!M&&0!==M){
                        M=L.clientY||0;
                        if(Ext.isIE){
                            M+=this.getScroll()[0]
                        }
                    }
                    return M
                },
                getXY:function(L){
                    L=L.browserEvent||L;
                    return[this.getPageX(L),this.getPageY(L)]
                },
                getRelatedTarget:function(M){
                    M=M.browserEvent||M;
                    var L=M.relatedTarget;
                    if(!L){
                        if(M.type=="mouseout"){
                            L=M.toElement
                        }else{
                            if(M.type=="mouseover"){
                                L=M.fromElement
                            }
                        }
                    }
                    return this.resolveTextNode(L)
                },
                getTime:function(N){
                    N=N.browserEvent||N;
                    if(!N.time){
                        var M=new Date().getTime();
                        try{
                            N.time=M
                        }catch(L){
                            this.lastError=L;
                            return M
                        }
                    }
                    return N.time
                },
                stopEvent:function(L){
                    this.stopPropagation(L);
                    this.preventDefault(L)
                },
                stopPropagation:function(L){
                    L=L.browserEvent||L;
                    if(L.stopPropagation){
                        L.stopPropagation()
                    }else{
                        L.cancelBubble=true
                    }
                },
                preventDefault:function(L){
                    L=L.browserEvent||L;
                    if(L.preventDefault){
                        L.preventDefault()
                    }else{
                        L.returnValue=false
                    }
                },
                getEvent:function(M){
                    var L=M||window.event;
                    if(!L){
                        var N=this.getEvent.caller;
                        while(N){
                            L=N.arguments[0];
                            if(L&&Event==L.constructor){
                                break
                            }
                            N=N.caller
                        }
                    }
                    return L
                },
                getCharCode:function(L){
                    L=L.browserEvent||L;
                    return L.charCode||L.keyCode||0
                },
                _getCacheIndex:function(Q,N,P){
                    for(var O=0,M=G.length;O<M;++O){
                        var L=G[O];
                        if(L&&L[this.FN]==P&&L[this.EL]==Q&&L[this.TYPE]==N){
                            return O
                        }
                    }
                    return-1
                },
                elCache:{},
                getEl:function(L){
                    return document.getElementById(L)
                },
                clearCache:function(){},
                _load:function(M){
                    F=true;
                    var L=Ext.lib.Event;
                    if(Ext.isIE){
                        L.doRemove(window,"load",L._load)
                    }
                },
                _tryPreloadAttach:function(){
                    if(this.locked){
                        return false
                    }
                    this.locked=true;
                    var R=!F;
                    if(!R){
                        R=(I>0)
                    }
                    var Q=[];
                    for(var M=0,L=H.length;M<L;++M){
                        var P=H[M];
                        if(P){
                            var O=this.getEl(P.id);
                            if(O){
                                if(!P.checkReady||F||O.nextSibling||(document&&document.body)){
                                    var N=O;
                                    if(P.override){
                                        if(P.override===true){
                                            N=P.obj
                                        }else{
                                            N=P.override
                                        }
                                    }
                                    P.fn.call(N,P.obj);
                                    H[M]=null
                                }
                            }else{
                                Q.push(P)
                            }
                        }
                    }
                    I=(Q.length===0)?0:I-1;
                    if(R){
                        this.startInterval()
                    }else{
                        clearInterval(this._interval);
                        this._interval=null
                    }
                    this.locked=false;
                    return true
                },
                purgeElement:function(P,Q,N){
                    var R=this.getListeners(P,N);
                    if(R){
                        for(var O=0,L=R.length;O<L;++O){
                            var M=R[O];
                            this.removeListener(P,M.type,M.fn)
                        }
                    }
                    if(Q&&P&&P.childNodes){
                        for(O=0,L=P.childNodes.length;O<L;++O){
                            this.purgeElement(P.childNodes[O],Q,N)
                        }
                    }
                },
                getListeners:function(M,R){
                    var P=[],L;
                    if(!R){
                        L=[G,K]
                    }else{
                        if(R=="unload"){
                            L=[K]
                        }else{
                            L=[G]
                        }
                    }
                    for(var O=0;O<L.length;++O){
                        var T=L[O];
                        if(T&&T.length>0){
                            for(var Q=0,S=T.length;Q<S;++Q){
                                var N=T[Q];
                                if(N&&N[this.EL]===M&&(!R||R===N[this.TYPE])){
                                    P.push({
                                        type:N[this.TYPE],
                                        fn:N[this.FN],
                                        obj:N[this.OBJ],
                                        adjust:N[this.ADJ_SCOPE],
                                        index:Q
                                    })
                                }
                            }
                        }
                    }
                    return(P.length)?P:null
                },
                _unload:function(S){
                    var R=Ext.lib.Event,P,O,M,L,N;
                    for(P=0,L=K.length;P<L;++P){
                        M=K[P];
                        if(M){
                            var Q=window;
                            if(M[R.ADJ_SCOPE]){
                                if(M[R.ADJ_SCOPE]===true){
                                    Q=M[R.OBJ]
                                }else{
                                    Q=M[R.ADJ_SCOPE]
                                }
                            }
                            M[R.FN].call(Q,R.getEvent(S),M[R.OBJ]);
                            K[P]=null;
                            M=null;
                            Q=null
                        }
                    }
                    K=null;
                    if(G&&G.length>0){
                        O=G.length;
                        while(O){
                            N=O-1;
                            M=G[N];
                            if(M){
                                R.removeListener(M[R.EL],M[R.TYPE],M[R.FN],N)
                            }
                            O=O-1
                        }
                        M=null;
                        R.clearCache()
                    }
                    R.doRemove(window,"unload",R._unload)
                },
                getScroll:function(){
                    var L=document.documentElement,M=document.body;
                    if(L&&(L.scrollTop||L.scrollLeft)){
                        return[L.scrollTop,L.scrollLeft]
                    }else{
                        if(M){
                            return[M.scrollTop,M.scrollLeft]
                        }else{
                            return[0,0]
                        }
                    }
                },
                doAdd:function(){
                    if(window.addEventListener){
                        return function(O,M,N,L){
                            O.addEventListener(M,N,(L))
                        }
                    }else{
                        if(window.attachEvent){
                            return function(O,M,N,L){
                                O.attachEvent("on"+M,N)
                            }
                        }else{
                            return function(){}
                        }
                    }
                }(),
                doRemove:function(){
                    if(window.removeEventListener){
                        return function(O,M,N,L){
                            O.removeEventListener(M,N,(L))
                        }
                    }
                    else{
                        if(window.detachEvent){
                            return function(N,L,M){
                                N.detachEvent("on"+L,M)
                            }
                        }else{
                            return function(){}
                        }
                    }
                }()
            }
        }();
        var D=Ext.lib.Event;
        D.on=D.addListener;
        D.un=D.removeListener;
        if(document&&document.body){
            D._load()
        }
        else{
            D.doAdd(window,"load",D._load)
        }
        D.doAdd(window,"unload",D._unload);
        D._tryPreloadAttach();
        Ext.lib.Ajax={
            request:function(K,I,E,J,F){
                if(F){
                    var G=F.headers;
                    if(G){
                        for(var H in G){
                            if(G.hasOwnProperty(H)){
                                this.initHeader(H,G[H],false)
                            }
                        }
                    }
                    if(F.xmlData){
                        this.initHeader("Content-Type","text/xml",false);
                        K="POST";
                        J=F.xmlData
                    }else{
                        if(F.jsonData){
                            this.initHeader("Content-Type","text/javascript",false);
                            K="POST";
                            J=typeof F.jsonData=="object"?Ext.encode(F.jsonData):F.jsonData
                        }
                    }
                }
                return this.asyncRequest(K,I,E,J)
            },
            serializeForm:function(F){
                if(typeof F=="string"){
                    F=(document.getElementById(F)||document.forms[F])
                }
                var G,E,H,J,K="",M=false;
                for(var L=0;L<F.elements.length;L++){
                    G=F.elements[L];
                    J=F.elements[L].disabled;
                    E=F.elements[L].name;
                    H=F.elements[L].value;
                    if(!J&&E){
                        switch(G.type){
                            case"select-one":case"select-multiple":
                                for(var I=0;I<G.options.length;I++){
                                    if(G.options[I].selected){
                                        if(Ext.isIE){
                                            K+=encodeURIComponent(E)+"="+encodeURIComponent(G.options[I].attributes["value"].specified?G.options[I].value:G.options[I].text)+"&"
                                        }else{
                                            K+=encodeURIComponent(E)+"="+encodeURIComponent(G.options[I].hasAttribute("value")?G.options[I].value:G.options[I].text)+"&"
                                        }
                                    }
                                }
                                break;
                            case"radio":case"checkbox":
                                if(G.checked){
                                    K+=encodeURIComponent(E)+"="+encodeURIComponent(H)+"&"
                                }
                                break;
                            case"file":case undefined:case"reset":case"button":
                                break;
                            case"submit":
                                if(M==false){
                                    K+=encodeURIComponent(E)+"="+encodeURIComponent(H)+"&";
                                    M=true
                                }
                                break;
                            default:
                                K+=encodeURIComponent(E)+"="+encodeURIComponent(H)+"&";
                                break
                        }
                    }
                }
                K=K.substr(0,K.length-1);
                return K
            },
            headers:{},
            hasHeaders:false,
            useDefaultHeader:true,
            defaultPostHeader:"application/x-www-form-urlencoded",
            useDefaultXhrHeader:true,
            defaultXhrHeader:"XMLHttpRequest",
            hasDefaultHeaders:true,
            defaultHeaders:{},
            poll:{},
            timeout:{},
            pollInterval:50,
            transactionId:0,
            setProgId:function(E){
                this.activeX.unshift(E)
            },
            setDefaultPostHeader:function(E){
                this.useDefaultHeader=E
            },
            setDefaultXhrHeader:function(E){
                this.useDefaultXhrHeader=E
            },
            setPollingInterval:function(E){
                if(typeof E=="number"&&isFinite(E)){
                    this.pollInterval=E
                }
            },
            createXhrObject:function(I){
                var H,E;
                try{
                    E=new XMLHttpRequest();
                    H={
                        conn:E,
                        tId:I
                    }
                }catch(G){
                    for(var F=0;F<this.activeX.length;++F){
                        try{
                            E=new ActiveXObject(this.activeX[F]);
                            H={
                                conn:E,
                                tId:I
                            };
            
                            break
                        }catch(G){}
                    }
                }finally{
                    return H
                }
            },
            getConnectionObject:function(){
                var F;
                var G=this.transactionId;
                try{
                    F=this.createXhrObject(G);
                    if(F){
                        this.transactionId++
                    }
                }catch(E){}finally{
                    return F
                }
            },
            asyncRequest:function(I,F,H,E){
                var G=this.getConnectionObject();
                if(!G){
                    return null
                }else{
                    G.conn.open(I,F,true);
                    if(this.useDefaultXhrHeader){
                        if(!this.defaultHeaders["X-Requested-With"]){
                            this.initHeader("X-Requested-With",this.defaultXhrHeader,true)
                        }
                    }
                    if(E&&this.useDefaultHeader){
                        this.initHeader("Content-Type",this.defaultPostHeader)
                    }
                    if(this.hasDefaultHeaders||this.hasHeaders){
                        this.setHeader(G)
                    }
                    this.handleReadyState(G,H);
                    G.conn.send(E||null);
                    return G
                }
            },
            handleReadyState:function(F,G){
                var E=this;
                if(G&&G.timeout){
                    this.timeout[F.tId]=window.setTimeout(function(){
                        E.abort(F,G,true)
                    },G.timeout)
                }
                this.poll[F.tId]=window.setInterval(function(){
                    if(F.conn&&F.conn.readyState==4){
                        window.clearInterval(E.poll[F.tId]);
                        delete E.poll[F.tId];
                        if(G&&G.timeout){
                            window.clearTimeout(E.timeout[F.tId]);
                            delete E.timeout[F.tId]
                        }
                        E.handleTransactionResponse(F,G)
                    }
                },this.pollInterval)
            },
            handleTransactionResponse:function(I,J,E){
                if(!J){
                    this.releaseObject(I);
                    return
                }
                var G,F;
                try{
                    if(I.conn.status!==undefined&&I.conn.status!=0){
                        G=I.conn.status
                    }
                    else{
                        G=13030
                    }
                }catch(H){
                    G=13030
                }
                if(G>=200&&G<300){
                    F=this.createResponseObject(I,J.argument);
                    if(J.success){
                        if(!J.scope){
                            J.success(F)
                        }else{
                            J.success.apply(J.scope,[F])
                        }
                    }
                }else{
                    switch(G){
                        case 12002:case 12029:case 12030:case 12031:case 12152:case 13030:
                            F=this.createExceptionObject(I.tId,J.argument,(E?E:false));
                            if(J.failure){
                                if(!J.scope){
                                    J.failure(F)
                                }else{
                                    J.failure.apply(J.scope,[F])
                                }
                            }
                            break;
                        default:
                            F=this.createResponseObject(I,J.argument);
                            if(J.failure){
                                if(!J.scope){
                                    J.failure(F)
                                }else{
                                    J.failure.apply(J.scope,[F])
                                }
                            }
                    }
                }
                this.releaseObject(I);
                F=null
            },
            createResponseObject:function(E,K){
                var H={};
    
                var M={};
    
                try{
                    var G=E.conn.getAllResponseHeaders();
                    var J=G.split("\n");
                    for(var I=0;I<J.length;I++){
                        var F=J[I].indexOf(":");
                        if(F!=-1){
                            M[J[I].substring(0,F)]=J[I].substring(F+2)
                        }
                    }
                }catch(L){}
                H.tId=E.tId;
                H.status=E.conn.status;
                H.statusText=E.conn.statusText;
                H.getResponseHeader=M;
                H.getAllResponseHeaders=G;
                H.responseText=E.conn.responseText;
                H.responseXML=E.conn.responseXML;
                if(typeof K!==undefined){
                    H.argument=K
                }
                return H
            },
            createExceptionObject:function(L,H,E){
                var J=0;
                var K="communication failure";
                var G=-1;
                var F="transaction aborted";
                var I={};
    
                I.tId=L;
                if(E){
                    I.status=G;
                    I.statusText=F
                }else{
                    I.status=J;
                    I.statusText=K
                }
                if(H){
                    I.argument=H
                }
                return I
            },
            initHeader:function(E,H,G){
                var F=(G)?this.defaultHeaders:this.headers;
                if(F[E]===undefined){
                    F[E]=H
                }else{
                    F[E]=H+","+F[E]
                }
                if(G){
                    this.hasDefaultHeaders=true
                }else{
                    this.hasHeaders=true
                }
            },
            setHeader:function(E){
                if(this.hasDefaultHeaders){
                    for(var F in this.defaultHeaders){
                        if(this.defaultHeaders.hasOwnProperty(F)){
                            E.conn.setRequestHeader(F,this.defaultHeaders[F])
                        }
                    }
                }
                if(this.hasHeaders){
                    for(var F in this.headers){
                        if(this.headers.hasOwnProperty(F)){
                            E.conn.setRequestHeader(F,this.headers[F])
                        }
                    }
                    this.headers={};

                    this.hasHeaders=false
                }
            },
            resetDefaultHeaders:function(){
                delete this.defaultHeaders;
                this.defaultHeaders={};
    
                this.hasDefaultHeaders=false
            },
            abort:function(F,G,E){
                if(this.isCallInProgress(F)){
                    F.conn.abort();
                    window.clearInterval(this.poll[F.tId]);
                    delete this.poll[F.tId];
                    if(E){
                        delete this.timeout[F.tId]
                    }
                    this.handleTransactionResponse(F,G,true);
                    return true
                }else{
                    return false
                }
            },
            isCallInProgress:function(E){
                if(E.conn){
                    return E.conn.readyState!=4&&E.conn.readyState!=0
                }else{
                    return false
                }
            },
            releaseObject:function(E){
                E.conn=null;
                E=null
            },
            activeX:["MSXML2.XMLHTTP.3.0","MSXML2.XMLHTTP","Microsoft.XMLHTTP"]
        };

        Ext.lib.Region=function(G,H,E,F){
            this.top=G;
            this[1]=G;
            this.right=H;
            this.bottom=E;
            this.left=F;
            this[0]=F
        };
    
        Ext.lib.Region.prototype={
            contains:function(E){
                return(E.left>=this.left&&E.right<=this.right&&E.top>=this.top&&E.bottom<=this.bottom)
            },
            getArea:function(){
                return((this.bottom-this.top)*(this.right-this.left))
            },
            intersect:function(I){
                var G=Math.max(this.top,I.top);
                var H=Math.min(this.right,I.right);
                var E=Math.min(this.bottom,I.bottom);
                var F=Math.max(this.left,I.left);
                if(E>=G&&H>=F){
                    return new Ext.lib.Region(G,H,E,F)
                }else{
                    return null
                }
            },
            union:function(I){
                var G=Math.min(this.top,I.top);
                var H=Math.max(this.right,I.right);
                var E=Math.max(this.bottom,I.bottom);
                var F=Math.min(this.left,I.left);
                return new Ext.lib.Region(G,H,E,F)
            },
            constrainTo:function(E){
                this.top=this.top.constrain(E.top,E.bottom);
                this.bottom=this.bottom.constrain(E.top,E.bottom);
                this.left=this.left.constrain(E.left,E.right);
                this.right=this.right.constrain(E.left,E.right);
                return this
            },
            adjust:function(G,F,E,H){
                this.top+=G;
                this.left+=F;
                this.right+=H;
                this.bottom+=E;
                return this
            }
        };

        Ext.lib.Region.getRegion=function(H){
            var J=Ext.lib.Dom.getXY(H);
            var G=J[1];
            var I=J[0]+H.offsetWidth;
            var E=J[1]+H.offsetHeight;
            var F=J[0];
            return new Ext.lib.Region(G,I,E,F)
        };
    
        Ext.lib.Point=function(E,F){
            if(Ext.isArray(E)){
                F=E[1];
                E=E[0]
            }
            this.x=this.right=this.left=this[0]=E;
            this.y=this.top=this.bottom=this[1]=F
        };
    
        Ext.lib.Point.prototype=new Ext.lib.Region();
        Ext.lib.Anim={
            scroll:function(H,F,I,J,E,G){
                return this.run(H,F,I,J,E,G,Ext.lib.Scroll)
            },
            motion:function(H,F,I,J,E,G){
                return this.run(H,F,I,J,E,G,Ext.lib.Motion)
            },
            color:function(H,F,I,J,E,G){
                return this.run(H,F,I,J,E,G,Ext.lib.ColorAnim)
            },
            run:function(I,F,K,L,E,H,G){
                G=G||Ext.lib.AnimBase;
                if(typeof L=="string"){
                    L=Ext.lib.Easing[L]
                }
                var J=new G(I,F,K,L);
                J.animateX(function(){
                    Ext.callback(E,H)
                });
                return J
            }
        };

        function C(E){
            if(!B){
                B=new Ext.Element.Flyweight()
            }
            B.dom=E;
            return B
        }
        if(Ext.isIE){
            function A(){
                var E=Function.prototype;
                delete E.createSequence;
                delete E.defer;
                delete E.createDelegate;
                delete E.createCallback;
                delete E.createInterceptor;
                window.detachEvent("onunload",A)
            }
            window.attachEvent("onunload",A)
        }
        Ext.lib.AnimBase=function(F,E,G,H){
            if(F){
                this.init(F,E,G,H)
            }
        };

        Ext.lib.AnimBase.prototype={
            toString:function(){
                var E=this.getEl();
                var F=E.id||E.tagName;
                return("Anim "+F)
            },
            patterns:{
                noNegatives:/width|height|opacity|padding/i,
                offsetAttribute:/^((width|height)|(top|left))$/,
                defaultUnit:/width|height|top$|bottom$|left$|right$/i,
                offsetUnit:/\d+(em|%|en|ex|pt|in|cm|mm|pc)$/i
            },
            doMethod:function(E,G,F){
                return this.method(this.currentFrame,G,F-G,this.totalFrames)
            },
            setAttribute:function(E,G,F){
                if(this.patterns.noNegatives.test(E)){
                    G=(G>0)?G:0
                }
                Ext.fly(this.getEl(),"_anim").setStyle(E,G+F)
            },
            getAttribute:function(E){
                var G=this.getEl();
                var I=C(G).getStyle(E);
                if(I!=="auto"&&!this.patterns.offsetUnit.test(I)){
                    return parseFloat(I)
                }
                var F=this.patterns.offsetAttribute.exec(E)||[];
                var J=!!(F[3]);
                var H=!!(F[2]);
                if(H||(C(G).getStyle("position")=="absolute"&&J)){
                    I=G["offset"+F[0].charAt(0).toUpperCase()+F[0].substr(1)]
                }else{
                    I=0
                }
                return I
            },
            getDefaultUnit:function(E){
                if(this.patterns.defaultUnit.test(E)){
                    return"px"
                }
                return""
            },
            animateX:function(G,E){
                var F=function(){
                    this.onComplete.removeListener(F);
                    if(typeof G=="function"){
                        G.call(E||this,this)
                    }
                };
        
                this.onComplete.addListener(F,this);
                this.animate()
            },
            setRuntimeAttribute:function(F){
                var K;
                var G;
                var H=this.attributes;
                this.runtimeAttributes[F]={};
    
                var J=function(L){
                    return(typeof L!=="undefined")
                };
        
                if(!J(H[F]["to"])&&!J(H[F]["by"])){
                    return false
                }
                K=(J(H[F]["from"]))?H[F]["from"]:this.getAttribute(F);
                if(J(H[F]["to"])){
                    G=H[F]["to"]
                }else{
                    if(J(H[F]["by"])){
                        if(K.constructor==Array){
                            G=[];
                            for(var I=0,E=K.length;I<E;++I){
                                G[I]=K[I]+H[F]["by"][I]
                            }
                        }
                        else{
                            G=K+H[F]["by"]
                        }
                    }
                }
                this.runtimeAttributes[F].start=K;
                this.runtimeAttributes[F].end=G;
                this.runtimeAttributes[F].unit=(J(H[F].unit))?H[F]["unit"]:this.getDefaultUnit(F)
            },
            init:function(G,L,K,E){
                var F=false;
                var H=null;
                var J=0;
                G=Ext.getDom(G);
                this.attributes=L||{};
    
                this.duration=K||1;
                this.method=E||Ext.lib.Easing.easeNone;
                this.useSeconds=true;
                this.currentFrame=0;
                this.totalFrames=Ext.lib.AnimMgr.fps;
                this.getEl=function(){
                    return G
                };
        
                this.isAnimated=function(){
                    return F
                };
        
                this.getStartTime=function(){
                    return H
                };
        
                this.runtimeAttributes={};
    
                this.animate=function(){
                    if(this.isAnimated()){
                        return false
                    }
                    this.currentFrame=0;
                    this.totalFrames=(this.useSeconds)?Math.ceil(Ext.lib.AnimMgr.fps*this.duration):this.duration;
                    Ext.lib.AnimMgr.registerElement(this)
                };
        
                this.stop=function(O){
                    if(O){
                        this.currentFrame=this.totalFrames;
                        this._onTween.fire()
                    }
                    Ext.lib.AnimMgr.stop(this)
                };
        
                var N=function(){
                    this.onStart.fire();
                    this.runtimeAttributes={};
        
                    for(var O in this.attributes){
                        this.setRuntimeAttribute(O)
                    }
                    F=true;
                    J=0;
                    H=new Date()
                };
        
                var M=function(){
                    var Q={
                        duration:new Date()-this.getStartTime(),
                        currentFrame:this.currentFrame
                    };
            
                    Q.toString=function(){
                        return("duration: "+Q.duration+", currentFrame: "+Q.currentFrame)
                    };
            
                    this.onTween.fire(Q);
                    var P=this.runtimeAttributes;
                    for(var O in P){
                        this.setAttribute(O,this.doMethod(O,P[O].start,P[O].end),P[O].unit)
                    }
                    J+=1
                };
        
                var I=function(){
                    var O=(new Date()-H)/1000;
                    var P={
                        duration:O,
                        frames:J,
                        fps:J/O
                    };
            
                    P.toString=function(){
                        return("duration: "+P.duration+", frames: "+P.frames+", fps: "+P.fps)
                    };
            
                    F=false;
                    J=0;
                    this.onComplete.fire(P)
                };
        
                this._onStart=new Ext.util.Event(this);
                this.onStart=new Ext.util.Event(this);
                this.onTween=new Ext.util.Event(this);
                this._onTween=new Ext.util.Event(this);
                this.onComplete=new Ext.util.Event(this);
                this._onComplete=new Ext.util.Event(this);
                this._onStart.addListener(N);
                this._onTween.addListener(M);
                this._onComplete.addListener(I)
            }
        };

        Ext.lib.AnimMgr=new function(){
            var G=null;
            var F=[];
            var E=0;
            this.fps=1000;
            this.delay=1;
            this.registerElement=function(J){
                F[F.length]=J;
                E+=1;
                J._onStart.fire();
                this.start()
            };
        
            this.unRegister=function(K,J){
                K._onComplete.fire();
                J=J||I(K);
                if(J!=-1){
                    F.splice(J,1)
                }
                E-=1;
                if(E<=0){
                    this.stop()
                }
            };
    
            this.start=function(){
                if(G===null){
                    G=setInterval(this.run,this.delay)
                }
            };

            this.stop=function(L){
                if(!L){
                    clearInterval(G);
                    for(var K=0,J=F.length;K<J;++K){
                        if(F[0].isAnimated()){
                            this.unRegister(F[0],0)
                        }
                    }
                    F=[];
                    G=null;
                    E=0
                }else{
                    this.unRegister(L)
                }
            };

            this.run=function(){
                for(var L=0,J=F.length;L<J;++L){
                    var K=F[L];
                    if(!K||!K.isAnimated()){
                        continue
                    }
                    if(K.currentFrame<K.totalFrames||K.totalFrames===null){
                        K.currentFrame+=1;
                        if(K.useSeconds){
                            H(K)
                        }
                        K._onTween.fire()
                    }else{
                        Ext.lib.AnimMgr.stop(K,L)
                    }
                }
            };

            var I=function(L){
                for(var K=0,J=F.length;K<J;++K){
                    if(F[K]==L){
                        return K
                    }
                }
                return-1
            };

            var H=function(K){
                var N=K.totalFrames;
                var M=K.currentFrame;
                var L=(K.currentFrame*K.duration*1000/K.totalFrames);
                var J=(new Date()-K.getStartTime());
                var O=0;
                if(J<K.duration*1000){
                    O=Math.round((J/L-1)*K.currentFrame)
                }else{
                    O=N-(M+1)
                }
                if(O>0&&isFinite(O)){
                    if(K.currentFrame+O>=N){
                        O=N-(M+1)
                    }
                    K.currentFrame+=O
                }
            }
        };

        Ext.lib.Bezier=new function(){
            this.getPosition=function(I,H){
                var J=I.length;
                var G=[];
                for(var F=0;F<J;++F){
                    G[F]=[I[F][0],I[F][1]]
                }
                for(var E=1;E<J;++E){
                    for(F=0;F<J-E;++F){
                        G[F][0]=(1-H)*G[F][0]+H*G[parseInt(F+1,10)][0];
                        G[F][1]=(1-H)*G[F][1]+H*G[parseInt(F+1,10)][1]
                    }
                }
                return[G[0][0],G[0][1]]
            }
        };
        (function(){
            Ext.lib.ColorAnim=function(I,H,J,K){
                Ext.lib.ColorAnim.superclass.constructor.call(this,I,H,J,K)
            };
        
            Ext.extend(Ext.lib.ColorAnim,Ext.lib.AnimBase);
            var F=Ext.lib;
            var G=F.ColorAnim.superclass;
            var E=F.ColorAnim.prototype;
            E.toString=function(){
                var H=this.getEl();
                var I=H.id||H.tagName;
                return("ColorAnim "+I)
            };
        
            E.patterns.color=/color$/i;
            E.patterns.rgb=/^rgb\(([0-9]+)\s*,\s*([0-9]+)\s*,\s*([0-9]+)\)$/i;
            E.patterns.hex=/^#?([0-9A-F]{2})([0-9A-F]{2})([0-9A-F]{2})$/i;
            E.patterns.hex3=/^#?([0-9A-F]{1})([0-9A-F]{1})([0-9A-F]{1})$/i;
            E.patterns.transparent=/^transparent|rgba\(0, 0, 0, 0\)$/;
            E.parseColor=function(H){
                if(H.length==3){
                    return H
                }
                var I=this.patterns.hex.exec(H);
                if(I&&I.length==4){
                    return[parseInt(I[1],16),parseInt(I[2],16),parseInt(I[3],16)]
                }
                I=this.patterns.rgb.exec(H);
                if(I&&I.length==4){
                    return[parseInt(I[1],10),parseInt(I[2],10),parseInt(I[3],10)]
                }
                I=this.patterns.hex3.exec(H);
                if(I&&I.length==4){
                    return[parseInt(I[1]+I[1],16),parseInt(I[2]+I[2],16),parseInt(I[3]+I[3],16)]
                }
                return null
            };
        
            E.getAttribute=function(H){
                var J=this.getEl();
                if(this.patterns.color.test(H)){
                    var K=C(J).getStyle(H);
                    if(this.patterns.transparent.test(K)){
                        var I=J.parentNode;
                        K=C(I).getStyle(H);
                        while(I&&this.patterns.transparent.test(K)){
                            I=I.parentNode;
                            K=C(I).getStyle(H);
                            if(I.tagName.toUpperCase()=="HTML"){
                                K="#fff"
                            }
                        }
                    }
                }else{
                    K=G.getAttribute.call(this,H)
                }
                return K
            };

            E.doMethod=function(I,M,J){
                var L;
                if(this.patterns.color.test(I)){
                    L=[];
                    for(var K=0,H=M.length;K<H;++K){
                        L[K]=G.doMethod.call(this,I,M[K],J[K])
                    }
                    L="rgb("+Math.floor(L[0])+","+Math.floor(L[1])+","+Math.floor(L[2])+")"
                }else{
                    L=G.doMethod.call(this,I,M,J)
                }
                return L
            };
    
            E.setRuntimeAttribute=function(I){
                G.setRuntimeAttribute.call(this,I);
                if(this.patterns.color.test(I)){
                    var K=this.attributes;
                    var M=this.parseColor(this.runtimeAttributes[I].start);
                    var J=this.parseColor(this.runtimeAttributes[I].end);
                    if(typeof K[I]["to"]==="undefined"&&typeof K[I]["by"]!=="undefined"){
                        J=this.parseColor(K[I].by);
                        for(var L=0,H=M.length;L<H;++L){
                            J[L]=M[L]+J[L]
                        }
                    }
                    this.runtimeAttributes[I].start=M;
                    this.runtimeAttributes[I].end=J
                }
            }
        })();
        Ext.lib.Easing={
            easeNone:function(F,E,H,G){
                return H*F/G+E
            },
            easeIn:function(F,E,H,G){
                return H*(F/=G)*F+E
            },
            easeOut:function(F,E,H,G){
                return-H*(F/=G)*(F-2)+E
            },
            easeBoth:function(F,E,H,G){
                if((F/=G/2)<1){
                    return H/2*F*F+E
                }
                return-H/2*((--F)*(F-2)-1)+E
            },
            easeInStrong:function(F,E,H,G){
                return H*(F/=G)*F*F*F+E
            },
            easeOutStrong:function(F,E,H,G){
                return-H*((F=F/G-1)*F*F*F-1)+E
            },
            easeBothStrong:function(F,E,H,G){
                if((F/=G/2)<1){
                    return H/2*F*F*F*F+E
                }
                return-H/2*((F-=2)*F*F*F-2)+E
            },
            elasticIn:function(G,E,K,J,F,I){
                if(G==0){
                    return E
                }
                if((G/=J)==1){
                    return E+K
                }
                if(!I){
                    I=J*0.3
                }
                if(!F||F<Math.abs(K)){
                    F=K;
                    var H=I/4
                }else{
                    var H=I/(2*Math.PI)*Math.asin(K/F)
                }
                return-(F*Math.pow(2,10*(G-=1))*Math.sin((G*J-H)*(2*Math.PI)/I))+E
            },
            elasticOut:function(G,E,K,J,F,I){
                if(G==0){
                    return E
                }
                if((G/=J)==1){
                    return E+K
                }
                if(!I){
                    I=J*0.3
                }
                if(!F||F<Math.abs(K)){
                    F=K;
                    var H=I/4
                }else{
                    var H=I/(2*Math.PI)*Math.asin(K/F)
                }
                return F*Math.pow(2,-10*G)*Math.sin((G*J-H)*(2*Math.PI)/I)+K+E
            },
            elasticBoth:function(G,E,K,J,F,I){
                if(G==0){
                    return E
                }
                if((G/=J/2)==2){
                    return E+K
                }
                if(!I){
                    I=J*(0.3*1.5)
                }
                if(!F||F<Math.abs(K)){
                    F=K;
                    var H=I/4
                }else{
                    var H=I/(2*Math.PI)*Math.asin(K/F)
                }
                if(G<1){
                    return-0.5*(F*Math.pow(2,10*(G-=1))*Math.sin((G*J-H)*(2*Math.PI)/I))+E
                }
                return F*Math.pow(2,-10*(G-=1))*Math.sin((G*J-H)*(2*Math.PI)/I)*0.5+K+E
            },
            backIn:function(F,E,I,H,G){
                if(typeof G=="undefined"){
                    G=1.70158
                }
                return I*(F/=H)*F*((G+1)*F-G)+E
            },
            backOut:function(F,E,I,H,G){
                if(typeof G=="undefined"){
                    G=1.70158
                }
                return I*((F=F/H-1)*F*((G+1)*F+G)+1)+E
            },
            backBoth:function(F,E,I,H,G){
                if(typeof G=="undefined"){
                    G=1.70158
                }
                if((F/=H/2)<1){
                    return I/2*(F*F*(((G*=(1.525))+1)*F-G))+E
                }
                return I/2*((F-=2)*F*(((G*=(1.525))+1)*F+G)+2)+E
            },
            bounceIn:function(F,E,H,G){
                return H-Ext.lib.Easing.bounceOut(G-F,0,H,G)+E
            },
            bounceOut:function(F,E,H,G){
                if((F/=G)<(1/2.75)){
                    return H*(7.5625*F*F)+E
                }else{
                    if(F<(2/2.75)){
                        return H*(7.5625*(F-=(1.5/2.75))*F+0.75)+E
                    }else{
                        if(F<(2.5/2.75)){
                            return H*(7.5625*(F-=(2.25/2.75))*F+0.9375)+E
                        }
                    }
                }
                return H*(7.5625*(F-=(2.625/2.75))*F+0.984375)+E
            },
            bounceBoth:function(F,E,H,G){
                if(F<G/2){
                    return Ext.lib.Easing.bounceIn(F*2,0,H,G)*0.5+E
                }
                return Ext.lib.Easing.bounceOut(F*2-G,0,H,G)*0.5+H*0.5+E
            }
        };
        (function(){
            Ext.lib.Motion=function(K,J,L,M){
                if(K){
                    Ext.lib.Motion.superclass.constructor.call(this,K,J,L,M)
                }
            };
    
            Ext.extend(Ext.lib.Motion,Ext.lib.ColorAnim);
            var H=Ext.lib;
            var I=H.Motion.superclass;
            var F=H.Motion.prototype;
            F.toString=function(){
                var J=this.getEl();
                var K=J.id||J.tagName;
                return("Motion "+K)
            };
    
            F.patterns.points=/^points$/i;
            F.setAttribute=function(J,L,K){
                if(this.patterns.points.test(J)){
                    K=K||"px";
                    I.setAttribute.call(this,"left",L[0],K);
                    I.setAttribute.call(this,"top",L[1],K)
                }else{
                    I.setAttribute.call(this,J,L,K)
                }
            };

            F.getAttribute=function(J){
                if(this.patterns.points.test(J)){
                    var K=[I.getAttribute.call(this,"left"),I.getAttribute.call(this,"top")]
                }else{
                    K=I.getAttribute.call(this,J)
                }
                return K
            };
    
            F.doMethod=function(J,N,K){
                var M=null;
                if(this.patterns.points.test(J)){
                    var L=this.method(this.currentFrame,0,100,this.totalFrames)/100;
                    M=H.Bezier.getPosition(this.runtimeAttributes[J],L)
                }else{
                    M=I.doMethod.call(this,J,N,K)
                }
                return M
            };
    
            F.setRuntimeAttribute=function(S){
                if(this.patterns.points.test(S)){
                    var K=this.getEl();
                    var M=this.attributes;
                    var J;
                    var O=M["points"]["control"]||[];
                    var L;
                    var P,R;
                    if(O.length>0&&!Ext.isArray(O[0])){
                        O=[O]
                    }else{
                        var N=[];
                        for(P=0,R=O.length;P<R;++P){
                            N[P]=O[P]
                        }
                        O=N
                    }
                    Ext.fly(K).position();
                    if(G(M["points"]["from"])){
                        Ext.lib.Dom.setXY(K,M["points"]["from"])
                    }else{
                        Ext.lib.Dom.setXY(K,Ext.lib.Dom.getXY(K))
                    }
                    J=this.getAttribute("points");
                    if(G(M["points"]["to"])){
                        L=E.call(this,M["points"]["to"],J);
                        var Q=Ext.lib.Dom.getXY(this.getEl());
                        for(P=0,R=O.length;P<R;++P){
                            O[P]=E.call(this,O[P],J)
                        }
                    }else{
                        if(G(M["points"]["by"])){
                            L=[J[0]+M["points"]["by"][0],J[1]+M["points"]["by"][1]];
                            for(P=0,R=O.length;P<R;++P){
                                O[P]=[J[0]+O[P][0],J[1]+O[P][1]]
                            }
                        }
                    }
                    this.runtimeAttributes[S]=[J];
                    if(O.length>0){
                        this.runtimeAttributes[S]=this.runtimeAttributes[S].concat(O)
                    }
                    this.runtimeAttributes[S][this.runtimeAttributes[S].length]=L
                }else{
                    I.setRuntimeAttribute.call(this,S)
                }
            };

            var E=function(J,L){
                var K=Ext.lib.Dom.getXY(this.getEl());
                J=[J[0]-K[0]+L[0],J[1]-K[1]+L[1]];
                return J
            };
    
            var G=function(J){
                return(typeof J!=="undefined")
            }
        })();
        (function(){
            Ext.lib.Scroll=function(I,H,J,K){
                if(I){
                    Ext.lib.Scroll.superclass.constructor.call(this,I,H,J,K)
                }
            };
    
            Ext.extend(Ext.lib.Scroll,Ext.lib.ColorAnim);
            var F=Ext.lib;
            var G=F.Scroll.superclass;
            var E=F.Scroll.prototype;
            E.toString=function(){
                var H=this.getEl();
                var I=H.id||H.tagName;
                return("Scroll "+I)
            };
    
            E.doMethod=function(H,K,I){
                var J=null;
                if(H=="scroll"){
                    J=[this.method(this.currentFrame,K[0],I[0]-K[0],this.totalFrames),this.method(this.currentFrame,K[1],I[1]-K[1],this.totalFrames)]
                }else{
                    J=G.doMethod.call(this,H,K,I)
                }
                return J
            };
    
            E.getAttribute=function(H){
                var J=null;
                var I=this.getEl();
                if(H=="scroll"){
                    J=[I.scrollLeft,I.scrollTop]
                }else{
                    J=G.getAttribute.call(this,H)
                }
                return J
            };
    
            E.setAttribute=function(H,K,J){
                var I=this.getEl();
                if(H=="scroll"){
                    I.scrollLeft=K[0];
                    I.scrollTop=K[1]
                }else{
                    G.setAttribute.call(this,H,K,J)
                }
            }
        })()
    })();
    Ext.DomHelper=function(){
        var L=null;
        var F=/^(?:br|frame|hr|img|input|link|meta|range|spacer|wbr|area|param|col)$/i;
        var B=/^table|tbody|tr|td$/i;
        var A=function(T){
            if(typeof T=="string"){
                return T
            }
            var O="";
            if(Ext.isArray(T)){
                for(var R=0,P=T.length;R<P;R++){
                    O+=A(T[R])
                }
                return O
            }
            if(!T.tag){
                T.tag="div"
            }
            O+="<"+T.tag;
            for(var N in T){
                if(N=="tag"||N=="children"||N=="cn"||N=="html"||typeof T[N]=="function"){
                    continue
                }
                if(N=="style"){
                    var S=T["style"];
                    if(typeof S=="function"){
                        S=S.call()
                    }
                    if(typeof S=="string"){
                        O+=" style=\""+S+"\""
                    }else{
                        if(typeof S=="object"){
                            O+=" style=\"";
                            for(var Q in S){
                                if(typeof S[Q]!="function"){
                                    O+=Q+":"+S[Q]+";"
                                }
                            }
                            O+="\""
                        }
                    }
                }else{
                    if(N=="cls"){
                        O+=" class=\""+T["cls"]+"\""
                    }else{
                        if(N=="htmlFor"){
                            O+=" for=\""+T["htmlFor"]+"\""
                        }else{
                            O+=" "+N+"=\""+T[N]+"\""
                        }
                    }
                }
            }
            if(F.test(T.tag)){
                O+="/>"
            }else{
                O+=">";
                var U=T.children||T.cn;
                if(U){
                    O+=A(U)
                }else{
                    if(T.html){
                        O+=T.html
                    }
                }
                O+="</"+T.tag+">"
            }
            return O
        };

        var M=function(T,O){
            var S;
            if(Ext.isArray(T)){
                S=document.createDocumentFragment();
                for(var R=0,P=T.length;R<P;R++){
                    M(T[R],S)
                }
            }else{
                if(typeof T=="string)"){
                    S=document.createTextNode(T)
                }else{
                    S=document.createElement(T.tag||"div");
                    var Q=!!S.setAttribute;
                    for(var N in T){
                        if(N=="tag"||N=="children"||N=="cn"||N=="html"||N=="style"||typeof T[N]=="function"){
                            continue
                        }
                        if(N=="cls"){
                            S.className=T["cls"]
                        }else{
                            if(Q){
                                S.setAttribute(N,T[N])
                            }else{
                                S[N]=T[N]
                            }
                        }
                    }
                    Ext.DomHelper.applyStyles(S,T.style);
                    var U=T.children||T.cn;
                    if(U){
                        M(U,S)
                    }else{
                        if(T.html){
                            S.innerHTML=T.html
                        }
                    }
                }
            }
            if(O){
                O.appendChild(S)
            }
            return S
        };

        var I=function(S,Q,P,R){
            L.innerHTML=[Q,P,R].join("");
            var N=-1,O=L;
            while(++N<S){
                O=O.firstChild
            }
            return O
        };
    
        var J="<table>",E="</table>",C=J+"<tbody>",K="</tbody>"+E,H=C+"<tr>",D="</tr>"+K;
        var G=function(N,O,Q,P){
            if(!L){
                L=document.createElement("div")
            }
            var R;
            var S=null;
            if(N=="td"){
                if(O=="afterbegin"||O=="beforeend"){
                    return
                }
                if(O=="beforebegin"){
                    S=Q;
                    Q=Q.parentNode
                }else{
                    S=Q.nextSibling;
                    Q=Q.parentNode
                }
                R=I(4,H,P,D)
            }else{
                if(N=="tr"){
                    if(O=="beforebegin"){
                        S=Q;
                        Q=Q.parentNode;
                        R=I(3,C,P,K)
                    }else{
                        if(O=="afterend"){
                            S=Q.nextSibling;
                            Q=Q.parentNode;
                            R=I(3,C,P,K)
                        }else{
                            if(O=="afterbegin"){
                                S=Q.firstChild
                            }
                            R=I(4,H,P,D)
                        }
                    }
                }else{
                    if(N=="tbody"){
                        if(O=="beforebegin"){
                            S=Q;
                            Q=Q.parentNode;
                            R=I(2,J,P,E)
                        }else{
                            if(O=="afterend"){
                                S=Q.nextSibling;
                                Q=Q.parentNode;
                                R=I(2,J,P,E)
                            }else{
                                if(O=="afterbegin"){
                                    S=Q.firstChild
                                }
                                R=I(3,C,P,K)
                            }
                        }
                    }else{
                        if(O=="beforebegin"||O=="afterend"){
                            return
                        }
                        if(O=="afterbegin"){
                            S=Q.firstChild
                        }
                        R=I(2,J,P,E)
                    }
                }
            }
            Q.insertBefore(R,S);
            return R
        };

        return{
            useDom:false,
            markup:function(N){
                return A(N)
            },
            applyStyles:function(P,Q){
                if(Q){
                    P=Ext.fly(P);
                    if(typeof Q=="string"){
                        var O=/\s?([a-z\-]*)\:\s?([^;]*);?/gi;
                        var R;
                        while((R=O.exec(Q))!=null){
                            P.setStyle(R[1],R[2])
                        }
                    }else{
                        if(typeof Q=="object"){
                            for(var N in Q){
                                P.setStyle(N,Q[N])
                            }
                        }else{
                            if(typeof Q=="function"){
                                Ext.DomHelper.applyStyles(P,Q.call())
                            }
                        }
                    }
                }
            },
            insertHtml:function(P,R,Q){
                P=P.toLowerCase();
                if(R.insertAdjacentHTML){
                    if(B.test(R.tagName)){
                        var O;
                        if(O=G(R.tagName.toLowerCase(),P,R,Q)){
                            return O
                        }
                    }
                    switch(P){
                        case"beforebegin":
                            R.insertAdjacentHTML("BeforeBegin",Q);
                            return R.previousSibling;
                        case"afterbegin":
                            R.insertAdjacentHTML("AfterBegin",Q);
                            return R.firstChild;
                        case"beforeend":
                            R.insertAdjacentHTML("BeforeEnd",Q);
                            return R.lastChild;
                        case"afterend":
                            R.insertAdjacentHTML("AfterEnd",Q);
                            return R.nextSibling
                    }
                    throw"Illegal insertion point -> \""+P+"\""
                }
                var N=R.ownerDocument.createRange();
                var S;
                switch(P){
                    case"beforebegin":
                        N.setStartBefore(R);
                        S=N.createContextualFragment(Q);
                        R.parentNode.insertBefore(S,R);
                        return R.previousSibling;
                    case"afterbegin":
                        if(R.firstChild){
                            N.setStartBefore(R.firstChild);
                            S=N.createContextualFragment(Q);
                            R.insertBefore(S,R.firstChild);
                            return R.firstChild
                        }else{
                            R.innerHTML=Q;
                            return R.firstChild
                        }
                    case"beforeend":
                        if(R.lastChild){
                            N.setStartAfter(R.lastChild);
                            S=N.createContextualFragment(Q);
                            R.appendChild(S);
                            return R.lastChild
                        }else{
                            R.innerHTML=Q;
                            return R.lastChild
                        }
                    case"afterend":
                        N.setStartAfter(R);
                        S=N.createContextualFragment(Q);
                        R.parentNode.insertBefore(S,R.nextSibling);
                        return R.nextSibling
                }
                throw"Illegal insertion point -> \""+P+"\""
            },
            insertBefore:function(N,P,O){
                return this.doInsert(N,P,O,"beforeBegin")
            },
            insertAfter:function(N,P,O){
                return this.doInsert(N,P,O,"afterEnd","nextSibling")
            },
            insertFirst:function(N,P,O){
                return this.doInsert(N,P,O,"afterBegin","firstChild")
            },
            doInsert:function(Q,S,R,T,P){
                Q=Ext.getDom(Q);
                var O;
                if(this.useDom){
                    O=M(S,null);
                    (P==="firstChild"?Q:Q.parentNode).insertBefore(O,P?Q[P]:Q)
                }else{
                    var N=A(S);
                    O=this.insertHtml(T,Q,N)
                }
                return R?Ext.get(O,true):O
            },
            append:function(P,R,Q){
                P=Ext.getDom(P);
                var O;
                if(this.useDom){
                    O=M(R,null);
                    P.appendChild(O)
                }else{
                    var N=A(R);
                    O=this.insertHtml("beforeEnd",P,N)
                }
                return Q?Ext.get(O,true):O
            },
            overwrite:function(N,P,O){
                N=Ext.getDom(N);
                N.innerHTML=A(P);
                return O?Ext.get(N.firstChild,true):N.firstChild
            },
            createTemplate:function(O){
                var N=A(O);
                return new Ext.Template(N)
            }
        }
    }();
    Ext.Template=function(E){
        var B=arguments;
        if(Ext.isArray(E)){
            E=E.join("")
        }else{
            if(B.length>1){
                var C=[];
                for(var D=0,A=B.length;D<A;D++){
                    if(typeof B[D]=="object"){
                        Ext.apply(this,B[D])
                    }else{
                        C[C.length]=B[D]
                    }
                }
                E=C.join("")
            }
        }
        this.html=E;
        if(this.compiled){
            this.compile()
        }
    };

    Ext.Template.prototype={
        applyTemplate:function(B){
            if(this.compiled){
                return this.compiled(B)
            }
            var A=this.disableFormats!==true;
            var E=Ext.util.Format,C=this;
            var D=function(G,I,L,H){
                if(L&&A){
                    if(L.substr(0,5)=="this."){
                        return C.call(L.substr(5),B[I],B)
                    }else{
                        if(H){
                            var K=/^\s*['"](.*)["']\s*$/;
                            H=H.split(",");
                            for(var J=0,F=H.length;J<F;J++){
                                H[J]=H[J].replace(K,"$1")
                            }
                            H=[B[I]].concat(H)
                        }else{
                            H=[B[I]]
                        }
                        return E[L].apply(E,H)
                    }
                }else{
                    return B[I]!==undefined?B[I]:""
                }
            };
    
            return this.html.replace(this.re,D)
        },
        set:function(A,B){
            this.html=A;
            this.compiled=null;
            if(B){
                this.compile()
            }
            return this
        },
        disableFormats:false,
        re:/\{([\w-]+)(?:\:([\w\.]*)(?:\((.*?)?\))?)?\}/g,
        compile:function(){
            var fm=Ext.util.Format;
            var useF=this.disableFormats!==true;
            var sep=Ext.isGecko?"+":",";
            var fn=function(m,name,format,args){
                if(format&&useF){
                    args=args?","+args:"";
                    if(format.substr(0,5)!="this."){
                        format="fm."+format+"("
                    }else{
                        format="this.call(\""+format.substr(5)+"\", ";
                        args=", values"
                    }
                }else{
                    args="";
                    format="(values['"+name+"'] == undefined ? '' : "
                }
                return"'"+sep+format+"values['"+name+"']"+args+")"+sep+"'"
            };
    
            var body;
            if(Ext.isGecko){
                body="this.compiled = function(values){ return '"+this.html.replace(/\\/g,"\\\\").replace(/(\r\n|\n)/g,"\\n").replace(/'/g,"\\'").replace(this.re,fn)+"';};"
            }else{
                body=["this.compiled = function(values){ return ['"];
                body.push(this.html.replace(/\\/g,"\\\\").replace(/(\r\n|\n)/g,"\\n").replace(/'/g,"\\'").replace(this.re,fn));
                body.push("'].join('');};");
                body=body.join("")
            }
            eval(body);
            return this
        },
        call:function(C,B,A){
            return this[C](B,A)
        },
        insertFirst:function(B,A,C){
            return this.doInsert("afterBegin",B,A,C)
        },
        insertBefore:function(B,A,C){
            return this.doInsert("beforeBegin",B,A,C)
        },
        insertAfter:function(B,A,C){
            return this.doInsert("afterEnd",B,A,C)
        },
        append:function(B,A,C){
            return this.doInsert("beforeEnd",B,A,C)
        },
        doInsert:function(C,E,B,A){
            E=Ext.getDom(E);
            var D=Ext.DomHelper.insertHtml(C,E,this.applyTemplate(B));
            return A?Ext.get(D,true):D
        },
        overwrite:function(B,A,C){
            B=Ext.getDom(B);
            B.innerHTML=this.applyTemplate(A);
            return C?Ext.get(B.firstChild,true):B.firstChild
        }
    };

    Ext.Template.prototype.apply=Ext.Template.prototype.applyTemplate;
    Ext.DomHelper.Template=Ext.Template;
    Ext.Template.from=function(B,A){
        B=Ext.getDom(B);
        return new Ext.Template(B.value||B.innerHTML,A||"")
    };
    
    Ext.DomQuery=function(){
        var cache={},simpleCache={},valueCache={};
    
        var nonSpace=/\S/;
        var trimRe=/^\s+|\s+$/g;
        var tplRe=/\{(\d+)\}/g;
        var modeRe=/^(\s?[\/>+~]\s?|\s|$)/;
        var tagTokenRe=/^(#)?([\w-\*]+)/;
        var nthRe=/(\d*)n\+?(\d*)/,nthRe2=/\D/;
        function child(p,index){
            var i=0;
            var n=p.firstChild;
            while(n){
                if(n.nodeType==1){
                    if(++i==index){
                        return n
                    }
                }
                n=n.nextSibling
            }
            return null
        }
        function next(n){
            while((n=n.nextSibling)&&n.nodeType!=1){}
            return n
        }
        function prev(n){
            while((n=n.previousSibling)&&n.nodeType!=1){}
            return n
        }
        function children(d){
            var n=d.firstChild,ni=-1;
            while(n){
                var nx=n.nextSibling;
                if(n.nodeType==3&&!nonSpace.test(n.nodeValue)){
                    d.removeChild(n)
                }else{
                    n.nodeIndex=++ni
                }
                n=nx
            }
            return this
        }
        function byClassName(c,a,v){
            if(!v){
                return c
            }
            var r=[],ri=-1,cn;
            for(var i=0,ci;ci=c[i];i++){
                if((" "+ci.className+" ").indexOf(v)!=-1){
                    r[++ri]=ci
                }
            }
            return r
        }
        function attrValue(n,attr){
            if(!n.tagName&&typeof n.length!="undefined"){
                n=n[0]
            }
            if(!n){
                return null
            }
            if(attr=="for"){
                return n.htmlFor
            }
            if(attr=="class"||attr=="className"){
                return n.className
            }
            return n.getAttribute(attr)||n[attr]
        }
        function getNodes(ns,mode,tagName){
            var result=[],ri=-1,cs;
            if(!ns){
                return result
            }
            tagName=tagName||"*";
            if(typeof ns.getElementsByTagName!="undefined"){
                ns=[ns]
            }
            if(!mode){
                for(var i=0,ni;ni=ns[i];i++){
                    cs=ni.getElementsByTagName(tagName);
                    for(var j=0,ci;ci=cs[j];j++){
                        result[++ri]=ci
                    }
                }
            }else{
                if(mode=="/"||mode==">"){
                    var utag=tagName.toUpperCase();
                    for(var i=0,ni,cn;ni=ns[i];i++){
                        cn=ni.children||ni.childNodes;
                        for(var j=0,cj;cj=cn[j];j++){
                            if(cj.nodeName==utag||cj.nodeName==tagName||tagName=="*"){
                                result[++ri]=cj
                            }
                        }
                    }
                }else{
                    if(mode=="+"){
                        var utag=tagName.toUpperCase();
                        for(var i=0,n;n=ns[i];i++){
                            while((n=n.nextSibling)&&n.nodeType!=1){}
                            if(n&&(n.nodeName==utag||n.nodeName==tagName||tagName=="*")){
                                result[++ri]=n
                            }
                        }
                    }else{
                        if(mode=="~"){
                            for(var i=0,n;n=ns[i];i++){
                                while((n=n.nextSibling)&&(n.nodeType!=1||(tagName=="*"||n.tagName.toLowerCase()!=tagName))){}
                                if(n){
                                    result[++ri]=n
                                }
                            }
                        }
                    }
                }
            }
            return result
        }
        function concat(a,b){
            if(b.slice){
                return a.concat(b)
            }
            for(var i=0,l=b.length;i<l;i++){
                a[a.length]=b[i]
            }
            return a
        }
        function byTag(cs,tagName){
            if(cs.tagName||cs==document){
                cs=[cs]
            }
            if(!tagName){
                return cs
            }
            var r=[],ri=-1;
            tagName=tagName.toLowerCase();
            for(var i=0,ci;ci=cs[i];i++){
                if(ci.nodeType==1&&ci.tagName.toLowerCase()==tagName){
                    r[++ri]=ci
                }
            }
            return r
        }
        function byId(cs,attr,id){
            if(cs.tagName||cs==document){
                cs=[cs]
            }
            if(!id){
                return cs
            }
            var r=[],ri=-1;
            for(var i=0,ci;ci=cs[i];i++){
                if(ci&&ci.id==id){
                    r[++ri]=ci;
                    return r
                }
            }
            return r
        }
        function byAttribute(cs,attr,value,op,custom){
            var r=[],ri=-1,st=custom=="{";
            var f=Ext.DomQuery.operators[op];
            for(var i=0,ci;ci=cs[i];i++){
                var a;
                if(st){
                    a=Ext.DomQuery.getStyle(ci,attr)
                }else{
                    if(attr=="class"||attr=="className"){
                        a=ci.className
                    }else{
                        if(attr=="for"){
                            a=ci.htmlFor
                        }else{
                            if(attr=="href"){
                                a=ci.getAttribute("href",2)
                            }else{
                                a=ci.getAttribute(attr)
                            }
                        }
                    }
                }
                if((f&&f(a,value))||(!f&&a)){
                    r[++ri]=ci
                }
            }
            return r
        }
        function byPseudo(cs,name,value){
            return Ext.DomQuery.pseudos[name](cs,value)
        }
        var isIE=window.ActiveXObject?true:false;
        eval("var batch = 30803;");
        var key=30803;
        function nodupIEXml(cs){
            var d=++key;
            cs[0].setAttribute("_nodup",d);
            var r=[cs[0]];
            for(var i=1,len=cs.length;i<len;i++){
                var c=cs[i];
                if(!c.getAttribute("_nodup")!=d){
                    c.setAttribute("_nodup",d);
                    r[r.length]=c
                }
            }
            for(var i=0,len=cs.length;i<len;i++){
                cs[i].removeAttribute("_nodup")
            }
            return r
        }
        function nodup(cs){
            if(!cs){
                return[]
            }
            var len=cs.length,c,i,r=cs,cj,ri=-1;
            if(!len||typeof cs.nodeType!="undefined"||len==1){
                return cs
            }
            if(isIE&&typeof cs[0].selectSingleNode!="undefined"){
                return nodupIEXml(cs)
            }
            var d=++key;
            cs[0]._nodup=d;
            for(i=1;c=cs[i];i++){
                if(c._nodup!=d){
                    c._nodup=d
                }else{
                    r=[];
                    for(var j=0;j<i;j++){
                        r[++ri]=cs[j]
                    }
                    for(j=i+1;cj=cs[j];j++){
                        if(cj._nodup!=d){
                            cj._nodup=d;
                            r[++ri]=cj
                        }
                    }
                    return r
                }
            }
            return r
        }
        function quickDiffIEXml(c1,c2){
            var d=++key;
            for(var i=0,len=c1.length;i<len;i++){
                c1[i].setAttribute("_qdiff",d)
            }
            var r=[];
            for(var i=0,len=c2.length;i<len;i++){
                if(c2[i].getAttribute("_qdiff")!=d){
                    r[r.length]=c2[i]
                }
            }
            for(var i=0,len=c1.length;i<len;i++){
                c1[i].removeAttribute("_qdiff")
            }
            return r
        }
        function quickDiff(c1,c2){
            var len1=c1.length;
            if(!len1){
                return c2
            }
            if(isIE&&c1[0].selectSingleNode){
                return quickDiffIEXml(c1,c2)
            }
            var d=++key;
            for(var i=0;i<len1;i++){
                c1[i]._qdiff=d
            }
            var r=[];
            for(var i=0,len=c2.length;i<len;i++){
                if(c2[i]._qdiff!=d){
                    r[r.length]=c2[i]
                }
            }
            return r
        }
        function quickId(ns,mode,root,id){
            if(ns==root){
                var d=root.ownerDocument||root;
                return d.getElementById(id)
            }
            ns=getNodes(ns,mode,"*");
            return byId(ns,null,id)
        }
        return{
            getStyle:function(el,name){
                return Ext.fly(el).getStyle(name)
            },
            compile:function(path,type){
                type=type||"select";
                var fn=["var f = function(root){\n var mode; ++batch; var n = root || document;\n"];
                var q=path,mode,lq;
                var tk=Ext.DomQuery.matchers;
                var tklen=tk.length;
                var mm;
                var lmode=q.match(modeRe);
                if(lmode&&lmode[1]){
                    fn[fn.length]="mode=\""+lmode[1].replace(trimRe,"")+"\";";
                    q=q.replace(lmode[1],"")
                }while(path.substr(0,1)=="/"){
                    path=path.substr(1)
                }while(q&&lq!=q){
                    lq=q;
                    var tm=q.match(tagTokenRe);
                    if(type=="select"){
                        if(tm){
                            if(tm[1]=="#"){
                                fn[fn.length]="n = quickId(n, mode, root, \""+tm[2]+"\");"
                            }else{
                                fn[fn.length]="n = getNodes(n, mode, \""+tm[2]+"\");"
                            }
                            q=q.replace(tm[0],"")
                        }else{
                            if(q.substr(0,1)!="@"){
                                fn[fn.length]="n = getNodes(n, mode, \"*\");"
                            }
                        }
                    }else{
                        if(tm){
                            if(tm[1]=="#"){
                                fn[fn.length]="n = byId(n, null, \""+tm[2]+"\");"
                            }else{
                                fn[fn.length]="n = byTag(n, \""+tm[2]+"\");"
                            }
                            q=q.replace(tm[0],"")
                        }
                    }while(!(mm=q.match(modeRe))){
                        var matched=false;
                        for(var j=0;j<tklen;j++){
                            var t=tk[j];
                            var m=q.match(t.re);
                            if(m){
                                fn[fn.length]=t.select.replace(tplRe,function(x,i){
                                    return m[i]
                                });
                                q=q.replace(m[0],"");
                                matched=true;
                                break
                            }
                        }
                        if(!matched){
                            throw"Error parsing selector, parsing failed at \""+q+"\""
                        }
                    }
                    if(mm[1]){
                        fn[fn.length]="mode=\""+mm[1].replace(trimRe,"")+"\";";
                        q=q.replace(mm[1],"")
                    }
                }
                fn[fn.length]="return nodup(n);\n}";
                eval(fn.join(""));
                return f
            },
            select:function(path,root,type){
                if(!root||root==document){
                    root=document
                }
                if(typeof root=="string"){
                    root=document.getElementById(root)
                }
                var paths=path.split(",");
                var results=[];
                for(var i=0,len=paths.length;i<len;i++){
                    var p=paths[i].replace(trimRe,"");
                    if(!cache[p]){
                        cache[p]=Ext.DomQuery.compile(p);
                        if(!cache[p]){
                            throw p+" is not a valid selector"
                        }
                    }
                    var result=cache[p](root);
                    if(result&&result!=document){
                        results=results.concat(result)
                    }
                }
                if(paths.length>1){
                    return nodup(results)
                }
                return results
            },
            selectNode:function(path,root){
                return Ext.DomQuery.select(path,root)[0]
            },
            selectValue:function(path,root,defaultValue){
                path=path.replace(trimRe,"");
                if(!valueCache[path]){
                    valueCache[path]=Ext.DomQuery.compile(path,"select")
                }
                var n=valueCache[path](root);
                n=n[0]?n[0]:n;
                var v=(n&&n.firstChild?n.firstChild.nodeValue:null);
                return((v===null||v===undefined||v==="")?defaultValue:v)
            },
            selectNumber:function(path,root,defaultValue){
                var v=Ext.DomQuery.selectValue(path,root,defaultValue||0);
                return parseFloat(v)
            },
            is:function(el,ss){
                if(typeof el=="string"){
                    el=document.getElementById(el)
                }
                var isArray=Ext.isArray(el);
                var result=Ext.DomQuery.filter(isArray?el:[el],ss);
                return isArray?(result.length==el.length):(result.length>0)
            },
            filter:function(els,ss,nonMatches){
                ss=ss.replace(trimRe,"");
                if(!simpleCache[ss]){
                    simpleCache[ss]=Ext.DomQuery.compile(ss,"simple")
                }
                var result=simpleCache[ss](els);
                return nonMatches?quickDiff(result,els):result
            },
            matchers:[{
                re:/^\.([\w-]+)/,
                select:"n = byClassName(n, null, \" {1} \");"
            },{
                re:/^\:([\w-]+)(?:\(((?:[^\s>\/]*|.*?))\))?/,
                select:"n = byPseudo(n, \"{1}\", \"{2}\");"
            },{
                re:/^(?:([\[\{])(?:@)?([\w-]+)\s?(?:(=|.=)\s?['"]?(.*?)["']?)?[\]\}])/,
                select:"n = byAttribute(n, \"{2}\", \"{4}\", \"{3}\", \"{1}\");"
            },{
                re:/^#([\w-]+)/,
                select:"n = byId(n, null, \"{1}\");"
            },{
                re:/^@([\w-]+)/,
                select:"return {firstChild:{nodeValue:attrValue(n, \"{1}\")}};"
            }],
            operators:{
                "=":function(a,v){
                    return a==v
                },
                "!=":function(a,v){
                    return a!=v
                },
                "^=":function(a,v){
                    return a&&a.substr(0,v.length)==v
                },
                "$=":function(a,v){
                    return a&&a.substr(a.length-v.length)==v
                },
                "*=":function(a,v){
                    return a&&a.indexOf(v)!==-1
                },
                "%=":function(a,v){
                    return(a%v)==0
                },
                "|=":function(a,v){
                    return a&&(a==v||a.substr(0,v.length+1)==v+"-")
                },
                "~=":function(a,v){
                    return a&&(" "+a+" ").indexOf(" "+v+" ")!=-1
                }
            },
            pseudos:{
                "first-child":function(c){
                    var r=[],ri=-1,n;
                    for(var i=0,ci;ci=n=c[i];i++){
                        while((n=n.previousSibling)&&n.nodeType!=1){}
                        if(!n){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "last-child":function(c){
                    var r=[],ri=-1,n;
                    for(var i=0,ci;ci=n=c[i];i++){
                        while((n=n.nextSibling)&&n.nodeType!=1){}
                        if(!n){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "nth-child":function(c,a){
                    var r=[],ri=-1;
                    var m=nthRe.exec(a=="even"&&"2n"||a=="odd"&&"2n+1"||!nthRe2.test(a)&&"n+"+a||a);
                    var f=(m[1]||1)-0,l=m[2]-0;
                    for(var i=0,n;n=c[i];i++){
                        var pn=n.parentNode;
                        if(batch!=pn._batch){
                            var j=0;
                            for(var cn=pn.firstChild;cn;cn=cn.nextSibling){
                                if(cn.nodeType==1){
                                    cn.nodeIndex=++j
                                }
                            }
                            pn._batch=batch
                        }
                        if(f==1){
                            if(l==0||n.nodeIndex==l){
                                r[++ri]=n
                            }
                        }else{
                            if((n.nodeIndex+l)%f==0){
                                r[++ri]=n
                            }
                        }
                    }
                    return r
                },
                "only-child":function(c){
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        if(!prev(ci)&&!next(ci)){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "empty":function(c){
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        var cns=ci.childNodes,j=0,cn,empty=true;
                        while(cn=cns[j]){
                            ++j;
                            if(cn.nodeType==1||cn.nodeType==3){
                                empty=false;
                                break
                            }
                        }
                        if(empty){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "contains":function(c,v){
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        if((ci.textContent||ci.innerText||"").indexOf(v)!=-1){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "nodeValue":function(c,v){
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        if(ci.firstChild&&ci.firstChild.nodeValue==v){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "checked":function(c){
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        if(ci.checked==true){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "not":function(c,ss){
                    return Ext.DomQuery.filter(c,ss,true)
                },
                "any":function(c,selectors){
                    var ss=selectors.split("|");
                    var r=[],ri=-1,s;
                    for(var i=0,ci;ci=c[i];i++){
                        for(var j=0;s=ss[j];j++){
                            if(Ext.DomQuery.is(ci,s)){
                                r[++ri]=ci;
                                break
                            }
                        }
                    }
                    return r
                },
                "odd":function(c){
                    return this["nth-child"](c,"odd")
                },
                "even":function(c){
                    return this["nth-child"](c,"even")
                },
                "nth":function(c,a){
                    return c[a-1]||[]
                },
                "first":function(c){
                    return c[0]||[]
                },
                "last":function(c){
                    return c[c.length-1]||[]
                },
                "has":function(c,ss){
                    var s=Ext.DomQuery.select;
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        if(s(ss,ci).length>0){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "next":function(c,ss){
                    var is=Ext.DomQuery.is;
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        var n=next(ci);
                        if(n&&is(n,ss)){
                            r[++ri]=ci
                        }
                    }
                    return r
                },
                "prev":function(c,ss){
                    var is=Ext.DomQuery.is;
                    var r=[],ri=-1;
                    for(var i=0,ci;ci=c[i];i++){
                        var n=prev(ci);
                        if(n&&is(n,ss)){
                            r[++ri]=ci
                        }
                    }
                    return r
                }
            }
        }
    }();
    Ext.query=Ext.DomQuery.select;
    Ext.util.Observable=function(){
        if(this.listeners){
            this.on(this.listeners);
            delete this.listeners
        }
    };

    Ext.util.Observable.prototype={
        fireEvent:function(){
            if(this.eventsSuspended!==true){
                var A=this.events[arguments[0].toLowerCase()];
                if(typeof A=="object"){
                    return A.fire.apply(A,Array.prototype.slice.call(arguments,1))
                }
            }
            return true
        },
        filterOptRe:/^(?:scope|delay|buffer|single)$/,
        addListener:function(A,C,B,F){
            if(typeof A=="object"){
                F=A;
                for(var E in F){
                    if(this.filterOptRe.test(E)){
                        continue
                    }
                    if(typeof F[E]=="function"){
                        this.addListener(E,F[E],F.scope,F)
                    }else{
                        this.addListener(E,F[E].fn,F[E].scope,F[E])
                    }
                }
                return
            }
            F=(!F||typeof F=="boolean")?{}:F;
            A=A.toLowerCase();
            var D=this.events[A]||true;
            if(typeof D=="boolean"){
                D=new Ext.util.Event(this,A);
                this.events[A]=D
            }
            D.addListener(C,B,F)
        },
        removeListener:function(A,C,B){
            var D=this.events[A.toLowerCase()];
            if(typeof D=="object"){
                D.removeListener(C,B)
            }
        },
        purgeListeners:function(){
            for(var A in this.events){
                if(typeof this.events[A]=="object"){
                    this.events[A].clearListeners()
                }
            }
        },
        relayEvents:function(F,D){
            var E=function(G){
                return function(){
                    return this.fireEvent.apply(this,Ext.combine(G,Array.prototype.slice.call(arguments,0)))
                }
            };
    
            for(var C=0,A=D.length;C<A;C++){
                var B=D[C];
                if(!this.events[B]){
                    this.events[B]=true
                }
                F.on(B,E(B),this)
            }
        },
        addEvents:function(D){
            if(!this.events){
                this.events={}
            }
            if(typeof D=="string"){
                for(var C=0,A=arguments,B;B=A[C];C++){
                    if(!this.events[A[C]]){
                        D[A[C]]=true
                    }
                }
            }else{
                Ext.applyIf(this.events,D)
            }
        },
        hasListener:function(A){
            var B=this.events[A];
            return typeof B=="object"&&B.listeners.length>0
        },
        suspendEvents:function(){
            this.eventsSuspended=true
        },
        resumeEvents:function(){
            this.eventsSuspended=false
        },
        getMethodEvent:function(G){
            if(!this.methodEvents){
                this.methodEvents={}
            }
            var F=this.methodEvents[G];
            if(!F){
                F={};
    
                this.methodEvents[G]=F;
                F.originalFn=this[G];
                F.methodName=G;
                F.before=[];
                F.after=[];
                var C,B,D;
                var E=this;
                var A=function(J,I,H){
                    if((B=J.apply(I||E,H))!==undefined){
                        if(typeof B==="object"){
                            if(B.returnValue!==undefined){
                                C=B.returnValue
                            }else{
                                C=B
                            }
                            if(B.cancel===true){
                                D=true
                            }
                        }else{
                            if(B===false){
                                D=true
                            }else{
                                C=B
                            }
                        }
                    }
                };

                this[G]=function(){
                    C=B=undefined;
                    D=false;
                    var I=Array.prototype.slice.call(arguments,0);
                    for(var J=0,H=F.before.length;J<H;J++){
                        A(F.before[J].fn,F.before[J].scope,I);
                        if(D){
                            return C
                        }
                    }
                    if((B=F.originalFn.apply(E,I))!==undefined){
                        C=B
                    }
                    for(var J=0,H=F.after.length;J<H;J++){
                        A(F.after[J].fn,F.after[J].scope,I);
                        if(D){
                            return C
                        }
                    }
                    return C
                }
            }
            return F
        },
        beforeMethod:function(D,B,A){
            var C=this.getMethodEvent(D);
            C.before.push({
                fn:B,
                scope:A
            })
        },
        afterMethod:function(D,B,A){
            var C=this.getMethodEvent(D);
            C.after.push({
                fn:B,
                scope:A
            })
        },
        removeMethodListener:function(F,D,C){
            var E=this.getMethodEvent(F);
            for(var B=0,A=E.before.length;B<A;B++){
                if(E.before[B].fn==D&&E.before[B].scope==C){
                    E.before.splice(B,1);
                    return
                }
            }
            for(var B=0,A=E.after.length;B<A;B++){
                if(E.after[B].fn==D&&E.after[B].scope==C){
                    E.after.splice(B,1);
                    return
                }
            }
        }
    };

    Ext.util.Observable.prototype.on=Ext.util.Observable.prototype.addListener;
    Ext.util.Observable.prototype.un=Ext.util.Observable.prototype.removeListener;
    Ext.util.Observable.capture=function(C,B,A){
        C.fireEvent=C.fireEvent.createInterceptor(B,A)
    };
    
    Ext.util.Observable.releaseCapture=function(A){
        A.fireEvent=Ext.util.Observable.prototype.fireEvent
    };
    (function(){
        var B=function(F,G,E){
            var D=new Ext.util.DelayedTask();
            return function(){
                D.delay(G.buffer,F,E,Array.prototype.slice.call(arguments,0))
            }
        };
    
        var C=function(F,G,E,D){
            return function(){
                G.removeListener(E,D);
                return F.apply(D,arguments)
            }
        };

        var A=function(E,F,D){
            return function(){
                var G=Array.prototype.slice.call(arguments,0);
                setTimeout(function(){
                    E.apply(D,G)
                },F.delay||10)
            }
        };

        Ext.util.Event=function(E,D){
            this.name=D;
            this.obj=E;
            this.listeners=[]
        };
    
        Ext.util.Event.prototype={
            addListener:function(G,F,E){
                F=F||this.obj;
                if(!this.isListening(G,F)){
                    var D=this.createListener(G,F,E);
                    if(!this.firing){
                        this.listeners.push(D)
                    }else{
                        this.listeners=this.listeners.slice(0);
                        this.listeners.push(D)
                    }
                }
            },
            createListener:function(G,F,H){
                H=H||{};
    
                F=F||this.obj;
                var D={
                    fn:G,
                    scope:F,
                    options:H
                };
    
                var E=G;
                if(H.delay){
                    E=A(E,H,F)
                }
                if(H.single){
                    E=C(E,this,G,F)
                }
                if(H.buffer){
                    E=B(E,H,F)
                }
                D.fireFn=E;
                return D
            },
            findListener:function(I,H){
                H=H||this.obj;
                var F=this.listeners;
                for(var G=0,D=F.length;G<D;G++){
                    var E=F[G];
                    if(E.fn==I&&E.scope==H){
                        return G
                    }
                }
                return-1
            },
            isListening:function(E,D){
                return this.findListener(E,D)!=-1
            },
            removeListener:function(F,E){
                var D;
                if((D=this.findListener(F,E))!=-1){
                    if(!this.firing){
                        this.listeners.splice(D,1)
                    }else{
                        this.listeners=this.listeners.slice(0);
                        this.listeners.splice(D,1)
                    }
                    return true
                }
                return false
            },
            clearListeners:function(){
                this.listeners=[]
            },
            fire:function(){
                var F=this.listeners,I,D=F.length;
                if(D>0){
                    this.firing=true;
                    var G=Array.prototype.slice.call(arguments,0);
                    for(var H=0;H<D;H++){
                        var E=F[H];
                        if(E.fireFn.apply(E.scope||this.obj||window,arguments)===false){
                            this.firing=false;
                            return false
                        }
                    }
                    this.firing=false
                }
                return true
            }
        }
    })();
    Ext.EventManager=function(){
        var T,M,I=false;
        var K,S,C,O;
        var L=Ext.lib.Event;
        var N=Ext.lib.Dom;
        var B=function(){
            if(!I){
                I=Ext.isReady=true;
                if(Ext.isGecko||Ext.isOpera){
                    document.removeEventListener("DOMContentLoaded",B,false);
                }
            }
            if(M){
                clearInterval(M);
                M=null;
            }
            if(T){
                T.fire();
                T.clearListeners();
            }
        };

        var A=function(){
            T=new Ext.util.Event();
            if(Ext.isReady){
                return;
            }
            L.on(window,"load",B);
            if(Ext.isGecko||Ext.isOpera){
                document.addEventListener("DOMContentLoaded",B,false);
            }else{
                if(Ext.isIE){
                    M=setInterval(function(){
                        try{
                            Ext.isReady||(document.documentElement.doScroll("left"));
                        }catch(D){
                            return;
                        }
                        B();
                    },5);
                    document.onreadystatechange=function(){
                        if(document.readyState=="complete"){
                            document.onreadystatechange=null;
                            B();
                        }
                    };
        
                }else{
                    if(Ext.isSafari){
                        M=setInterval(function(){
                            var D=document.readyState;
                            if(D=="complete"){
                                B();
                            }
                        },10);
                    }
                }
            }
        };

        var R=function(E,U){
            var D=new Ext.util.DelayedTask(E);
            return function(V){
                V=new Ext.EventObjectImpl(V);
                D.delay(U.buffer,E,null,[V]);
            };

        };

        var P=function(V,U,D,E){
            return function(W){
                Ext.EventManager.removeListener(U,D,E);
                V(W);
            };

        };

        var F=function(D,E){
            return function(U){
                U=new Ext.EventObjectImpl(U);
                setTimeout(function(){
                    D(U);
                },E.delay||10);
            };

        };

        var J=function(U,E,D,Y,X){
            var Z=(!D||typeof D=="boolean")?{}:D;
            Y=Y||Z.fn;
            X=X||Z.scope;
            var W=Ext.getDom(U);
            if(!W){
                throw'Error listening for "'+E+'". Element "'+U+"\" doesn't exist.";
            }
            var V=function(b){
                b=Ext.EventObject.setEvent(b);
                var a;
                if(Z.delegate){
                    a=b.getTarget(Z.delegate,W);
                    if(!a){
                        return;
                    }
                }else{
                    a=b.target;
                }
                if(Z.stopEvent===true){
                    b.stopEvent();
                }
                if(Z.preventDefault===true){
                    b.preventDefault();
                }
                if(Z.stopPropagation===true){
                    b.stopPropagation();
                }
                if(Z.normalized===false){
                    b=b.browserEvent;
                }
                Y.call(X||W,b,a,Z);
            };

            if(Z.delay){
                V=F(V,Z);
            }
            if(Z.single){
                V=P(V,W,E,Y);
            }
            if(Z.buffer){
                V=R(V,Z);
            }
            Y._handlers=Y._handlers||[];
            Y._handlers.push([Ext.id(W),E,V]);
            L.on(W,E,V);
            if(E=="mousewheel"&&W.addEventListener){
                W.addEventListener("DOMMouseScroll",V,false);
                L.on(window,"unload",function(){
                    W.removeEventListener("DOMMouseScroll",V,false);
                });
            }
            if(E=="mousedown"&&W==document){
                Ext.EventManager.stoppedMouseDownEvent.addListener(V);
            }
            return V;
        };

        var G=function(E,U,Z){
            var D=Ext.id(E),a=Z._handlers,X=Z;
            if(a){
                for(var V=0,Y=a.length;V<Y;V++){
                    var W=a[V];
                    if(W[0]==D&&W[1]==U){
                        X=W[2];
                        a.splice(V,1);
                        break;
                    }
                }
            }
            L.un(E,U,X);
            E=Ext.getDom(E);
            if(U=="mousewheel"&&E.addEventListener){
                E.removeEventListener("DOMMouseScroll",X,false);
            }
            if(U=="mousedown"&&E==document){
                Ext.EventManager.stoppedMouseDownEvent.removeListener(X);
            }
        };

        var H=/^(?:scope|delay|buffer|single|stopEvent|preventDefault|stopPropagation|normalized|args|delegate)$/;
        var Q={
            addListener:function(U,D,W,V,E){
                if(typeof D=="object"){
                    var Y=D;
                    for(var X in Y){
                        if(H.test(X)){
                            continue;
                        }
                        if(typeof Y[X]=="function"){
                            J(U,X,Y,Y[X],Y.scope);
                        }else{
                            J(U,X,Y[X]);
                        }
                    }
                    return;
                }
                return J(U,D,E,W,V);
            },
            removeListener:function(E,D,U){
                return G(E,D,U);
            },
            onDocumentReady:function(U,E,D){
                if(!T){
                    A();
                }
                if(I||Ext.isReady){
                    D||(D={});
                    U.defer(D.delay||0,E);
                }else{
                    T.addListener(U,E,D);
                }
            },
            onWindowResize:function(U,E,D){
                if(!K){
                    K=new Ext.util.Event();
                    S=new Ext.util.DelayedTask(function(){
                        K.fire(N.getViewWidth(),N.getViewHeight());
                    });
                    L.on(window,"resize",this.fireWindowResize,this);
                }
                K.addListener(U,E,D);
            },
            fireWindowResize:function(){
                if(K){
                    if((Ext.isIE||Ext.isAir)&&S){
                        S.delay(50);
                    }else{
                        K.fire(N.getViewWidth(),N.getViewHeight());
                    }
                }
            },
            onTextResize:function(V,U,D){
                if(!C){
                    C=new Ext.util.Event();
                    var E=new Ext.Element(document.createElement("div"));
                    E.dom.className="x-text-resize";
                    E.dom.innerHTML="X";
                    E.appendTo(document.body);
                    O=E.dom.offsetHeight;
                    setInterval(function(){
                        if(E.dom.offsetHeight!=O){
                            C.fire(O,O=E.dom.offsetHeight);
                        }
                    },this.textResizeInterval);
                }
                C.addListener(V,U,D);
            },
            removeResizeListener:function(E,D){
                if(K){
                    K.removeListener(E,D);
                }
            },
            fireResize:function(){
                if(K){
                    K.fire(N.getViewWidth(),N.getViewHeight());
                }
            },
            ieDeferSrc:false,
            textResizeInterval:50
        };

        Q.on=Q.addListener;
        Q.un=Q.removeListener;
        Q.stoppedMouseDownEvent=new Ext.util.Event();
        return Q;
    }();
    Ext.onReady=Ext.EventManager.onDocumentReady;
    Ext.onReady(function(){
        var B=Ext.getBody();
        if(!B){
            return;
        }
        var A=[Ext.isIE?"ext-ie "+(Ext.isIE6?"ext-ie6":"ext-ie7"):Ext.isGecko?"ext-gecko":Ext.isOpera?"ext-opera":Ext.isSafari?"ext-safari":""];
        if(Ext.isMac){
            A.push("ext-mac");
        }
        if(Ext.isLinux){
            A.push("ext-linux");
        }
        if(Ext.isBorderBox){
            A.push("ext-border-box");
        }
        if(Ext.isStrict){
            var C=B.dom.parentNode;
            if(C){
                C.className+=" ext-strict";
            }
        }
        B.addClass(A.join(" "));
    });
    Ext.EventObject=function(){
        var B=Ext.lib.Event;
        var A={
            63234:37,
            63235:39,
            63232:38,
            63233:40,
            63276:33,
            63277:34,
            63272:46,
            63273:36,
            63275:35
        };
    
        var C=Ext.isIE?{
            1:0,
            4:1,
            2:2
        }:(Ext.isSafari?{
            1:0,
            2:1,
            3:2
        }:{
            0:0,
            1:1,
            2:2
        });
        Ext.EventObjectImpl=function(D){
            if(D){
                this.setEvent(D.browserEvent||D);
            }
        };
    
        Ext.EventObjectImpl.prototype={
            browserEvent:null,
            button:-1,
            shiftKey:false,
            ctrlKey:false,
            altKey:false,
            BACKSPACE:8,
            TAB:9,
            RETURN:13,
            ENTER:13,
            SHIFT:16,
            CONTROL:17,
            ESC:27,
            SPACE:32,
            PAGEUP:33,
            PAGEDOWN:34,
            END:35,
            HOME:36,
            LEFT:37,
            UP:38,
            RIGHT:39,
            DOWN:40,
            DELETE:46,
            F5:116,
            setEvent:function(D){
                if(D==this||(D&&D.browserEvent)){
                    return D;
                }
                this.browserEvent=D;
                if(D){
                    this.button=D.button?C[D.button]:(D.which?D.which-1:-1);
                    if(D.type=="click"&&this.button==-1){
                        this.button=0;
                    }
                    this.type=D.type;
                    this.shiftKey=D.shiftKey;
                    this.ctrlKey=D.ctrlKey||D.metaKey;
                    this.altKey=D.altKey;
                    this.keyCode=D.keyCode;
                    this.charCode=D.charCode;
                    this.target=B.getTarget(D);
                    this.xy=B.getXY(D);
                }else{
                    this.button=-1;
                    this.shiftKey=false;
                    this.ctrlKey=false;
                    this.altKey=false;
                    this.keyCode=0;
                    this.charCode=0;
                    this.target=null;
                    this.xy=[0,0];
                }
                return this;
            },
            stopEvent:function(){
                if(this.browserEvent){
                    if(this.browserEvent.type=="mousedown"){
                        Ext.EventManager.stoppedMouseDownEvent.fire(this);
                    }
                    B.stopEvent(this.browserEvent);
                }
            },
            preventDefault:function(){
                if(this.browserEvent){
                    B.preventDefault(this.browserEvent);
                }
            },
            isNavKeyPress:function(){
                var D=this.keyCode;
                D=Ext.isSafari?(A[D]||D):D;
                return(D>=33&&D<=40)||D==this.RETURN||D==this.TAB||D==this.ESC;
            },
            isSpecialKey:function(){
                var D=this.keyCode;
                return(this.type=="keypress"&&this.ctrlKey)||D==9||D==13||D==40||D==27||(D==16)||(D==17)||(D>=18&&D<=20)||(D>=33&&D<=35)||(D>=36&&D<=39)||(D>=44&&D<=45);
            },
            stopPropagation:function(){
                if(this.browserEvent){
                    if(this.browserEvent.type=="mousedown"){
                        Ext.EventManager.stoppedMouseDownEvent.fire(this);
                    }
                    B.stopPropagation(this.browserEvent);
                }
            },
            getCharCode:function(){
                return this.charCode||this.keyCode;
            },
            getKey:function(){
                var D=this.keyCode||this.charCode;
                return Ext.isSafari?(A[D]||D):D;
            },
            getPageX:function(){
                return this.xy[0];
            },
            getPageY:function(){
                return this.xy[1];
            },
            getTime:function(){
                if(this.browserEvent){
                    return B.getTime(this.browserEvent);
                }
                return null;
            },
            getXY:function(){
                return this.xy;
            },
            getTarget:function(E,G,D){
                var F=Ext.get(this.target);
                return E?F.findParent(E,G,D):(D?F:this.target);
            },
            getRelatedTarget:function(){
                if(this.browserEvent){
                    return B.getRelatedTarget(this.browserEvent);
                }
                return null;
            },
            getWheelDelta:function(){
                var D=this.browserEvent;
                var E=0;
                if(D.wheelDelta){
                    E=D.wheelDelta/120;
                }else{
                    if(D.detail){
                        E=-D.detail/3;
                    }
                }
                return E;
            },
            hasModifier:function(){
                return((this.ctrlKey||this.altKey)||this.shiftKey)?true:false;
            },
            within:function(E,F){
                var D=this[F?"getRelatedTarget":"getTarget"]();
                return D&&Ext.fly(E).contains(D);
            },
            getPoint:function(){
                return new Ext.lib.Point(this.xy[0],this.xy[1]);
            }
        };

        return new Ext.EventObjectImpl();
    }();
    (function(){
        var D=Ext.lib.Dom;
        var E=Ext.lib.Event;
        var A=Ext.lib.Anim;
        var propCache={};
    
        var camelRe=/(-[a-z])/gi;
        var camelFn=function(m,a){
            return a.charAt(1).toUpperCase()
        };
        
        var view=document.defaultView;
        Ext.Element=function(element,forceNew){
            var dom=typeof element=="string"?document.getElementById(element):element;
            if(!dom){
                return null
            }
            var id=dom.id;
            if(forceNew!==true&&id&&Ext.Element.cache[id]){
                return Ext.Element.cache[id]
            }
            this.dom=dom;
            this.id=id||Ext.id(dom)
        };
        
        var El=Ext.Element;
        El.prototype={
            originalDisplay:"",
            visibilityMode:1,
            defaultUnit:"px",
            setVisibilityMode:function(visMode){
                this.visibilityMode=visMode;
                return this
            },
            enableDisplayMode:function(display){
                this.setVisibilityMode(El.DISPLAY);
                if(typeof display!="undefined"){
                    this.originalDisplay=display
                }
                return this
            },
            findParent:function(simpleSelector,maxDepth,returnEl){
                var p=this.dom,b=document.body,depth=0,dq=Ext.DomQuery,stopEl;
                maxDepth=maxDepth||50;
                if(typeof maxDepth!="number"){
                    stopEl=Ext.getDom(maxDepth);
                    maxDepth=10
                }while(p&&p.nodeType==1&&depth<maxDepth&&p!=b&&p!=stopEl){
                    if(dq.is(p,simpleSelector)){
                        return returnEl?Ext.get(p):p
                    }
                    depth++;
                    p=p.parentNode
                }
                return null
            },
            findParentNode:function(simpleSelector,maxDepth,returnEl){
                var p=Ext.fly(this.dom.parentNode,"_internal");
                return p?p.findParent(simpleSelector,maxDepth,returnEl):null
            },
            up:function(simpleSelector,maxDepth){
                return this.findParentNode(simpleSelector,maxDepth,true)
            },
            is:function(simpleSelector){
                return Ext.DomQuery.is(this.dom,simpleSelector)
            },
            animate:function(args,duration,onComplete,easing,animType){
                this.anim(args,{
                    duration:duration,
                    callback:onComplete,
                    easing:easing
                },animType);
                return this
            },
            anim:function(args,opt,animType,defaultDur,defaultEase,cb){
                animType=animType||"run";
                opt=opt||{};
            
                var anim=Ext.lib.Anim[animType](this.dom,args,(opt.duration||defaultDur)||0.35,(opt.easing||defaultEase)||"easeOut",function(){
                    Ext.callback(cb,this);
                    Ext.callback(opt.callback,opt.scope||this,[this,opt])
                },this);
                opt.anim=anim;
                return anim
            },
            preanim:function(a,i){
                return!a[i]?false:(typeof a[i]=="object"?a[i]:{
                    duration:a[i+1],
                    callback:a[i+2],
                    easing:a[i+3]
                })
            },
            clean:function(forceReclean){
                if(this.isCleaned&&forceReclean!==true){
                    return this
                }
                var ns=/\S/;
                var d=this.dom,n=d.firstChild,ni=-1;
                while(n){
                    var nx=n.nextSibling;
                    if(n.nodeType==3&&!ns.test(n.nodeValue)){
                        d.removeChild(n)
                    }else{
                        n.nodeIndex=++ni
                    }
                    n=nx
                }
                this.isCleaned=true;
                return this
            },
            scrollIntoView:function(container,hscroll){
                var c=Ext.getDom(container)||Ext.getBody().dom;
                var el=this.dom;
                var o=this.getOffsetsTo(c),l=o[0]+c.scrollLeft,t=o[1]+c.scrollTop,b=t+el.offsetHeight,r=l+el.offsetWidth;
                var ch=c.clientHeight;
                var ct=parseInt(c.scrollTop,10);
                var cl=parseInt(c.scrollLeft,10);
                var cb=ct+ch;
                var cr=cl+c.clientWidth;
                if(el.offsetHeight>ch||t<ct){
                    c.scrollTop=t
                }else{
                    if(b>cb){
                        c.scrollTop=b-ch
                    }
                }
                c.scrollTop=c.scrollTop;
                if(hscroll!==false){
                    if(el.offsetWidth>c.clientWidth||l<cl){
                        c.scrollLeft=l
                    }else{
                        if(r>cr){
                            c.scrollLeft=r-c.clientWidth
                        }
                    }
                    c.scrollLeft=c.scrollLeft
                }
                return this
            },
            scrollChildIntoView:function(child,hscroll){
                Ext.fly(child,"_scrollChildIntoView").scrollIntoView(this,hscroll)
            },
            autoHeight:function(animate,duration,onComplete,easing){
                var oldHeight=this.getHeight();
                this.clip();
                this.setHeight(1);
                setTimeout(function(){
                    var height=parseInt(this.dom.scrollHeight,10);
                    if(!animate){
                        this.setHeight(height);
                        this.unclip();
                        if(typeof onComplete=="function"){
                            onComplete()
                        }
                    }else{
                        this.setHeight(oldHeight);
                        this.setHeight(height,animate,duration,function(){
                            this.unclip();
                            if(typeof onComplete=="function"){
                                onComplete()
                            }
                        }.createDelegate(this),easing)
                    }
                }.createDelegate(this),0);
                return this
            },
            contains:function(el){
                if(!el){
                    return false
                }
                return D.isAncestor(this.dom,el.dom?el.dom:el)
            },
            isVisible:function(deep){
                var vis=!(this.getStyle("visibility")=="hidden"||this.getStyle("display")=="none");
                if(deep!==true||!vis){
                    return vis
                }
                var p=this.dom.parentNode;
                while(p&&p.tagName.toLowerCase()!="body"){
                    if(!Ext.fly(p,"_isVisible").isVisible()){
                        return false
                    }
                    p=p.parentNode
                }
                return true
            },
            select:function(selector,unique){
                return El.select(selector,unique,this.dom)
            },
            query:function(selector,unique){
                return Ext.DomQuery.select(selector,this.dom)
            },
            child:function(selector,returnDom){
                var n=Ext.DomQuery.selectNode(selector,this.dom);
                return returnDom?n:Ext.get(n)
            },
            down:function(selector,returnDom){
                var n=Ext.DomQuery.selectNode(" > "+selector,this.dom);
                return returnDom?n:Ext.get(n)
            },
            initDD:function(group,config,overrides){
                var dd=new Ext.dd.DD(Ext.id(this.dom),group,config);
                return Ext.apply(dd,overrides)
            },
            initDDProxy:function(group,config,overrides){
                var dd=new Ext.dd.DDProxy(Ext.id(this.dom),group,config);
                return Ext.apply(dd,overrides)
            },
            initDDTarget:function(group,config,overrides){
                var dd=new Ext.dd.DDTarget(Ext.id(this.dom),group,config);
                return Ext.apply(dd,overrides)
            },
            setVisible:function(visible,animate){
                if(!animate||!A){
                    if(this.visibilityMode==El.DISPLAY){
                        this.setDisplayed(visible)
                    }else{
                        this.fixDisplay();
                        this.dom.style.visibility=visible?"visible":"hidden"
                    }
                }else{
                    var dom=this.dom;
                    var visMode=this.visibilityMode;
                    if(visible){
                        this.setOpacity(0.01);
                        this.setVisible(true)
                    }
                    this.anim({
                        opacity:{
                            to:(visible?1:0)
                        }
                    },this.preanim(arguments,1),null,0.35,"easeIn",function(){
                        if(!visible){
                            if(visMode==El.DISPLAY){
                                dom.style.display="none"
                            }else{
                                dom.style.visibility="hidden"
                            }
                            Ext.get(dom).setOpacity(1)
                        }
                    })
                }
                return this
            },
            isDisplayed:function(){
                return this.getStyle("display")!="none"
            },
            toggle:function(animate){
                this.setVisible(!this.isVisible(),this.preanim(arguments,0));
                return this
            },
            setDisplayed:function(value){
                if(typeof value=="boolean"){
                    value=value?this.originalDisplay:"none"
                }
                this.setStyle("display",value);
                return this
            },
            focus:function(){
                try{
                    this.dom.focus()
                }catch(e){}
                return this
            },
            blur:function(){
                try{
                    this.dom.blur()
                }catch(e){}
                return this
            },
            addClass:function(className){
                if(Ext.isArray(className)){
                    for(var i=0,len=className.length;i<len;i++){
                        this.addClass(className[i])
                    }
                }else{
                    if(className&&!this.hasClass(className)){
                        this.dom.className=this.dom.className+" "+className
                    }
                }
                return this
            },
            radioClass:function(className){
                var siblings=this.dom.parentNode.childNodes;
                for(var i=0;i<siblings.length;i++){
                    var s=siblings[i];
                    if(s.nodeType==1){
                        Ext.get(s).removeClass(className)
                    }
                }
                this.addClass(className);
                return this
            },
            removeClass:function(className){
                if(!className||!this.dom.className){
                    return this
                }
                if(Ext.isArray(className)){
                    for(var i=0,len=className.length;i<len;i++){
                        this.removeClass(className[i])
                    }
                }else{
                    if(this.hasClass(className)){
                        var re=this.classReCache[className];
                        if(!re){
                            re=new RegExp("(?:^|\\s+)"+className+"(?:\\s+|$)","g");
                            this.classReCache[className]=re
                        }
                        this.dom.className=this.dom.className.replace(re," ")
                    }
                }
                return this
            },
            classReCache:{},
            toggleClass:function(className){
                if(this.hasClass(className)){
                    this.removeClass(className)
                }else{
                    this.addClass(className)
                }
                return this
            },
            hasClass:function(className){
                return className&&(" "+this.dom.className+" ").indexOf(" "+className+" ")!=-1
            },
            replaceClass:function(oldClassName,newClassName){
                this.removeClass(oldClassName);
                this.addClass(newClassName);
                return this
            },
            getStyles:function(){
                var a=arguments,len=a.length,r={};
    
                for(var i=0;i<len;i++){
                    r[a[i]]=this.getStyle(a[i])
                }
                return r
            },
            getStyle:function(){
                return view&&view.getComputedStyle?function(prop){
                    var el=this.dom,v,cs,camel;
                    if(prop=="float"){
                        prop="cssFloat"
                    }
                    if(v=el.style[prop]){
                        return v
                    }
                    if(cs=view.getComputedStyle(el,"")){
                        if(!(camel=propCache[prop])){
                            camel=propCache[prop]=prop.replace(camelRe,camelFn)
                        }
                        return cs[camel]
                    }
                    return null
                }:function(prop){
                    var el=this.dom,v,cs,camel;
                    if(prop=="opacity"){
                        if(typeof el.style.filter=="string"){
                            var m=el.style.filter.match(/alpha\(opacity=(.*)\)/i);
                            if(m){
                                var fv=parseFloat(m[1]);
                                if(!isNaN(fv)){
                                    return fv?fv/100:0
                                }
                            }
                        }
                        return 1
                    }else{
                        if(prop=="float"){
                            prop="styleFloat"
                        }
                    }
                    if(!(camel=propCache[prop])){
                        camel=propCache[prop]=prop.replace(camelRe,camelFn)
                    }
                    if(v=el.style[camel]){
                        return v
                    }
                    if(cs=el.currentStyle){
                        return cs[camel]
                    }
                    return null
                }
            }(),
            setStyle:function(prop,value){
                if(typeof prop=="string"){
                    var camel;
                    if(!(camel=propCache[prop])){
                        camel=propCache[prop]=prop.replace(camelRe,camelFn)
                    }
                    if(camel=="opacity"){
                        this.setOpacity(value)
                    }else{
                        this.dom.style[camel]=value
                    }
                }else{
                    for(var style in prop){
                        if(typeof prop[style]!="function"){
                            this.setStyle(style,prop[style])
                        }
                    }
                }
                return this
            },
            applyStyles:function(style){
                Ext.DomHelper.applyStyles(this.dom,style);
                return this
            },
            getX:function(){
                return D.getX(this.dom)
            },
            getY:function(){
                return D.getY(this.dom)
            },
            getXY:function(){
                return D.getXY(this.dom)
            },
            getOffsetsTo:function(el){
                var o=this.getXY();
                var e=Ext.fly(el,"_internal").getXY();
                return[o[0]-e[0],o[1]-e[1]]
            },
            setX:function(x,animate){
                if(!animate||!A){
                    D.setX(this.dom,x)
                }else{
                    this.setXY([x,this.getY()],this.preanim(arguments,1))
                }
                return this
            },
            setY:function(y,animate){
                if(!animate||!A){
                    D.setY(this.dom,y)
                }else{
                    this.setXY([this.getX(),y],this.preanim(arguments,1))
                }
                return this
            },
            setLeft:function(left){
                this.setStyle("left",this.addUnits(left));
                return this
            },
            setTop:function(top){
                this.setStyle("top",this.addUnits(top));
                return this
            },
            setRight:function(right){
                this.setStyle("right",this.addUnits(right));
                return this
            },
            setBottom:function(bottom){
                this.setStyle("bottom",this.addUnits(bottom));
                return this
            },
            setXY:function(pos,animate){
                if(!animate||!A){
                    D.setXY(this.dom,pos)
                }else{
                    this.anim({
                        points:{
                            to:pos
                        }
                    },this.preanim(arguments,1),"motion")
                }
                return this
            },
            setLocation:function(x,y,animate){
                this.setXY([x,y],this.preanim(arguments,2));
                return this
            },
            moveTo:function(x,y,animate){
                this.setXY([x,y],this.preanim(arguments,2));
                return this
            },
            getRegion:function(){
                return D.getRegion(this.dom)
            },
            getHeight:function(contentHeight){
                var h=this.dom.offsetHeight||0;
                h=contentHeight!==true?h:h-this.getBorderWidth("tb")-this.getPadding("tb");
                return h<0?0:h
            },
            getWidth:function(contentWidth){
                var w=this.dom.offsetWidth||0;
                w=contentWidth!==true?w:w-this.getBorderWidth("lr")-this.getPadding("lr");
                return w<0?0:w
            },
            getComputedHeight:function(){
                var h=Math.max(this.dom.offsetHeight,this.dom.clientHeight);
                if(!h){
                    h=parseInt(this.getStyle("height"),10)||0;
                    if(!this.isBorderBox()){
                        h+=this.getFrameWidth("tb")
                    }
                }
                return h
            },
            getComputedWidth:function(){
                var w=Math.max(this.dom.offsetWidth,this.dom.clientWidth);
                if(!w){
                    w=parseInt(this.getStyle("width"),10)||0;
                    if(!this.isBorderBox()){
                        w+=this.getFrameWidth("lr")
                    }
                }
                return w
            },
            getSize:function(contentSize){
                return{
                    width:this.getWidth(contentSize),
                    height:this.getHeight(contentSize)
                }
            },
            getStyleSize:function(){
                var w,h,d=this.dom,s=d.style;
                if(s.width&&s.width!="auto"){
                    w=parseInt(s.width,10);
                    if(Ext.isBorderBox){
                        w-=this.getFrameWidth("lr")
                    }
                }
                if(s.height&&s.height!="auto"){
                    h=parseInt(s.height,10);
                    if(Ext.isBorderBox){
                        h-=this.getFrameWidth("tb")
                    }
                }
                return{
                    width:w||this.getWidth(true),
                    height:h||this.getHeight(true)
                }
            },
            getViewSize:function(){
                var d=this.dom,doc=document,aw=0,ah=0;
                if(d==doc||d==doc.body){
                    return{
                        width:D.getViewWidth(),
                        height:D.getViewHeight()
                    }
                }else{
                    return{
                        width:d.clientWidth,
                        height:d.clientHeight
                    }
                }
            },
            getValue:function(asNumber){
                return asNumber?parseInt(this.dom.value,10):this.dom.value
            },
            adjustWidth:function(width){
                if(typeof width=="number"){
                    if(this.autoBoxAdjust&&!this.isBorderBox()){
                        width-=(this.getBorderWidth("lr")+this.getPadding("lr"))
                    }
                    if(width<0){
                        width=0
                    }
                }
                return width
            },
            adjustHeight:function(height){
                if(typeof height=="number"){
                    if(this.autoBoxAdjust&&!this.isBorderBox()){
                        height-=(this.getBorderWidth("tb")+this.getPadding("tb"))
                    }
                    if(height<0){
                        height=0
                    }
                }
                return height
            },
            setWidth:function(width,animate){
                width=this.adjustWidth(width);
                if(!animate||!A){
                    this.dom.style.width=this.addUnits(width)
                }else{
                    this.anim({
                        width:{
                            to:width
                        }
                    },this.preanim(arguments,1))
                }
                return this
            },
            setHeight:function(height,animate){
                height=this.adjustHeight(height);
                if(!animate||!A){
                    this.dom.style.height=this.addUnits(height)
                }else{
                    this.anim({
                        height:{
                            to:height
                        }
                    },this.preanim(arguments,1))
                }
                return this
            },
            setSize:function(width,height,animate){
                if(typeof width=="object"){
                    height=width.height;
                    width=width.width
                }
                width=this.adjustWidth(width);
                height=this.adjustHeight(height);
                if(!animate||!A){
                    this.dom.style.width=this.addUnits(width);
                    this.dom.style.height=this.addUnits(height)
                }else{
                    this.anim({
                        width:{
                            to:width
                        },
                        height:{
                            to:height
                        }
                    },this.preanim(arguments,2))
                }
                return this
            },
            setBounds:function(x,y,width,height,animate){
                if(!animate||!A){
                    this.setSize(width,height);
                    this.setLocation(x,y)
                }else{
                    width=this.adjustWidth(width);
                    height=this.adjustHeight(height);
                    this.anim({
                        points:{
                            to:[x,y]
                        },
                        width:{
                            to:width
                        },
                        height:{
                            to:height
                        }
                    },this.preanim(arguments,4),"motion")
                }
                return this
            },
            setRegion:function(region,animate){
                this.setBounds(region.left,region.top,region.right-region.left,region.bottom-region.top,this.preanim(arguments,1));
                return this
            },
            addListener:function(eventName,fn,scope,options){
                Ext.EventManager.on(this.dom,eventName,fn,scope||this,options)
            },
            removeListener:function(eventName,fn){
                Ext.EventManager.removeListener(this.dom,eventName,fn);
                return this
            },
            removeAllListeners:function(){
                E.purgeElement(this.dom);
                return this
            },
            relayEvent:function(eventName,observable){
                this.on(eventName,function(e){
                    observable.fireEvent(eventName,e)
                })
            },
            setOpacity:function(opacity,animate){
                if(!animate||!A){
                    var s=this.dom.style;
                    if(Ext.isIE){
                        s.zoom=1;
                        s.filter=(s.filter||"").replace(/alpha\([^\)]*\)/gi,"")+(opacity==1?"":" alpha(opacity="+opacity*100+")")
                    }else{
                        s.opacity=opacity
                    }
                }else{
                    this.anim({
                        opacity:{
                            to:opacity
                        }
                    },this.preanim(arguments,1),null,0.35,"easeIn")
                }
                return this
            },
            getLeft:function(local){
                if(!local){
                    return this.getX()
                }else{
                    return parseInt(this.getStyle("left"),10)||0
                }
            },
            getRight:function(local){
                if(!local){
                    return this.getX()+this.getWidth()
                }else{
                    return(this.getLeft(true)+this.getWidth())||0
                }
            },
            getTop:function(local){
                if(!local){
                    return this.getY()
                }else{
                    return parseInt(this.getStyle("top"),10)||0
                }
            },
            getBottom:function(local){
                if(!local){
                    return this.getY()+this.getHeight()
                }else{
                    return(this.getTop(true)+this.getHeight())||0
                }
            },
            position:function(pos,zIndex,x,y){
                if(!pos){
                    if(this.getStyle("position")=="static"){
                        this.setStyle("position","relative")
                    }
                }else{
                    this.setStyle("position",pos)
                }
                if(zIndex){
                    this.setStyle("z-index",zIndex)
                }
                if(x!==undefined&&y!==undefined){
                    this.setXY([x,y])
                }else{
                    if(x!==undefined){
                        this.setX(x)
                    }else{
                        if(y!==undefined){
                            this.setY(y)
                        }
                    }
                }
            },
            clearPositioning:function(value){
                value=value||"";
                this.setStyle({
                    "left":value,
                    "right":value,
                    "top":value,
                    "bottom":value,
                    "z-index":"",
                    "position":"static"
                });
                return this
            },
            getPositioning:function(){
                var l=this.getStyle("left");
                var t=this.getStyle("top");
                return{
                    "position":this.getStyle("position"),
                    "left":l,
                    "right":l?"":this.getStyle("right"),
                    "top":t,
                    "bottom":t?"":this.getStyle("bottom"),
                    "z-index":this.getStyle("z-index")
                }
            },
            getBorderWidth:function(side){
                return this.addStyles(side,El.borders)
            },
            getPadding:function(side){
                return this.addStyles(side,El.paddings)
            },
            setPositioning:function(pc){
                this.applyStyles(pc);
                if(pc.right=="auto"){
                    this.dom.style.right=""
                }
                if(pc.bottom=="auto"){
                    this.dom.style.bottom=""
                }
                return this
            },
            fixDisplay:function(){
                if(this.getStyle("display")=="none"){
                    this.setStyle("visibility","hidden");
                    this.setStyle("display",this.originalDisplay);
                    if(this.getStyle("display")=="none"){
                        this.setStyle("display","block")
                    }
                }
            },
            setOverflow:function(v){
                if(v=="auto"&&Ext.isMac&&Ext.isGecko){
                    this.dom.style.overflow="hidden";
                    (function(){
                        this.dom.style.overflow="auto"
                    }).defer(1,this)
                }else{
                    this.dom.style.overflow=v
                }
            },
            setLeftTop:function(left,top){
                this.dom.style.left=this.addUnits(left);
                this.dom.style.top=this.addUnits(top);
                return this
            },
            move:function(direction,distance,animate){
                var xy=this.getXY();
                direction=direction.toLowerCase();
                switch(direction){
                    case"l":case"left":
                        this.moveTo(xy[0]-distance,xy[1],this.preanim(arguments,2));
                        break;
                    case"r":case"right":
                        this.moveTo(xy[0]+distance,xy[1],this.preanim(arguments,2));
                        break;
                    case"t":case"top":case"up":
                        this.moveTo(xy[0],xy[1]-distance,this.preanim(arguments,2));
                        break;
                    case"b":case"bottom":case"down":
                        this.moveTo(xy[0],xy[1]+distance,this.preanim(arguments,2));
                        break
                }
                return this
            },
            clip:function(){
                if(!this.isClipped){
                    this.isClipped=true;
                    this.originalClip={
                        "o":this.getStyle("overflow"),
                        "x":this.getStyle("overflow-x"),
                        "y":this.getStyle("overflow-y")
                    };
            
                    this.setStyle("overflow","hidden");
                    this.setStyle("overflow-x","hidden");
                    this.setStyle("overflow-y","hidden")
                }
                return this
            },
            unclip:function(){
                if(this.isClipped){
                    this.isClipped=false;
                    var o=this.originalClip;
                    if(o.o){
                        this.setStyle("overflow",o.o)
                    }
                    if(o.x){
                        this.setStyle("overflow-x",o.x)
                    }
                    if(o.y){
                        this.setStyle("overflow-y",o.y)
                    }
                }
                return this
            },
            getAnchorXY:function(anchor,local,s){
                var w,h,vp=false;
                if(!s){
                    var d=this.dom;
                    if(d==document.body||d==document){
                        vp=true;
                        w=D.getViewWidth();
                        h=D.getViewHeight()
                    }else{
                        w=this.getWidth();
                        h=this.getHeight()
                    }
                }else{
                    w=s.width;
                    h=s.height
                }
                var x=0,y=0,r=Math.round;
                switch((anchor||"tl").toLowerCase()){
                    case"c":
                        x=r(w*0.5);
                        y=r(h*0.5);
                        break;
                    case"t":
                        x=r(w*0.5);
                        y=0;
                        break;
                    case"l":
                        x=0;
                        y=r(h*0.5);
                        break;
                    case"r":
                        x=w;
                        y=r(h*0.5);
                        break;
                    case"b":
                        x=r(w*0.5);
                        y=h;
                        break;
                    case"tl":
                        x=0;
                        y=0;
                        break;
                    case"bl":
                        x=0;
                        y=h;
                        break;
                    case"br":
                        x=w;
                        y=h;
                        break;
                    case"tr":
                        x=w;
                        y=0;
                        break
                }
                if(local===true){
                    return[x,y]
                }
                if(vp){
                    var sc=this.getScroll();
                    return[x+sc.left,y+sc.top]
                }
                var o=this.getXY();
                return[x+o[0],y+o[1]]
            },
            getAlignToXY:function(el,p,o){
                el=Ext.get(el);
                if(!el||!el.dom){
                    throw"Element.alignToXY with an element that doesn't exist"
                }
                var d=this.dom;
                var c=false;
                var p1="",p2="";
                o=o||[0,0];
                if(!p){
                    p="tl-bl"
                }else{
                    if(p=="?"){
                        p="tl-bl?"
                    }else{
                        if(p.indexOf("-")==-1){
                            p="tl-"+p
                        }
                    }
                }
                p=p.toLowerCase();
                var m=p.match(/^([a-z]+)-([a-z]+)(\?)?$/);
                if(!m){
                    throw"Element.alignTo with an invalid alignment "+p
                }
                p1=m[1];
                p2=m[2];
                c=!!m[3];
                var a1=this.getAnchorXY(p1,true);
                var a2=el.getAnchorXY(p2,false);
                var x=a2[0]-a1[0]+o[0];
                var y=a2[1]-a1[1]+o[1];
                if(c){
                    var w=this.getWidth(),h=this.getHeight(),r=el.getRegion();
                    var dw=D.getViewWidth()-5,dh=D.getViewHeight()-5;
                    var p1y=p1.charAt(0),p1x=p1.charAt(p1.length-1);
                    var p2y=p2.charAt(0),p2x=p2.charAt(p2.length-1);
                    var swapY=((p1y=="t"&&p2y=="b")||(p1y=="b"&&p2y=="t"));
                    var swapX=((p1x=="r"&&p2x=="l")||(p1x=="l"&&p2x=="r"));
                    var doc=document;
                    var scrollX=(doc.documentElement.scrollLeft||doc.body.scrollLeft||0)+5;
                    var scrollY=(doc.documentElement.scrollTop||doc.body.scrollTop||0)+5;
                    if((x+w)>dw+scrollX){
                        x=swapX?r.left-w:dw+scrollX-w
                    }
                    if(x<scrollX){
                        x=swapX?r.right:scrollX
                    }
                    if((y+h)>dh+scrollY){
                        y=swapY?r.top-h:dh+scrollY-h
                    }
                    if(y<scrollY){
                        y=swapY?r.bottom:scrollY
                    }
                }
                return[x,y]
            },
            getConstrainToXY:function(){
                var os={
                    top:0,
                    left:0,
                    bottom:0,
                    right:0
                };
    
                return function(el,local,offsets,proposedXY){
                    el=Ext.get(el);
                    offsets=offsets?Ext.applyIf(offsets,os):os;
                    var vw,vh,vx=0,vy=0;
                    if(el.dom==document.body||el.dom==document){
                        vw=Ext.lib.Dom.getViewWidth();
                        vh=Ext.lib.Dom.getViewHeight()
                    }else{
                        vw=el.dom.clientWidth;
                        vh=el.dom.clientHeight;
                        if(!local){
                            var vxy=el.getXY();
                            vx=vxy[0];
                            vy=vxy[1]
                        }
                    }
                    var s=el.getScroll();
                    vx+=offsets.left+s.left;
                    vy+=offsets.top+s.top;
                    vw-=offsets.right;
                    vh-=offsets.bottom;
                    var vr=vx+vw;
                    var vb=vy+vh;
                    var xy=proposedXY||(!local?this.getXY():[this.getLeft(true),this.getTop(true)]);
                    var x=xy[0],y=xy[1];
                    var w=this.dom.offsetWidth,h=this.dom.offsetHeight;
                    var moved=false;
                    if((x+w)>vr){
                        x=vr-w;
                        moved=true
                    }
                    if((y+h)>vb){
                        y=vb-h;
                        moved=true
                    }
                    if(x<vx){
                        x=vx;
                        moved=true
                    }
                    if(y<vy){
                        y=vy;
                        moved=true
                    }
                    return moved?[x,y]:false
                }
            }(),
            adjustForConstraints:function(xy,parent,offsets){
                return this.getConstrainToXY(parent||document,false,offsets,xy)||xy
            },
            alignTo:function(element,position,offsets,animate){
                var xy=this.getAlignToXY(element,position,offsets);
                this.setXY(xy,this.preanim(arguments,3));
                return this
            },
            anchorTo:function(el,alignment,offsets,animate,monitorScroll,callback){
                var action=function(){
                    this.alignTo(el,alignment,offsets,animate);
                    Ext.callback(callback,this)
                };
        
                Ext.EventManager.onWindowResize(action,this);
                var tm=typeof monitorScroll;
                if(tm!="undefined"){
                    Ext.EventManager.on(window,"scroll",action,this,{
                        buffer:tm=="number"?monitorScroll:50
                    })
                }
                action.call(this);
                return this
            },
            clearOpacity:function(){
                if(window.ActiveXObject){
                    if(typeof this.dom.style.filter=="string"&&(/alpha/i).test(this.dom.style.filter)){
                        this.dom.style.filter=""
                    }
                }else{
                    this.dom.style.opacity="";
                    this.dom.style["-moz-opacity"]="";
                    this.dom.style["-khtml-opacity"]=""
                }
                return this
            },
            hide:function(animate){
                this.setVisible(false,this.preanim(arguments,0));
                return this
            },
            show:function(animate){
                this.setVisible(true,this.preanim(arguments,0));
                return this
            },
            addUnits:function(size){
                return Ext.Element.addUnits(size,this.defaultUnit)
            },
            update:function(html,loadScripts,callback){
                if(typeof html=="undefined"){
                    html=""
                }
                if(loadScripts!==true){
                    this.dom.innerHTML=html;
                    if(typeof callback=="function"){
                        callback()
                    }
                    return this
                }
                var id=Ext.id();
                var dom=this.dom;
                html+="<span id=\""+id+"\"></span>";
                E.onAvailable(id,function(){
                    var hd=document.getElementsByTagName("head")[0];
                    var re=/(?:<script([^>]*)?>)((\n|\r|.)*?)(?:<\/script>)/ig;
                    var srcRe=/\ssrc=([\'\"])(.*?)\1/i;
                    var typeRe=/\stype=([\'\"])(.*?)\1/i;
                    var match;
                    while(match=re.exec(html)){
                        var attrs=match[1];
                        var srcMatch=attrs?attrs.match(srcRe):false;
                        if(srcMatch&&srcMatch[2]){
                            var s=document.createElement("script");
                            s.src=srcMatch[2];
                            var typeMatch=attrs.match(typeRe);
                            if(typeMatch&&typeMatch[2]){
                                s.type=typeMatch[2]
                            }
                            hd.appendChild(s)
                        }else{
                            if(match[2]&&match[2].length>0){
                                if(window.execScript){
                                    window.execScript(match[2])
                                }else{
                                    window.eval(match[2])
                                }
                            }
                        }
                    }
                    var el=document.getElementById(id);
                    if(el){
                        Ext.removeNode(el)
                    }
                    if(typeof callback=="function"){
                        callback()
                    }
                });
                dom.innerHTML=html.replace(/(?:<script.*?>)((\n|\r|.)*?)(?:<\/script>)/ig,"");
                return this
            },
            load:function(){
                var um=this.getUpdater();
                um.update.apply(um,arguments);
                return this
            },
            getUpdater:function(){
                if(!this.updateManager){
                    this.updateManager=new Ext.Updater(this)
                }
                return this.updateManager
            },
            unselectable:function(){
                this.dom.unselectable="on";
                this.swallowEvent("selectstart",true);
                this.applyStyles("-moz-user-select:none;-khtml-user-select:none;");
                this.addClass("x-unselectable");
                return this
            },
            getCenterXY:function(){
                return this.getAlignToXY(document,"c-c")
            },
            center:function(centerIn){
                this.alignTo(centerIn||document,"c-c");
                return this
            },
            isBorderBox:function(){
                return noBoxAdjust[this.dom.tagName.toLowerCase()]||Ext.isBorderBox
            },
            getBox:function(contentBox,local){
                var xy;
                if(!local){
                    xy=this.getXY()
                }else{
                    var left=parseInt(this.getStyle("left"),10)||0;
                    var top=parseInt(this.getStyle("top"),10)||0;
                    xy=[left,top]
                }
                var el=this.dom,w=el.offsetWidth,h=el.offsetHeight,bx;
                if(!contentBox){
                    bx={
                        x:xy[0],
                        y:xy[1],
                        0:xy[0],
                        1:xy[1],
                        width:w,
                        height:h
                    }
                }else{
                    var l=this.getBorderWidth("l")+this.getPadding("l");
                    var r=this.getBorderWidth("r")+this.getPadding("r");
                    var t=this.getBorderWidth("t")+this.getPadding("t");
                    var b=this.getBorderWidth("b")+this.getPadding("b");
                    bx={
                        x:xy[0]+l,
                        y:xy[1]+t,
                        0:xy[0]+l,
                        1:xy[1]+t,
                        width:w-(l+r),
                        height:h-(t+b)
                    }
                }
                bx.right=bx.x+bx.width;
                bx.bottom=bx.y+bx.height;
                return bx
            },
            getFrameWidth:function(sides,onlyContentBox){
                return onlyContentBox&&Ext.isBorderBox?0:(this.getPadding(sides)+this.getBorderWidth(sides))
            },
            setBox:function(box,adjust,animate){
                var w=box.width,h=box.height;
                if((adjust&&!this.autoBoxAdjust)&&!this.isBorderBox()){
                    w-=(this.getBorderWidth("lr")+this.getPadding("lr"));
                    h-=(this.getBorderWidth("tb")+this.getPadding("tb"))
                }
                this.setBounds(box.x,box.y,w,h,this.preanim(arguments,2));
                return this
            },
            repaint:function(){
                var dom=this.dom;
                this.addClass("x-repaint");
                setTimeout(function(){
                    Ext.get(dom).removeClass("x-repaint")
                },1);
                return this
            },
            getMargins:function(side){
                if(!side){
                    return{
                        top:parseInt(this.getStyle("margin-top"),10)||0,
                        left:parseInt(this.getStyle("margin-left"),10)||0,
                        bottom:parseInt(this.getStyle("margin-bottom"),10)||0,
                        right:parseInt(this.getStyle("margin-right"),10)||0
                    }
                }else{
                    return this.addStyles(side,El.margins)
                }
            },
            addStyles:function(sides,styles){
                var val=0,v,w;
                for(var i=0,len=sides.length;i<len;i++){
                    v=this.getStyle(styles[sides.charAt(i)]);
                    if(v){
                        w=parseInt(v,10);
                        if(w){
                            val+=(w>=0?w:-1*w)
                        }
                    }
                }
                return val
            },
            createProxy:function(config,renderTo,matchBox){
                config=typeof config=="object"?config:{
                    tag:"div",
                    cls:config
                };
    
                var proxy;
                if(renderTo){
                    proxy=Ext.DomHelper.append(renderTo,config,true)
                }else{
                    proxy=Ext.DomHelper.insertBefore(this.dom,config,true)
                }
                if(matchBox){
                    proxy.setBox(this.getBox())
                }
                return proxy
            },
            mask:function(msg,msgCls){
                if(this.getStyle("position")=="static"){
                    this.setStyle("position","relative")
                }
                if(this._maskMsg){
                    this._maskMsg.remove()
                }
                if(this._mask){
                    this._mask.remove()
                }
                this._mask=Ext.DomHelper.append(this.dom,{
                    cls:"ext-el-mask"
                },true);
                this.addClass("x-masked");
                this._mask.setDisplayed(true);
                if(typeof msg=="string"){
                    this._maskMsg=Ext.DomHelper.append(this.dom,{
                        cls:"ext-el-mask-msg",
                        cn:{
                            tag:"div"
                        }
                    },true);
                    var mm=this._maskMsg;
                    mm.dom.className=msgCls?"ext-el-mask-msg "+msgCls:"ext-el-mask-msg";
                    mm.dom.firstChild.innerHTML=msg;
                    mm.setDisplayed(true);
                    mm.center(this)
                }
                if(Ext.isIE&&!(Ext.isIE7&&Ext.isStrict)&&this.getStyle("height")=="auto"){
                    this._mask.setSize(this.dom.clientWidth,this.getHeight())
                }
                return this._mask
            },
            unmask:function(){
                if(this._mask){
                    if(this._maskMsg){
                        this._maskMsg.remove();
                        delete this._maskMsg
                    }
                    this._mask.remove();
                    delete this._mask
                }
                this.removeClass("x-masked")
            },
            isMasked:function(){
                return this._mask&&this._mask.isVisible()
            },
            createShim:function(){
                var el=document.createElement("iframe");
                el.frameBorder="no";
                el.className="ext-shim";
                if(Ext.isIE&&Ext.isSecure){
                    el.src=Ext.SSL_SECURE_URL
                }
                var shim=Ext.get(this.dom.parentNode.insertBefore(el,this.dom));
                shim.autoBoxAdjust=false;
                return shim
            },
            remove:function(){
                Ext.removeNode(this.dom);
                delete El.cache[this.dom.id]
            },
            hover:function(overFn,outFn,scope){
                var preOverFn=function(e){
                    if(!e.within(this,true)){
                        overFn.apply(scope||this,arguments)
                    }
                };
    
                var preOutFn=function(e){
                    if(!e.within(this,true)){
                        outFn.apply(scope||this,arguments)
                    }
                };

                this.on("mouseover",preOverFn,this.dom);
                this.on("mouseout",preOutFn,this.dom);
                return this
            },
            addClassOnOver:function(className,preventFlicker){
                this.hover(function(){
                    Ext.fly(this,"_internal").addClass(className)
                },function(){
                    Ext.fly(this,"_internal").removeClass(className)
                });
                return this
            },
            addClassOnFocus:function(className){
                this.on("focus",function(){
                    Ext.fly(this,"_internal").addClass(className)
                },this.dom);
                this.on("blur",function(){
                    Ext.fly(this,"_internal").removeClass(className)
                },this.dom);
                return this
            },
            addClassOnClick:function(className){
                var dom=this.dom;
                this.on("mousedown",function(){
                    Ext.fly(dom,"_internal").addClass(className);
                    var d=Ext.getDoc();
                    var fn=function(){
                        Ext.fly(dom,"_internal").removeClass(className);
                        d.removeListener("mouseup",fn)
                    };
            
                    d.on("mouseup",fn)
                });
                return this
            },
            swallowEvent:function(eventName,preventDefault){
                var fn=function(e){
                    e.stopPropagation();
                    if(preventDefault){
                        e.preventDefault()
                    }
                };
    
                if(Ext.isArray(eventName)){
                    for(var i=0,len=eventName.length;i<len;i++){
                        this.on(eventName[i],fn)
                    }
                    return this
                }
                this.on(eventName,fn);
                return this
            },
            parent:function(selector,returnDom){
                return this.matchNode("parentNode","parentNode",selector,returnDom)
            },
            next:function(selector,returnDom){
                return this.matchNode("nextSibling","nextSibling",selector,returnDom)
            },
            prev:function(selector,returnDom){
                return this.matchNode("previousSibling","previousSibling",selector,returnDom)
            },
            first:function(selector,returnDom){
                return this.matchNode("nextSibling","firstChild",selector,returnDom)
            },
            last:function(selector,returnDom){
                return this.matchNode("previousSibling","lastChild",selector,returnDom)
            },
            matchNode:function(dir,start,selector,returnDom){
                var n=this.dom[start];
                while(n){
                    if(n.nodeType==1&&(!selector||Ext.DomQuery.is(n,selector))){
                        return!returnDom?Ext.get(n):n
                    }
                    n=n[dir]
                }
                return null
            },
            appendChild:function(el){
                el=Ext.get(el);
                el.appendTo(this);
                return this
            },
            createChild:function(config,insertBefore,returnDom){
                config=config||{
                    tag:"div"
                };
    
                if(insertBefore){
                    return Ext.DomHelper.insertBefore(insertBefore,config,returnDom!==true)
                }
                return Ext.DomHelper[!this.dom.firstChild?"overwrite":"append"](this.dom,config,returnDom!==true)
            },
            appendTo:function(el){
                el=Ext.getDom(el);
                el.appendChild(this.dom);
                return this
            },
            insertBefore:function(el){
                el=Ext.getDom(el);
                el.parentNode.insertBefore(this.dom,el);
                return this
            },
            insertAfter:function(el){
                el=Ext.getDom(el);
                el.parentNode.insertBefore(this.dom,el.nextSibling);
                return this
            },
            insertFirst:function(el,returnDom){
                el=el||{};
    
                if(typeof el=="object"&&!el.nodeType&&!el.dom){
                    return this.createChild(el,this.dom.firstChild,returnDom)
                }else{
                    el=Ext.getDom(el);
                    this.dom.insertBefore(el,this.dom.firstChild);
                    return!returnDom?Ext.get(el):el
                }
            },
            insertSibling:function(el,where,returnDom){
                var rt;
                if(Ext.isArray(el)){
                    for(var i=0,len=el.length;i<len;i++){
                        rt=this.insertSibling(el[i],where,returnDom)
                    }
                    return rt
                }
                where=where?where.toLowerCase():"before";
                el=el||{};
    
                var refNode=where=="before"?this.dom:this.dom.nextSibling;
                if(typeof el=="object"&&!el.nodeType&&!el.dom){
                    if(where=="after"&&!this.dom.nextSibling){
                        rt=Ext.DomHelper.append(this.dom.parentNode,el,!returnDom)
                    }else{
                        rt=Ext.DomHelper[where=="after"?"insertAfter":"insertBefore"](this.dom,el,!returnDom)
                    }
                }else{
                    rt=this.dom.parentNode.insertBefore(Ext.getDom(el),refNode);
                    if(!returnDom){
                        rt=Ext.get(rt)
                    }
                }
                return rt
            },
            wrap:function(config,returnDom){
                if(!config){
                    config={
                        tag:"div"
                    }
                }
                var newEl=Ext.DomHelper.insertBefore(this.dom,config,!returnDom);
                newEl.dom?newEl.dom.appendChild(this.dom):newEl.appendChild(this.dom);
                return newEl
            },
            replace:function(el){
                el=Ext.get(el);
                this.insertBefore(el);
                el.remove();
                return this
            },
            replaceWith:function(el){
                if(typeof el=="object"&&!el.nodeType&&!el.dom){
                    el=this.insertSibling(el,"before")
                }else{
                    el=Ext.getDom(el);
                    this.dom.parentNode.insertBefore(el,this.dom)
                }
                El.uncache(this.id);
                this.dom.parentNode.removeChild(this.dom);
                this.dom=el;
                this.id=Ext.id(el);
                El.cache[this.id]=this;
                return this
            },
            insertHtml:function(where,html,returnEl){
                var el=Ext.DomHelper.insertHtml(where,this.dom,html);
                return returnEl?Ext.get(el):el
            },
            set:function(o,useSet){
                var el=this.dom;
                useSet=typeof useSet=="undefined"?(el.setAttribute?true:false):useSet;
                for(var attr in o){
                    if(attr=="style"||typeof o[attr]=="function"){
                        continue
                    }
                    if(attr=="cls"){
                        el.className=o["cls"]
                    }else{
                        if(o.hasOwnProperty(attr)){
                            if(useSet){
                                el.setAttribute(attr,o[attr])
                            }else{
                                el[attr]=o[attr]
                            }
                        }
                    }
                }
                if(o.style){
                    Ext.DomHelper.applyStyles(el,o.style)
                }
                return this
            },
            addKeyListener:function(key,fn,scope){
                var config;
                if(typeof key!="object"||Ext.isArray(key)){
                    config={
                        key:key,
                        fn:fn,
                        scope:scope
                    }
                }else{
                    config={
                        key:key.key,
                        shift:key.shift,
                        ctrl:key.ctrl,
                        alt:key.alt,
                        fn:fn,
                        scope:scope
                    }
                }
                return new Ext.KeyMap(this,config)
            },
            addKeyMap:function(config){
                return new Ext.KeyMap(this,config)
            },
            isScrollable:function(){
                var dom=this.dom;
                return dom.scrollHeight>dom.clientHeight||dom.scrollWidth>dom.clientWidth
            },
            scrollTo:function(side,value,animate){
                var prop=side.toLowerCase()=="left"?"scrollLeft":"scrollTop";
                if(!animate||!A){
                    this.dom[prop]=value
                }else{
                    var to=prop=="scrollLeft"?[value,this.dom.scrollTop]:[this.dom.scrollLeft,value];
                    this.anim({
                        scroll:{
                            "to":to
                        }
                    },this.preanim(arguments,2),"scroll")
                }
                return this
            },
            scroll:function(direction,distance,animate){
                if(!this.isScrollable()){
                    return
                }
                var el=this.dom;
                var l=el.scrollLeft,t=el.scrollTop;
                var w=el.scrollWidth,h=el.scrollHeight;
                var cw=el.clientWidth,ch=el.clientHeight;
                direction=direction.toLowerCase();
                var scrolled=false;
                var a=this.preanim(arguments,2);
                switch(direction){
                    case"l":case"left":
                        if(w-l>cw){
                            var v=Math.min(l+distance,w-cw);
                            this.scrollTo("left",v,a);
                            scrolled=true
                        }
                        break;
                    case"r":case"right":
                        if(l>0){
                            var v=Math.max(l-distance,0);
                            this.scrollTo("left",v,a);
                            scrolled=true
                        }
                        break;
                    case"t":case"top":case"up":
                        if(t>0){
                            var v=Math.max(t-distance,0);
                            this.scrollTo("top",v,a);
                            scrolled=true
                        }
                        break;
                    case"b":case"bottom":case"down":
                        if(h-t>ch){
                            var v=Math.min(t+distance,h-ch);
                            this.scrollTo("top",v,a);
                            scrolled=true
                        }
                        break
                }
                return scrolled
            },
            translatePoints:function(x,y){
                if(typeof x=="object"||Ext.isArray(x)){
                    y=x[1];
                    x=x[0]
                }
                var p=this.getStyle("position");
                var o=this.getXY();
                var l=parseInt(this.getStyle("left"),10);
                var t=parseInt(this.getStyle("top"),10);
                if(isNaN(l)){
                    l=(p=="relative")?0:this.dom.offsetLeft
                }
                if(isNaN(t)){
                    t=(p=="relative")?0:this.dom.offsetTop
                }
                return{
                    left:(x-o[0]+l),
                    top:(y-o[1]+t)
                }
            },
            getScroll:function(){
                var d=this.dom,doc=document;
                if(d==doc||d==doc.body){
                    var l,t;
                    if(Ext.isIE&&Ext.isStrict){
                        l=doc.documentElement.scrollLeft||(doc.body.scrollLeft||0);
                        t=doc.documentElement.scrollTop||(doc.body.scrollTop||0)
                    }else{
                        l=window.pageXOffset||(doc.body.scrollLeft||0);
                        t=window.pageYOffset||(doc.body.scrollTop||0)
                    }
                    return{
                        left:l,
                        top:t
                    }
                }else{
                    return{
                        left:d.scrollLeft,
                        top:d.scrollTop
                    }
                }
            },
            getColor:function(attr,defaultValue,prefix){
                var v=this.getStyle(attr);
                if(!v||v=="transparent"||v=="inherit"){
                    return defaultValue
                }
                var color=typeof prefix=="undefined"?"#":prefix;
                if(v.substr(0,4)=="rgb("){
                    var rvs=v.slice(4,v.length-1).split(",");
                    for(var i=0;i<3;i++){
                        var h=parseInt(rvs[i]);
                        var s=h.toString(16);
                        if(h<16){
                            s="0"+s
                        }
                        color+=s
                    }
                }else{
                    if(v.substr(0,1)=="#"){
                        if(v.length==4){
                            for(var i=1;i<4;i++){
                                var c=v.charAt(i);
                                color+=c+c
                            }
                        }else{
                            if(v.length==7){
                                color+=v.substr(1)
                            }
                        }
                    }
                }
                return(color.length>5?color.toLowerCase():defaultValue)
            },
            boxWrap:function(cls){
                cls=cls||"x-box";
                var el=Ext.get(this.insertHtml("beforeBegin",String.format("<div class=\"{0}\">"+El.boxMarkup+"</div>",cls)));
                el.child("."+cls+"-mc").dom.appendChild(this.dom);
                return el
            },
            getAttributeNS:Ext.isIE?function(ns,name){
                var d=this.dom;
                var type=typeof d[ns+":"+name];
                if(type!="undefined"&&type!="unknown"){
                    return d[ns+":"+name]
                }
                return d[name]
            }:function(ns,name){
                var d=this.dom;
                return d.getAttributeNS(ns,name)||d.getAttribute(ns+":"+name)||d.getAttribute(name)||d[name]
            },
            getTextWidth:function(text,min,max){
                return(Ext.util.TextMetrics.measure(this.dom,Ext.value(text,this.dom.innerHTML,true)).width).constrain(min||0,max||1000000)
            }
        };

        var ep=El.prototype;
        ep.on=ep.addListener;
        ep.mon=ep.addListener;
        ep.getUpdateManager=ep.getUpdater;
        ep.un=ep.removeListener;
        ep.autoBoxAdjust=true;
        El.unitPattern=/\d+(px|em|%|en|ex|pt|in|cm|mm|pc)$/i;
        El.addUnits=function(v,defaultUnit){
            if(v===""||v=="auto"){
                return v
            }
            if(v===undefined){
                return""
            }
            if(typeof v=="number"||!El.unitPattern.test(v)){
                return v+(defaultUnit||"px")
            }
            return v
        };
    
        El.boxMarkup="<div class=\"{0}-tl\"><div class=\"{0}-tr\"><div class=\"{0}-tc\"></div></div></div><div class=\"{0}-ml\"><div class=\"{0}-mr\"><div class=\"{0}-mc\"></div></div></div><div class=\"{0}-bl\"><div class=\"{0}-br\"><div class=\"{0}-bc\"></div></div></div>";
        El.VISIBILITY=1;
        El.DISPLAY=2;
        El.borders={
            l:"border-left-width",
            r:"border-right-width",
            t:"border-top-width",
            b:"border-bottom-width"
        };

        El.paddings={
            l:"padding-left",
            r:"padding-right",
            t:"padding-top",
            b:"padding-bottom"
        };

        El.margins={
            l:"margin-left",
            r:"margin-right",
            t:"margin-top",
            b:"margin-bottom"
        };

        El.cache={};

        var docEl;
        El.get=function(el){
            var ex,elm,id;
            if(!el){
                return null
            }
            if(typeof el=="string"){
                if(!(elm=document.getElementById(el))){
                    return null
                }
                if(ex=El.cache[el]){
                    ex.dom=elm
                }else{
                    ex=El.cache[el]=new El(elm)
                }
                return ex
            }else{
                if(el.tagName){
                    if(!(id=el.id)){
                        id=Ext.id(el)
                    }
                    if(ex=El.cache[id]){
                        ex.dom=el
                    }else{
                        ex=El.cache[id]=new El(el)
                    }
                    return ex
                }else{
                    if(el instanceof El){
                        if(el!=docEl){
                            el.dom=document.getElementById(el.id)||el.dom;
                            El.cache[el.id]=el
                        }
                        return el
                    }else{
                        if(el.isComposite){
                            return el
                        }else{
                            if(Ext.isArray(el)){
                                return El.select(el)
                            }else{
                                if(el==document){
                                    if(!docEl){
                                        var f=function(){};
                                
                                        f.prototype=El.prototype;
                                        docEl=new f();
                                        docEl.dom=document
                                    }
                                    return docEl
                                }
                            }
                        }
                    }
                }
            }
            return null
        };

        El.uncache=function(el){
            for(var i=0,a=arguments,len=a.length;i<len;i++){
                if(a[i]){
                    delete El.cache[a[i].id||a[i]]
                }
            }
        };

        El.garbageCollect=function(){
            if(!Ext.enableGarbageCollector){
                clearInterval(El.collectorThread);
                return
            }
            for(var eid in El.cache){
                var el=El.cache[eid],d=el.dom;
                if(!d||!d.parentNode||(!d.offsetParent&&!document.getElementById(eid))){
                    delete El.cache[eid];
                    if(d&&Ext.enableListenerCollection){
                        E.purgeElement(d)
                    }
                }
            }
        };

        El.collectorThreadId=setInterval(El.garbageCollect,30000);
        var flyFn=function(){};

        flyFn.prototype=El.prototype;
        var _cls=new flyFn();
        El.Flyweight=function(dom){
            this.dom=dom
        };
    
        El.Flyweight.prototype=_cls;
        El.Flyweight.prototype.isFlyweight=true;
        El._flyweights={};

        El.fly=function(el,named){
            named=named||"_global";
            el=Ext.getDom(el);
            if(!el){
                return null
            }
            if(!El._flyweights[named]){
                El._flyweights[named]=new El.Flyweight()
            }
            El._flyweights[named].dom=el;
            return El._flyweights[named]
        };
    
        Ext.get=El.get;
        Ext.fly=El.fly;
        var noBoxAdjust=Ext.isStrict?{
            select:1
        }:{
            input:1,
            select:1,
            textarea:1
        };

        if(Ext.isIE||Ext.isGecko){
            noBoxAdjust["button"]=1
        }
        Ext.EventManager.on(window,"unload",function(){
            delete El.cache;
            delete El._flyweights
        })
    })();
    Ext.enableFx=true;
    Ext.Fx={
        slideIn:function(A,C){
            var B=this.getFxEl();
            C=C||{};
        
            B.queueFx(C,function(){
                A=A||"t";
                this.fixDisplay();
                var D=this.getFxRestore();
                var I=this.getBox();
                this.setSize(I);
                var F=this.fxWrap(D.pos,C,"hidden");
                var K=this.dom.style;
                K.visibility="visible";
                K.position="absolute";
                var E=function(){
                    B.fxUnwrap(F,D.pos,C);
                    K.width=D.width;
                    K.height=D.height;
                    B.afterFx(C)
                };
                
                var J,L={
                    to:[I.x,I.y]
                },H={
                    to:I.width
                },G={
                    to:I.height
                };
                
                switch(A.toLowerCase()){
                    case"t":
                        F.setSize(I.width,0);
                        K.left=K.bottom="0";
                        J={
                            height:G
                        };
                
                        break;
                    case"l":
                        F.setSize(0,I.height);
                        K.right=K.top="0";
                        J={
                            width:H
                        };
                
                        break;
                    case"r":
                        F.setSize(0,I.height);
                        F.setX(I.right);
                        K.left=K.top="0";
                        J={
                            width:H,
                            points:L
                        };
                
                        break;
                    case"b":
                        F.setSize(I.width,0);
                        F.setY(I.bottom);
                        K.left=K.top="0";
                        J={
                            height:G,
                            points:L
                        };
                
                        break;
                    case"tl":
                        F.setSize(0,0);
                        K.right=K.bottom="0";
                        J={
                            width:H,
                            height:G
                        };
                
                        break;
                    case"bl":
                        F.setSize(0,0);
                        F.setY(I.y+I.height);
                        K.right=K.top="0";
                        J={
                            width:H,
                            height:G,
                            points:L
                        };
                
                        break;
                    case"br":
                        F.setSize(0,0);
                        F.setXY([I.right,I.bottom]);
                        K.left=K.top="0";
                        J={
                            width:H,
                            height:G,
                            points:L
                        };
                
                        break;
                    case"tr":
                        F.setSize(0,0);
                        F.setX(I.x+I.width);
                        K.left=K.bottom="0";
                        J={
                            width:H,
                            height:G,
                            points:L
                        };
                
                        break
                }
                this.dom.style.visibility="visible";
                F.show();
                arguments.callee.anim=F.fxanim(J,C,"motion",0.5,"easeOut",E)
            });
            return this
        },
        slideOut:function(A,C){
            var B=this.getFxEl();
            C=C||{};
        
            B.queueFx(C,function(){
                A=A||"t";
                var I=this.getFxRestore();
                var D=this.getBox();
                this.setSize(D);
                var G=this.fxWrap(I.pos,C,"visible");
                var F=this.dom.style;
                F.visibility="visible";
                F.position="absolute";
                G.setSize(D);
                var J=function(){
                    if(C.useDisplay){
                        B.setDisplayed(false)
                    }else{
                        B.hide()
                    }
                    B.fxUnwrap(G,I.pos,C);
                    F.width=I.width;
                    F.height=I.height;
                    B.afterFx(C)
                };
                
                var E,H={
                    to:0
                };
            
                switch(A.toLowerCase()){
                    case"t":
                        F.left=F.bottom="0";
                        E={
                            height:H
                        };
                
                        break;
                    case"l":
                        F.right=F.top="0";
                        E={
                            width:H
                        };
                
                        break;
                    case"r":
                        F.left=F.top="0";
                        E={
                            width:H,
                            points:{
                                to:[D.right,D.y]
                            }
                        };
                
                        break;
                    case"b":
                        F.left=F.top="0";
                        E={
                            height:H,
                            points:{
                                to:[D.x,D.bottom]
                            }
                        };
            
                        break;
                    case"tl":
                        F.right=F.bottom="0";
                        E={
                            width:H,
                            height:H
                        };
        
                        break;
                    case"bl":
                        F.right=F.top="0";
                        E={
                            width:H,
                            height:H,
                            points:{
                                to:[D.x,D.bottom]
                            }
                        };
        
                        break;
                    case"br":
                        F.left=F.top="0";
                        E={
                            width:H,
                            height:H,
                            points:{
                                to:[D.x+D.width,D.bottom]
                            }
                        };
        
                        break;
                    case"tr":
                        F.left=F.bottom="0";
                        E={
                            width:H,
                            height:H,
                            points:{
                                to:[D.right,D.y]
                            }
                        };
    
                        break
                }
                arguments.callee.anim=G.fxanim(E,C,"motion",0.5,"easeOut",J)
            });
            return this
        },
        puff:function(B){
            var A=this.getFxEl();
            B=B||{};
    
            A.queueFx(B,function(){
                this.clearOpacity();
                this.show();
                var F=this.getFxRestore();
                var D=this.dom.style;
                var G=function(){
                    if(B.useDisplay){
                        A.setDisplayed(false)
                    }
                    else{
                        A.hide()
                    }
                    A.clearOpacity();
                    A.setPositioning(F.pos);
                    D.width=F.width;
                    D.height=F.height;
                    D.fontSize="";
                    A.afterFx(B)
                };
            
                var E=this.getWidth();
                var C=this.getHeight();
                arguments.callee.anim=this.fxanim({
                    width:{
                        to:this.adjustWidth(E*2)
                    },
                    height:{
                        to:this.adjustHeight(C*2)
                    },
                    points:{
                        by:[-(E*0.5),-(C*0.5)]
                    },
                    opacity:{
                        to:0
                    },
                    fontSize:{
                        to:200,
                        unit:"%"
                    }
                },B,"motion",0.5,"easeOut",G)
            });
            return this
        },
        switchOff:function(B){
            var A=this.getFxEl();
            B=B||{};
    
            A.queueFx(B,function(){
                this.clearOpacity();
                this.clip();
                var D=this.getFxRestore();
                var C=this.dom.style;
                var E=function(){
                    if(B.useDisplay){
                        A.setDisplayed(false)
                    }else{
                        A.hide()
                    }
                    A.clearOpacity();
                    A.setPositioning(D.pos);
                    C.width=D.width;
                    C.height=D.height;
                    A.afterFx(B)
                };
            
                this.fxanim({
                    opacity:{
                        to:0.3
                    }
                },null,null,0.1,null,function(){
                    this.clearOpacity();
                    (function(){
                        this.fxanim({
                            height:{
                                to:1
                            },
                            points:{
                                by:[0,this.getHeight()*0.5]
                            }
                        },B,"motion",0.3,"easeIn",E)
                    }).defer(100,this)
                })
            });
            return this
        },
        highlight:function(A,C){
            var B=this.getFxEl();
            C=C||{};
    
            B.queueFx(C,function(){
                A=A||"ffff9c";
                var D=C.attr||"backgroundColor";
                this.clearOpacity();
                this.show();
                var G=this.getColor(D);
                var H=this.dom.style[D];
                var F=(C.endColor||G)||"ffffff";
                var I=function(){
                    B.dom.style[D]=H;
                    B.afterFx(C)
                };
            
                var E={};
        
                E[D]={
                    from:A,
                    to:F
                };
        
                arguments.callee.anim=this.fxanim(E,C,"color",1,"easeIn",I)
            });
            return this
        },
        frame:function(A,C,D){
            var B=this.getFxEl();
            D=D||{};
    
            B.queueFx(D,function(){
                A=A||"#C3DAF9";
                if(A.length==6){
                    A="#"+A
                }
                C=C||1;
                var G=D.duration||1;
                this.show();
                var E=this.getBox();
                var F=function(){
                    var H=Ext.getBody().createChild({
                        style:{
                            visbility:"hidden",
                            position:"absolute",
                            "z-index":"35000",
                            border:"0px solid "+A
                        }
                    });
                    var I=Ext.isBorderBox?2:1;
                    H.animate({
                        top:{
                            from:E.y,
                            to:E.y-20
                        },
                        left:{
                            from:E.x,
                            to:E.x-20
                        },
                        borderWidth:{
                            from:0,
                            to:10
                        },
                        opacity:{
                            from:1,
                            to:0
                        },
                        height:{
                            from:E.height,
                            to:(E.height+(20*I))
                        },
                        width:{
                            from:E.width,
                            to:(E.width+(20*I))
                        }
                    },G,function(){
                        H.remove();
                        if(--C>0){
                            F()
                        }else{
                            B.afterFx(D)
                        }
                    })
                };
    
                F.call(this)
            });
            return this
        },
        pause:function(C){
            var A=this.getFxEl();
            var B={};
    
            A.queueFx(B,function(){
                setTimeout(function(){
                    A.afterFx(B)
                },C*1000)
            });
            return this
        },
        fadeIn:function(B){
            var A=this.getFxEl();
            B=B||{};
    
            A.queueFx(B,function(){
                this.setOpacity(0);
                this.fixDisplay();
                this.dom.style.visibility="visible";
                var C=B.endOpacity||1;
                arguments.callee.anim=this.fxanim({
                    opacity:{
                        to:C
                    }
                },B,null,0.5,"easeOut",function(){
                    if(C==1){
                        this.clearOpacity()
                    }
                    A.afterFx(B)
                })
            });
            return this
        },
        fadeOut:function(B){
            var A=this.getFxEl();
            B=B||{};
    
            A.queueFx(B,function(){
                arguments.callee.anim=this.fxanim({
                    opacity:{
                        to:B.endOpacity||0
                    }
                },B,null,0.5,"easeOut",function(){
                    if(this.visibilityMode==Ext.Element.DISPLAY||B.useDisplay){
                        this.dom.style.display="none"
                    }else{
                        this.dom.style.visibility="hidden"
                    }
                    this.clearOpacity();
                    A.afterFx(B)
                })
            });
            return this
        },
        scale:function(A,B,C){
            this.shift(Ext.apply({},C,{
                width:A,
                height:B
            }));
            return this
        },
        shift:function(B){
            var A=this.getFxEl();
            B=B||{};
    
            A.queueFx(B,function(){
                var E={},D=B.width,F=B.height,C=B.x,H=B.y,G=B.opacity;
                if(D!==undefined){
                    E.width={
                        to:this.adjustWidth(D)
                    }
                }
                if(F!==undefined){
                    E.height={
                        to:this.adjustHeight(F)
                    }
                }
                if(C!==undefined||H!==undefined){
                    E.points={
                        to:[C!==undefined?C:this.getX(),H!==undefined?H:this.getY()]
                    }
                }
                if(G!==undefined){
                    E.opacity={
                        to:G
                    }
                }
                if(B.xy!==undefined){
                    E.points={
                        to:B.xy
                    }
                }
                arguments.callee.anim=this.fxanim(E,B,"motion",0.35,"easeOut",function(){
                    A.afterFx(B)
                })
            });
            return this
        },
        ghost:function(A,C){
            var B=this.getFxEl();
            C=C||{};
    
            B.queueFx(C,function(){
                A=A||"b";
                var H=this.getFxRestore();
                var E=this.getWidth(),G=this.getHeight();
                var F=this.dom.style;
                var J=function(){
                    if(C.useDisplay){
                        B.setDisplayed(false)
                    }else{
                        B.hide()
                    }
                    B.clearOpacity();
                    B.setPositioning(H.pos);
                    F.width=H.width;
                    F.height=H.height;
                    B.afterFx(C)
                };
            
                var D={
                    opacity:{
                        to:0
                    },
                    points:{}
                },I=D.points;
                switch(A.toLowerCase()){
                    case"t":
                        I.by=[0,-G];
                        break;
                    case"l":
                        I.by=[-E,0];
                        break;
                    case"r":
                        I.by=[E,0];
                        break;
                    case"b":
                        I.by=[0,G];
                        break;
                    case"tl":
                        I.by=[-E,-G];
                        break;
                    case"bl":
                        I.by=[-E,G];
                        break;
                    case"br":
                        I.by=[E,G];
                        break;
                    case"tr":
                        I.by=[E,-G];
                        break
                }
                arguments.callee.anim=this.fxanim(D,C,"motion",0.5,"easeOut",J)
            });
            return this
        },
        syncFx:function(){
            this.fxDefaults=Ext.apply(this.fxDefaults||{},{
                block:false,
                concurrent:true,
                stopFx:false
            });
            return this
        },
        sequenceFx:function(){
            this.fxDefaults=Ext.apply(this.fxDefaults||{},{
                block:false,
                concurrent:false,
                stopFx:false
            });
            return this
        },
        nextFx:function(){
            var A=this.fxQueue[0];
            if(A){
                A.call(this)
            }
        },
        hasActiveFx:function(){
            return this.fxQueue&&this.fxQueue[0]
        },
        stopFx:function(){
            if(this.hasActiveFx()){
                var A=this.fxQueue[0];
                if(A&&A.anim&&A.anim.isAnimated()){
                    this.fxQueue=[A];
                    A.anim.stop(true)
                }
            }
            return this
        },
        beforeFx:function(A){
            if(this.hasActiveFx()&&!A.concurrent){
                if(A.stopFx){
                    this.stopFx();
                    return true
                }
                return false
            }
            return true
        },
        hasFxBlock:function(){
            var A=this.fxQueue;
            return A&&A[0]&&A[0].block
        },
        queueFx:function(C,A){
            if(!this.fxQueue){
                this.fxQueue=[]
            }
            if(!this.hasFxBlock()){
                Ext.applyIf(C,this.fxDefaults);
                if(!C.concurrent){
                    var B=this.beforeFx(C);
                    A.block=C.block;
                    this.fxQueue.push(A);
                    if(B){
                        this.nextFx()
                    }
                }else{
                    A.call(this)
                }
            }
            return this
        },
        fxWrap:function(F,D,C){
            var B;
            if(!D.wrap||!(B=Ext.get(D.wrap))){
                var A;
                if(D.fixPosition){
                    A=this.getXY()
                }
                var E=document.createElement("div");
                E.style.visibility=C;
                B=Ext.get(this.dom.parentNode.insertBefore(E,this.dom));
                B.setPositioning(F);
                if(B.getStyle("position")=="static"){
                    B.position("relative")
                }
                this.clearPositioning("auto");
                B.clip();
                B.dom.appendChild(this.dom);
                if(A){
                    B.setXY(A)
                }
            }
            return B
        },
        fxUnwrap:function(A,C,B){
            this.clearPositioning();
            this.setPositioning(C);
            if(!B.wrap){
                A.dom.parentNode.insertBefore(this.dom,A.dom);
                A.remove()
            }
        },
        getFxRestore:function(){
            var A=this.dom.style;
            return{
                pos:this.getPositioning(),
                width:A.width,
                height:A.height
            }
        },
        afterFx:function(A){
            if(A.afterStyle){
                this.applyStyles(A.afterStyle)
            }
            if(A.afterCls){
                this.addClass(A.afterCls)
            }
            if(A.remove===true){
                this.remove()
            }
            Ext.callback(A.callback,A.scope,[this]);
            if(!A.concurrent){
                this.fxQueue.shift();
                this.nextFx()
            }
        },
        getFxEl:function(){
            return Ext.get(this.dom)
        },
        fxanim:function(D,E,B,F,C,A){
            B=B||"run";
            E=E||{};
    
            var G=Ext.lib.Anim[B](this.dom,D,(E.duration||F)||0.35,(E.easing||C)||"easeOut",function(){
                Ext.callback(A,this)
            },this);
            E.anim=G;
            return G
        }
    };

    Ext.Fx.resize=Ext.Fx.scale;
    Ext.apply(Ext.Element.prototype,Ext.Fx);
    Ext.CompositeElement=function(A){
        this.elements=[];
        this.addElements(A)
    };
    
    Ext.CompositeElement.prototype={
        isComposite:true,
        addElements:function(E){
            if(!E){
                return this
            }
            if(typeof E=="string"){
                E=Ext.Element.selectorFunction(E)
            }
            var D=this.elements;
            var B=D.length-1;
            for(var C=0,A=E.length;C<A;C++){
                D[++B]=Ext.get(E[C])
            }
            return this
        },
        fill:function(A){
            this.elements=[];
            this.add(A);
            return this
        },
        filter:function(A){
            var B=[];
            this.each(function(C){
                if(C.is(A)){
                    B[B.length]=C.dom
                }
            });
            this.fill(B);
            return this
        },
        invoke:function(E,B){
            var D=this.elements;
            for(var C=0,A=D.length;C<A;C++){
                Ext.Element.prototype[E].apply(D[C],B)
            }
            return this
        },
        add:function(A){
            if(typeof A=="string"){
                this.addElements(Ext.Element.selectorFunction(A))
            }else{
                if(A.length!==undefined){
                    this.addElements(A)
                }
                else{
                    this.addElements([A])
                }
            }
            return this
        },
        each:function(E,D){
            var C=this.elements;
            for(var B=0,A=C.length;B<A;B++){
                if(E.call(D||C[B],C[B],this,B)===false){
                    break
                }
            }
            return this
        },
        item:function(A){
            return this.elements[A]||null
        },
        first:function(){
            return this.item(0)
        },
        last:function(){
            return this.item(this.elements.length-1)
        },
        getCount:function(){
            return this.elements.length
        },
        contains:function(A){
            return this.indexOf(A)!==-1
        },
        indexOf:function(A){
            return this.elements.indexOf(Ext.get(A))
        },
        removeElement:function(D,F){
            if(Ext.isArray(D)){
                for(var C=0,A=D.length;C<A;C++){
                    this.removeElement(D[C])
                }
                return this
            }
            var B=typeof D=="number"?D:this.indexOf(D);
            if(B!==-1&&this.elements[B]){
                if(F){
                    var E=this.elements[B];
                    if(E.dom){
                        E.remove()
                    }else{
                        Ext.removeNode(E)
                    }
                }
                this.elements.splice(B,1)
            }
            return this
        },
        replaceElement:function(D,C,A){
            var B=typeof D=="number"?D:this.indexOf(D);
            if(B!==-1){
                if(A){
                    this.elements[B].replaceWith(C)
                }else{
                    this.elements.splice(B,1,Ext.get(C))
                }
            }
            return this
        },
        clear:function(){
            this.elements=[]
        }
    };
    (function(){
        Ext.CompositeElement.createCall=function(B,C){
            if(!B[C]){
                B[C]=function(){
                    return this.invoke(C,arguments)
                }
            }
        };

        for(var A in Ext.Element.prototype){
            if(typeof Ext.Element.prototype[A]=="function"){
                Ext.CompositeElement.createCall(Ext.CompositeElement.prototype,A)
            }
        }
    })();
    Ext.CompositeElementLite=function(A){
        Ext.CompositeElementLite.superclass.constructor.call(this,A);
        this.el=new Ext.Element.Flyweight()
    };
    
    Ext.extend(Ext.CompositeElementLite,Ext.CompositeElement,{
        addElements:function(E){
            if(E){
                if(Ext.isArray(E)){
                    this.elements=this.elements.concat(E)
                }
                else{
                    var D=this.elements;
                    var B=D.length-1;
                    for(var C=0,A=E.length;C<A;C++){
                        D[++B]=E[C]
                    }
                }
            }
            return this
        },
        invoke:function(F,B){
            var D=this.elements;
            var E=this.el;
            for(var C=0,A=D.length;C<A;C++){
                E.dom=D[C];
                Ext.Element.prototype[F].apply(E,B)
            }
            return this
        },
        item:function(A){
            if(!this.elements[A]){
                return null
            }
            this.el.dom=this.elements[A];
            return this.el
        },
        addListener:function(B,G,F,E){
            var D=this.elements;
            for(var C=0,A=D.length;C<A;C++){
                Ext.EventManager.on(D[C],B,G,F||D[C],E)
            }
            return this
        },
        each:function(F,E){
            var C=this.elements;
            var D=this.el;
            for(var B=0,A=C.length;B<A;B++){
                D.dom=C[B];
                if(F.call(E||D,D,this,B)===false){
                    break
                }
            }
            return this
        },
        indexOf:function(A){
            return this.elements.indexOf(Ext.getDom(A))
        },
        replaceElement:function(D,C,A){
            var B=typeof D=="number"?D:this.indexOf(D);
            if(B!==-1){
                C=Ext.getDom(C);
                if(A){
                    var E=this.elements[B];
                    E.parentNode.insertBefore(C,E);
                    Ext.removeNode(E)
                }
                this.elements.splice(B,1,C)
            }
            return this
        }
    });
    Ext.CompositeElementLite.prototype.on=Ext.CompositeElementLite.prototype.addListener;
    if(Ext.DomQuery){
        Ext.Element.selectorFunction=Ext.DomQuery.select
    }
    Ext.Element.select=function(A,D,B){
        var C;
        if(typeof A=="string"){
            C=Ext.Element.selectorFunction(A,B)
        }else{
            if(A.length!==undefined){
                C=A
            }else{
                throw"Invalid selector"
            }
        }
        if(D===true){
            return new Ext.CompositeElement(C)
        }else{
            return new Ext.CompositeElementLite(C)
        }
    };

    Ext.select=Ext.Element.select;
    Ext.data.Connection=function(A){
        Ext.apply(this,A);
        this.addEvents("beforerequest","requestcomplete","requestexception");
        Ext.data.Connection.superclass.constructor.call(this)
    };
    
    Ext.extend(Ext.data.Connection,Ext.util.Observable,{
        timeout:30000,
        autoAbort:false,
        disableCaching:true,
        request:function(E){
            if(this.fireEvent("beforerequest",this,E)!==false){
                var C=E.params;
                if(typeof C=="function"){
                    C=C.call(E.scope||window,E)
                }
                if(typeof C=="object"){
                    C=Ext.urlEncode(C)
                }
                if(this.extraParams){
                    var G=Ext.urlEncode(this.extraParams);
                    C=C?(C+"&"+G):G
                }
                var B=E.url||this.url;
                if(typeof B=="function"){
                    B=B.call(E.scope||window,E)
                }
                if(E.form){
                    var D=Ext.getDom(E.form);
                    B=B||D.action;
                    var I=D.getAttribute("enctype");
                    if(E.isUpload||(I&&I.toLowerCase()=="multipart/form-data")){
                        return this.doFormUpload(E,C,B)
                    }
                    var H=Ext.lib.Ajax.serializeForm(D);
                    C=C?(C+"&"+H):H
                }
                var J=E.headers;
                if(this.defaultHeaders){
                    J=Ext.apply(J||{},this.defaultHeaders);
                    if(!E.headers){
                        E.headers=J
                    }
                }
                var F={
                    success:this.handleResponse,
                    failure:this.handleFailure,
                    scope:this,
                    argument:{
                        options:E
                    },
                    timeout:E.timeout||this.timeout
                };
            
                var A=E.method||this.method||(C?"POST":"GET");
                if(A=="GET"&&(this.disableCaching&&E.disableCaching!==false)||E.disableCaching===true){
                    B+=(B.indexOf("?")!=-1?"&":"?")+"_dc="+(new Date().getTime())
                }
                if(typeof E.autoAbort=="boolean"){
                    if(E.autoAbort){
                        this.abort()
                    }
                }else{
                    if(this.autoAbort!==false){
                        this.abort()
                    }
                }
                if((A=="GET"&&C)||E.xmlData||E.jsonData){
                    B+=(B.indexOf("?")!=-1?"&":"?")+C;
                    C=""
                }
                this.transId=Ext.lib.Ajax.request(A,B,F,C,E);
                return this.transId
            }else{
                Ext.callback(E.callback,E.scope,[E,null,null]);
                return null
            }
        },
        isLoading:function(A){
            if(A){
                return Ext.lib.Ajax.isCallInProgress(A)
            }
            else{
                return this.transId?true:false
            }
        },
        abort:function(A){
            if(A||this.isLoading()){
                Ext.lib.Ajax.abort(A||this.transId)
            }
        },
        handleResponse:function(A){
            this.transId=false;
            var B=A.argument.options;
            A.argument=B?B.argument:null;
            this.fireEvent("requestcomplete",this,A,B);
            Ext.callback(B.success,B.scope,[A,B]);
            Ext.callback(B.callback,B.scope,[B,true,A])
        },
        handleFailure:function(A,C){
            this.transId=false;
            var B=A.argument.options;
            A.argument=B?B.argument:null;
            this.fireEvent("requestexception",this,A,B,C);
            Ext.callback(B.failure,B.scope,[A,B]);
            Ext.callback(B.callback,B.scope,[B,false,A])
        },
        doFormUpload:function(E,A,B){
            var C=Ext.id();
            var F=document.createElement("iframe");
            F.id=C;
            F.name=C;
            F.className="x-hidden";
            if(Ext.isIE){
                F.src=Ext.SSL_SECURE_URL
            }
            document.body.appendChild(F);
            if(Ext.isIE){
                document.frames[C].name=C
            }
            var D=Ext.getDom(E.form);
            D.target=C;
            D.method="POST";
            D.enctype=D.encoding="multipart/form-data";
            if(B){
                D.action=B
            }
            var L,J;
            if(A){
                L=[];
                A=Ext.urlDecode(A,false);
                for(var H in A){
                    if(A.hasOwnProperty(H)){
                        J=document.createElement("input");
                        J.type="hidden";
                        J.name=H;
                        J.value=A[H];
                        D.appendChild(J);
                        L.push(J)
                    }
                }
            }
            function G(){
                var M={
                    responseText:"",
                    responseXML:null
                };
    
                M.argument=E?E.argument:null;
                try{
                    var O;
                    if(Ext.isIE){
                        O=F.contentWindow.document
                    }
                    else{
                        O=(F.contentDocument||window.frames[C].document)
                    }
                    if(O&&O.body){
                        M.responseText=O.body.innerHTML
                    }
                    if(O&&O.XMLDocument){
                        M.responseXML=O.XMLDocument
                    }else{
                        M.responseXML=O
                    }
                }catch(N){}
                Ext.EventManager.removeListener(F,"load",G,this);
                this.fireEvent("requestcomplete",this,M,E);
                Ext.callback(E.success,E.scope,[M,E]);
                Ext.callback(E.callback,E.scope,[E,true,M]);
                setTimeout(function(){
                    Ext.removeNode(F)
                },100)
            }
            Ext.EventManager.on(F,"load",G,this);
            D.submit();
            if(L){
                for(var I=0,K=L.length;I<K;I++){
                    Ext.removeNode(L[I])
                }
            }
        }
    });
    Ext.Ajax=new Ext.data.Connection({
        autoAbort:false,
        serializeForm:function(A){
            return Ext.lib.Ajax.serializeForm(A)
        }
    });
    Ext.Updater=function(B,A){
        B=Ext.get(B);
        if(!A&&B.updateManager){
            return B.updateManager
        }
        this.el=B;
        this.defaultUrl=null;
        this.addEvents("beforeupdate","update","failure");
        var C=Ext.Updater.defaults;
        this.sslBlankUrl=C.sslBlankUrl;
        this.disableCaching=C.disableCaching;
        this.indicatorText=C.indicatorText;
        this.showLoadIndicator=C.showLoadIndicator;
        this.timeout=C.timeout;
        this.loadScripts=C.loadScripts;
        this.transaction=null;
        this.autoRefreshProcId=null;
        this.refreshDelegate=this.refresh.createDelegate(this);
        this.updateDelegate=this.update.createDelegate(this);
        this.formUpdateDelegate=this.formUpdate.createDelegate(this);
        if(!this.renderer){
            this.renderer=new Ext.Updater.BasicRenderer()
        }
        Ext.Updater.superclass.constructor.call(this)
    };
    
    Ext.extend(Ext.Updater,Ext.util.Observable,{
        getEl:function(){
            return this.el
        },
        update:function(B,F,H,D){
            if(this.fireEvent("beforeupdate",this.el,B,F)!==false){
                var G=this.method,A,C;
                if(typeof B=="object"){
                    A=B;
                    B=A.url;
                    F=F||A.params;
                    H=H||A.callback;
                    D=D||A.discardUrl;
                    C=A.scope;
                    if(typeof A.method!="undefined"){
                        G=A.method
                    }
                    if(typeof A.nocache!="undefined"){
                        this.disableCaching=A.nocache
                    }
                    if(typeof A.text!="undefined"){
                        this.indicatorText="<div class=\"loading-indicator\">"+A.text+"</div>"
                    }
                    if(typeof A.scripts!="undefined"){
                        this.loadScripts=A.scripts
                    }
                    if(typeof A.timeout!="undefined"){
                        this.timeout=A.timeout
                    }
                }
                this.showLoading();
                if(!D){
                    this.defaultUrl=B
                }
                if(typeof B=="function"){
                    B=B.call(this)
                }
                G=G||(F?"POST":"GET");
                if(G=="GET"){
                    B=this.prepareUrl(B)
                }
                var E=Ext.apply(A||{},{
                    url:B,
                    params:(typeof F=="function"&&C)?F.createDelegate(C):F,
                    success:this.processSuccess,
                    failure:this.processFailure,
                    scope:this,
                    callback:undefined,
                    timeout:(this.timeout*1000),
                    argument:{
                        "options":A,
                        "url":B,
                        "form":null,
                        "callback":H,
                        "scope":C||window,
                        "params":F
                    }
                });
                this.transaction=Ext.Ajax.request(E)
            }
        },
        formUpdate:function(C,A,B,D){
            if(this.fireEvent("beforeupdate",this.el,C,A)!==false){
                if(typeof A=="function"){
                    A=A.call(this)
                }
                C=Ext.getDom(C);
                this.transaction=Ext.Ajax.request({
                    form:C,
                    url:A,
                    success:this.processSuccess,
                    failure:this.processFailure,
                    scope:this,
                    timeout:(this.timeout*1000),
                    argument:{
                        "url":A,
                        "form":C,
                        "callback":D,
                        "reset":B
                    }
                });
                this.showLoading.defer(1,this)
            }
        },
        refresh:function(A){
            if(this.defaultUrl==null){
                return
            }
            this.update(this.defaultUrl,null,A,true)
        },
        startAutoRefresh:function(B,C,D,E,A){
            if(A){
                this.update(C||this.defaultUrl,D,E,true)
            }
            if(this.autoRefreshProcId){
                clearInterval(this.autoRefreshProcId)
            }
            this.autoRefreshProcId=setInterval(this.update.createDelegate(this,[C||this.defaultUrl,D,E,true]),B*1000)
        },
        stopAutoRefresh:function(){
            if(this.autoRefreshProcId){
                clearInterval(this.autoRefreshProcId);
                delete this.autoRefreshProcId
            }
        },
        isAutoRefreshing:function(){
            return this.autoRefreshProcId?true:false
        },
        showLoading:function(){
            if(this.showLoadIndicator){
                this.el.update(this.indicatorText)
            }
        },
        prepareUrl:function(B){
            if(this.disableCaching){
                var A="_dc="+(new Date().getTime());
                if(B.indexOf("?")!==-1){
                    B+="&"+A
                }else{
                    B+="?"+A
                }
            }
            return B
        },
        processSuccess:function(A){
            this.transaction=null;
            if(A.argument.form&&A.argument.reset){
                try{
                    A.argument.form.reset()
                }catch(B){}
            }
            if(this.loadScripts){
                this.renderer.render(this.el,A,this,this.updateComplete.createDelegate(this,[A]))
            }else{
                this.renderer.render(this.el,A,this);
                this.updateComplete(A)
            }
        },
        updateComplete:function(A){
            this.fireEvent("update",this.el,A);
            if(typeof A.argument.callback=="function"){
                A.argument.callback.call(A.argument.scope,this.el,true,A,A.argument.options)
            }
        },
        processFailure:function(A){
            this.transaction=null;
            this.fireEvent("failure",this.el,A);
            if(typeof A.argument.callback=="function"){
                A.argument.callback.call(A.argument.scope,this.el,false,A,A.argument.options)
            }
        },
        setRenderer:function(A){
            this.renderer=A
        },
        getRenderer:function(){
            return this.renderer
        },
        setDefaultUrl:function(A){
            this.defaultUrl=A
        },
        abort:function(){
            if(this.transaction){
                Ext.Ajax.abort(this.transaction)
            }
        },
        isUpdating:function(){
            if(this.transaction){
                return Ext.Ajax.isLoading(this.transaction)
            }
            return false
        }
    });
    Ext.Updater.defaults={
        timeout:30,
        loadScripts:false,
        sslBlankUrl:(Ext.SSL_SECURE_URL||"javascript:false"),
        disableCaching:false,
        showLoadIndicator:true,
        indicatorText:"<div class=\"loading-indicator\">Loading...</div>"
    };

    Ext.Updater.updateElement=function(D,C,E,B){
        var A=Ext.get(D).getUpdater();
        Ext.apply(A,B);
        A.update(C,E,B?B.callback:null)
    };
    
    Ext.Updater.update=Ext.Updater.updateElement;
    Ext.Updater.BasicRenderer=function(){};
    
    Ext.Updater.BasicRenderer.prototype={
        render:function(C,A,B,D){
            C.update(A.responseText,B.loadScripts,D)
        }
    };

    Ext.UpdateManager=Ext.Updater;
    Ext.util.DelayedTask=function(E,D,A){
        var G=null,F,B;
        var C=function(){
            var H=new Date().getTime();
            if(H-B>=F){
                clearInterval(G);
                G=null;
                E.apply(D,A||[])
            }
        };
    
        this.delay=function(I,K,J,H){
            if(G&&I!=F){
                this.cancel()
            }
            F=I;
            B=new Date().getTime();
            E=K||E;
            D=J||D;
            A=H||A;
            if(!G){
                G=setInterval(C,F)
            }
        };

        this.cancel=function(){
            if(G){
                clearInterval(G);
                G=null
            }
        }
    };

    Ext.util.MixedCollection=function(B,A){
        this.items=[];
        this.map={};
    
        this.keys=[];
        this.length=0;
        this.addEvents("clear","add","replace","remove","sort");
        this.allowFunctions=B===true;
        if(A){
            this.getKey=A
        }
        Ext.util.MixedCollection.superclass.constructor.call(this)
    };
    
    Ext.extend(Ext.util.MixedCollection,Ext.util.Observable,{
        allowFunctions:false,
        add:function(B,C){
            if(arguments.length==1){
                C=arguments[0];
                B=this.getKey(C)
            }
            if(typeof B=="undefined"||B===null){
                this.length++;
                this.items.push(C);
                this.keys.push(null)
            }
            else{
                var A=this.map[B];
                if(A){
                    return this.replace(B,C)
                }
                this.length++;
                this.items.push(C);
                this.map[B]=C;
                this.keys.push(B)
            }
            this.fireEvent("add",this.length-1,C,B);
            return C
        },
        getKey:function(A){
            return A.id
        },
        replace:function(C,D){
            if(arguments.length==1){
                D=arguments[0];
                C=this.getKey(D)
            }
            var A=this.item(C);
            if(typeof C=="undefined"||C===null||typeof A=="undefined"){
                return this.add(C,D)
            }
            var B=this.indexOfKey(C);
            this.items[B]=D;
            this.map[C]=D;
            this.fireEvent("replace",C,A,D);
            return D
        },
        addAll:function(E){
            if(arguments.length>1||Ext.isArray(E)){
                var B=arguments.length>1?arguments:E;
                for(var D=0,A=B.length;D<A;D++){
                    this.add(B[D])
                }
            }else{
                for(var C in E){
                    if(this.allowFunctions||typeof E[C]!="function"){
                        this.add(C,E[C])
                    }
                }
            }
        },
        each:function(E,D){
            var B=[].concat(this.items);
            for(var C=0,A=B.length;C<A;C++){
                if(E.call(D||B[C],B[C],C,A)===false){
                    break
                }
            }
        },
        eachKey:function(D,C){
            for(var B=0,A=this.keys.length;B<A;B++){
                D.call(C||window,this.keys[B],this.items[B],B,A)
            }
        },
        find:function(D,C){
            for(var B=0,A=this.items.length;B<A;B++){
                if(D.call(C||window,this.items[B],this.keys[B])){
                    return this.items[B]
                }
            }
            return null
        },
        insert:function(A,B,C){
            if(arguments.length==2){
                C=arguments[1];
                B=this.getKey(C)
            }
            if(A>=this.length){
                return this.add(B,C)
            }
            this.length++;
            this.items.splice(A,0,C);
            if(typeof B!="undefined"&&B!=null){
                this.map[B]=C
            }
            this.keys.splice(A,0,B);
            this.fireEvent("add",A,C,B);
            return C
        },
        remove:function(A){
            return this.removeAt(this.indexOf(A))
        },
        removeAt:function(A){
            if(A<this.length&&A>=0){
                this.length--;
                var C=this.items[A];
                this.items.splice(A,1);
                var B=this.keys[A];
                if(typeof B!="undefined"){
                    delete this.map[B]
                }
                this.keys.splice(A,1);
                this.fireEvent("remove",C,B);
                return C
            }
            return false
        },
        removeKey:function(A){
            return this.removeAt(this.indexOfKey(A))
        },
        getCount:function(){
            return this.length
        },
        indexOf:function(A){
            return this.items.indexOf(A)
        },
        indexOfKey:function(A){
            return this.keys.indexOf(A)
        },
        item:function(A){
            var B=typeof this.map[A]!="undefined"?this.map[A]:this.items[A];
            return typeof B!="function"||this.allowFunctions?B:null
        },
        itemAt:function(A){
            return this.items[A]
        },
        key:function(A){
            return this.map[A]
        },
        contains:function(A){
            return this.indexOf(A)!=-1
        },
        containsKey:function(A){
            return typeof this.map[A]!="undefined"
        },
        clear:function(){
            this.length=0;
            this.items=[];
            this.keys=[];
            this.map={};
    
            this.fireEvent("clear")
        },
        first:function(){
            return this.items[0]
        },
        last:function(){
            return this.items[this.length-1]
        },
        _sort:function(I,A,H){
            var C=String(A).toUpperCase()=="DESC"?-1:1;
            H=H||function(K,J){
                return K-J
            };
        
            var G=[],B=this.keys,F=this.items;
            for(var D=0,E=F.length;D<E;D++){
                G[G.length]={
                    key:B[D],
                    value:F[D],
                    index:D
                }
            }
            G.sort(function(K,J){
                var L=H(K[I],J[I])*C;
                if(L==0){
                    L=(K.index<J.index?-1:1)
                }
                return L
            });
            for(var D=0,E=G.length;D<E;D++){
                F[D]=G[D].value;
                B[D]=G[D].key
            }
            this.fireEvent("sort",this)
        },
        sort:function(A,B){
            this._sort("value",A,B)
        },
        keySort:function(A,B){
            this._sort("key",A,B||function(D,C){
                return String(D).toUpperCase()-String(C).toUpperCase()
            })
        },
        getRange:function(E,A){
            var B=this.items;
            if(B.length<1){
                return[]
            }
            E=E||0;
            A=Math.min(typeof A=="undefined"?this.length-1:A,this.length-1);
            var D=[];
            if(E<=A){
                for(var C=E;C<=A;C++){
                    D[D.length]=B[C]
                }
            }else{
                for(var C=E;C>=A;C--){
                    D[D.length]=B[C]
                }
            }
            return D
        },
        filter:function(C,B,D,A){
            if(Ext.isEmpty(B,false)){
                return this.clone()
            }
            B=this.createValueMatcher(B,D,A);
            return this.filterBy(function(E){
                return E&&B.test(E[C])
            })
        },
        filterBy:function(F,E){
            var G=new Ext.util.MixedCollection();
            G.getKey=this.getKey;
            var B=this.keys,D=this.items;
            for(var C=0,A=D.length;C<A;C++){
                if(F.call(E||this,D[C],B[C])){
                    G.add(B[C],D[C])
                }
            }
            return G
        },
        findIndex:function(C,B,E,D,A){
            if(Ext.isEmpty(B,false)){
                return-1
            }
            B=this.createValueMatcher(B,D,A);
            return this.findIndexBy(function(F){
                return F&&B.test(F[C])
            },null,E)
        },
        findIndexBy:function(F,E,G){
            var B=this.keys,D=this.items;
            for(var C=(G||0),A=D.length;C<A;C++){
                if(F.call(E||this,D[C],B[C])){
                    return C
                }
            }
            if(typeof G=="number"&&G>0){
                for(var C=0;C<G;C++){
                    if(F.call(E||this,D[C],B[C])){
                        return C
                    }
                }
            }
            return-1
        },
        createValueMatcher:function(B,C,A){
            if(!B.exec){
                B=String(B);
                B=new RegExp((C===true?"":"^")+Ext.escapeRe(B),A?"":"i")
            }
            return B
        },
        clone:function(){
            var E=new Ext.util.MixedCollection();
            var B=this.keys,D=this.items;
            for(var C=0,A=D.length;C<A;C++){
                E.add(B[C],D[C])
            }
            E.getKey=this.getKey;
            return E
        }
    });
    Ext.util.MixedCollection.prototype.get=Ext.util.MixedCollection.prototype.item;
    Ext.util.JSON=new(function(){
        var useHasOwn={}.hasOwnProperty?true:false;
        var pad=function(n){
            return n<10?"0"+n:n
        };
        
        var m={
            "\b":"\\b",
            "\t":"\\t",
            "\n":"\\n",
            "\f":"\\f",
            "\r":"\\r",
            "\"":"\\\"",
            "\\":"\\\\"
        };
    
        var encodeString=function(s){
            if(/["\\\x00-\x1f]/.test(s)){
                return"\""+s.replace(/([\x00-\x1f\\"])/g,function(a,b){
                    var c=m[b];
                    if(c){
                        return c
                    }
                    c=b.charCodeAt();
                    return"\\u00"+Math.floor(c/16).toString(16)+(c%16).toString(16)
                })+"\""
            }
            return"\""+s+"\""
        };
        
        var encodeArray=function(o){
            var a=["["],b,i,l=o.length,v;
            for(i=0;i<l;i+=1){
                v=o[i];
                switch(typeof v){
                    case"undefined":case"function":case"unknown":
                        break;
                    default:
                        if(b){
                            a.push(",")
                        }
                        a.push(v===null?"null":Ext.util.JSON.encode(v));
                        b=true
                }
            }
            a.push("]");
            return a.join("")
        };
    
        var encodeDate=function(o){
            return"\""+o.getFullYear()+"-"+pad(o.getMonth()+1)+"-"+pad(o.getDate())+"T"+pad(o.getHours())+":"+pad(o.getMinutes())+":"+pad(o.getSeconds())+"\""
        };
    
        this.encode=function(o){
            if(typeof o=="undefined"||o===null){
                return"null"
            }else{
                if(Ext.isArray(o)){
                    return encodeArray(o)
                }else{
                    if(Ext.isDate(o)){
                        return encodeDate(o)
                    }else{
                        if(typeof o=="string"){
                            return encodeString(o)
                        }else{
                            if(typeof o=="number"){
                                return isFinite(o)?String(o):"null"
                            }else{
                                if(typeof o=="boolean"){
                                    return String(o)
                                }else{
                                    var a=["{"],b,i,v;
                                    for(i in o){
                                        if(!useHasOwn||o.hasOwnProperty(i)){
                                            v=o[i];
                                            switch(typeof v){
                                                case"undefined":case"function":case"unknown":
                                                    break;
                                                default:
                                                    if(b){
                                                        a.push(",")
                                                    }
                                                    a.push(this.encode(i),":",v===null?"null":this.encode(v));
                                                    b=true
                                            }
                                        }
                                    }
                                    a.push("}");
                                    return a.join("")
                                }
                            }
                        }
                    }
                }
            }
        };

        this.decode=function(json){
            return eval("("+json+")")
        }
    })();
    Ext.encode=Ext.util.JSON.encode;
    Ext.decode=Ext.util.JSON.decode;
    Ext.XTemplate=function(){
        Ext.XTemplate.superclass.constructor.apply(this,arguments);
        var P=this.html;
        P=["<tpl>",P,"</tpl>"].join("");
        var O=/<tpl\b[^>]*>((?:(?=([^<]+))\2|<(?!tpl\b[^>]*>))*?)<\/tpl>/;
        var N=/^<tpl\b[^>]*?for="(.*?)"/;
        var L=/^<tpl\b[^>]*?if="(.*?)"/;
        var J=/^<tpl\b[^>]*?exec="(.*?)"/;
        var C,B=0;
        var G=[];
        while(C=P.match(O)){
            var M=C[0].match(N);
            var K=C[0].match(L);
            var I=C[0].match(J);
            var E=null,H=null,D=null;
            var A=M&&M[1]?M[1]:"";
            if(K){
                E=K&&K[1]?K[1]:null;
                if(E){
                    H=new Function("values","parent","xindex","xcount","with(values){ return "+(Ext.util.Format.htmlDecode(E))+"; }")
                }
            }
            if(I){
                E=I&&I[1]?I[1]:null;
                if(E){
                    D=new Function("values","parent","xindex","xcount","with(values){ "+(Ext.util.Format.htmlDecode(E))+"; }")
                }
            }
            if(A){
                switch(A){
                    case".":
                        A=new Function("values","parent","with(values){ return values; }");
                        break;
                    case"..":
                        A=new Function("values","parent","with(values){ return parent; }");
                        break;
                    default:
                        A=new Function("values","parent","with(values){ return "+A+"; }")
                }
            }
            G.push({
                id:B,
                target:A,
                exec:D,
                test:H,
                body:C[1]||""
            });
            P=P.replace(C[0],"{xtpl"+B+"}");
            ++B
        }
        for(var F=G.length-1;F>=0;--F){
            this.compileTpl(G[F])
        }
        this.master=G[G.length-1];
        this.tpls=G
    };

    Ext.extend(Ext.XTemplate,Ext.Template,{
        re:/\{([\w-\.\#]+)(?:\:([\w\.]*)(?:\((.*?)?\))?)?(\s?[\+\-\*\\]\s?[\d\.\+\-\*\\\(\)]+)?\}/g,
        codeRe:/\{\[((?:\\\]|.|\n)*?)\]\}/g,
        applySubTemplate:function(A,H,G,D,C){
            var J=this.tpls[A];
            if(J.test&&!J.test.call(this,H,G,D,C)){
                return""
            }
            if(J.exec&&J.exec.call(this,H,G,D,C)){
                return""
            }
            var I=J.target?J.target.call(this,H,G):H;
            G=J.target?H:G;
            if(J.target&&Ext.isArray(I)){
                var B=[];
                for(var E=0,F=I.length;E<F;E++){
                    B[B.length]=J.compiled.call(this,I[E],G,E+1,F)
                }
                return B.join("")
            }
            return J.compiled.call(this,I,G,D,C)
        },
        compileTpl:function(tpl){
            var fm=Ext.util.Format;
            var useF=this.disableFormats!==true;
            var sep=Ext.isGecko?"+":",";
            var fn=function(m,name,format,args,math){
                if(name.substr(0,4)=="xtpl"){
                    return"'"+sep+"this.applySubTemplate("+name.substr(4)+", values, parent, xindex, xcount)"+sep+"'"
                }
                var v;
                if(name==="."){
                    v="values"
                }else{
                    if(name==="#"){
                        v="xindex"
                    }else{
                        if(name.indexOf(".")!=-1){
                            v=name
                        }else{
                            v="values['"+name+"']"
                        }
                    }
                }
                if(math){
                    v="("+v+math+")"
                }
                if(format&&useF){
                    args=args?","+args:"";
                    if(format.substr(0,5)!="this."){
                        format="fm."+format+"("
                    }else{
                        format="this.call(\""+format.substr(5)+"\", ";
                        args=", values"
                    }
                }else{
                    args="";
                    format="("+v+" === undefined ? '' : "
                }
                return"'"+sep+format+v+args+")"+sep+"'"
            };

            var codeFn=function(m,code){
                return"'"+sep+"("+code+")"+sep+"'"
            };
    
            var body;
            if(Ext.isGecko){
                body="tpl.compiled = function(values, parent, xindex, xcount){ return '"+tpl.body.replace(/(\r\n|\n)/g,"\\n").replace(/'/g,"\\'").replace(this.re,fn).replace(this.codeRe,codeFn)+"';};"
            }else{
                body=["tpl.compiled = function(values, parent, xindex, xcount){ return ['"];
                body.push(tpl.body.replace(/(\r\n|\n)/g,"\\n").replace(/'/g,"\\'").replace(this.re,fn).replace(this.codeRe,codeFn));
                body.push("'].join('');};");
                body=body.join("")
            }
            eval(body);
            return this
        },
        apply:function(A){
            return this.master.compiled.call(this,A,{},1,1)
        },
        applyTemplate:function(A){
            return this.master.compiled.call(this,A,{},1,1)
        },
        compile:function(){
            return this
        }
    });
    Ext.XTemplate.from=function(A){
        A=Ext.getDom(A);
        return new Ext.XTemplate(A.value||A.innerHTML)
    };
    
    Ext.KeyNav=function(B,A){
        this.el=Ext.get(B);
        Ext.apply(this,A);
        if(!this.disabled){
            this.disabled=true;
            this.enable()
        }
    };

    Ext.KeyNav.prototype={
        disabled:false,
        defaultEventAction:"stopEvent",
        forceKeyDown:false,
        prepareEvent:function(C){
            var A=C.getKey();
            var B=this.keyToHandler[A];
            if(Ext.isSafari&&B&&A>=37&&A<=40){
                C.stopEvent()
            }
        },
        relay:function(C){
            var A=C.getKey();
            var B=this.keyToHandler[A];
            if(B&&this[B]){
                if(this.doRelay(C,this[B],B)!==true){
                    C[this.defaultEventAction]()
                }
            }
        },
        doRelay:function(C,B,A){
            return B.call(this.scope||this,C)
        },
        enter:false,
        left:false,
        right:false,
        up:false,
        down:false,
        tab:false,
        esc:false,
        pageUp:false,
        pageDown:false,
        del:false,
        home:false,
        end:false,
        keyToHandler:{
            37:"left",
            39:"right",
            38:"up",
            40:"down",
            33:"pageUp",
            34:"pageDown",
            46:"del",
            36:"home",
            35:"end",
            13:"enter",
            27:"esc",
            9:"tab"
        },
        enable:function(){
            if(this.disabled){
                if(this.forceKeyDown||Ext.isIE||Ext.isAir){
                    this.el.on("keydown",this.relay,this)
                }else{
                    this.el.on("keydown",this.prepareEvent,this);
                    this.el.on("keypress",this.relay,this)
                }
                this.disabled=false
            }
        },
        disable:function(){
            if(!this.disabled){
                if(this.forceKeyDown||Ext.isIE||Ext.isAir){
                    this.el.un("keydown",this.relay)
                }else{
                    this.el.un("keydown",this.prepareEvent);
                    this.el.un("keypress",this.relay)
                }
                this.disabled=true
            }
        }
    };

    Ext.data.SortTypes={
        none:function(A){
            return A
        },
        stripTagsRE:/<\/?[^>]+>/gi,
        asText:function(A){
            return String(A).replace(this.stripTagsRE,"")
        },
        asUCText:function(A){
            return String(A).toUpperCase().replace(this.stripTagsRE,"")
        },
        asUCString:function(A){
            return String(A).toUpperCase()
        },
        asDate:function(A){
            if(!A){
                return 0
            }
            if(Ext.isDate(A)){
                return A.getTime()
            }
            return Date.parse(String(A))
        },
        asFloat:function(A){
            var B=parseFloat(String(A).replace(/,/g,""));
            if(isNaN(B)){
                B=0
            }
            return B
        },
        asInt:function(A){
            var B=parseInt(String(A).replace(/,/g,""));
            if(isNaN(B)){
                B=0
            }
            return B
        }
    };

    Ext.data.Record=function(A,B){
        this.id=(B||B===0)?B:++Ext.data.Record.AUTO_ID;
        this.data=A
    };
    
    Ext.data.Record.create=function(E){
        var C=Ext.extend(Ext.data.Record,{});
        var D=C.prototype;
        D.fields=new Ext.util.MixedCollection(false,function(F){
            return F.name
        });
        for(var B=0,A=E.length;B<A;B++){
            D.fields.add(new Ext.data.Field(E[B]))
        }
        C.getField=function(F){
            return D.fields.get(F)
        };
        
        return C
    };
    
    Ext.data.Record.AUTO_ID=1000;
    Ext.data.Record.EDIT="edit";
    Ext.data.Record.REJECT="reject";
    Ext.data.Record.COMMIT="commit";
    Ext.data.Record.prototype={
        dirty:false,
        editing:false,
        error:null,
        modified:null,
        join:function(A){
            this.store=A
        },
        set:function(A,B){
            if(String(this.data[A])==String(B)){
                return
            }
            this.dirty=true;
            if(!this.modified){
                this.modified={}
            }
            if(typeof this.modified[A]=="undefined"){
                this.modified[A]=this.data[A]
            }
            this.data[A]=B;
            if(!this.editing&&this.store){
                this.store.afterEdit(this)
            }
        },
        get:function(A){
            return this.data[A]
        },
        beginEdit:function(){
            this.editing=true;
            this.modified={}
        },
        cancelEdit:function(){
            this.editing=false;
            delete this.modified
        },
        endEdit:function(){
            this.editing=false;
            if(this.dirty&&this.store){
                this.store.afterEdit(this)
            }
        },
        reject:function(B){
            var A=this.modified;
            for(var C in A){
                if(typeof A[C]!="function"){
                    this.data[C]=A[C]
                }
            }
            this.dirty=false;
            delete this.modified;
            this.editing=false;
            if(this.store&&B!==true){
                this.store.afterReject(this)
            }
        },
        commit:function(A){
            this.dirty=false;
            delete this.modified;
            this.editing=false;
            if(this.store&&A!==true){
                this.store.afterCommit(this)
            }
        },
        getChanges:function(){
            var A=this.modified,B={};
    
            for(var C in A){
                if(A.hasOwnProperty(C)){
                    B[C]=this.data[C]
                }
            }
            return B
        },
        hasError:function(){
            return this.error!=null
        },
        clearError:function(){
            this.error=null
        },
        copy:function(A){
            return new this.constructor(Ext.apply({},this.data),A||this.id)
        },
        isModified:function(A){
            return this.modified&&this.modified.hasOwnProperty(A)
        }
    };

    Ext.StoreMgr=Ext.apply(new Ext.util.MixedCollection(),{
        register:function(){
            for(var A=0,B;B=arguments[A];A++){
                this.add(B)
            }
        },
        unregister:function(){
            for(var A=0,B;B=arguments[A];A++){
                this.remove(this.lookup(B))
            }
        },
        lookup:function(A){
            return typeof A=="object"?A:this.get(A)
        },
        getKey:function(A){
            return A.storeId||A.id
        }
    });
    Ext.data.Store=function(A){
        this.data=new Ext.util.MixedCollection(false);
        this.data.getKey=function(B){
            return B.id
        };
        
        this.baseParams={};
    
        this.paramNames={
            "start":"start",
            "limit":"limit",
            "sort":"sort",
            "dir":"dir"
        };
    
        if(A&&A.data){
            this.inlineData=A.data;
            delete A.data
        }
        Ext.apply(this,A);
        if(this.url&&!this.proxy){
            this.proxy=new Ext.data.HttpProxy({
                url:this.url
            })
        }
        if(this.reader){
            if(!this.recordType){
                this.recordType=this.reader.recordType
            }
            if(this.reader.onMetaChange){
                this.reader.onMetaChange=this.onMetaChange.createDelegate(this)
            }
        }
        if(this.recordType){
            this.fields=this.recordType.prototype.fields
        }
        this.modified=[];
        this.addEvents("datachanged","metachange","add","remove","update","clear","beforeload","load","loadexception");
        if(this.proxy){
            this.relayEvents(this.proxy,["loadexception"])
        }
        this.sortToggle={};

        if(this.sortInfo){
            this.setDefaultSort(this.sortInfo.field,this.sortInfo.direction)
        }
        Ext.data.Store.superclass.constructor.call(this);
        if(this.storeId||this.id){
            Ext.StoreMgr.register(this)
        }
        if(this.inlineData){
            this.loadData(this.inlineData);
            delete this.inlineData
        }else{
            if(this.autoLoad){
                this.load.defer(10,this,[typeof this.autoLoad=="object"?this.autoLoad:undefined])
            }
        }
    };

    Ext.extend(Ext.data.Store,Ext.util.Observable,{
        remoteSort:false,
        pruneModifiedRecords:false,
        lastOptions:null,
        destroy:function(){
            if(this.id){
                Ext.StoreMgr.unregister(this)
            }
            this.data=null;
            this.purgeListeners()
        },
        add:function(B){
            B=[].concat(B);
            if(B.length<1){
                return
            }
            for(var D=0,A=B.length;D<A;D++){
                B[D].join(this)
            }
            var C=this.data.length;
            this.data.addAll(B);
            if(this.snapshot){
                this.snapshot.addAll(B)
            }
            this.fireEvent("add",this,B,C)
        },
        addSorted:function(A){
            var B=this.findInsertIndex(A);
            this.insert(B,A)
        },
        remove:function(A){
            var B=this.data.indexOf(A);
            this.data.removeAt(B);
            if(this.pruneModifiedRecords){
                this.modified.remove(A)
            }
            if(this.snapshot){
                this.snapshot.remove(A)
            }
            this.fireEvent("remove",this,A,B)
        },
        removeAll:function(){
            this.data.clear();
            if(this.snapshot){
                this.snapshot.clear()
            }
            if(this.pruneModifiedRecords){
                this.modified=[]
            }
            this.fireEvent("clear",this)
        },
        insert:function(C,B){
            B=[].concat(B);
            for(var D=0,A=B.length;D<A;D++){
                this.data.insert(C,B[D]);
                B[D].join(this)
            }
            this.fireEvent("add",this,B,C)
        },
        indexOf:function(A){
            return this.data.indexOf(A)
        },
        indexOfId:function(A){
            return this.data.indexOfKey(A)
        },
        getById:function(A){
            return this.data.key(A)
        },
        getAt:function(A){
            return this.data.itemAt(A)
        },
        getRange:function(B,A){
            return this.data.getRange(B,A)
        },
        storeOptions:function(A){
            A=Ext.apply({},A);
            delete A.callback;
            delete A.scope;
            this.lastOptions=A
        },
        load:function(B){
            B=B||{};
        
            if(this.fireEvent("beforeload",this,B)!==false){
                this.storeOptions(B);
                var C=Ext.apply(B.params||{},this.baseParams);
                if(this.sortInfo&&this.remoteSort){
                    var A=this.paramNames;
                    C[A["sort"]]=this.sortInfo.field;
                    C[A["dir"]]=this.sortInfo.direction
                }
                this.proxy.load(C,this.reader,this.loadRecords,this,B);
                return true
            }else{
                return false
            }
        },
        reload:function(A){
            this.load(Ext.applyIf(A||{},this.lastOptions))
        },
        loadRecords:function(G,B,F){
            if(!G||F===false){
                if(F!==false){
                    this.fireEvent("load",this,[],B)
                }
                if(B.callback){
                    B.callback.call(B.scope||this,[],B,false)
                }
                return
            }
            var E=G.records,D=G.totalRecords||E.length;
            if(!B||B.add!==true){
                if(this.pruneModifiedRecords){
                    this.modified=[]
                }
                for(var C=0,A=E.length;C<A;C++){
                    E[C].join(this)
                }
                if(this.snapshot){
                    this.data=this.snapshot;
                    delete this.snapshot
                }
                this.data.clear();
                this.data.addAll(E);
                this.totalLength=D;
                this.applySort();
                this.fireEvent("datachanged",this)
            }else{
                this.totalLength=Math.max(D,this.data.length+E.length);
                this.add(E)
            }
            this.fireEvent("load",this,E,B);
            if(B.callback){
                B.callback.call(B.scope||this,E,B,true)
            }
        },
        loadData:function(C,A){
            var B=this.reader.readRecords(C);
            this.loadRecords(B,{
                add:A
            },true)
        },
        getCount:function(){
            return this.data.length||0
        },
        getTotalCount:function(){
            return this.totalLength||0
        },
        getSortState:function(){
            return this.sortInfo
        },
        applySort:function(){
            if(this.sortInfo&&!this.remoteSort){
                var A=this.sortInfo,B=A.field;
                this.sortData(B,A.direction)
            }
        },
        sortData:function(C,D){
            D=D||"ASC";
            var A=this.fields.get(C).sortType;
            var B=function(F,E){
                var H=A(F.data[C]),G=A(E.data[C]);
                return H>G?1:(H<G?-1:0)
            };
        
            this.data.sort(D,B);
            if(this.snapshot&&this.snapshot!=this.data){
                this.snapshot.sort(D,B)
            }
        },
        setDefaultSort:function(B,A){
            A=A?A.toUpperCase():"ASC";
            this.sortInfo={
                field:B,
                direction:A
            };
    
            this.sortToggle[B]=A
        },
        sort:function(E,C){
            var D=this.fields.get(E);
            if(!D){
                return false
            }
            if(!C){
                if(this.sortInfo&&this.sortInfo.field==D.name){
                    C=(this.sortToggle[D.name]||"ASC").toggle("ASC","DESC")
                }else{
                    C=D.sortDir
                }
            }
            var B=(this.sortToggle)?this.sortToggle[D.name]:null;
            var A=(this.sortInfo)?this.sortInfo:null;
            this.sortToggle[D.name]=C;
            this.sortInfo={
                field:D.name,
                direction:C
            };

            if(!this.remoteSort){
                this.applySort();
                this.fireEvent("datachanged",this)
            }else{
                if(!this.load(this.lastOptions)){
                    if(B){
                        this.sortToggle[D.name]=B
                    }
                    if(A){
                        this.sortInfo=A
                    }
                }
            }
        },
        each:function(B,A){
            this.data.each(B,A)
        },
        getModifiedRecords:function(){
            return this.modified
        },
        createFilterFn:function(C,B,D,A){
            if(Ext.isEmpty(B,false)){
                return false
            }
            B=this.data.createValueMatcher(B,D,A);
            return function(E){
                return B.test(E.data[C])
            }
        },
        sum:function(E,F,A){
            var C=this.data.items,B=0;
            F=F||0;
            A=(A||A===0)?A:C.length-1;
            for(var D=F;D<=A;D++){
                B+=(C[D].data[E]||0)
            }
            return B
        },
        filter:function(D,C,E,A){
            var B=this.createFilterFn(D,C,E,A);
            return B?this.filterBy(B):this.clearFilter()
        },
        filterBy:function(B,A){
            this.snapshot=this.snapshot||this.data;
            this.data=this.queryBy(B,A||this);
            this.fireEvent("datachanged",this)
        },
        query:function(D,C,E,A){
            var B=this.createFilterFn(D,C,E,A);
            return B?this.queryBy(B):this.data.clone()
        },
        queryBy:function(B,A){
            var C=this.snapshot||this.data;
            return C.filterBy(B,A||this)
        },
        find:function(D,C,F,E,A){
            var B=this.createFilterFn(D,C,E,A);
            return B?this.data.findIndexBy(B,null,F):-1
        },
        findBy:function(B,A,C){
            return this.data.findIndexBy(B,A,C)
        },
        collect:function(G,H,B){
            var F=(B===true&&this.snapshot)?this.snapshot.items:this.data.items;
            var I,J,A=[],C={};
    
            for(var D=0,E=F.length;D<E;D++){
                I=F[D].data[G];
                J=String(I);
                if((H||!Ext.isEmpty(I))&&!C[J]){
                    C[J]=true;
                    A[A.length]=I
                }
            }
            return A
        },
        clearFilter:function(A){
            if(this.isFiltered()){
                this.data=this.snapshot;
                delete this.snapshot;
                if(A!==true){
                    this.fireEvent("datachanged",this)
                }
            }
        },
        isFiltered:function(){
            return this.snapshot&&this.snapshot!=this.data
        },
        afterEdit:function(A){
            if(this.modified.indexOf(A)==-1){
                this.modified.push(A)
            }
            this.fireEvent("update",this,A,Ext.data.Record.EDIT)
        },
        afterReject:function(A){
            this.modified.remove(A);
            this.fireEvent("update",this,A,Ext.data.Record.REJECT)
        },
        afterCommit:function(A){
            this.modified.remove(A);
            this.fireEvent("update",this,A,Ext.data.Record.COMMIT)
        },
        commitChanges:function(){
            var B=this.modified.slice(0);
            this.modified=[];
            for(var C=0,A=B.length;C<A;C++){
                B[C].commit()
            }
        },
        rejectChanges:function(){
            var B=this.modified.slice(0);
            this.modified=[];
            for(var C=0,A=B.length;C<A;C++){
                B[C].reject()
            }
        },
        onMetaChange:function(B,A,C){
            this.recordType=A;
            this.fields=A.prototype.fields;
            delete this.snapshot;
            this.sortInfo=B.sortInfo;
            this.modified=[];
            this.fireEvent("metachange",this,this.reader.meta)
        },
        findInsertIndex:function(A){
            this.suspendEvents();
            var C=this.data.clone();
            this.data.add(A);
            this.applySort();
            var B=this.data.indexOf(A);
            this.data=C;
            this.resumeEvents();
            return B
        }
    });
    Ext.data.Field=function(D){
        if(typeof D=="string"){
            D={
                name:D
            }
        }
        Ext.apply(this,D);
        if(!this.type){
            this.type="auto"
        }
        var C=Ext.data.SortTypes;
        if(typeof this.sortType=="string"){
            this.sortType=C[this.sortType]
        }
        if(!this.sortType){
            switch(this.type){
                case"string":
                    this.sortType=C.asUCString;
                    break;
                case"date":
                    this.sortType=C.asDate;
                    break;
                default:
                    this.sortType=C.none
            }
        }
        var E=/[\$,%]/g;
        if(!this.convert){
            var B,A=this.dateFormat;
            switch(this.type){
                case"":case"auto":case undefined:
                    B=function(F){
                        return F
                    };
            
                    break;
                case"string":
                    B=function(F){
                        return(F===undefined||F===null)?"":String(F)
                    };
            
                    break;
                case"int":
                    B=function(F){
                        return F!==undefined&&F!==null&&F!==""?parseInt(String(F).replace(E,""),10):""
                    };
            
                    break;
                case"float":
                    B=function(F){
                        return F!==undefined&&F!==null&&F!==""?parseFloat(String(F).replace(E,""),10):""
                    };
            
                    break;
                case"bool":case"boolean":
                    B=function(F){
                        return F===true||F==="true"||F==1
                    };
            
                    break;
                case"date":
                    B=function(G){
                        if(!G){
                            return""
                        }
                        if(Ext.isDate(G)){
                            return G
                        }
                        if(A){
                            if(A=="timestamp"){
                                return new Date(G*1000)
                            }
                            if(A=="time"){
                                return new Date(parseInt(G,10))
                            }
                            return Date.parseDate(G,A)
                        }
                        var F=Date.parse(G);
                        return F?new Date(F):null
                    };
            
                    break
            }
            this.convert=B
        }
    };

    Ext.data.Field.prototype={
        dateFormat:null,
        defaultValue:"",
        mapping:null,
        sortType:null,
        sortDir:"ASC"
    };

    Ext.data.DataReader=function(A,B){
        this.meta=A;
        this.recordType=Ext.isArray(B)?Ext.data.Record.create(B):B
    };
    
    Ext.data.DataReader.prototype={};
    
    Ext.data.DataProxy=function(){
        this.addEvents("beforeload","load","loadexception");
        Ext.data.DataProxy.superclass.constructor.call(this)
    };
    
    Ext.extend(Ext.data.DataProxy,Ext.util.Observable);
    Ext.data.MemoryProxy=function(A){
        Ext.data.MemoryProxy.superclass.constructor.call(this);
        this.data=A
    };
    
    Ext.extend(Ext.data.MemoryProxy,Ext.data.DataProxy,{
        load:function(F,C,G,D,B){
            F=F||{};
        
            var A;
            try{
                A=C.readRecords(this.data)
            }catch(E){
                this.fireEvent("loadexception",this,B,null,E);
                G.call(D,null,B,false);
                return
            }
            G.call(D,A,B,true)
        },
        update:function(B,A){}
    });
    Ext.data.HttpProxy=function(A){
        Ext.data.HttpProxy.superclass.constructor.call(this);
        this.conn=A;
        this.useAjax=!A||!A.events
    };
    
    Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{
        getConnection:function(){
            return this.useAjax?Ext.Ajax:this.conn
        },
        load:function(E,B,F,C,A){
            if(this.fireEvent("beforeload",this,E)!==false){
                var D={
                    params:E||{},
                    request:{
                        callback:F,
                        scope:C,
                        arg:A
                    },
                    reader:B,
                    callback:this.loadResponse,
                    scope:this
                };
            
                if(this.useAjax){
                    Ext.applyIf(D,this.conn);
                    if(this.activeRequest){
                        Ext.Ajax.abort(this.activeRequest)
                    }
                    this.activeRequest=Ext.Ajax.request(D)
                }else{
                    this.conn.request(D)
                }
            }else{
                F.call(C||this,null,A,false)
            }
        },
        loadResponse:function(E,D,B){
            delete this.activeRequest;
            if(!D){
                this.fireEvent("loadexception",this,E,B);
                E.request.callback.call(E.request.scope,null,E.request.arg,false);
                return
            }
            var A;
            try{
                A=E.reader.read(B)
            }catch(C){
                this.fireEvent("loadexception",this,E,B,C);
                E.request.callback.call(E.request.scope,null,E.request.arg,false);
                return
            }
            this.fireEvent("load",this,E,E.request.arg);
            E.request.callback.call(E.request.scope,A,E.request.arg,true)
        },
        update:function(A){},
        updateResponse:function(A){}
    });
    Ext.data.ScriptTagProxy=function(A){
        Ext.data.ScriptTagProxy.superclass.constructor.call(this);
        Ext.apply(this,A);
        this.head=document.getElementsByTagName("head")[0]
    };
    
    Ext.data.ScriptTagProxy.TRANS_ID=1000;
    Ext.extend(Ext.data.ScriptTagProxy,Ext.data.DataProxy,{
        timeout:30000,
        callbackParam:"callback",
        nocache:true,
        load:function(E,F,H,I,J){
            if(this.fireEvent("beforeload",this,E)!==false){
                var C=Ext.urlEncode(Ext.apply(E,this.extraParams));
                var B=this.url;
                B+=(B.indexOf("?")!=-1?"&":"?")+C;
                if(this.nocache){
                    B+="&_dc="+(new Date().getTime())
                }
                var A=++Ext.data.ScriptTagProxy.TRANS_ID;
                var K={
                    id:A,
                    cb:"stcCallback"+A,
                    scriptId:"stcScript"+A,
                    params:E,
                    arg:J,
                    url:B,
                    callback:H,
                    scope:I,
                    reader:F
                };
            
                var D=this;
                window[K.cb]=function(L){
                    D.handleResponse(L,K)
                };
                
                B+=String.format("&{0}={1}",this.callbackParam,K.cb);
                if(this.autoAbort!==false){
                    this.abort()
                }
                K.timeoutId=this.handleFailure.defer(this.timeout,this,[K]);
                var G=document.createElement("script");
                G.setAttribute("src",B);
                G.setAttribute("type","text/javascript");
                G.setAttribute("id",K.scriptId);
                this.head.appendChild(G);
                this.trans=K
            }else{
                H.call(I||this,null,J,false)
            }
        },
        isLoading:function(){
            return this.trans?true:false
        },
        abort:function(){
            if(this.isLoading()){
                this.destroyTrans(this.trans)
            }
        },
        destroyTrans:function(B,A){
            this.head.removeChild(document.getElementById(B.scriptId));
            clearTimeout(B.timeoutId);
            if(A){
                window[B.cb]=undefined;
                try{
                    delete window[B.cb]
                }catch(C){}
            }else{
                window[B.cb]=function(){
                    window[B.cb]=undefined;
                    try{
                        delete window[B.cb]
                    }catch(D){}
                }
            }
        },
        handleResponse:function(D,B){
            this.trans=false;
            this.destroyTrans(B,true);
            var A;
            try{
                A=B.reader.readRecords(D)
            }catch(C){
                this.fireEvent("loadexception",this,D,B.arg,C);
                B.callback.call(B.scope||window,null,B.arg,false);
                return
            }
            this.fireEvent("load",this,D,B.arg);
            B.callback.call(B.scope||window,A,B.arg,true)
        },
        handleFailure:function(A){
            this.trans=false;
            this.destroyTrans(A,false);
            this.fireEvent("loadexception",this,null,A.arg);
            A.callback.call(A.scope||window,null,A.arg,false)
        }
    });
    Ext.data.JsonReader=function(A,B){
        A=A||{};
    
        Ext.data.JsonReader.superclass.constructor.call(this,A,B||A.fields)
    };
    
    Ext.extend(Ext.data.JsonReader,Ext.data.DataReader,{
        read:function(response){
            var json=response.responseText;
            var o=eval("("+json+")");
            if(!o){
                throw{
                    message:"JsonReader.read: Json object not found"
                }
            }
            if(o.metaData){
                delete this.ef;
                this.meta=o.metaData;
                this.recordType=Ext.data.Record.create(o.metaData.fields);
                this.onMetaChange(this.meta,this.recordType,o)
            }
            return this.readRecords(o)
        },
        onMetaChange:function(A,C,B){},
        simpleAccess:function(B,A){
            return B[A]
        },
        getJsonAccessor:function(){
            var A=/[\[\.]/;
            return function(C){
                try{
                    return(A.test(C))?new Function("obj","return obj."+C):function(D){
                        return D[C]
                    }
                }catch(B){}
                return Ext.emptyFn
            }
        }(),
        readRecords:function(K){
            this.jsonData=K;
            var H=this.meta,A=this.recordType,R=A.prototype.fields,F=R.items,E=R.length;
            if(!this.ef){
                if(H.totalProperty){
                    this.getTotal=this.getJsonAccessor(H.totalProperty)
                }
                if(H.successProperty){
                    this.getSuccess=this.getJsonAccessor(H.successProperty)
                }
                this.getRoot=H.root?this.getJsonAccessor(H.root):function(U){
                    return U
                };
            
                if(H.id){
                    var Q=this.getJsonAccessor(H.id);
                    this.getId=function(V){
                        var U=Q(V);
                        return(U===undefined||U==="")?null:U
                    }
                }else{
                    this.getId=function(){
                        return null
                    }
                }
                this.ef=[];
                for(var O=0;O<E;O++){
                    R=F[O];
                    var T=(R.mapping!==undefined&&R.mapping!==null)?R.mapping:R.name;
                    this.ef[O]=this.getJsonAccessor(T)
                }
            }
            var M=this.getRoot(K),S=M.length,I=S,D=true;
            if(H.totalProperty){
                var G=parseInt(this.getTotal(K),10);
                if(!isNaN(G)){
                    I=G
                }
            }
            if(H.successProperty){
                var G=this.getSuccess(K);
                if(G===false||G==="false"){
                    D=false
                }
            }
            var P=[];
            for(var O=0;O<S;O++){
                var L=M[O];
                var B={};
    
                var J=this.getId(L);
                for(var N=0;N<E;N++){
                    R=F[N];
                    var G=this.ef[N](L);
                    B[R.name]=R.convert((G!==undefined)?G:R.defaultValue,L)
                }
                var C=new A(B,J);
                C.json=L;
                P[O]=C
            }
            return{
                success:D,
                records:P,
                totalRecords:I
            }
        }
    });
    Ext.ComponentMgr=function(){
        var B=new Ext.util.MixedCollection();
        var A={};
    
        return{
            register:function(C){
                B.add(C)
            },
            unregister:function(C){
                B.remove(C)
            },
            get:function(C){
                return B.get(C)
            },
            onAvailable:function(E,D,C){
                B.on("add",function(F,G){
                    if(G.id==E){
                        D.call(C||G,G);
                        B.un("add",D,C)
                    }
                })
            },
            all:B,
            registerType:function(D,C){
                A[D]=C;
                C.xtype=D
            },
            create:function(C,D){
                return new A[C.xtype||D](C)
            }
        }
    }();
    Ext.reg=Ext.ComponentMgr.registerType;
    Ext.Component=function(B){
        B=B||{};
    
        if(B.initialConfig){
            if(B.isAction){
                this.baseAction=B
            }
            B=B.initialConfig
        }else{
            if(B.tagName||B.dom||typeof B=="string"){
                B={
                    applyTo:B,
                    id:B.id||B
                }
            }
        }
        this.initialConfig=B;
        Ext.apply(this,B);
        this.addEvents("disable","enable","beforeshow","show","beforehide","hide","beforerender","render","beforedestroy","destroy","beforestaterestore","staterestore","beforestatesave","statesave");
        this.getId();
        Ext.ComponentMgr.register(this);
        Ext.Component.superclass.constructor.call(this);
        if(this.baseAction){
            this.baseAction.addComponent(this)
        }
        this.initComponent();
        if(this.plugins){
            if(Ext.isArray(this.plugins)){
                for(var C=0,A=this.plugins.length;C<A;C++){
                    this.plugins[C].init(this)
                }
            }else{
                this.plugins.init(this)
            }
        }
        if(this.stateful!==false){
            this.initState(B)
        }
        if(this.applyTo){
            this.applyToMarkup(this.applyTo);
            delete this.applyTo
        }else{
            if(this.renderTo){
                this.render(this.renderTo);
                delete this.renderTo
            }
        }
    };

    Ext.Component.AUTO_ID=1000;
    Ext.extend(Ext.Component,Ext.util.Observable,{
        disabledClass:"x-item-disabled",
        allowDomMove:true,
        autoShow:false,
        hideMode:"display",
        hideParent:false,
        hidden:false,
        disabled:false,
        rendered:false,
        ctype:"Ext.Component",
        actionMode:"el",
        getActionEl:function(){
            return this[this.actionMode]
        },
        initComponent:Ext.emptyFn,
        render:function(B,A){
            if(!this.rendered&&this.fireEvent("beforerender",this)!==false){
                if(!B&&this.el){
                    this.el=Ext.get(this.el);
                    B=this.el.dom.parentNode;
                    this.allowDomMove=false
                }
                this.container=Ext.get(B);
                if(this.ctCls){
                    this.container.addClass(this.ctCls)
                }
                this.rendered=true;
                if(A!==undefined){
                    if(typeof A=="number"){
                        A=this.container.dom.childNodes[A]
                    }else{
                        A=Ext.getDom(A)
                    }
                }
                this.onRender(this.container,A||null);
                if(this.autoShow){
                    this.el.removeClass(["x-hidden","x-hide-"+this.hideMode])
                }
                if(this.cls){
                    this.el.addClass(this.cls);
                    delete this.cls
                }
                if(this.style){
                    this.el.applyStyles(this.style);
                    delete this.style
                }
                this.fireEvent("render",this);
                this.afterRender(this.container);
                if(this.hidden){
                    this.hide()
                }
                if(this.disabled){
                    this.disable()
                }
                this.initStateEvents()
            }
            return this
        },
        initState:function(A){
            if(Ext.state.Manager){
                var B=Ext.state.Manager.get(this.stateId||this.id);
                if(B){
                    if(this.fireEvent("beforestaterestore",this,B)!==false){
                        this.applyState(B);
                        this.fireEvent("staterestore",this,B)
                    }
                }
            }
        },
        initStateEvents:function(){
            if(this.stateEvents){
                for(var A=0,B;B=this.stateEvents[A];A++){
                    this.on(B,this.saveState,this,{
                        delay:100
                    })
                }
            }
        },
        applyState:function(B,A){
            if(B){
                Ext.apply(this,B)
            }
        },
        getState:function(){
            return null
        },
        saveState:function(){
            if(Ext.state.Manager){
                var A=this.getState();
                if(this.fireEvent("beforestatesave",this,A)!==false){
                    Ext.state.Manager.set(this.stateId||this.id,A);
                    this.fireEvent("statesave",this,A)
                }
            }
        },
        applyToMarkup:function(A){
            this.allowDomMove=false;
            this.el=Ext.get(A);
            this.render(this.el.dom.parentNode)
        },
        addClass:function(A){
            if(this.el){
                this.el.addClass(A)
            }else{
                this.cls=this.cls?this.cls+" "+A:A
            }
        },
        removeClass:function(A){
            if(this.el){
                this.el.removeClass(A)
            }else{
                if(this.cls){
                    this.cls=this.cls.split(" ").remove(A).join(" ")
                }
            }
        },
        onRender:function(B,A){
            if(this.autoEl){
                if(typeof this.autoEl=="string"){
                    this.el=document.createElement(this.autoEl)
                }else{
                    var C=document.createElement("div");
                    Ext.DomHelper.overwrite(C,this.autoEl);
                    this.el=C.firstChild
                }
                if(!this.el.id){
                    this.el.id=this.getId()
                }
            }
            if(this.el){
                this.el=Ext.get(this.el);
                if(this.allowDomMove!==false){
                    B.dom.insertBefore(this.el.dom,A)
                }
            }
        },
        getAutoCreate:function(){
            var A=typeof this.autoCreate=="object"?this.autoCreate:Ext.apply({},this.defaultAutoCreate);
            if(this.id&&!A.id){
                A.id=this.id
            }
            return A
        },
        afterRender:Ext.emptyFn,
        destroy:function(){
            if(this.fireEvent("beforedestroy",this)!==false){
                this.beforeDestroy();
                if(this.rendered){
                    this.el.removeAllListeners();
                    this.el.remove();
                    if(this.actionMode=="container"){
                        this.container.remove()
                    }
                }
                this.onDestroy();
                Ext.ComponentMgr.unregister(this);
                this.fireEvent("destroy",this);
                this.purgeListeners()
            }
        },
        beforeDestroy:Ext.emptyFn,
        onDestroy:Ext.emptyFn,
        getEl:function(){
            return this.el
        },
        getId:function(){
            return this.id||(this.id="ext-comp-"+(++Ext.Component.AUTO_ID))
        },
        getItemId:function(){
            return this.itemId||this.getId()
        },
        focus:function(B,A){
            if(A){
                this.focus.defer(typeof A=="number"?A:10,this,[B,false]);
                return
            }
            if(this.rendered){
                this.el.focus();
                if(B===true){
                    this.el.dom.select()
                }
            }
            return this
        },
        blur:function(){
            if(this.rendered){
                this.el.blur()
            }
            return this
        },
        disable:function(){
            if(this.rendered){
                this.onDisable()
            }
            this.disabled=true;
            this.fireEvent("disable",this);
            return this
        },
        onDisable:function(){
            this.getActionEl().addClass(this.disabledClass);
            this.el.dom.disabled=true
        },
        enable:function(){
            if(this.rendered){
                this.onEnable()
            }
            this.disabled=false;
            this.fireEvent("enable",this);
            return this
        },
        onEnable:function(){
            this.getActionEl().removeClass(this.disabledClass);
            this.el.dom.disabled=false
        },
        setDisabled:function(A){
            this[A?"disable":"enable"]()
        },
        show:function(){
            if(this.fireEvent("beforeshow",this)!==false){
                this.hidden=false;
                if(this.autoRender){
                    this.render(typeof this.autoRender=="boolean"?Ext.getBody():this.autoRender)
                }
                if(this.rendered){
                    this.onShow()
                }
                this.fireEvent("show",this)
            }
            return this
        },
        onShow:function(){
            if(this.hideParent){
                this.container.removeClass("x-hide-"+this.hideMode)
            }else{
                this.getActionEl().removeClass("x-hide-"+this.hideMode)
            }
        },
        hide:function(){
            if(this.fireEvent("beforehide",this)!==false){
                this.hidden=true;
                if(this.rendered){
                    this.onHide()
                }
                this.fireEvent("hide",this)
            }
            return this
        },
        onHide:function(){
            if(this.hideParent){
                this.container.addClass("x-hide-"+this.hideMode)
            }else{
                this.getActionEl().addClass("x-hide-"+this.hideMode)
            }
        },
        setVisible:function(A){
            if(A){
                this.show()
            }else{
                this.hide()
            }
            return this
        },
        isVisible:function(){
            return this.rendered&&this.getActionEl().isVisible()
        },
        cloneConfig:function(B){
            B=B||{};
    
            var C=B.id||Ext.id();
            var A=Ext.applyIf(B,this.initialConfig);
            A.id=C;
            return new this.constructor(A)
        },
        getXType:function(){
            return this.constructor.xtype
        },
        isXType:function(B,A){
            return!A?("/"+this.getXTypes()+"/").indexOf("/"+B+"/")!=-1:this.constructor.xtype==B
        },
        getXTypes:function(){
            var A=this.constructor;
            if(!A.xtypes){
                var C=[],B=this;
                while(B&&B.constructor.xtype){
                    C.unshift(B.constructor.xtype);
                    B=B.constructor.superclass
                }
                A.xtypeChain=C;
                A.xtypes=C.join("/")
            }
            return A.xtypes
        },
        findParentBy:function(A){
            for(var B=this.ownerCt;(B!=null)&&!A(B,this);B=B.ownerCt){}
            return B||null
        },
        findParentByType:function(A){
            return typeof A=="function"?this.findParentBy(function(B){
                return B.constructor===A
            }):this.findParentBy(function(B){
                return B.constructor.xtype===A
            })
        }
    });
    Ext.reg("component",Ext.Component);
    (function(){
        Ext.Layer=function(D,C){
            D=D||{};
        
            var E=Ext.DomHelper;
            var G=D.parentEl,F=G?Ext.getDom(G):document.body;
            if(C){
                this.dom=Ext.getDom(C)
            }
            if(!this.dom){
                var H=D.dh||{
                    tag:"div",
                    cls:"x-layer"
                };
            
                this.dom=E.append(F,H)
            }
            if(D.cls){
                this.addClass(D.cls)
            }
            this.constrain=D.constrain!==false;
            this.visibilityMode=Ext.Element.VISIBILITY;
            if(D.id){
                this.id=this.dom.id=D.id
            }else{
                this.id=Ext.id(this.dom)
            }
            this.zindex=D.zindex||this.getZIndex();
            this.position("absolute",this.zindex);
            if(D.shadow){
                this.shadowOffset=D.shadowOffset||4;
                this.shadow=new Ext.Shadow({
                    offset:this.shadowOffset,
                    mode:D.shadow
                })
            }else{
                this.shadowOffset=0
            }
            this.useShim=D.shim!==false&&Ext.useShims;
            this.useDisplay=D.useDisplay;
            this.hide()
        };
        
        var A=Ext.Element.prototype;
        var B=[];
        Ext.extend(Ext.Layer,Ext.Element,{
            getZIndex:function(){
                return this.zindex||parseInt(this.getStyle("z-index"),10)||11000
            },
            getShim:function(){
                if(!this.useShim){
                    return null
                }
                if(this.shim){
                    return this.shim
                }
                var D=B.shift();
                if(!D){
                    D=this.createShim();
                    D.enableDisplayMode("block");
                    D.dom.style.display="none";
                    D.dom.style.visibility="visible"
                }
                var C=this.dom.parentNode;
                if(D.dom.parentNode!=C){
                    C.insertBefore(D.dom,this.dom)
                }
                D.setStyle("z-index",this.getZIndex()-2);
                this.shim=D;
                return D
            },
            hideShim:function(){
                if(this.shim){
                    this.shim.setDisplayed(false);
                    B.push(this.shim);
                    delete this.shim
                }
            },
            disableShadow:function(){
                if(this.shadow){
                    this.shadowDisabled=true;
                    this.shadow.hide();
                    this.lastShadowOffset=this.shadowOffset;
                    this.shadowOffset=0
                }
            },
            enableShadow:function(C){
                if(this.shadow){
                    this.shadowDisabled=false;
                    this.shadowOffset=this.lastShadowOffset;
                    delete this.lastShadowOffset;
                    if(C){
                        this.sync(true)
                    }
                }
            },
            sync:function(C){
                var I=this.shadow;
                if(!this.updating&&this.isVisible()&&(I||this.useShim)){
                    var F=this.getShim();
                    var H=this.getWidth(),E=this.getHeight();
                    var D=this.getLeft(true),J=this.getTop(true);
                    if(I&&!this.shadowDisabled){
                        if(C&&!I.isVisible()){
                            I.show(this)
                        }else{
                            I.realign(D,J,H,E)
                        }
                        if(F){
                            if(C){
                                F.show()
                            }
                            var G=I.adjusts,K=F.dom.style;
                            K.left=(Math.min(D,D+G.l))+"px";
                            K.top=(Math.min(J,J+G.t))+"px";
                            K.width=(H+G.w)+"px";
                            K.height=(E+G.h)+"px"
                        }
                    }else{
                        if(F){
                            if(C){
                                F.show()
                            }
                            F.setSize(H,E);
                            F.setLeftTop(D,J)
                        }
                    }
                }
            },
            destroy:function(){
                this.hideShim();
                if(this.shadow){
                    this.shadow.hide()
                }
                this.removeAllListeners();
                Ext.removeNode(this.dom);
                Ext.Element.uncache(this.id)
            },
            remove:function(){
                this.destroy()
            },
            beginUpdate:function(){
                this.updating=true
            },
            endUpdate:function(){
                this.updating=false;
                this.sync(true)
            },
            hideUnders:function(C){
                if(this.shadow){
                    this.shadow.hide()
                }
                this.hideShim()
            },
            constrainXY:function(){
                if(this.constrain){
                    var G=Ext.lib.Dom.getViewWidth(),C=Ext.lib.Dom.getViewHeight();
                    var L=Ext.getDoc().getScroll();
                    var K=this.getXY();
                    var H=K[0],F=K[1];
                    var I=this.dom.offsetWidth+this.shadowOffset,D=this.dom.offsetHeight+this.shadowOffset;
                    var E=false;
                    if((H+I)>G+L.left){
                        H=G-I-this.shadowOffset;
                        E=true
                    }
                    if((F+D)>C+L.top){
                        F=C-D-this.shadowOffset;
                        E=true
                    }
                    if(H<L.left){
                        H=L.left;
                        E=true
                    }
                    if(F<L.top){
                        F=L.top;
                        E=true
                    }
                    if(E){
                        if(this.avoidY){
                            var J=this.avoidY;
                            if(F<=J&&(F+D)>=J){
                                F=J-D-5
                            }
                        }
                        K=[H,F];
                        this.storeXY(K);
                        A.setXY.call(this,K);
                        this.sync()
                    }
                }
            },
            isVisible:function(){
                return this.visible
            },
            showAction:function(){
                this.visible=true;
                if(this.useDisplay===true){
                    this.setDisplayed("")
                }else{
                    if(this.lastXY){
                        A.setXY.call(this,this.lastXY)
                    }else{
                        if(this.lastLT){
                            A.setLeftTop.call(this,this.lastLT[0],this.lastLT[1])
                        }
                    }
                }
            },
            hideAction:function(){
                this.visible=false;
                if(this.useDisplay===true){
                    this.setDisplayed(false)
                }else{
                    this.setLeftTop(-10000,-10000)
                }
            },
            setVisible:function(E,D,G,H,F){
                if(E){
                    this.showAction()
                }
                if(D&&E){
                    var C=function(){
                        this.sync(true);
                        if(H){
                            H()
                        }
                    }.createDelegate(this);
                    A.setVisible.call(this,true,true,G,C,F)
                }else{
                    if(!E){
                        this.hideUnders(true)
                    }
                    var C=H;
                    if(D){
                        C=function(){
                            this.hideAction();
                            if(H){
                                H()
                            }
                        }.createDelegate(this)
                    }
                    A.setVisible.call(this,E,D,G,C,F);
                    if(E){
                        this.sync(true)
                    }else{
                        if(!D){
                            this.hideAction()
                        }
                    }
                }
            },
            storeXY:function(C){
                delete this.lastLT;
                this.lastXY=C
            },
            storeLeftTop:function(D,C){
                delete this.lastXY;
                this.lastLT=[D,C]
            },
            beforeFx:function(){
                this.beforeAction();
                return Ext.Layer.superclass.beforeFx.apply(this,arguments)
            },
            afterFx:function(){
                Ext.Layer.superclass.afterFx.apply(this,arguments);
                this.sync(this.isVisible())
            },
            beforeAction:function(){
                if(!this.updating&&this.shadow){
                    this.shadow.hide()
                }
            },
            setLeft:function(C){
                this.storeLeftTop(C,this.getTop(true));
                A.setLeft.apply(this,arguments);
                this.sync()
            },
            setTop:function(C){
                this.storeLeftTop(this.getLeft(true),C);
                A.setTop.apply(this,arguments);
                this.sync()
            },
            setLeftTop:function(D,C){
                this.storeLeftTop(D,C);
                A.setLeftTop.apply(this,arguments);
                this.sync()
            },
            setXY:function(F,D,G,H,E){
                this.fixDisplay();
                this.beforeAction();
                this.storeXY(F);
                var C=this.createCB(H);
                A.setXY.call(this,F,D,G,C,E);
                if(!D){
                    C()
                }
            },
            createCB:function(D){
                var C=this;
                return function(){
                    C.constrainXY();
                    C.sync(true);
                    if(D){
                        D()
                    }
                }
            },
            setX:function(C,D,F,G,E){
                this.setXY([C,this.getY()],D,F,G,E)
            },
            setY:function(G,C,E,F,D){
                this.setXY([this.getX(),G],C,E,F,D)
            },
            setSize:function(E,F,D,H,I,G){
                this.beforeAction();
                var C=this.createCB(I);
                A.setSize.call(this,E,F,D,H,C,G);
                if(!D){
                    C()
                }
            },
            setWidth:function(E,D,G,H,F){
                this.beforeAction();
                var C=this.createCB(H);
                A.setWidth.call(this,E,D,G,C,F);
                if(!D){
                    C()
                }
            },
            setHeight:function(E,D,G,H,F){
                this.beforeAction();
                var C=this.createCB(H);
                A.setHeight.call(this,E,D,G,C,F);
                if(!D){
                    C()
                }
            },
            setBounds:function(J,H,K,D,I,F,G,E){
                this.beforeAction();
                var C=this.createCB(G);
                if(!I){
                    this.storeXY([J,H]);
                    A.setXY.call(this,[J,H]);
                    A.setSize.call(this,K,D,I,F,C,E);
                    C()
                }else{
                    A.setBounds.call(this,J,H,K,D,I,F,C,E)
                }
                return this
            },
            setZIndex:function(C){
                this.zindex=C;
                this.setStyle("z-index",C+2);
                if(this.shadow){
                    this.shadow.setZIndex(C+1)
                }
                if(this.shim){
                    this.shim.setStyle("z-index",C)
                }
            }
        })
    })();
    Ext.Shadow=function(C){
        Ext.apply(this,C);
        if(typeof this.mode!="string"){
            this.mode=this.defaultMode
        }
        var D=this.offset,B={
            h:0
        };
    
        var A=Math.floor(this.offset/2);
        switch(this.mode.toLowerCase()){
            case"drop":
                B.w=0;
                B.l=B.t=D;
                B.t-=1;
                if(Ext.isIE){
                    B.l-=this.offset+A;
                    B.t-=this.offset+A;
                    B.w-=A;
                    B.h-=A;
                    B.t+=1
                }
                break;
            case"sides":
                B.w=(D*2);
                B.l=-D;
                B.t=D-1;
                if(Ext.isIE){
                    B.l-=(this.offset-A);
                    B.t-=this.offset+A;
                    B.l+=1;
                    B.w-=(this.offset-A)*2;
                    B.w-=A+1;
                    B.h-=1
                }
                break;
            case"frame":
                B.w=B.h=(D*2);
                B.l=B.t=-D;
                B.t+=1;
                B.h-=2;
                if(Ext.isIE){
                    B.l-=(this.offset-A);
                    B.t-=(this.offset-A);
                    B.l+=1;
                    B.w-=(this.offset+A+1);
                    B.h-=(this.offset+A);
                    B.h+=1
                }
                break
        }
        this.adjusts=B
    };
    
    Ext.Shadow.prototype={
        offset:4,
        defaultMode:"drop",
        show:function(A){
            A=Ext.get(A);
            if(!this.el){
                this.el=Ext.Shadow.Pool.pull();
                if(this.el.dom.nextSibling!=A.dom){
                    this.el.insertBefore(A)
                }
            }
            this.el.setStyle("z-index",this.zIndex||parseInt(A.getStyle("z-index"),10)-1);
            if(Ext.isIE){
                this.el.dom.style.filter="progid:DXImageTransform.Microsoft.alpha(opacity=50) progid:DXImageTransform.Microsoft.Blur(pixelradius="+(this.offset)+")"
            }
            this.realign(A.getLeft(true),A.getTop(true),A.getWidth(),A.getHeight());
            this.el.dom.style.display="block"
        },
        isVisible:function(){
            return this.el?true:false
        },
        realign:function(A,M,L,D){
            if(!this.el){
                return
            }
            var I=this.adjusts,G=this.el.dom,N=G.style;
            var E=0;
            N.left=(A+I.l)+"px";
            N.top=(M+I.t)+"px";
            var K=(L+I.w),C=(D+I.h),F=K+"px",J=C+"px";
            if(N.width!=F||N.height!=J){
                N.width=F;
                N.height=J;
                if(!Ext.isIE){
                    var H=G.childNodes;
                    var B=Math.max(0,(K-12))+"px";
                    H[0].childNodes[1].style.width=B;
                    H[1].childNodes[1].style.width=B;
                    H[2].childNodes[1].style.width=B;
                    H[1].style.height=Math.max(0,(C-12))+"px"
                }
            }
        },
        hide:function(){
            if(this.el){
                this.el.dom.style.display="none";
                Ext.Shadow.Pool.push(this.el);
                delete this.el
            }
        },
        setZIndex:function(A){
            this.zIndex=A;
            if(this.el){
                this.el.setStyle("z-index",A)
            }
        }
    };

    Ext.Shadow.Pool=function(){
        var B=[];
        var A=Ext.isIE?"<div class=\"x-ie-shadow\"></div>":"<div class=\"x-shadow\"><div class=\"xst\"><div class=\"xstl\"></div><div class=\"xstc\"></div><div class=\"xstr\"></div></div><div class=\"xsc\"><div class=\"xsml\"></div><div class=\"xsmc\"></div><div class=\"xsmr\"></div></div><div class=\"xsb\"><div class=\"xsbl\"></div><div class=\"xsbc\"></div><div class=\"xsbr\"></div></div></div>";
        return{
            pull:function(){
                var C=B.shift();
                if(!C){
                    C=Ext.get(Ext.DomHelper.insertHtml("beforeBegin",document.body.firstChild,A));
                    C.autoBoxAdjust=false
                }
                return C
            },
            push:function(C){
                B.push(C)
            }
        }
    }();
    Ext.BoxComponent=Ext.extend(Ext.Component,{
        initComponent:function(){
            Ext.BoxComponent.superclass.initComponent.call(this);
            this.addEvents("resize","move")
        },
        boxReady:false,
        deferHeight:false,
        setSize:function(B,D){
            if(typeof B=="object"){
                D=B.height;
                B=B.width
            }
            if(!this.boxReady){
                this.width=B;
                this.height=D;
                return this
            }
            if(this.lastSize&&this.lastSize.width==B&&this.lastSize.height==D){
                return this
            }
            this.lastSize={
                width:B,
                height:D
            };
        
            var C=this.adjustSize(B,D);
            var F=C.width,A=C.height;
            if(F!==undefined||A!==undefined){
                var E=this.getResizeEl();
                if(!this.deferHeight&&F!==undefined&&A!==undefined){
                    E.setSize(F,A)
                }else{
                    if(!this.deferHeight&&A!==undefined){
                        E.setHeight(A)
                    }else{
                        if(F!==undefined){
                            E.setWidth(F)
                        }
                    }
                }
                this.onResize(F,A,B,D);
                this.fireEvent("resize",this,F,A,B,D)
            }
            return this
        },
        setWidth:function(A){
            return this.setSize(A)
        },
        setHeight:function(A){
            return this.setSize(undefined,A)
        },
        getSize:function(){
            return this.el.getSize()
        },
        getPosition:function(A){
            if(A===true){
                return[this.el.getLeft(true),this.el.getTop(true)]
            }
            return this.xy||this.el.getXY()
        },
        getBox:function(A){
            var B=this.el.getSize();
            if(A===true){
                B.x=this.el.getLeft(true);
                B.y=this.el.getTop(true)
            }else{
                var C=this.xy||this.el.getXY();
                B.x=C[0];
                B.y=C[1]
            }
            return B
        },
        updateBox:function(A){
            this.setSize(A.width,A.height);
            this.setPagePosition(A.x,A.y);
            return this
        },
        getResizeEl:function(){
            return this.resizeEl||this.el
        },
        getPositionEl:function(){
            return this.positionEl||this.el
        },
        setPosition:function(A,F){
            if(A&&typeof A[1]=="number"){
                F=A[1];
                A=A[0]
            }
            this.x=A;
            this.y=F;
            if(!this.boxReady){
                return this
            }
            var B=this.adjustPosition(A,F);
            var E=B.x,D=B.y;
            var C=this.getPositionEl();
            if(E!==undefined||D!==undefined){
                if(E!==undefined&&D!==undefined){
                    C.setLeftTop(E,D)
                }else{
                    if(E!==undefined){
                        C.setLeft(E)
                    }else{
                        if(D!==undefined){
                            C.setTop(D)
                        }
                    }
                }
                this.onPosition(E,D);
                this.fireEvent("move",this,E,D)
            }
            return this
        },
        setPagePosition:function(A,C){
            if(A&&typeof A[1]=="number"){
                C=A[1];
                A=A[0]
            }
            this.pageX=A;
            this.pageY=C;
            if(!this.boxReady){
                return
            }
            if(A===undefined||C===undefined){
                return
            }
            var B=this.el.translatePoints(A,C);
            this.setPosition(B.left,B.top);
            return this
        },
        onRender:function(B,A){
            Ext.BoxComponent.superclass.onRender.call(this,B,A);
            if(this.resizeEl){
                this.resizeEl=Ext.get(this.resizeEl)
            }
            if(this.positionEl){
                this.positionEl=Ext.get(this.positionEl)
            }
        },
        afterRender:function(){
            Ext.BoxComponent.superclass.afterRender.call(this);
            this.boxReady=true;
            this.setSize(this.width,this.height);
            if(this.x||this.y){
                this.setPosition(this.x,this.y)
            }else{
                if(this.pageX||this.pageY){
                    this.setPagePosition(this.pageX,this.pageY)
                }
            }
        },
        syncSize:function(){
            delete this.lastSize;
            this.setSize(this.autoWidth?undefined:this.el.getWidth(),this.autoHeight?undefined:this.el.getHeight());
            return this
        },
        onResize:function(D,B,A,C){},
        onPosition:function(A,B){},
        adjustSize:function(A,B){
            if(this.autoWidth){
                A="auto"
            }
            if(this.autoHeight){
                B="auto"
            }
            return{
                width:A,
                height:B
            }
        },
        adjustPosition:function(A,B){
            return{
                x:A,
                y:B
            }
        }
    });
    Ext.reg("box",Ext.BoxComponent);
    Ext.DataView=Ext.extend(Ext.BoxComponent,{
        selectedClass:"x-view-selected",
        emptyText:"",
        last:false,
        initComponent:function(){
            Ext.DataView.superclass.initComponent.call(this);
            if(typeof this.tpl=="string"){
                this.tpl=new Ext.XTemplate(this.tpl)
            }
            this.addEvents("beforeclick","click","containerclick","dblclick","contextmenu","selectionchange","beforeselect");
            this.all=new Ext.CompositeElementLite();
            this.selected=new Ext.CompositeElementLite()
        },
        onRender:function(){
            if(!this.el){
                this.el=document.createElement("div")
            }
            Ext.DataView.superclass.onRender.apply(this,arguments)
        },
        afterRender:function(){
            Ext.DataView.superclass.afterRender.call(this);
            this.el.on({
                "click":this.onClick,
                "dblclick":this.onDblClick,
                "contextmenu":this.onContextMenu,
                scope:this
            });
            if(this.overClass){
                this.el.on({
                    "mouseover":this.onMouseOver,
                    "mouseout":this.onMouseOut,
                    scope:this
                })
            }
            if(this.store){
                this.setStore(this.store,true)
            }
        },
        refresh:function(){
            this.clearSelections(false,true);
            this.el.update("");
            var B=[];
            var A=this.store.getRange();
            if(A.length<1){
                this.el.update(this.emptyText);
                this.all.clear();
                return
            }
            this.tpl.overwrite(this.el,this.collectData(A,0));
            this.all.fill(Ext.query(this.itemSelector,this.el.dom));
            this.updateIndexes(0)
        },
        prepareData:function(A){
            return A
        },
        collectData:function(B,E){
    
            var D=[];
            for(var C=0,A=B.length;C<A;C++){
                if(B[C].json.expanded_name != undefined && B[C].data.name != undefined) {
                    var display_obj = Object();
                    display_obj.name = B[C].json.expanded_name;
                    D[D.length]=this.prepareData(display_obj,E+C,B[C])
                }else{
                    D[D.length]=this.prepareData(B[C].data,E+C,B[C])
                }
        
        
            }
        
            return D
        },
        bufferRender:function(A){
            var B=document.createElement("div");
            this.tpl.overwrite(B,this.collectData(A));
            return Ext.query(this.itemSelector,B)
        },
        onUpdate:function(F,A){
            var B=this.store.indexOf(A);
            var E=this.isSelected(B);
            var C=this.all.elements[B];
            var D=this.bufferRender([A],B)[0];
            this.all.replaceElement(B,D,true);
            if(E){
                this.selected.replaceElement(C,D);
                this.all.item(B).addClass(this.selectedClass)
            }
            this.updateIndexes(B,B)
        },
        onAdd:function(D,B,C){
            if(this.all.getCount()==0){
                this.refresh();
                return
            }
            var A=this.bufferRender(B,C),E;
            if(C<this.all.getCount()){
                E=this.all.item(C).insertSibling(A,"before",true);
                this.all.elements.splice(C,0,E)
            }else{
                E=this.all.last().insertSibling(A,"after",true);
                this.all.elements.push(E)
            }
            this.updateIndexes(C)
        },
        onRemove:function(C,A,B){
            this.deselect(B);
            this.all.removeElement(B,true);
            this.updateIndexes(B)
        },
        refreshNode:function(A){
            this.onUpdate(this.store,this.store.getAt(A))
        },
        updateIndexes:function(D,C){
            var B=this.all.elements;
            D=D||0;
            C=C||((C===0)?0:(B.length-1));
            for(var A=D;A<=C;A++){
                B[A].viewIndex=A
            }
        },
        setStore:function(A,B){
            if(!B&&this.store){
                this.store.un("beforeload",this.onBeforeLoad,this);
                this.store.un("datachanged",this.refresh,this);
                this.store.un("add",this.onAdd,this);
                this.store.un("remove",this.onRemove,this);
                this.store.un("update",this.onUpdate,this);
                this.store.un("clear",this.refresh,this)
            }
            if(A){
                A=Ext.StoreMgr.lookup(A);
                A.on("beforeload",this.onBeforeLoad,this);
                A.on("datachanged",this.refresh,this);
                A.on("add",this.onAdd,this);
                A.on("remove",this.onRemove,this);
                A.on("update",this.onUpdate,this);
                A.on("clear",this.refresh,this)
            }
            this.store=A;
            if(A){
                this.refresh()
            }
        },
        findItemFromChild:function(A){
            return Ext.fly(A).findParent(this.itemSelector,this.el)
        },
        onClick:function(C){
            var B=C.getTarget(this.itemSelector,this.el);
            if(B){
                var A=this.indexOf(B);
                if(this.onItemClick(B,A,C)!==false){
                    this.fireEvent("click",this,A,B,C)
                }
            }else{
                if(this.fireEvent("containerclick",this,C)!==false){
                    this.clearSelections()
                }
            }
        },
        onContextMenu:function(B){
            var A=B.getTarget(this.itemSelector,this.el);
            if(A){
                this.fireEvent("contextmenu",this,this.indexOf(A),A,B)
            }
        },
        onDblClick:function(B){
            var A=B.getTarget(this.itemSelector,this.el);
            if(A){
                this.fireEvent("dblclick",this,this.indexOf(A),A,B)
            }
        },
        onMouseOver:function(B){
            var A=B.getTarget(this.itemSelector,this.el);
            if(A&&A!==this.lastItem){
                this.lastItem=A;
                Ext.fly(A).addClass(this.overClass)
            }
        },
        onMouseOut:function(A){
            if(this.lastItem){
                if(!A.within(this.lastItem,true)){
                    Ext.fly(this.lastItem).removeClass(this.overClass);
                    delete this.lastItem
                }
            }
        },
        onItemClick:function(B,A,C){
            if(this.fireEvent("beforeclick",this,A,B,C)===false){
                return false
            }
            if(this.multiSelect){
                this.doMultiSelection(B,A,C);
                C.preventDefault()
            }else{
                if(this.singleSelect){
                    this.doSingleSelection(B,A,C);
                    C.preventDefault()
                }
            }
            return true
        },
        doSingleSelection:function(B,A,C){
            if(C.ctrlKey&&this.isSelected(A)){
                this.deselect(A)
            }else{
                this.select(A,false)
            }
        },
        doMultiSelection:function(C,A,D){
            if(D.shiftKey&&this.last!==false){
                var B=this.last;
                this.selectRange(B,A,D.ctrlKey);
                this.last=B
            }else{
                if((D.ctrlKey||this.simpleSelect)&&this.isSelected(A)){
                    this.deselect(A)
                }else{
                    this.select(A,D.ctrlKey||D.shiftKey||this.simpleSelect)
                }
            }
        },
        getSelectionCount:function(){
            return this.selected.getCount()
        },
        getSelectedNodes:function(){
            return this.selected.elements
        },
        getSelectedIndexes:function(){
            var B=[],D=this.selected.elements;
            for(var C=0,A=D.length;C<A;C++){
                B.push(D[C].viewIndex)
            }
            return B
        },
        getSelectedRecords:function(){
            var D=[],C=this.selected.elements;
            for(var B=0,A=C.length;B<A;B++){
                D[D.length]=this.store.getAt(C[B].viewIndex)
            }
            return D
        },
        getRecords:function(B){
    
            var E=[],D=B;
            for(var C=0,A=D.length;C<A;C++){
                E[E.length]=this.store.getAt(D[C].viewIndex)
            }
            return E
        },
        getRecord:function(A){
            return this.store.getAt(A.viewIndex)
        },
        clearSelections:function(A,B){
            if(this.multiSelect||this.singleSelect){
                if(!B){
                    this.selected.removeClass(this.selectedClass)
                }
                this.selected.clear();
                this.last=false;
                if(!A){
                    this.fireEvent("selectionchange",this,this.selected.elements)
                }
            }
        },
        isSelected:function(A){
            return this.selected.contains(this.getNode(A))
        },
        deselect:function(A){
            if(this.isSelected(A)){
                var A=this.getNode(A);
                this.selected.removeElement(A);
                if(this.last==A.viewIndex){
                    this.last=false
                }
                Ext.fly(A).removeClass(this.selectedClass);
                this.fireEvent("selectionchange",this,this.selected.elements)
            }
        },
        select:function(D,F,B){
            if(Ext.isArray(D)){
                if(!F){
                    this.clearSelections(true)
                }
                for(var C=0,A=D.length;C<A;C++){
                    this.select(D[C],true,true)
                }
            }else{
                var E=this.getNode(D);
                if(!F){
                    this.clearSelections(true)
                }
                if(E&&!this.isSelected(E)){
                    if(this.fireEvent("beforeselect",this,E,this.selected.elements)!==false){
                        Ext.fly(E).addClass(this.selectedClass);
                        this.selected.add(E);
                        this.last=E.viewIndex;
                        if(!B){
                            this.fireEvent("selectionchange",this,this.selected.elements)
                        }
                    }
                }
            }
        },
        selectRange:function(C,A,B){
            if(!B){
                this.clearSelections(true)
            }
            this.select(this.getNodes(C,A),true)
        },
        getNode:function(A){
            if(typeof A=="string"){
                return document.getElementById(A)
            }else{
                if(typeof A=="number"){
                    return this.all.elements[A]
                }
            }
            return A
        },
        getNodes:function(E,A){
            var D=this.all.elements;
            E=E||0;
            A=typeof A=="undefined"?D.length-1:A;
            var B=[],C;
            if(E<=A){
                for(C=E;C<=A;C++){
                    B.push(D[C])
                }
            }else{
                for(C=E;C>=A;C--){
                    B.push(D[C])
                }
            }
            return B
        },
        indexOf:function(A){
            A=this.getNode(A);
            if(typeof A.viewIndex=="number"){
                return A.viewIndex
            }
            return this.all.indexOf(A)
        },
        onBeforeLoad:function(){
            if(this.loadingText){
                this.clearSelections(false,true);
                this.el.update("<div class=\"loading-indicator\">"+this.loadingText+"</div>");
                this.all.clear()
            }
        }
    });
    Ext.reg("dataview",Ext.DataView);
    Ext.form.Field=Ext.extend(Ext.BoxComponent,{
        invalidClass:"x-form-invalid",
        invalidText:"The value in this field is invalid",
        focusClass:"x-form-focus",
        validationEvent:"keyup",
        validateOnBlur:true,
        validationDelay:250,
        defaultAutoCreate:{
            tag:"input",
            type:"text",
            size:"20",
            autocomplete:"off"
        },
        fieldClass:"x-form-field",
        msgTarget:"qtip",
        msgFx:"normal",
        readOnly:false,
        disabled:false,
        isFormField:true,
        hasFocus:false,
        initComponent:function(){
            Ext.form.Field.superclass.initComponent.call(this);
            this.addEvents("focus","blur","specialkey","change","invalid","valid")
        },
        getName:function(){
            return this.rendered&&this.el.dom.name?this.el.dom.name:(this.hiddenName||"")
        },
        onRender:function(C,A){
            Ext.form.Field.superclass.onRender.call(this,C,A);
            if(!this.el){
                var B=this.getAutoCreate();
                if(!B.name){
                    B.name=this.name||this.id
                }
                if(this.inputType){
                    B.type=this.inputType
                }
                this.el=C.createChild(B,A)
            }
            var D=this.el.dom.type;
            if(D){
                if(D=="password"){
                    D="text"
                }
                this.el.addClass("x-form-"+D)
            }
            if(this.readOnly){
                this.el.dom.readOnly=true
            }
            if(this.tabIndex!==undefined){
                this.el.dom.setAttribute("tabIndex",this.tabIndex)
            }
            this.el.addClass([this.fieldClass,this.cls]);
            this.initValue()
        },
        initValue:function(){
            if(this.value!==undefined){
                this.setValue(this.value)
            }else{
                if(this.el.dom.value.length>0){
                    this.setValue(this.el.dom.value)
                }
            }
        },
        isDirty:function(){
            if(this.disabled){
                return false
            }
            return String(this.getValue())!==String(this.originalValue)
        },
        afterRender:function(){
            Ext.form.Field.superclass.afterRender.call(this);
            this.initEvents()
        },
        fireKey:function(A){
            if(A.isSpecialKey()){
                this.fireEvent("specialkey",this,A)
            }
        },
        reset:function(){
            this.setValue(this.originalValue);
            this.clearInvalid()
        },
        initEvents:function(){
            this.el.on(Ext.isIE?"keydown":"keypress",this.fireKey,this);
            this.el.on("focus",this.onFocus,this);
            this.el.on("blur",this.onBlur,this);
            this.originalValue=this.getValue()
        },
        onFocus:function(){
            if(!Ext.isOpera&&this.focusClass){
                this.el.addClass(this.focusClass)
            }
            if(!this.hasFocus){
                this.hasFocus=true;
                this.startValue=this.getValue();
                this.fireEvent("focus",this)
            }
        },
        beforeBlur:Ext.emptyFn,
        onBlur:function(){
            this.beforeBlur();
            if(!Ext.isOpera&&this.focusClass){
                this.el.removeClass(this.focusClass)
            }
            this.hasFocus=false;
            if(this.validationEvent!==false&&this.validateOnBlur&&this.validationEvent!="blur"){
                this.validate()
            }
            var A=this.getValue();
            if(String(A)!==String(this.startValue)){
                this.fireEvent("change",this,A,this.startValue)
            }
            this.fireEvent("blur",this)
        },
        isValid:function(A){
    
            if(this.disabled){
                return true
            }
            var C=this.preventMark;
            this.preventMark=A===true;
            var B=this.validateValue(this.processValue(this.getRawValue()));
            this.preventMark=C;
            return B
        },
        validate:function(){
            if(this.disabled||this.validateValue(this.processValue(this.getRawValue()))){
                this.clearInvalid();
                return true
            }
            return false
        },
        processValue:function(A){
            return A
        },
        validateValue:function(A){
            return true
        },
        markInvalid:function(C){
            if(!this.rendered||this.preventMark){
                return
            }
            this.el.addClass(this.invalidClass);
            C=C||this.invalidText;
            switch(this.msgTarget){
                case"qtip":
                    this.el.dom.qtip=C;
                    this.el.dom.qclass="x-form-invalid-tip";
                    if(Ext.QuickTips){
                        Ext.QuickTips.enable()
                    }
                    break;
                case"title":
                    this.el.dom.title=C;
                    break;
                case"under":
                    if(!this.errorEl){
                        var B=this.el.findParent(".x-form-element",5,true);
                        this.errorEl=B.createChild({
                            cls:"x-form-invalid-msg"
                        });
                        this.errorEl.setWidth(B.getWidth(true)-20)
                    }
                    this.errorEl.update(C);
                    Ext.form.Field.msgFx[this.msgFx].show(this.errorEl,this);
                    break;
                case"side":
                    if(!this.errorIcon){
                        var B=this.el.findParent(".x-form-element",5,true);
                        this.errorIcon=B.createChild({
                            cls:"x-form-invalid-icon"
                        })
                    }
                    this.alignErrorIcon();
                    this.errorIcon.dom.qtip=C;
                    this.errorIcon.dom.qclass="x-form-invalid-tip";
                    this.errorIcon.show();
                    this.on("resize",this.alignErrorIcon,this);
                    break;
                default:
                    var A=Ext.getDom(this.msgTarget);
                    A.innerHTML=C;
                    A.style.display=this.msgDisplay;
                    break
            }
            this.fireEvent("invalid",this,C)
        },
        alignErrorIcon:function(){
            this.errorIcon.alignTo(this.el,"tl-tr",[2,0])
        },
        clearInvalid:function(){
            if(!this.rendered||this.preventMark){
                return
            }
            this.el.removeClass(this.invalidClass);
            switch(this.msgTarget){
                case"qtip":
                    this.el.dom.qtip="";
                    break;
                case"title":
                    this.el.dom.title="";
                    break;
                case"under":
                    if(this.errorEl){
                        Ext.form.Field.msgFx[this.msgFx].hide(this.errorEl,this)
                    }
                    break;
                case"side":
                    if(this.errorIcon){
                        this.errorIcon.dom.qtip="";
                        this.errorIcon.hide();
                        this.un("resize",this.alignErrorIcon,this)
                    }
                    break;
                default:
                    var A=Ext.getDom(this.msgTarget);
                    A.innerHTML="";
                    A.style.display="none";
                    break
            }
            this.fireEvent("valid",this)
        },
        getRawValue:function(){
            var A=this.rendered?this.el.getValue():Ext.value(this.value,"");
            if(A===this.emptyText){
                A=""
            }
            return A
        },
        getValue:function(){
            if(!this.rendered){
                return this.value
            }
            var A=this.el.getValue();
            if(A===this.emptyText||A===undefined){
                A=""
            }
            return A
        },
        setRawValue:function(A){
            return this.el.dom.value=(A===null||A===undefined?"":A)
        },
        setValue:function(A){
            this.value=A;
            if(this.rendered){
                this.el.dom.value=(A===null||A===undefined?"":A);
                this.validate()
            }
        },
        adjustSize:function(A,C){
            var B=Ext.form.Field.superclass.adjustSize.call(this,A,C);
            B.width=this.adjustWidth(this.el.dom.tagName,B.width);
            return B
        },
        adjustWidth:function(A,B){
            A=A.toLowerCase();
            if(typeof B=="number"&&!Ext.isSafari){
                if(Ext.isIE&&(A=="input"||A=="textarea")){
                    if(A=="input"&&!Ext.isStrict){
                        return this.inEditor?B:B-3
                    }
                    if(A=="input"&&Ext.isStrict){
                        return B-(Ext.isIE6?4:1)
                    }
                    if(A="textarea"&&Ext.isStrict){
                        return B-2
                    }
                }else{
                    if(Ext.isOpera&&Ext.isStrict){
                        if(A=="input"){
                            return B+2
                        }
                        if(A="textarea"){
                            return B-2
                        }
                    }
                }
            }
            return B
        }
    });
    Ext.form.Field.msgFx={
        normal:{
            show:function(A,B){
                A.setDisplayed("block")
            },
            hide:function(A,B){
                A.setDisplayed(false).update("")
            }
        },
        slide:{
            show:function(A,B){
                A.slideIn("t",{
                    stopFx:true
                })
            },
            hide:function(A,B){
                A.slideOut("t",{
                    stopFx:true,
                    useDisplay:true
                })
            }
        },
        slideRight:{
            show:function(A,B){
                A.fixDisplay();
                A.alignTo(B.el,"tl-tr");
                A.slideIn("l",{
                    stopFx:true
                })
            },
            hide:function(A,B){
                A.slideOut("l",{
                    stopFx:true,
                    useDisplay:true
                })
            }
        }
    };

    Ext.reg("field",Ext.form.Field);
    Ext.form.TextField=Ext.extend(Ext.form.Field,{
        grow:false,
        growMin:30,
        growMax:800,
        vtype:null,
        maskRe:null,
        disableKeyFilter:false,
        allowBlank:true,
        minLength:0,
        maxLength:Number.MAX_VALUE,
        minLengthText:"The minimum length for this field is {0}",
        maxLengthText:"The maximum length for this field is {0}",
        selectOnFocus:false,
        blankText:"This field is required",
        validator:null,
        regex:null,
        regexText:"",
        emptyText:null,
        emptyClass:"x-form-empty-field",
        initComponent:function(){
            Ext.form.TextField.superclass.initComponent.call(this);
            this.addEvents("autosize")
        },
        initEvents:function(){
            Ext.form.TextField.superclass.initEvents.call(this);
            if(this.validationEvent=="keyup"){
                this.validationTask=new Ext.util.DelayedTask(this.validate,this);
                this.el.on("keyup",this.filterValidation,this)
            }else{
                if(this.validationEvent!==false){
                    this.el.on(this.validationEvent,this.validate,this,{
                        buffer:this.validationDelay
                    })
                }
            }
            if(this.selectOnFocus||this.emptyText){
                this.on("focus",this.preFocus,this);
                if(this.emptyText){
                    this.on("blur",this.postBlur,this);
                    this.applyEmptyText()
                }
            }
            if(this.maskRe||(this.vtype&&this.disableKeyFilter!==true&&(this.maskRe=Ext.form.VTypes[this.vtype+"Mask"]))){
                this.el.on("keypress",this.filterKeys,this)
            }
            if(this.grow){
                this.el.on("keyup",this.onKeyUp,this,{
                    buffer:50
                });
                this.el.on("click",this.autoSize,this)
            }
        },
        processValue:function(A){
            if(this.stripCharsRe){
                var B=A.replace(this.stripCharsRe,"");
                if(B!==A){
                    this.setRawValue(B);
                    return B
                }
            }
            return A
        },
        filterValidation:function(A){
            if(!A.isNavKeyPress()){
                this.validationTask.delay(this.validationDelay)
            }
        },
        onKeyUp:function(A){
            if(!A.isNavKeyPress()){
                this.autoSize()
            }
        },
        reset:function(){
            Ext.form.TextField.superclass.reset.call(this);
            this.applyEmptyText()
        },
        applyEmptyText:function(){
            if(this.rendered&&this.emptyText&&this.getRawValue().length<1){
                this.setRawValue(this.emptyText);
                this.el.addClass(this.emptyClass)
            }
        },
        preFocus:function(){
            if(this.emptyText){
                if(this.el.dom.value==this.emptyText){
                    this.setRawValue("")
                }
                this.el.removeClass(this.emptyClass)
            }
            if(this.selectOnFocus){
                this.el.dom.select()
            }
        },
        postBlur:function(){
            this.applyEmptyText()
        },
        filterKeys:function(B){
            var A=B.getKey();
            if(!Ext.isIE&&(B.isNavKeyPress()||A==B.BACKSPACE||(A==B.DELETE&&B.button==-1))){
                return
            }
            var D=B.getCharCode(),C=String.fromCharCode(D);
            if(Ext.isIE&&(B.isSpecialKey()||!C)){
                return
            }
            if(!this.maskRe.test(C)){
                B.stopEvent()
            }
        },
        setValue:function(A){
            if(this.emptyText&&this.el&&A!==undefined&&A!==null&&A!==""){
                this.el.removeClass(this.emptyClass)
            }
            Ext.form.TextField.superclass.setValue.apply(this,arguments);
            this.applyEmptyText();
            this.autoSize()
        },
        validateValue:function(A){
            if(A.length<1||A===this.emptyText){
                if(this.allowBlank){
                    this.clearInvalid();
                    return true
                }else{
                    this.markInvalid(this.blankText);
                    return false
                }
            }
            if(A.length<this.minLength){
                this.markInvalid(String.format(this.minLengthText,this.minLength));
                return false
            }
            if(A.length>this.maxLength){
                this.markInvalid(String.format(this.maxLengthText,this.maxLength));
                return false
            }
            if(this.vtype){
                var C=Ext.form.VTypes;
                if(!C[this.vtype](A,this)){
                    this.markInvalid(this.vtypeText||C[this.vtype+"Text"]);
                    return false
                }
            }
            if(typeof this.validator=="function"){
                var B=this.validator(A);
                if(B!==true){
                    this.markInvalid(B);
                    return false
                }
            }
            if(this.regex&&!this.regex.test(A)){
                this.markInvalid(this.regexText);
                return false
            }
            return true
        },
        selectText:function(E,A){
            var C=this.getRawValue();
            if(C.length>0){
                E=E===undefined?0:E;
                A=A===undefined?C.length:A;
                var D=this.el.dom;
                if(D.setSelectionRange){
                    D.setSelectionRange(E,A)
                }else{
                    if(D.createTextRange){
                        var B=D.createTextRange();
                        B.moveStart("character",E);
                        B.moveEnd("character",A-C.length);
                        B.select()
                    }
                }
            }
        },
        autoSize:function(){
            if(!this.grow||!this.rendered){
                return
            }
            if(!this.metrics){
                this.metrics=Ext.util.TextMetrics.createInstance(this.el)
            }
            var C=this.el;
            var B=C.dom.value;
            var D=document.createElement("div");
            D.appendChild(document.createTextNode(B));
            B=D.innerHTML;
            D=null;
            B+="&#160;";
            var A=Math.min(this.growMax,Math.max(this.metrics.getWidth(B)+10,this.growMin));
            this.el.setWidth(A);
            this.fireEvent("autosize",this,A)
        }
    });
    Ext.reg("textfield",Ext.form.TextField);
    Ext.form.TriggerField=Ext.extend(Ext.form.TextField,{
        defaultAutoCreate:{
            tag:"input",
            type:"text",
            size:"16",
            autocomplete:"off"
        },
        hideTrigger:false,
        autoSize:Ext.emptyFn,
        monitorTab:true,
        deferHeight:true,
        mimicing:false,
        onResize:function(A,B){
            Ext.form.TriggerField.superclass.onResize.call(this,A,B);
            if(typeof A=="number"){
                this.el.setWidth(this.adjustWidth("input",A-this.trigger.getWidth()))
            }
            this.wrap.setWidth(this.el.getWidth()+this.trigger.getWidth())
        },
        adjustSize:Ext.BoxComponent.prototype.adjustSize,
        getResizeEl:function(){
            return this.wrap
        },
        getPositionEl:function(){
            return this.wrap
        },
        alignErrorIcon:function(){
            this.errorIcon.alignTo(this.wrap,"tl-tr",[2,0])
        },
        onRender:function(B,A){
            Ext.form.TriggerField.superclass.onRender.call(this,B,A);
            this.wrap=this.el.wrap({
                cls:"x-form-field-wrap"
            });
            this.trigger=this.wrap.createChild(this.triggerConfig||{
                tag:"img",
                src:Ext.BLANK_IMAGE_URL,
                cls:"x-form-trigger "+this.triggerClass
            });
            if(this.hideTrigger){
                this.trigger.setDisplayed(false)
            }
            this.initTrigger();
            if(!this.width){
                this.wrap.setWidth(this.el.getWidth()+this.trigger.getWidth())
            }
        },
        initTrigger:function(){
            this.trigger.on("click",this.onTriggerClick,this,{
                preventDefault:true
            });
            this.trigger.addClassOnOver("x-form-trigger-over");
            this.trigger.addClassOnClick("x-form-trigger-click")
        },
        onDestroy:function(){
            if(this.trigger){
                this.trigger.removeAllListeners();
                this.trigger.remove()
            }
            if(this.wrap){
                this.wrap.remove()
            }
            Ext.form.TriggerField.superclass.onDestroy.call(this)
        },
        onFocus:function(){
            Ext.form.TriggerField.superclass.onFocus.call(this);
            if(!this.mimicing){
                this.wrap.addClass("x-trigger-wrap-focus");
                this.mimicing=true;
                Ext.get(Ext.isIE?document.body:document).on("mousedown",this.mimicBlur,this,{
                    delay:10
                });
                if(this.monitorTab){
                    this.el.on("keydown",this.checkTab,this)
                }
            }
        },
        checkTab:function(A){
            if(A.getKey()==A.TAB){
                this.triggerBlur()
            }
        },
        onBlur:function(){},
        mimicBlur:function(A){
            if(!this.wrap.contains(A.target)&&this.validateBlur(A)){
                this.triggerBlur()
            }
        },
        triggerBlur:function(){
            this.mimicing=false;
            Ext.get(Ext.isIE?document.body:document).un("mousedown",this.mimicBlur);
            if(this.monitorTab){
                this.el.un("keydown",this.checkTab,this)
            }
            this.beforeBlur();
            this.wrap.removeClass("x-trigger-wrap-focus");
            Ext.form.TriggerField.superclass.onBlur.call(this)
        },
        beforeBlur:Ext.emptyFn,
        validateBlur:function(A){
            return true
        },
        onDisable:function(){
            Ext.form.TriggerField.superclass.onDisable.call(this);
            if(this.wrap){
                this.wrap.addClass("x-item-disabled")
            }
        },
        onEnable:function(){
            Ext.form.TriggerField.superclass.onEnable.call(this);
            if(this.wrap){
                this.wrap.removeClass("x-item-disabled")
            }
        },
        onShow:function(){
            if(this.wrap){
                this.wrap.dom.style.display="";
                this.wrap.dom.style.visibility="visible"
            }
        },
        onHide:function(){
            this.wrap.dom.style.display="none"
        },
        onTriggerClick:Ext.emptyFn
    });
    Ext.form.TwinTriggerField=Ext.extend(Ext.form.TriggerField,{
        initComponent:function(){
            Ext.form.TwinTriggerField.superclass.initComponent.call(this);
            this.triggerConfig={
                tag:"span",
                cls:"x-form-twin-triggers",
                cn:[{
                    tag:"img",
                    src:Ext.BLANK_IMAGE_URL,
                    cls:"x-form-trigger "+this.trigger1Class
                },{
                    tag:"img",
                    src:Ext.BLANK_IMAGE_URL,
                    cls:"x-form-trigger "+this.trigger2Class
                }]
            }
        },
        getTrigger:function(A){
            return this.triggers[A]
        },
        initTrigger:function(){
            var A=this.trigger.select(".x-form-trigger",true);
            this.wrap.setStyle("overflow","hidden");
            var B=this;
            A.each(function(D,F,C){
                D.hide=function(){
                    var G=B.wrap.getWidth();
                    this.dom.style.display="none";
                    B.el.setWidth(G-B.trigger.getWidth())
                };
            
                D.show=function(){
                    var G=B.wrap.getWidth();
                    this.dom.style.display="";
                    B.el.setWidth(G-B.trigger.getWidth())
                };
            
                var E="Trigger"+(C+1);
                if(this["hide"+E]){
                    D.dom.style.display="none"
                }
                D.on("click",this["on"+E+"Click"],this,{
                    preventDefault:true
                });
                D.addClassOnOver("x-form-trigger-over");
                D.addClassOnClick("x-form-trigger-click")
            },this);
            this.triggers=A.elements
        },
        onTrigger1Click:Ext.emptyFn,
        onTrigger2Click:Ext.emptyFn
    });
    Ext.reg("trigger",Ext.form.TriggerField);
    Ext.form.ComboBox=Ext.extend(Ext.form.TriggerField,{
        defaultAutoCreate:{
            tag:"input",
            type:"text",
            size:"24",
            autocomplete:"off"
        },
        listClsClass:'x-combo-list',
        selectedClass:"x-combo-selected",
        triggerClass:"x-form-arrow-trigger",
        shadow:"sides",
        listAlign:"tl-bl?",
        maxHeight:300,
        minHeight:90,
        triggerAction:"query",
        minChars:4,
        typeAhead:false,
        queryDelay:500,
        pageSize:0,
        selectOnFocus:false,
        queryParam:"query",
        loadingText:"Loading...",
        resizable:false,
        handleHeight:8,
        editable:true,
        allQuery:"",
        mode:"remote",
        minListWidth:70,
        forceSelection:false,
        typeAheadDelay:250,
        lazyInit:true,
        initComponent:function(){
            Ext.form.ComboBox.superclass.initComponent.call(this);
            this.addEvents("expand","collapse","beforeselect","select","beforequery");
            if(this.transform){
                this.allowDomMove=false;
                var C=Ext.getDom(this.transform);
                if(!this.hiddenName){
                    this.hiddenName=C.name
                }
                if(!this.store){
                    this.mode="local";
                    var G=[],D=C.options;
                    for(var B=0,A=D.length;B<A;B++){
                        var F=D[B];
                        var E=(Ext.isIE?F.getAttributeNode("value").specified:F.hasAttribute("value"))?F.value:F.text;
                        if(F.selected){
                            this.value=E
                        }
                        G.push([E,F.text])
                    }
                    this.store=new Ext.data.SimpleStore({
                        "id":0,
                        fields:["value","text"],
                        data:G
                    });
                    this.valueField="value";
                    this.displayField="text"
                }
                C.name=Ext.id();
                if(!this.lazyRender){
                    this.target=true;
                    this.el=Ext.DomHelper.insertBefore(C,this.autoCreate||this.defaultAutoCreate);
                    Ext.removeNode(C);
                    this.render(this.el.parentNode)
                }else{
                    Ext.removeNode(C)
                }
            }
            this.selectedIndex=-1;
            if(this.mode=="local"){
                if(this.initialConfig.queryDelay===undefined){
                    this.queryDelay=10
                }
                if(this.initialConfig.minChars===undefined){
                    this.minChars=0
                }
            }
        },
        onRender:function(B,A){
            Ext.form.ComboBox.superclass.onRender.call(this,B,A);
            if(this.hiddenName){
                this.hiddenField=this.el.insertSibling({
                    tag:"input",
                    type:"hidden",
                    name:this.hiddenName,
                    id:(this.hiddenId||this.hiddenName)
                },"before",true);
                this.hiddenField.value=this.hiddenValue!==undefined?this.hiddenValue:this.value!==undefined?this.value:"";
                this.el.dom.removeAttribute("name")
            }
            if(Ext.isGecko){
                this.el.dom.setAttribute("autocomplete","off")
            }
            if(!this.lazyInit){
                this.initList()
            }else{
                this.on("focus",this.initList,this,{
                    single:true
                })
            }
            if(!this.editable){
                this.editable=true;
                this.setEditable(false)
            }
        },
        initList:function(){
            if(!this.list){
                var A=this.listClsClass;
                this.list=new Ext.Layer({
                    shadow:this.shadow,
                    cls:[A,this.listClass].join(" "),
                    constrain:false
                });
                //console.log(this)
                var id_ofinput = this.id
                if(id_ofinput.match(/custno/)) {
                    B = '82'
                }else{
                var B=this.listWidth||Math.max(this.wrap.getWidth(),this.minListWidth);
                }
                this.list.setWidth(B);
                this.list.swallowEvent("mousewheel");
                this.assetHeight=0;
                if(this.title){
                    this.header=this.list.createChild({
                        cls:A+"-hd",
                        html:this.title
                    });
                    this.assetHeight+=this.header.getHeight()
                }
                this.innerList=this.list.createChild({
                    cls:A+"-inner"
                });
                this.innerList.on("mouseover",this.onViewOver,this);
                this.innerList.on("mousemove",this.onViewMove,this);
                this.innerList.setWidth(B-this.list.getFrameWidth("lr"));
                if(this.pageSize){
                    this.footer=this.list.createChild({
                        cls:A+"-ft"
                    });
                    this.pageTb=new Ext.PagingToolbar({
                        store:this.store,
                        pageSize:this.pageSize,
                        renderTo:this.footer
                    });
                    this.assetHeight+=this.footer.getHeight()
                }
                if(!this.tpl){
                    this.tpl="<tpl for=\".\"><div class=\""+A+"-item\">{"+this.displayField+"}</div></tpl>"
                }
                this.view=new Ext.DataView({
                    applyTo:this.innerList,
                    tpl:this.tpl,
                    singleSelect:true,
                    selectedClass:this.selectedClass,
                    itemSelector:this.itemSelector||"."+A+"-item"
                });
                this.view.on("click",this.onViewClick,this);
                this.bindStore(this.store,true);
                if(this.resizable){
                    this.resizer=new Ext.Resizable(this.list,{
                        pinned:true,
                        handles:"se"
                    });
                    this.resizer.on("resize",function(E,C,D){
                        this.maxHeight=D-this.handleHeight-this.list.getFrameWidth("tb")-this.assetHeight;
                        this.listWidth=C;
                        this.innerList.setWidth(C-this.list.getFrameWidth("lr"));
                        this.restrictHeight()
                    },this);
                    this[this.pageSize?"footer":"innerList"].setStyle("margin-bottom",this.handleHeight+"px")
                }
            }
        },
        bindStore:function(A,B){
            if(this.store&&!B){
                this.store.un("beforeload",this.onBeforeLoad,this);
                this.store.un("load",this.onLoad,this);
                this.store.un("loadexception",this.collapse,this);
                if(!A){
                    this.store=null;
                    if(this.view){
                        this.view.setStore(null)
                    }
                }
            }
            if(A){
                this.store=Ext.StoreMgr.lookup(A);
                this.store.on("beforeload",this.onBeforeLoad,this);
                this.store.on("load",this.onLoad,this);
                this.store.on("loadexception",this.collapse,this);
                if(this.view){
                    this.view.setStore(A)
                }
            }
        },
        initEvents:function(){
            Ext.form.ComboBox.superclass.initEvents.call(this);
            this.keyNav=new Ext.KeyNav(this.el,{
                "up":function(A){
                    this.inKeyMode=true;
                    this.selectPrev()
                },
                "down":function(A){
                    if(!this.isExpanded()){
                        this.onTriggerClick()
                    }else{
                        this.inKeyMode=true;
                        this.selectNext()
                    }
                },
                "enter":function(A){
                    this.onViewClick();
                    this.delayedCheck=true;
                    this.unsetDelayCheck.defer(10,this)
                },
                "esc":function(A){
                    this.collapse()
                },
                "tab":function(A){
                    this.onViewClick(false);
                    return true
                },
                scope:this,
                doRelay:function(C,B,A){
                    if(A=="down"||this.scope.isExpanded()){
                        return Ext.KeyNav.prototype.doRelay.apply(this,arguments)
                    }
                    return true
                },
                forceKeyDown:true
            });
            this.queryDelay=Math.max(this.queryDelay||10,this.mode=="local"?10:250);
            this.dqTask=new Ext.util.DelayedTask(this.initQuery,this);
            if(this.typeAhead){
                this.taTask=new Ext.util.DelayedTask(this.onTypeAhead,this)
            }
            if(this.editable!==false){
                this.el.on("keyup",this.onKeyUp,this)
            }
            if(this.forceSelection){
                this.on("blur",this.doForce,this)
            }
        },
        onDestroy:function(){
            if(this.view){
                this.view.el.removeAllListeners();
                this.view.el.remove();
                this.view.purgeListeners()
            }
            if(this.list){
                this.list.destroy()
            }
            this.bindStore(null);
            Ext.form.ComboBox.superclass.onDestroy.call(this)
        },
        unsetDelayCheck:function(){
            delete this.delayedCheck
        },
        fireKey:function(A){
            if(A.isNavKeyPress()&&!this.isExpanded()&&!this.delayedCheck){
                this.fireEvent("specialkey",this,A)
            }
        },
        onResize:function(A,B){
            Ext.form.ComboBox.superclass.onResize.apply(this,arguments);
            if(this.list&&this.listWidth===undefined){
                var C=Math.max(A,this.minListWidth);
                this.list.setWidth(C);
                this.innerList.setWidth(C-this.list.getFrameWidth("lr"))
            }
        },
        onEnable:function(){
            Ext.form.ComboBox.superclass.onEnable.apply(this,arguments);
            if(this.hiddenField){
                this.hiddenField.disabled=false
            }
        },
        onDisable:function(){
            Ext.form.ComboBox.superclass.onDisable.apply(this,arguments);
            if(this.hiddenField){
                this.hiddenField.disabled=true
            }
        },
        setEditable:function(A){
            if(A==this.editable){
                return
            }
            this.editable=A;
            if(!A){
                this.el.dom.setAttribute("readOnly",true);
                this.el.on("mousedown",this.onTriggerClick,this);
                this.el.addClass("x-combo-noedit")
            }else{
                this.el.dom.setAttribute("readOnly",false);
                this.el.un("mousedown",this.onTriggerClick,this);
                this.el.removeClass("x-combo-noedit")
            }
        },
        onBeforeLoad:function(){
            if(!this.hasFocus){
                return
            }
            this.innerList.update(this.loadingText?"<div class=\"loading-indicator\">"+this.loadingText+"</div>":"");
            this.restrictHeight();
            this.selectedIndex=-1
        },
        onLoad:function(){
            if(!this.hasFocus){
                return
            }
            if(this.store.getCount()>0){
                this.expand();
                this.restrictHeight();
                if(this.lastQuery==this.allQuery){
                    if(this.editable){
                        this.el.dom.select()
                    }
                    if(!this.selectByValue(this.value,true)){
                        this.select(0,true)
                    }
                }else{
                    this.selectNext();
                    if(this.typeAhead&&this.lastKey!=Ext.EventObject.BACKSPACE&&this.lastKey!=Ext.EventObject.DELETE){
                        this.taTask.delay(this.typeAheadDelay)
                    }
                }
            }else{
                this.onEmptyResults()
            }
        },
        onTypeAhead:function(){
            if(this.store.getCount()>0){
                var B=this.store.getAt(0);
                var C=B.data[this.displayField];
                var A=C.length;
                var D=this.getRawValue().length;
                if(D!=A){
                    this.setRawValue(C);
                    this.selectText(D,C.length)
                }
            }
        },
        onSelect:function(A,B){
            if(this.fireEvent("beforeselect",this,A,B)!==false){
                this.setValue(A.data[this.valueField||this.displayField]);
                this.collapse();
                this.fireEvent("select",this,A,B)
            }
        },
        getValue:function(){
            if(this.valueField){
                return typeof this.value!="undefined"?this.value:""
            }else{
                return Ext.form.ComboBox.superclass.getValue.call(this)
            }
        },
        clearValue:function(){
            if(this.hiddenField){
                this.hiddenField.value=""
            }
            this.setRawValue("");
            this.lastSelectionText="";
            this.applyEmptyText();
            this.value=""
        },
        setValue:function(A){
            var C=A;
            if(this.valueField){
                var B=this.findRecord(this.valueField,A);
                if(B){
                    C=B.data[this.displayField]
                }else{
                    if(this.valueNotFoundText!==undefined){
                        C=this.valueNotFoundText
                    }
                }
            }
            this.lastSelectionText=C;
            if(this.hiddenField){
                this.hiddenField.value=A
            }
            Ext.form.ComboBox.superclass.setValue.call(this,C);
            this.value=A
        },
        findRecord:function(C,B){
            var A;
            if(this.store.getCount()>0){
                this.store.each(function(D){
                    if(D.data[C]==B){
                        A=D;
                        return false
                    }
                })
            }
            return A
        },
        onViewMove:function(B,A){
            this.inKeyMode=false
        },
        onViewOver:function(D,B){
            if(this.inKeyMode){
                return
            }
            var C=this.view.findItemFromChild(B);
            if(C){
                var A=this.view.indexOf(C);
                this.select(A,false)
            }
        },
        onViewClick:function(B){
            var A=this.view.getSelectedIndexes()[0];
            var C=this.store.getAt(A);
            if(C){
                this.onSelect(C,A)
            }
            if(B!==false){
                this.el.focus()
            }
        },
        restrictHeight:function(){
            this.innerList.dom.style.height="";
            var B=this.innerList.dom;
            var E=this.list.getFrameWidth("tb")+(this.resizable?this.handleHeight:0)+this.assetHeight;
            var C=Math.max(B.clientHeight,B.offsetHeight,B.scrollHeight);
            var A=this.getPosition()[1]-Ext.getBody().getScroll().top;
            var F=Ext.lib.Dom.getViewHeight()-A-this.getSize().height;
            var D=Math.max(A,F,this.minHeight||0)-this.list.shadow.offset-E-2;
            C=Math.min(C,D,this.maxHeight);
            this.innerList.setHeight(C);
            this.list.beginUpdate();
            this.list.setHeight(C+E);
            this.list.alignTo(this.el,this.listAlign);
            this.list.endUpdate()
        },
        onEmptyResults:function(){
            this.collapse()
        },
        isExpanded:function(){
            return this.list&&this.list.isVisible()
        },
        selectByValue:function(A,C){
            if(A!==undefined&&A!==null){
                var B=this.findRecord(this.valueField||this.displayField,A);
                if(B){
                    this.select(this.store.indexOf(B),C);
                    return true
                }
            }
            return false
        },
        select:function(A,C){
            this.selectedIndex=A;
            this.view.select(A);
            if(C!==false){
                var B=this.view.getNode(A);
                if(B){
                    this.innerList.scrollChildIntoView(B,false)
                }
            }
        },
        selectNext:function(){
            var A=this.store.getCount();
            if(A>0){
                if(this.selectedIndex==-1){
                    this.select(0)
                }else{
                    if(this.selectedIndex<A-1){
                        this.select(this.selectedIndex+1)
                    }
                }
            }
        },
        selectPrev:function(){
            var A=this.store.getCount();
            if(A>0){
                if(this.selectedIndex==-1){
                    this.select(0)
                }else{
                    if(this.selectedIndex!=0){
                        this.select(this.selectedIndex-1)
                    }
                }
            }
        },
        onKeyUp:function(A){
            if(this.editable!==false&&!A.isSpecialKey()){
                this.lastKey=A.getKey();
                this.dqTask.delay(this.queryDelay)
            }
        },
        validateBlur:function(){
            return!this.list||!this.list.isVisible()
        },
        initQuery:function(){
            this.doQuery(this.getRawValue())
        },
        doForce:function(){
            if(this.el.dom.value.length>0){
                this.el.dom.value=this.lastSelectionText===undefined?"":this.lastSelectionText;
                this.applyEmptyText()
            }
        },
        doQuery:function(C,B){
            if(C===undefined||C===null){
                C=""
            }
            var A={
                query:C,
                forceAll:B,
                combo:this,
                cancel:false
            };
    
            if(this.fireEvent("beforequery",A)===false||A.cancel){
                return false
            }
            C=A.query;
            B=A.forceAll;
            if(B===true||(C.length>=this.minChars)){
                if(this.lastQuery!==C){
                    this.lastQuery=C;
                    if(this.mode=="local"){
                        this.selectedIndex=-1;
                        if(B){
                            this.store.clearFilter()
                        }else{
                            this.store.filter(this.displayField,C)
                        }
                        this.onLoad()
                    }else{
                        this.store.baseParams[this.queryParam]=C;
                        this.store.load({
                            params:this.getParams(C)
                        });
                        this.expand()
                    }
                }else{
                    this.selectedIndex=-1;
                    this.onLoad()
                }
            }
        },
        getParams:function(A){
            var B={};
    
            if(this.pageSize){
                B.start=0;
                B.limit=this.pageSize
            }
            return B
        },
        collapse:function(){
            if(!this.isExpanded()){
                return
            }
            this.list.hide();
            Ext.getDoc().un("mousewheel",this.collapseIf,this);
            Ext.getDoc().un("mousedown",this.collapseIf,this);
            this.fireEvent("collapse",this)
        },
        collapseIf:function(A){
            if(!A.within(this.wrap)&&!A.within(this.list)){
                this.collapse()
            }
        },
        expand:function(){
            if(this.isExpanded()||!this.hasFocus){
                return
            }
            this.list.alignTo(this.wrap,this.listAlign);
            this.list.show();
            this.innerList.setOverflow("auto");
            Ext.getDoc().on("mousewheel",this.collapseIf,this);
            Ext.getDoc().on("mousedown",this.collapseIf,this);
            this.fireEvent("expand",this)
        },
        onTriggerClick:function(){
            if(this.disabled){
                return
            }
            if(this.isExpanded()){
                this.collapse();
                this.el.focus()
            }else{
                this.onFocus({});
                if(this.triggerAction=="all"){
                    this.doQuery(this.allQuery,true)
                }else{
                    this.doQuery(this.getRawValue())
                }
                this.el.focus()
            }
        }
    });
    Ext.reg("combo",Ext.form.ComboBox);
}// End of File include/javascript/ext-2.0/ext-quicksearch.js
                                
/**
 * Javascript file for Sugar
 *
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 */
function enableQS(noReload){
    Ext.onReady(function(){
        var qsFields=Ext.query('.sqsEnabled');
        for(var qsField in qsFields){
            var loaded=false;
            if(isInteger(qsField)&&(qsFields[qsField].id&&!document.getElementById(qsFields[qsField].id).readOnly)&&typeof sqs_objects!='undefined'&&sqs_objects[qsFields[qsField].id]&&sqs_objects[qsFields[qsField].id]['disable']!=true){
                if(typeof Ext.getCmp('combobox_'+qsFields[qsField].id)!='undefined'){
                    if(noReload==true){
                        loaded=true;
                    }else if(typeof QSFieldsArray[qsFields[qsField].id]!='undefined'){
                        Ext.getCmp('combobox_'+qsFields[qsField].id).destroy();
                        var parent=QSFieldsArray[qsFields[qsField].id][0];
                        if(typeof QSFieldsArray[qsFields[qsField].id][1]!='undefined'){
                            var nextSib=QSFieldsArray[qsFields[qsField].id][1];
                            parent.insertBefore(QSFieldsArray[qsFields[qsField].id][2],nextSib);
                        }
                        else{
                            parent.appendchild(QSFieldsArray[qsFields[qsField].id][2]);
                        }
                    }
                }
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
                            url:'index.php'
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
                        maxHeight:100,
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
                        loadingText:SUGAR.language.get('app_strings','LBL_SEARCHING'),
                        valueNotFoundText:sqs.no_match_text,
                        hideTrigger:true,
                        confirmed:false,
                        applyTo:typeof(Ext.version)!='undefined'?qsFields[qsField].id:Ext.form.ComboBox.prototype.applyTo,
                        minChars:1,
                        listeners:{
                            select:function(el,type){

                                Ext.EventObject.preventDefault();
                                Ext.EventObject.stopPropagation();
                                if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){
                                    var label_str='';
                                    var label_data_str='';
                                    var current_label_data_str='';
                                    for(var field in type.json){
                                        for(var key in sqs_objects[el.el.id].field_list){
                                            if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])){
                                                if(!sqs_objects[el.el.id].populate_list[key].match(/account/)){
                                                    var data_label=document.getElementById(sqs_objects[el.el.id].populate_list[key]+'_label').innerHTML.replace(/\n/gi,'');
                                                    label_str+=data_label+' \n';
                                                    label_data_str+=data_label+' '+type.json[field]+'\n';
                                                    current_label_data_str+=data_label+' '+document.getElementById(sqs_objects[el.el.id].populate_list[key]).value+'\n'
                                                }
                                            }
                                        }
                                    }
                                    if(label_data_str!=label_str&&current_label_data_str!=label_str){
                                        if(confirm(SUGAR.language.get('app_strings','NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM')+'\n\n'+label_data_str))

                                        {
                                            setFields(type,el,/\S/);
                                        }else{
                                            setFields(type,el,/account/);
                                        }
                                    }else if(label_data_str!=label_str&&current_label_data_str==label_str){
                                        setFields(type,el,/\S/);
                                    }else if(label_data_str==label_str){
                                        setFields(type,el,/account/);
                                    }
                                    el.confirmed=true;
                                }else{
                                    setFields(type,el,/\S/);
                                }
                                if(typeof(sqs_objects[el.el.id]['post_onblur_function'])!='undefined'){
                                    collection_extended=new Array();
                                    for(var field in type.json){
                                        for(var key in sqs_objects[el.el.id].field_list){
                                            if(field==sqs_objects[el.el.id].field_list[key]){
                                                collection_extended[sqs_objects[el.el.id].field_list[key]]=type.json[field];
                                            }
                                        }
                                    }
                                    eval(sqs_objects[el.el.id]['post_onblur_function']+'(collection_extended, el.el.id)');
                                }
                            },
                            autofill:function(el,ev){

                                el.lastQuery="";
                                el.doQuery(el.getRawValue());
                                el.store.on("load",function(){
                                    if(el.confirmed){
                                        el.confirmed=false;
                                    }
                                    else if(el.store.data.items!='undefined'&&el.store.data.items[0]){
                                        el.setRawValue(el.store.data.items[0].json[this.displayField]);
                                        if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){
                                            var label_str='';
                                            var label_data_str='';
                                            var current_label_data_str='';
                                            for(var i=0;i<el.store.data.length;i++){
                                                if(el.store.data.items[i].json[el.displayField]==el.getValue()){
                                                    for(var field in el.store.data.items[i].json){
                                                        for(var key in sqs_objects[el.el.id].field_list){
                                                            if(field==sqs_objects[el.el.id].field_list[key]&&!sqs_objects[el.el.id].populate_list[key].match(/account/)&&document.getElementById(sqs_objects[el.el.id].populate_list[key])){
                                                                var data_label=document.getElementById(sqs_objects[el.el.id].populate_list[key]+'_label').innerHTML.replace(/\n/gi,'');
                                                                label_str+=data_label+' \n';
                                                                label_data_str+=data_label+' '+el.store.data.items[i].json[field]+'\n';
                                                                current_label_data_str+=data_label+' '+document.getElementById(sqs_objects[el.el.id].populate_list[key]).value+'\n';
                                                            }
                                                        }
                                                    }
                                                    break;
                                                }
                                            }
                                            if(label_data_str!=label_str&&current_label_data_str!=label_str){
                                                if(confirm(SUGAR.language.get('app_strings','NTC_OVERWRITE_ADDRESS_PHONE_CONFIRM')+'\n\n'+label_data_str))

                                                {
                                                    setAll(el,this,/\S/);
                                                }else{
                                                    setAll(el,this,/account/);
                                                }
                                                el.confirmed=true;
                                            }else if(label_data_str!=label_str&&current_label_data_str==label_str){
                                                setAll(el,this,/\S/);
                                            }else if(label_data_str==label_str){
                                                setAll(el,this,/account/);
                                            }
                                        }else{
                                            setAll(el,this,/\S/);
                                        }
                                    }
                                    else{
                                        if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){
                                            var selected=clearFields(sqs_objects[el.el.id],/account/);
                                        }else{
                                            var selected=clearFields(sqs_objects[el.el.id],/\S/);
                                        }
                                    }
                                    el.confirmed=false;
                                },this,{
                                    single:true
                                });
                            },
                            blur:function(el){
//                                var selected=false;
//                                if(sqs_objects[el.el.id].populate_list.indexOf('account_id')!=-1){
//                                    setAll(el,this,/account/);
//                                    if(el.getRawValue()==""){
//                                        selected=clearFields(sqs_objects[el.el.id],/account/);
//                                    }
//                                    
//                                }else{
//                                    selected=setAll(el,this,/\S/);
//
//                                    if(el.getRawValue()==""){
//                                        selected=clearFields(sqs_objects[el.el.id],/\S/);
//                                        
//                                    }
//
//                                }
//                                if(!selected){
//                                    el.fireEvent("autofill",el);
//                                }
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
function setAll(el,e,filter){
    var selected=false;
    for(var i=0;i<el.store.data.length;i++){
        if(el.store.data.items[i].json[e.displayField]==el.getValue()){
            for(var field in el.store.data.items[i].json){
                for(var key in sqs_objects[el.el.id].field_list){
                    if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                        document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=el.store.data.items[i].json[field];
                    }
                }
            }
            selected=true;
            break;
        }
    }
    return selected;
}
function setFields(type,el,filter){
    if(type.json['module'] == 'Opportunity'){
        $.get("index.php?module=Opportunities&action=getOpportunitiesPL&return_module=Opportunities&return_action=DetailView&record_id="+type.json['id'], function(data) {
            var pl_value = [];
            var response = eval('('+data+')');
            for(var key in response) {
                pl_value[pl_value.length] = response[key].pid + "," + response[key].pcat  + "," + response[key].pcode;
            }
            var pl_value_input = ( ( pl_value instanceof Array ) ? pl_value.join ( "|" ) : pl_value );

            $("#product_line_hidden").val(pl_value_input);
            YAHOO.util.Event.onContentReady("pline_c", pline_init);
        }, "json");
//        $.get("index.php?module=Opportunities&action=getAccount&return_module=Opportunities&return_action=DetailView&opp_id="+type.json['id'], function(data) {       
//            var response = eval('('+data+')');
//            $("#account_id").val(response[0]['account_id']);
//            $("#account_name_custno").val(response[0]['custno_c']);
//        }, "json");
        for(var field in type.json){
            for(var key in sqs_objects[el.el.id].field_list){
                if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                    if(!isNaN(type.json[field]) && type.json[field] != ''){
                        type.json[field] = parseInt(type.json[field]);
                    }
                    if(sqs_objects[el.el.id].populate_list[key] == 'sales_stage'){
                        type.json[field] = $("#sales_stage option[label="+type.json[field]+"]").val();
                    }
                    document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];
                }
            }
        }
        
        eval_gp_dollar(); eval_month_sales();
    }else{
        for(var field in type.json){
            for(var key in sqs_objects[el.el.id].field_list){
                if(field==sqs_objects[el.el.id].field_list[key]&&document.getElementById(sqs_objects[el.el.id].populate_list[key])&&sqs_objects[el.el.id].populate_list[key].match(filter)){
                    document.getElementById(sqs_objects[el.el.id].populate_list[key]).value=type.json[field];
                }
            }
        }
    }
}
function clearFields(sqs_object,filter){
    for(var key in sqs_object.field_list){
        if(isInteger(key)&&sqs_object.populate_list[key]&&sqs_object.populate_list[key].match(filter))
            document.getElementById(sqs_object.populate_list[key]).value='';
    }
    return true;
}
function preventDef(event){
    if(event.keyCode=='13'){
        event.preventDefault();
    }
}
function registerSingleSmartInputListener(input){
    if((c=input.className)&&(c.indexOf("sqsEnabled")!=-1)){
        if(typeof Ext=='object'){
            enableQS(true);
        }
    }
}
QSFieldsArray=new Array();
if(typeof Ext=='object'){
    enableQS(true);
}// End of File include/javascript/quicksearch.js
                                
