var j = jQuery.noConflict();
"use strict";
var pathArray = window.location.pathname.split('/');
var segment1 = '';
var segment2 = 'lists';
var segment3 = '1';
if(pathArray.length>1){
    segment1 = pathArray[1];
    if(pathArray.length>2){
        segment2 = pathArray[2];
        if(pathArray.length>3){segment3 = pathArray[3];}
    }
}

document.cookie = "screenHeight="+window.innerHeight+"; path=/";
document.cookie = "screenWidth="+window.innerWidth+"; path=/";

function checkLogin(){
	var errMsg = document.getElementById("errRecaptcha");
	errMsg.innerHTML = '';
	var response = checkMathCaptcha();
	if(response !='Checked') {
		errMsg.innerHTML = 'Opps! wrong captcha.';
		return false;
	}
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	
	j.ajax({method: "POST", dataType: "json",
		url: '/Login/checkLoginId',
		data: j("#frmLogin").serialize(),
	}).done(function( data ) {
		if(jQuery.inArray( data.returnStr, ['Home', 'Success'] ) !=-1){
			window.location = '/Home';
		}
		else{
			j("#errMsg").html(data.returnStr);
		}		
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(checkLogin);
	});
	return false;
}

function checkForgotPass(){
	var errMsg = document.getElementById("errRecaptcha");
	errMsg.innerHTML = '';
	var response = checkMathCaptcha();
	if(response !='Checked') {
		errMsg.innerHTML = 'Opps! wrong captcha.';
		return false;
	}
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	
	j.ajax({method: "POST", dataType: "json",
		url: '/Login/AJsendForgotpass',
		data: j("#frmForgotPass").serialize(),
	}).done(function( data ) {
		if(jQuery.inArray( data.returnStr, ['Home', 'Success'] ) !=-1){
			window.location = '/Login/index/sent-success';
		}
		else{
			j("#errMsg").html(data.returnStr);
			j("#users_email").val('');
			if(data.returnStr=='Please check your email for a message from us'){
				window.location = '/Login/index/sent-success';
			}
		}		
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(checkForgotPass);
	});
	return false;
}

function checkNewPass(){
	var errMsg = document.getElementById("errRecaptcha");
	errMsg.innerHTML = '';
	var response = checkMathCaptcha();
	if(response !='Checked') {
		errMsg.innerHTML = 'Opps! wrong captcha.';
		return false;
	}
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	
	j.ajax({method: "POST", dataType: "json",
		url: '/Login/AJsaveNewPass',
		data: j("#frmNewPass").serialize(),
	}).done(function( data ) {
		if(jQuery.inArray( data.returnStr, ['Home', 'Success'] ) !=-1){
			window.location = '/Login/index/password-saved';
		}
		else{
			j("#errMsg").html(data.returnStr);
			j("#users_email").val('');
			if(data.returnStr=='Password changed successfully'){
				window.location = '/Login/index/password-saved';
			}
		}		
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(checkNewPass);
	});
	return false;
}
/*=====================All Pages Help Popup=========================*/
function showHelpPopup(){
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST",dataType: "json",
		url: '/Home/helpForm',
		data: {},
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else{			
			var formhtml = '<form action="#" name="frmhelpform" id="frmhelpform" onsubmit="return sendHelpMail();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
								'<div id="error_msg" class="errormsg"></div>'+
								'<div class="form-group">'+
									'<label for="helpname">Your name</label>'+
									'<input name="helpname" id="helpname" class="form-control helpForm" value="'+data.helpname+'" type="text" size="50" maxlength="50">'+
								'</div>'+
								'<div class="form-group">'+
									'<label for="helpemail">Email<span class="required">*</span></label>'+
									'<input required="required" name="helpemail" id="helpemail" class="form-control helpForm" value="'+data.helpemail+'" type="email" size="50" maxlength="50">'+
								'</div>'+
								'<div class="form-group">'+
									'<label for="helpsubject">Subject<span class="required">*</span></label>'+
									'<input required="required" name="helpsubject" id="helpsubject" class="form-control helpForm" value="" type="text" size="150" maxlength="150">'+
								'</div>'+
								'<div class="form-group">'+
									'<label for="helpdescription">How can we help<span class="required">*</span></label>'+
									'<textarea required="required" name="helpdescription" id="helpdescription" rows="4" class="form-control helpForm"></textarea>'+
								'</div>'+
								'<input type="hidden" name="helpbrowser" id="helpbrowser" value="" />'+
								'<input type="hidden" name="helpurl" id="helpurl" value="" />'+
							'</form>';
			
			form600dialog('Contact Us', formhtml, 'Send', sendHelpMail);
			
			setTimeout(function() {
				document.getElementById("helpbrowser").value = navigator.usersAgent;
				document.getElementById("helpurl").value = window.location.href;
				document.getElementById("helpname").focus();
				checkHelpForm();
				j(".helpForm").keyup(function() {
					checkHelpForm();
				});
			}, 500);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(showHelpPopup);
	});	
}

function checkHelpForm(){
	var returnval = true;
	var helpemail = document.getElementById("helpemail").value;
	if(helpemail==''){
		returnval = false;
	}
	else if(emailcheck(helpemail)==false){
		returnval = false;
	}
	
	var helpsubject = document.getElementById("helpsubject").value;
	if(helpsubject==''){
		returnval = false;
	}
	var helpdescription = document.getElementById("helpdescription").value;
	if(helpdescription==''){
		returnval = false;
	}
	
	if(returnval == false){
		j(".btnmodel").addClass('is-disabled').prop('disabled', true);
		return false;
	}
	else{
		j(".btnmodel").removeClass('is-disabled').prop('disabled', false);
		return true;
	}
}

function sendHelpMail(){
	if(checkHelpForm()==false){return false;}
	else{
		j(".btnmodel").html('Sending...').prop('disabled', true);		
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: '/Home/sendHelpMail',
			data: j("#frmhelpform").serialize(),
		})
		.done(function( data ) {
			if(data.login !=''){window.location = '/'+data.login;}
			else{
				if(data.returnStr=='sent'){					
					j("#form-dialog").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Your request has been successfully sent.</div></div>');
					setTimeout(function() {j("#form-dialog").html('').dialog( "close" );},5000);
				}
				else{
					j("#error_msg").html('Could not sent your mail. Try again.');
					j(".btnmodel").html('Send').prop('disabled', false);
				}
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {
			connection_dialog(sendHelpMail);
		});
		return false;
	}
}

function handleErr(err) {
	const jsonData = {name: err.name, message: err.line, url: document.location.href};
	const options = {method: "POST", body:JSON.stringify(jsonData), headers:{'Content-Type':'application/json'}};
    const url = '/Home/handleErr/';
	
	if(err.line !='undefined'){
		let data = fetch(url, options);
	}
	return new Response(JSON.stringify({code: 400,message: "Opps! Network Error."}));
}

function showTopMessage(parentEvent, msgClass, message){
	parentEvent.innerHTML = '';	
	if(parentEvent.classList.contains('hidden')){
		parentEvent.classList.remove('hidden');
	}
	let div = cTag('div',{ 'class':'columnXS12' });
		let divSuccess = cTag('div',{ 'class':'bs-callout bs-callout-info well '+msgClass });
	if(typeof message=='string'){
		divSuccess.innerHTML = message;
	}
	else{
		divSuccess.append(message);
	}
		
	div.appendChild(divSuccess);
	parentEvent.appendChild(div);
	setTimeout(() => {parentEvent.innerHTML = '';}, 5000);
}

function loadingActive(parentEvent){
	parentEvent.innerHTML = '';
	if(parentEvent.classList.contains('hidden')){
		parentEvent.classList.remove('hidden');
	}
		let disScreen = cTag('div',{ 'class': 'disScreen' });
		disScreen.appendChild(cTag('img',{ 'src': '/assets/images/ajax-loader.gif' }));
    parentEvent.appendChild(disScreen);
}

function checkPhone(fieldIdName,validForNumberOnly) {
	let contactNo = document.getElementById(fieldIdName).value;
	let regex;
	if(validForNumberOnly) regex = /\d+/;
	else regex = /[\d\s()-+.]+/;
	return regex.test(contactNo);
}

function AJremove_tableRow(tableName, tableIdValue, description, redirectURI){
	let table_id = 0;
	if(document.querySelector("#table_id")){
		table_id = document.querySelector("#table_id").value;
	}
	let popUpHml = document.createElement('div');
    popUpHml.appendChild(cTag('input',{ 'type':"hidden", 'id':'tableName', 'value':tableName }));
    popUpHml.appendChild(cTag('input',{ 'type':"hidden", 'id':'table_id', 'value':table_id }));
    popUpHml.appendChild(cTag('input',{ 'type':"hidden", 'id':'tableIdValue', 'value':tableIdValue }));
    popUpHml.appendChild(cTag('input',{ 'type':"hidden", 'id':'description', 'value':description }));
    popUpHml.appendChild(cTag('input',{ 'type':"hidden", 'id':'redirectURI', 'value':redirectURI }));
	popUpHml.append('Are you sure want to remove this information ('+description+')?');
	confirm_dialog('Remove '+description, popUpHml, confirmAJremove_tableRow);
}

async function confirmAJremove_tableRow(){
	let tableName = document.querySelector("#tableName").value;
	let tableIdValue = document.querySelector("#tableIdValue").value;
	let description = document.querySelector("#description").value;
	let redirectURI = document.querySelector("#redirectURI").value;

	const loader = document.querySelector('#showmessagehere');
	loadingActive(loader);

	const jsonData = {"tableName":tableName, "tableIdValue":tableIdValue, "description":description};
	const options = {method: "POST", body:JSON.stringify(jsonData), headers:{'Content-Type':'application/json'}};
	const url = '/Common/AJremove_tableRow/';
	let data = await (await fetch(url, options).catch(handleErr)).json();
	if (data.code && data.code == 400){
		connection_dialog(confirmAJremove_tableRow);
	}
	else{
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg=='Done'){
			if(redirectURI==''){
				let table_id = document.querySelector("#table_id").value;
				if(tableName=='forms_data'){
					document.querySelector( "#form-dialog" ).close();
				}
				else{
					document.querySelector( "#dialog-confirm" ).close();
				}
				checkAndLoadFilterData();
			}
			else{
				window.location = redirectURI;
			}
			showTopMessage(loader,'success_msg',description+' Data removed successfully.');
		}
		else{
			showTopMessage(loader,'error_msg','Could not remove information');
		}
	}
}

/*===================Invoice Module=======================*/
function filter_Appointments_lists(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['snotifications'] = j('#notifications').val();
	jsonData['sorting_type'] = j('#sorting_type').val();
	var sbranches_id = j('#sbranches_id').val();
	jsonData['sbranches_id'] = sbranches_id;
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Appointments/aJgetPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#sbranches_id").html(data.brancIdOpt);
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			j("#sbranches_id").val(sbranches_id);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Appointments_lists);
	});
}

function loadTableRows_Appointments_lists(){
	var jsonData = {};
	jsonData['snotifications'] = j('#notifications').val();
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['sbranches_id'] = j('#sbranches_id').val();
	jsonData['keyword_search'] = j('#keyword_search').val();	
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Appointments/aJgetPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Appointments_lists);
	});
}

function AJrefund_Appointments(invoice_no){
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Refund/AJrefund_Appointments/",
		data: {"invoice_no":invoice_no}
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else if(data.returnStr==''){
			window.location = '/Refund';
		}
		else{
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">'+data.returnStr+'</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(AJrefund_Appointments, invoice_no);
	});	
}

function checkInvoiceMethod(){
	var appointments_id = j("#appointments_id").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Appointments/showpaymentlist",
		data: {"appointments_id":appointments_id},
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#addAppointmentsPaymentList").html(data.returnStr);															
			checkInvoiceTotal();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {															
		connection_dialog(checkInvoiceMethod);
	});
}

function checkInvoiceTotal(){
	var grand_total = parseFloat(j("#grand_total").val());
	if(grand_total=='' || isNaN(grand_total)){grand_total = 0;}
	
	var listcount = j("#addAppointmentsPaymentList tr").length;
	if(listcount>0){														   
		var totalPaid = 0;
		var payment_amountarray = document.getElementsByName("payment_amount[]");
		for(var m = 0; m < listcount; m++) {
			var payment_amount = parseFloat(payment_amountarray[m].value);
			if(payment_amount=='' || isNaN(payment_amount)){payment_amount =0.00;}
			totalPaid = parseFloat(totalPaid+payment_amount);
		}
		grand_total = parseFloat(grand_total-totalPaid);
	}
	
	var prevDues = parseFloat(document.getElementById("prevDues").value);
	if(prevDues=='' || isNaN(prevDues) || prevDues<0){prevDues = 0;}
	grand_total = grand_total+prevDues;
	
	var grand_total = grand_total.toFixed(2);
	if(grand_total>0){
		j(".duerow").slideDown('fast');
		j("#amount").val(grand_total);
		j("#amountDueStr").html(currency+grand_total);
	}
	else{
		j(".duerow").slideUp('fast');
	}
}

function filter_Appointments_view(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	var jsonData = {};
	jsonData['sappointments_id'] = j('#sappointments_id').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Appointments/AJgetHPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#shistory_type").html(data.actFeeTitOpt);
			j("#totalTableRows").val(data.totalRows);
			j("#tableRows").html(data.tableRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Appointments_view);
	});
}

function loadTableRows_Appointments_view(){
	var jsonData = {};
	jsonData['sappointments_id'] = j('#sappointments_id').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Appointments/AJgetHPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Appointments_view);
	});
}

/*=====================Settings:: myInfo Module===============*/
function check_myInfo(){
	var oField = document.frmmyInfo.users_password;
	var oElement = document.getElementById('errmsg_users_password');
	oElement.innerHTML = "";
	var users_passwordstrlength=oField.value.length;
	if(oField.value != ""){
		if(users_passwordstrlength<5){
			oElement.innerHTML = 'Password should be greater than 4 letter';
			oField.focus();
			return(false);
		}
	}
	
	j("#submit").val('Save...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/AJsave_myInfo",
		data: j("#frmmyInfo").serialize(),
	})
	.done(function( data ) {
		
		if(data.login != ''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && data.id>0){
			j("#submit").val('Update').prop('disabled', false);
			
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">'+data.message+'</div></div>').slideDown(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);		
		}
		else{
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">'+data.message+'</div></div>').slideDown(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
			if(document.getElementById("users_id").value==0){
				j("#submit").val('Add').prop('disabled', false);
			}
			else{
				j("#submit").val('Update').prop('disabled', false);
			}
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {							
		connection_dialog(check_myInfo);
	});
	return false;
}

/*=============branches Module============*/
function filter_Settings_branches(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/aJgetPage_branches/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
			setTimeout(function() {
				loadPictureHover();
			}, 500);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Settings_branches);
	});
}

function loadTableRows_Settings_branches(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/aJgetPage_branches",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Settings_branches);
	});
}

function AJsave_branches(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Settings/AJsave_branches/',
		data: j("#frmbranches").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_branches();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Settings_branches();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_branches);
	});
	return false;
}

function resetForm_branches(){
	document.getElementById('formtitle').innerHTML = 'Add New branches';
	document.getElementById('branches_id').value = 0;
	document.getElementById('name').value = '';
	document.getElementById('address').value = '';
	document.getElementById('google_map').value = '';
	document.getElementById('working_hours').value = '';
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*=============Settings:: User Module============*/
function filter_Settings_users(){
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/aJgetPage_users/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Settings_users);
	});
}

function loadTableRows_Settings_users(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/aJgetPage_users",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Settings_users);
	});
}

function checkUserRolls(){
	var is_admin = document.frmusers.is_admin.value;								
	if(is_admin==1){
	   j(".full_access").prop('checked', true);
	}
	
	var dparray = document.getElementsByName("users_roll[]");
	
	if(dparray[0].checked == true){
		j(".users_roll").prop('checked', false).prop('disabled', true);
	}
	else{
		 j(".users_roll").prop('disabled', false);
		var users_rollYN = '';
		for(var d=1; d<dparray.length && users_rollYN==''; d++){
			if(dparray[d].checked == true){
				users_rollYN = 'N';
			}
		}
		
		if(users_rollYN !=''){
			j(".full_access").prop('checked', false);
		}
	}						

	var users_id = document.frmusers.users_id.value;								
	if(users_id>0 && is_admin==0){
		var usersFullName = document.frmusers.users_first_name.value+' '+document.frmusers.users_last_name.value;								
		var onClickAttr = "AJarchive_tableRow('users', 'users_id', "+users_id+", 'User Name: "+usersFullName+"', 'users_published', '');";
		j("#archive").removeClass('hidediv').attr('onClick', onClickAttr);
	}
	
}

function AJsave_users(){
	var checked = j("input[type=checkbox]:checked").length;
	var oElement = document.getElementById('errmsg_users_roll');
	oElement.innerHTML = "";
	if(!checked) {
		oElement.innerHTML = 'You must check at least one checkbox.';
		return false;
	}
	
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Settings/AJsave_users/',
		data: j("#frmusers").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_users();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Settings_users();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			resetForm_users();
			filter_Settings_users();	
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_users);
	});
	return false;
}

function resetForm_users(){
	j("#formtitle").html('Add New User');
	j(".full_access").prop('checked', false);
	j(".users_roll").prop('checked', false).prop('disabled', false);
	j("#users_id").val(0);
	j("#is_admin").val(0);
	j("#users_first_name").val('');
	j("#users_last_name").val('');
	j("#users_email").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

function setUserRoll(users_roll){
	if(users_roll.length<=2){
		j(".full_access").prop('checked', true);
		j(".users_roll").prop('checked', false).prop('disabled', true);
	}
	else{
		j(".full_access").prop('checked', false);
		j(".users_roll").prop('disabled', false);
		
		var users_rolleditarray = jQuery.parseJSON(users_roll);
		var users_rollarray = document.getElementsByName("users_roll[]");
		
		if(users_rollarray.length>0){
			for(var d=0; d<users_rollarray.length; d++){
				var users_roll = users_rollarray[d].value;
				var ck = 0;
				j.each(users_rolleditarray, function( index, valueArray ) {
					if(index==users_roll){
						users_rollarray[d].checked = true;
						ck = 1;
						if(valueArray.length>0){
							var IndexArray = document.getElementsByName(index+"[]");
							if(IndexArray.length>0){
								for(var b=0; b<IndexArray.length; b++){
									var index2Val = IndexArray[b].value;
									var ck2 = 0;
									j.each(valueArray, function( index2, value2 ) {
										if(value2==index2Val){
											IndexArray[b].checked = true;
											ck2 = 1;
										}
									});
									if(ck2==0){
										IndexArray[b].checked = false;
									}
								}
							}
						}
					}
				});
				
				if(ck==0){
					users_rollarray[d].checked = false;
				}
			}
		}
				
	}
	checkUserRolls();
}

/*===================Customers Module=====================*/
function filter_Customers_lists(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJgetPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
					
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Customers_lists);
	});
}

function loadTableRows_Customers_lists(){
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJgetPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Customers_lists);
	});
}

function AJget_CustomersPopup(customers_id){
	var frompage = j("#segment1").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');

	j.ajax({method: "POST", dataType: "json",
		url: '/Customers/aJget_CustomersPopup',
		data: {"customers_id":customers_id}
	}).done(function( data ) {
		
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			if(data.offers_email>0){var checkstr = ' checked';}
			else{var checkstr = ' ';}
			
			var formhtml = '<div class="floatleft padding0 maxheight600">'+
							  '<div id="error_customer" class="errormsg"></div>'+
							'<form action="#" name="frmcustomer" id="frmcustomer" onsubmit="return AJsave_Customers();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
								'<div class="col-sm-12">'+    
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="name">Name<span class="required">*</span></label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input required="required" type="text" class="form-control" name="name" id="name" value="'+data.name+'" maxlength="50" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="email">Email Address</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="email" class="form-control" name="email" id="email" value="'+data.email+'" maxlength="50" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="offers_email">Offers Email</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input autocomplete="off"'+checkstr+' type="checkbox" name="offers_email" id="offers_email" value="1" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="phone">Phone No.</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="tel" class="form-control" name="phone" id="phone" value="'+data.phone+'" maxlength="20" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="address">Address</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="address" id="address" value="'+data.address+'" maxlength="255" />'+
										'</div>'+
									'</div>'+
								'</div>'+ 
								'<input type="hidden" name="frompage" id="frompage" value="'+frompage+'">'+
								'<input type="hidden" name="customers_id" value="'+customers_id+'">'+
							'</form>'+
						'</div>';
			
			form600dialog('Customer Information', formhtml, 'Save', AJsave_Customers);
			
			setTimeout(function() {
				
				j( "#phone" ).keyup(function() {
					checkPhone('phone', 0);
				});
				document.getElementById("name").focus();
			}, 500);			
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJget_CustomersPopup, customers_id);
	});
	
	return true;
}

function AJsave_Customers(){
	var error_customer = document.getElementById("error_customer");
	error_customer.innerHTML = '';
	if(j("#name").val()==''){
		error_customer.innerHTML = 'Missing first name';
		j("#name").focus();
		return false;
	}
	var requiredFields = document.getElementsByClassName("required");
	if(requiredFields.length>0){
		for(var l=0;l<requiredFields.length; l++){
			var oneFieldVal = requiredFields[l].value;
			if(oneFieldVal==''){
				error_customer.innerHTML = requiredFields[l].title+' is missing.';
				requiredFields[l].focus();
				return false;
			}
		}
	}
				
	var customers_id = document.frmcustomer.customers_id.value;
	
	j(".btnmodel").html('Saving...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJsave_Customers/",
		data:j("#frmcustomer").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){
			window.location = '/'+data.login;
		}		
		if(data.savemsg !='error'){
			if(data.customers_id>0){
				window.location = '/Customers/view/'+data.customers_id;
			}
			else{
				document.getElementById('error_customer').innerHTML = data.message;
			}
		}
		else{						
			document.getElementById('error_customer').innerHTML = data.message;
		}		
		j(".btnmodel").html('Save').prop('disabled', false);
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_Customers);
	});
	return false;
}

function AJmergeCustomersPopup(customers_id){
	if(customers_id>0){
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: "/Customers/aJget_CustomersPopup",
			data: {"customers_id":customers_id}
		}).done(function( data ) {
			if(data.login != ''){window.location = '/'+data.login;}
			else{
				var formhtml = '<div id="error_customer" class="errormsg"></div>'+
					'<form action="#" name="frmMergeCustomer" id="frmMergeCustomer" onsubmit="return AJmergeCustomers();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<div class="form-group row">'+
							'<div class="col-sm-12" align="left">'+ 
								'<h4>Merge this customer information</h4>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12 image_content txtleft">'+ 
								'<p>Name: <span>'+data.name+'</span></p>'+
								'<p>Phone No: <span>'+data.phone+'</span></p>'+
								'<p>Email: <span>'+data.email+'</span></p>'+
								'<p>Address: <span>'+data.address+'</span></p>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12" align="left">'+ 
								'<h4>To this customer</h4>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-2" align="left">'+ 
								'<label for="customer_name">Name<span class="required">*</span></label>'+
							'</div>'+
							'<div class="col-sm-10">'+
								'<input maxlength="50" type="text" value="" required name="customer_name" id="customer_name" class="form-control" placeholder="Search Customers">'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12 image_content txtleft" id="toCustomerInfo"></div>'+
						'</div>'+
						'<input type="hidden" name="fromcustomers_id" id="fromcustomers_id" value="'+customers_id+'">'+
						'<input type="hidden" name="tocustomers_id" id="tocustomers_id" value="0">'+
					'</form>';
				
				form600dialog('Merge the following two customers', formhtml, 'Merge Customers', AJmergeCustomers);
				
				setTimeout(function() {		
					document.getElementById("customer_name").focus();
													
					j( "#customer_name" ).autocomplete({
						minLength: 0,
						source: customersData,
						focus: function( event, ui ) {
							return false;
						},
						select: function( event, ui ) {
							j(this).val(ui.item.label);
							j( "#tocustomers_id" ).val( ui.item.id );
							return false;
						}
					});
				}, 500);				
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {					
			connection_dialog(AJmergeCustomersPopup, customers_id);
		});
	}
	return true;
}

function AJmergeCustomers(){
	j("#error_customer").html('');
	if(j("#tocustomers_id").val() ==0){
		j("#error_customer").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Merging customers is empty. Please search and choose different customer.</div></div>').fadeIn(500);
		return false;
	}
	if(j("#fromcustomers_id").val() == j("#tocustomers_id").val()){
		j("#error_customer").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Merging customers is same. Please choose different customer.</div></div>').fadeIn(500);
		return false;
	}
	
	j(".btnmodel").html('Merging Customers...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJmergeCustomers/",
		data: j("#frmMergeCustomer").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg =='Success' && data.id>0){
			window.location = '/Customers/view/'+data.id;
		}
		else{
			j(".btnmodel").html('Merge Customers').prop('disabled', false);
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">There is an error while merging customers information.</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500)}, 5000);			
		}
	})
	.fail(function() {		
		connection_dialog(AJmergeCustomers);
	});
	return false;
}

function sentSMS(name, contact_no, sendType){
	var smstophoneLabel = 'To Phone';
	var popUpTitle = 'Send SMS';
	var fromName = '';
	var subject = '';
	var maxMsg = 159;
	if(sendType=='Email'){
		var smstophoneLabel = 'Email Address';
		var popUpTitle = 'Send Email';
		fromName = '<div class="form-group">'+
						'<label for="smsfromname">From Name</label>'+
						'<input name="smsfromname" id="smsfromname" class="form-control smsForm" value="'+name+'" type="text" size="50" maxlength="50">'+
					'</div>';
		subject = '<div class="form-group">'+
					'<label for="subject">Subject</label>'+
					'<input required name="subject" id="subject" class="form-control" value="" type="text" size="200" maxlength="200">'+
				'</div>';
		var maxMsg = 1000;
	}
	var formhtml = '<form action="#" name="frmsmsform" id="frmsmsform" onsubmit="return sendSMS();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<div id="error_msg" class="errormsg"></div>'+
						fromName+
						'<div class="form-group">'+
							'<label for="smstophone">'+smstophoneLabel+'<span class="required">*</span></label>'+
							'<input readonly required name="smstophone" id="smstophone" class="form-control smsForm" value="'+phone+'" type="text" size="50" maxlength="50">'+
						'</div>'+
						subject+
						'<div class="form-group">'+
							'<label for="smsmessage">Message<span class="required">*</span></label>'+
							'<textarea required name="smsmessage" id="smsmessage" rows="4" class="form-control smsForm" maxlength="'+maxMsg+'"></textarea>'+
							'<input type="hidden" name="sendType" id="sendType" value="'+sendType+'">'+
						'</div>'+
					'</form>';							
	form600dialog(popUpTitle, formhtml, 'Send', sendSMS);
	
	setTimeout(function() {		
		document.getElementById("smsmessage").focus();
		checkSMSForm();
		j( ".smsForm" ).keyup(function() {
			checkSMSForm();
		});
		
	}, 500);
}

function checkSMSForm(){
	var returnval = true;
	var sendType = document.getElementById("sendType").value;
	if(sendType=='Email'){
		var smsfromname = document.getElementById("smsfromname").value;
		if(smsfromname==''){returnval = false;}
	}
	
	var smstophone = document.getElementById("smstophone");
	if(smstophone.value==''){returnval = false;}
	else{
		if(j("#sendType").val()=='Email'){
			returnval = emailcheck(smstophone.value);
		}
		else{
			checkPhone("smstophone", 1);
		}
	}
	
	var smsmessage = document.getElementById("smsmessage").value;
	if(smsmessage==''){
		returnval = false;
	}
	
	if(returnval == false){
		j(".btnmodel").addClass('is-disabled').prop('disabled', true);
		return false;
	}
	else{
		j(".btnmodel").removeClass('is-disabled').prop('disabled', false);
		return true;
	}
}

function sendSMS(){
	if(checkSMSForm()==false){
		return false;
	}
	else{
		j(".btnmodel").html('Sending...').prop('disabled', true);
		var url = '/Home/sendSMS';
		var successMsg = 'Your sms has been sent successfully';
		if(j("#sendType").val()=='Email'){
			var url = '/Customers/sendEmail';
			var successMsg = 'Email sent successfully';
		}
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: url,
			data: j("#frmsmsform").serialize(),
		})
		.done(function( data ) {
			if(data.login != ''){window.location = '/'+data.login;}
			else if(data.returnStr=='sent'){													
				j("#form-dialog").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">'+successMsg+'</div></div>');
				setTimeout(function() {
					j( "#form-dialog" ).html('').dialog( "close" );
				}, 5000);
			}
			else{
				j("#error_msg").html(data.returnStr);
				j(".btnmodel").html('Send').prop('disabled', false);
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {
			connection_dialog(sendSMS);
		});
		return false;
	}
}

function filter_Customers_view(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	var jsonData = {};
	jsonData['customers_id'] = j('#table_idValue').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJgetHPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#shistory_type").html(data.actFeeTitOpt);
			j("#totalTableRows").val(data.totalRows);
			j("#tableRows").html(data.tableRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Customers_view);
	});
}

function loadTableRows_Customers_view(){
	var jsonData = {};
	jsonData['customers_id'] = j('#table_idValue').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Customers/aJgetHPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Customers_view);
	});
}

function checkFieldType(){
	var field_type = j("#field_type").val();
	if(field_type=='DropDown'){
		j("#TextOnlyRow").slideUp('fast');
		j("#DropDownRow").slideDown('fast');
		addDropDownOptions();
	}
	else if(field_type=='TextOnly'){
		j("#DropDownRow").slideUp('fast');
		j("#TextOnlyRow").slideDown('fast');
	}
	else{
		j("#TextOnlyRow").slideUp('fast');
		j("#DropDownRow").slideUp('fast');
	}
}	

function addDropDownOptions(){
	if(j("#field_type").val()=='DropDown'){
		var parametersHTML = '';
		var newRow = true;
		if(document.getElementsByClassName("DropDown").length>0){
			var DropDownObj = document.getElementsByName("DropDown[]");
			for(var l=0; l<DropDownObj.length; l++){
				var oneRow = DropDownObj[l].value;
				if(oneRow ==''){
					parametersHTML += '<li class="ptop0 pbottom0"><input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="'+oneRow+'" maxlength="50" /></li>';
					newRow = false;
				}
				else{
					parametersHTML += '<li class="ptop0 pbottom0">'+
										'<input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="'+oneRow+'" maxlength="50" />'+
										'<a class="removeicon" href="javascript:void(0);" title="Remove this row"><img align="absmiddle" alt="Remove this row" title="Remove this row" src="/assets/images/cross-on-white.gif"></a>'+
									'</li>';
				}
			}
		}
		
		if(newRow){
			parametersHTML += '<li class="ptop0 pbottom0"><input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="" maxlength="50" /></li>';
		}
		j("#DropDownOptions").html(parametersHTML);
		var DropDownObj = document.getElementsByClassName("DropDown");
		DropDownObj[DropDownObj.length-1].focus();
	}
	
	j('.removeicon').click(function(){
		if(j("ul#DropDownOptions").children().length>1){
			j(this).parent('li').slideUp(1000);											
			j(this).parent('li').remove();
		}
		else{
			alert_dialog('Remove Dropdown Option', 'You could not remove all Dropdown options.', 'Ok');
		}
	});
}

function AJorderup_custom_fields(order_val, custom_fields_id, precustom_fields_id){
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/AJorderup_custom_fields",
		data: {"order_val":order_val, "custom_fields_id":custom_fields_id, "precustom_fields_id":precustom_fields_id}
	}).done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			location.reload();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(AJorderup_custom_fields, order_val, custom_fields_id, precustom_fields_id);
	});
}

function archiveCustomers(){
	confirm_dialog('Customer Archive', 'Are you sure you want to archive this information?', archiveCustomerconfirm);
}

function archiveCustomerconfirm(){
	var customers_id = document.getElementById('customers_id');
	var customer_name = document.getElementById('customer_name');						
	j( ".archive").slideUp(500);
	
	archiveData('/Manage_Data/jquerycustomers_archive/', {"customer_name":customer_name.value, "customers_id":customers_id.value}, 'Customer');
	j( "#dialog-confirm" ).dialog( "close" );
	customer_name.value = '';
}

function archiveData(uri, sendingData, msgTitle){                        
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: uri,
		data: sendingData,
	})
	.done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else if(data.returnStr =='archive-success'){
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">'+msgTitle+' archived successfully</div></div>').fadeIn(500);
		   setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		else{
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">'+data.returnStr+'</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(archiveData, uri, sendingData, msgTitle);
	});	
	return false;
}


/*===================Orders Module=====================*/
function filter_Orders_lists(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJgetPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
					
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Orders_lists);
	});
}

function loadTableRows_Orders_lists(){
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJgetPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Orders_lists);
	});
}

function AJget_OrdersPopup(pos_id){
	var frompage = j("#segment1").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');

	j.ajax({method: "POST", dataType: "json",
		url: '/Orders/aJget_OrdersPopup',
		data: {"pos_id":pos_id}
	}).done(function( data ) {
		
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			if(data.offers_email>0){var checkstr = ' checked';}
			else{var checkstr = ' ';}
			
			var formhtml = '<div class="floatleft padding0 maxheight600">'+
							  '<div id="error_customer" class="errormsg"></div>'+
							'<form action="#" name="frmcustomer" id="frmcustomer" onsubmit="return AJsave_Orders();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
								'<div class="col-sm-12">'+    
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="name">Name<span class="required">*</span></label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input required="required" type="text" class="form-control" name="name" id="name" value="'+data.name+'" maxlength="50" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="email">Email Address</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="email" class="form-control" name="email" id="email" value="'+data.email+'" maxlength="50" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="offers_email">Offers Email</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input autocomplete="off"'+checkstr+' type="checkbox" name="offers_email" id="offers_email" value="1" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="phone">Phone No.</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="tel" class="form-control" name="phone" id="phone" value="'+data.phone+'" maxlength="20" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="address">Address</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="address" id="address" value="'+data.address+'" maxlength="255" />'+
										'</div>'+
									'</div>'+
								'</div>'+ 
								'<input type="hidden" name="frompage" id="frompage" value="'+frompage+'">'+
								'<input type="hidden" name="pos_id" value="'+pos_id+'">'+
							'</form>'+
						'</div>';
			
			form600dialog('Customer Information', formhtml, 'Save', AJsave_Orders);
			
			setTimeout(function() {
				
				j( "#phone" ).keyup(function() {
					checkPhone('phone', 0);
				});
				document.getElementById("name").focus();
			}, 500);			
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJget_OrdersPopup, pos_id);
	});
	
	return true;
}

function AJsave_Orders(){
	var error_customer = document.getElementById("error_customer");
	error_customer.innerHTML = '';
	if(j("#name").val()==''){
		error_customer.innerHTML = 'Missing first name';
		j("#name").focus();
		return false;
	}
	var requiredFields = document.getElementsByClassName("required");
	if(requiredFields.length>0){
		for(var l=0;l<requiredFields.length; l++){
			var oneFieldVal = requiredFields[l].value;
			if(oneFieldVal==''){
				error_customer.innerHTML = requiredFields[l].title+' is missing.';
				requiredFields[l].focus();
				return false;
			}
		}
	}
				
	var pos_id = document.frmcustomer.pos_id.value;
	
	j(".btnmodel").html('Saving...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJsave_Orders/",
		data:j("#frmcustomer").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){
			window.location = '/'+data.login;
		}		
		if(data.savemsg !='error'){
			if(data.pos_id>0){
				window.location = '/Orders/view/'+data.pos_id;
			}
			else{
				document.getElementById('error_customer').innerHTML = data.message;
			}
		}
		else{						
			document.getElementById('error_customer').innerHTML = data.message;
		}		
		j(".btnmodel").html('Save').prop('disabled', false);
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_Orders);
	});
	return false;
}

function AJmergeOrdersPopup(pos_id){
	if(pos_id>0){
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: "/Orders/aJget_OrdersPopup",
			data: {"pos_id":pos_id}
		}).done(function( data ) {
			if(data.login != ''){window.location = '/'+data.login;}
			else{
				var formhtml = '<div id="error_customer" class="errormsg"></div>'+
					'<form action="#" name="frmMergeCustomer" id="frmMergeCustomer" onsubmit="return AJmergeOrders();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<div class="form-group row">'+
							'<div class="col-sm-12" align="left">'+ 
								'<h4>Merge this customer information</h4>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12 image_content txtleft">'+ 
								'<p>Name: <span>'+data.name+'</span></p>'+
								'<p>Phone No: <span>'+data.phone+'</span></p>'+
								'<p>Email: <span>'+data.email+'</span></p>'+
								'<p>Address: <span>'+data.address+'</span></p>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12" align="left">'+ 
								'<h4>To this customer</h4>'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-2" align="left">'+ 
								'<label for="customer_name">Name<span class="required">*</span></label>'+
							'</div>'+
							'<div class="col-sm-10">'+
								'<input maxlength="50" type="text" value="" required name="customer_name" id="customer_name" class="form-control" placeholder="Search Orders">'+
							'</div>'+
						'</div>'+
						'<div class="form-group row">'+
							'<div class="col-sm-12 image_content txtleft" id="toCustomerInfo"></div>'+
						'</div>'+
						'<input type="hidden" name="frompos_id" id="frompos_id" value="'+pos_id+'">'+
						'<input type="hidden" name="topos_id" id="topos_id" value="0">'+
					'</form>';
				
				form600dialog('Merge the following two customers', formhtml, 'Merge Orders', AJmergeOrders);
				
				setTimeout(function() {		
					document.getElementById("customer_name").focus();
													
					j( "#customer_name" ).autocomplete({
						minLength: 0,
						source: customersData,
						focus: function( event, ui ) {
							return false;
						},
						select: function( event, ui ) {
							j(this).val(ui.item.label);
							j( "#topos_id" ).val( ui.item.id );
							return false;
						}
					});
				}, 500);				
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {					
			connection_dialog(AJmergeOrdersPopup, pos_id);
		});
	}
	return true;
}

function AJmergeOrders(){
	j("#error_customer").html('');
	if(j("#topos_id").val() ==0){
		j("#error_customer").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Merging customers is empty. Please search and choose different customer.</div></div>').fadeIn(500);
		return false;
	}
	if(j("#frompos_id").val() == j("#topos_id").val()){
		j("#error_customer").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Merging customers is same. Please choose different customer.</div></div>').fadeIn(500);
		return false;
	}
	
	j(".btnmodel").html('Merging Orders...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJmergeOrders/",
		data: j("#frmMergeCustomer").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg =='Success' && data.id>0){
			window.location = '/Orders/view/'+data.id;
		}
		else{
			j(".btnmodel").html('Merge Orders').prop('disabled', false);
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">There is an error while merging customers information.</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500)}, 5000);			
		}
	})
	.fail(function() {		
		connection_dialog(AJmergeOrders);
	});
	return false;
}

function sentSMS(name, contact_no, sendType){
	var smstophoneLabel = 'To Phone';
	var popUpTitle = 'Send SMS';
	var fromName = '';
	var subject = '';
	var maxMsg = 159;
	if(sendType=='Email'){
		var smstophoneLabel = 'Email Address';
		var popUpTitle = 'Send Email';
		fromName = '<div class="form-group">'+
						'<label for="smsfromname">From Name</label>'+
						'<input name="smsfromname" id="smsfromname" class="form-control smsForm" value="'+name+'" type="text" size="50" maxlength="50">'+
					'</div>';
		subject = '<div class="form-group">'+
					'<label for="subject">Subject</label>'+
					'<input required name="subject" id="subject" class="form-control" value="" type="text" size="200" maxlength="200">'+
				'</div>';
		var maxMsg = 1000;
	}
	var formhtml = '<form action="#" name="frmsmsform" id="frmsmsform" onsubmit="return sendSMS();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<div id="error_msg" class="errormsg"></div>'+
						fromName+
						'<div class="form-group">'+
							'<label for="smstophone">'+smstophoneLabel+'<span class="required">*</span></label>'+
							'<input readonly required name="smstophone" id="smstophone" class="form-control smsForm" value="'+phone+'" type="text" size="50" maxlength="50">'+
						'</div>'+
						subject+
						'<div class="form-group">'+
							'<label for="smsmessage">Message<span class="required">*</span></label>'+
							'<textarea required name="smsmessage" id="smsmessage" rows="4" class="form-control smsForm" maxlength="'+maxMsg+'"></textarea>'+
							'<input type="hidden" name="sendType" id="sendType" value="'+sendType+'">'+
						'</div>'+
					'</form>';							
	form600dialog(popUpTitle, formhtml, 'Send', sendSMS);
	
	setTimeout(function() {		
		document.getElementById("smsmessage").focus();
		checkSMSForm();
		j( ".smsForm" ).keyup(function() {
			checkSMSForm();
		});
		
	}, 500);
}

function checkSMSForm(){
	var returnval = true;
	var sendType = document.getElementById("sendType").value;
	if(sendType=='Email'){
		var smsfromname = document.getElementById("smsfromname").value;
		if(smsfromname==''){returnval = false;}
	}
	
	var smstophone = document.getElementById("smstophone");
	if(smstophone.value==''){returnval = false;}
	else{
		if(j("#sendType").val()=='Email'){
			returnval = emailcheck(smstophone.value);
		}
		else{
			checkPhone("smstophone", 1);
		}
	}
	
	var smsmessage = document.getElementById("smsmessage").value;
	if(smsmessage==''){
		returnval = false;
	}
	
	if(returnval == false){
		j(".btnmodel").addClass('is-disabled').prop('disabled', true);
		return false;
	}
	else{
		j(".btnmodel").removeClass('is-disabled').prop('disabled', false);
		return true;
	}
}

function sendSMS(){
	if(checkSMSForm()==false){
		return false;
	}
	else{
		j(".btnmodel").html('Sending...').prop('disabled', true);
		var url = '/Home/sendSMS';
		var successMsg = 'Your sms has been sent successfully';
		if(j("#sendType").val()=='Email'){
			var url = '/Orders/sendEmail';
			var successMsg = 'Email sent successfully';
		}
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: url,
			data: j("#frmsmsform").serialize(),
		})
		.done(function( data ) {
			if(data.login != ''){window.location = '/'+data.login;}
			else if(data.returnStr=='sent'){													
				j("#form-dialog").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">'+successMsg+'</div></div>');
				setTimeout(function() {
					j( "#form-dialog" ).html('').dialog( "close" );
				}, 5000);
			}
			else{
				j("#error_msg").html(data.returnStr);
				j(".btnmodel").html('Send').prop('disabled', false);
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {
			connection_dialog(sendSMS);
		});
		return false;
	}
}

function filter_Orders_view(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	var jsonData = {};
	jsonData['pos_id'] = j('#table_idValue').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJgetHPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#shistory_type").html(data.actFeeTitOpt);
			j("#totalTableRows").val(data.totalRows);
			j("#tableRows").html(data.tableRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Orders_view);
	});
}

function loadTableRows_Orders_view(){
	var jsonData = {};
	jsonData['pos_id'] = j('#table_idValue').val();
	jsonData['shistory_type'] = j('#shistory_type').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Orders/aJgetHPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Orders_view);
	});
}

function checkFieldType(){
	var field_type = j("#field_type").val();
	if(field_type=='DropDown'){
		j("#TextOnlyRow").slideUp('fast');
		j("#DropDownRow").slideDown('fast');
		addDropDownOptions();
	}
	else if(field_type=='TextOnly'){
		j("#DropDownRow").slideUp('fast');
		j("#TextOnlyRow").slideDown('fast');
	}
	else{
		j("#TextOnlyRow").slideUp('fast');
		j("#DropDownRow").slideUp('fast');
	}
}	

function addDropDownOptions(){
	if(j("#field_type").val()=='DropDown'){
		var parametersHTML = '';
		var newRow = true;
		if(document.getElementsByClassName("DropDown").length>0){
			var DropDownObj = document.getElementsByName("DropDown[]");
			for(var l=0; l<DropDownObj.length; l++){
				var oneRow = DropDownObj[l].value;
				if(oneRow ==''){
					parametersHTML += '<li class="ptop0 pbottom0"><input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="'+oneRow+'" maxlength="50" /></li>';
					newRow = false;
				}
				else{
					parametersHTML += '<li class="ptop0 pbottom0">'+
										'<input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="'+oneRow+'" maxlength="50" />'+
										'<a class="removeicon" href="javascript:void(0);" title="Remove this row"><img align="absmiddle" alt="Remove this row" title="Remove this row" src="/assets/images/cross-on-white.gif"></a>'+
									'</li>';
				}
			}
		}
		
		if(newRow){
			parametersHTML += '<li class="ptop0 pbottom0"><input type="text" class="form-control mbottom10 DropDown" name="DropDown[]" value="" maxlength="50" /></li>';
		}
		j("#DropDownOptions").html(parametersHTML);
		var DropDownObj = document.getElementsByClassName("DropDown");
		DropDownObj[DropDownObj.length-1].focus();
	}
	
	j('.removeicon').click(function(){
		if(j("ul#DropDownOptions").children().length>1){
			j(this).parent('li').slideUp(1000);											
			j(this).parent('li').remove();
		}
		else{
			alert_dialog('Remove Dropdown Option', 'You could not remove all Dropdown options.', 'Ok');
		}
	});
}

function AJorderup_custom_fields(order_val, custom_fields_id, precustom_fields_id){
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Settings/AJorderup_custom_fields",
		data: {"order_val":order_val, "custom_fields_id":custom_fields_id, "precustom_fields_id":precustom_fields_id}
	}).done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			location.reload();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(AJorderup_custom_fields, order_val, custom_fields_id, precustom_fields_id);
	});
}

function archiveOrders(){
	confirm_dialog('Customer Archive', 'Are you sure you want to archive this information?', archiveCustomerconfirm);
}

function archiveCustomerconfirm(){
	var pos_id = document.getElementById('pos_id');
	var customer_name = document.getElementById('customer_name');						
	j( ".archive").slideUp(500);
	
	archiveData('/Manage_Data/jquerycustomers_archive/', {"customer_name":customer_name.value, "pos_id":pos_id.value}, 'Customer');
	j( "#dialog-confirm" ).dialog( "close" );
	customer_name.value = '';
}

function archiveData(uri, sendingData, msgTitle){                        
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: uri,
		data: sendingData,
	})
	.done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else if(data.returnStr =='archive-success'){
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">'+msgTitle+' archived successfully</div></div>').fadeIn(500);
		   setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		else{
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">'+data.returnStr+'</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(archiveData, uri, sendingData, msgTitle);
	});	
	return false;
}


/*=====================Manage Data============================*/

async function checkExportType(){
	document.querySelector('#date_range').value = '';
	let export_type = document.querySelector("#export_type").value;
	if(export_type==''){
        if(!document.querySelector("#allthreecolumn").classList.contains('hidden')){
            document.querySelector("#allthreecolumn").classList.add('hidden');
        }
	}
	else{
		document.querySelector("#error_fieldsname").innerHTML = "";
        if(document.querySelector("#allthreecolumn").classList.contains('hidden')){
            document.querySelector("#allthreecolumn").classList.remove('hidden');
        }

		if(export_type=='customer'){
            document.querySelectorAll(".customerFilters").forEach(oneField=>{
                if(oneField.classList.contains('hidden')){
                    oneField.classList.remove('hidden');
                }
            });
        }
		else{
			document.querySelectorAll(".customerFilters").forEach(oneField=>{
					if(!oneField.classList.contains('hidden')){
						oneField.classList.add('hidden');
					}
			});
		}

        const loader = document.querySelector('#showmessagehere');
        loadingActive(loader);

        const jsonData = {export_type:export_type};
        const options = {method: "POST", body:JSON.stringify(jsonData), headers:{'Content-Type':'application/json'}};
        const url = '/Manage_Data/exportFieldsList';
        let data = await (await fetch(url, options).catch(handleErr)).json();

        if (data.code && data.code == 400){
            connection_dialog(checkExportType);
        }
        else{
            if(data.login != ''){window.location = '/'+data.login;}
            else{
				let fieldsList = document.querySelector("#fieldsList");
                fieldsList.innerHTML = data.fieldsList;
				document.querySelector("#lbdate_range").innerHTML = data.lbdate_range;
				j("#allthreecolumn").slideDown('slow');
			}
			loader.innerHTML = '';
        }		
	}
	return false;
}

function createURI(firstField, secondField, getField, Slug=0){
	if(getField==0){
		var nameObj = j("#"+firstField);
		var errorid = document.getElementById('error_'+firstField);
	}
	else{
		var nameObj = j("#"+secondField);
		var errorid = document.getElementById('error_'+secondField);
	}
	var nameValue = nameObj.val();
	errorid.innerHTML = '';
	if(!['', '#'].includes(nameValue)){
		var ValidChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-";
		if(Slug==1){
			ValidChars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-/";
		}
		var IsValid=true;
		var Char;
		var uriValue = '';
		for (var i = 0; i < nameValue.length; i++){ 
			Char = nameValue.charAt(i); 
			// Char = Char.replace(" ", '-');
			Char = Char.replace("'", '');
			Char = Char.replace('"', '');
			Char = Char.replace('.', '');
			Char = Char.replace(',', '');
			Char = Char.replace('!', '');
			if(Slug==0){
				Char = Char.replace('/', '-or-');
			}
			Char = Char.replace('?', '');
			Char = Char.replace('&', 'and');								
			Char = Char.replace('$', 'dollar');
			Char = Char.replace('+', '-plus-');
			Char = Char.replace('&amp;', 'and');
			Char = Char.replace('(', '');
			Char = Char.replace(')', '');
			uriValue = uriValue+Char;
		}
		
		uriValue = uriValue.replace('--', '-');
		var validURIValue = '';
		for (i = 0; i < uriValue.length && IsValid == true; i++){ 
			Char = uriValue.charAt(i); 
			// if (ValidChars.indexOf(Char) == -1){
			// 	IsValid = false;
			// 	validURIValue = validURIValue.substring(0,100);
			// 	j("#"+secondField).val(validURIValue.toLowerCase());
			// 	errorid.innerHTML = 'Invalid URI Value';
			// 	j("#"+secondField).focus();
			// 	return false;
			// }
			// else{
			// 	validURIValue = validURIValue+Char;
			// }
		}
		
		validURIValue = validURIValue.substring(0,100);
		j("#"+secondField).val(validURIValue.toLowerCase());			
		if(IsValid == false){
			return false;
		}
		else{
			return true;
		}
	}
}


/*=============Photo Gallery Module============*/
function filter_Manage_Data_photo_gallery(){
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_photo_gallery/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_photo_gallery);
	});
}

function loadTableRows_Manage_Data_photo_gallery(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_photo_gallery",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_photo_gallery);
	});
}

function AJsave_photo_gallery(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_photo_gallery/',
		data: j("#frmphoto_gallery").serialize(),
	})
	.done(function( data ) {		
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_photo_gallery();			
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-xs-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{				
				j("#showmessagehere").html('<div class="col-xs-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_photo_gallery();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{			
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(AJsave_photo_gallery);
	});
	return false;
}

function resetForm_photo_gallery(){
	j("#formtitle").html('Add New Photo Gallery');
	j("#photo_gallery_id").val(0);
	j("#name").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*=============Front Menu Module============*/
function filter_Manage_Data_front_menu(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_front_menu/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_front_menu);
	});
}

function loadTableRows_Manage_Data_front_menu(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_front_menu",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_front_menu);
	});
}

function AJsave_front_menu(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_front_menu/',
		data: j("#frmfront_menu").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_front_menu();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_front_menu();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_front_menu);
	});
	return false;
}

function resetForm_front_menu(){
	j("#formtitle").html('Add New Menu');
	j("#front_menu_id").val(0);
	j("#root_menu_id").val(0);
	j("#sub_menu_id").val(0);
	j("#name").val('');
	j("#menu_uri").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*=============Banners Module============*/
function filter_Manage_Data_banners(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_banners/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
			setTimeout(function() {
				loadPictureHover();
			}, 500);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_banners);
	});
}

function loadTableRows_Manage_Data_banners(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_banners",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_banners);
	});
}

function AJsave_banners(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_banners/',
		data: j("#frmbanners").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_banners();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_banners();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_banners);
	});
	return false;
}

function resetForm_banners(){
	document.getElementById('formtitle').innerHTML = 'Add New Banners';
	document.getElementById('banners_id').value = 0;
	document.getElementById('name').value = '';
	document.getElementById('description').value = '';
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*=============Pages Module============*/
function filter_Manage_Data_pages(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_pages/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_pages);
	});
}

function loadTableRows_Manage_Data_pages(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_pages",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_pages);
	});
}

function AJsave_pages(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_pages/',
		data: j("#frmpages").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_pages();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_pages();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_pages);
	});
	return false;
}

function resetForm_pages(){
	j("#formtitle").html('Add New Pages');
	j("#pages_id").val(0);
	j("#name").val('');
	j("#uri_value").val('');
	j("#short_description").val('');
	j("#description").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
	//let editor = document.getElementById('wysiwyrEditor');
	//editor.querySelector("#description").innerHTML = editor.querySelector("#editingArea").contentWindow.document.body.innerHTML = '';
}

/*=============Customer Reviews Module============*/
function filter_Manage_Data_customer_reviews(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_customer_reviews/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_customer_reviews);
	});
}

function loadTableRows_Manage_Data_customer_reviews(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_customer_reviews",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_customer_reviews);
	});
}

function AJsave_customer_reviews(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_customer_reviews/',
		data: j("#frmcustomer_reviews").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_customer_reviews();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_customer_reviews();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_customer_reviews);
	});
	return false;
}

function resetForm_customer_reviews(){
	j("#formtitle").html('Add New Customer Reviews');
	j("#customer_reviews_id").val(0);
	j("#name").val('');
	j("#address").val('');
	j("#reviews_date").val('');
	j("#reviews_rating").val(5);
	j("#description").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
	//let editor = document.getElementById('wysiwyrEditor');
	//editor.querySelector("#description").innerHTML = editor.querySelector("#editingArea").contentWindow.document.body.innerHTML = '';
}

/*===================News & Articles Module=====================*/
function filter_News_articles_lists(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/News_articles/aJgetPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
					
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_News_articles_lists);
	});
}

function loadTableRows_News_articles_lists(){
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/News_articles/aJgetPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_News_articles_lists);
	});
}

function AJget_News_articlesPopup(news_articles_id){
	var frompage = j("#segment1").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');

	j.ajax({method: "POST", dataType: "json",
		url: '/News_articles/aJget_News_articlesPopup',
		data: {"news_articles_id":news_articles_id}
	}).done(function( data ) {
		
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			
			var formhtml = '<div class="floatleft padding0 maxheight600">'+
							  '<div id="error_Fields" class="errormsg"></div>'+
							'<form action="#" name="frmFields" id="frmFields" onsubmit="return AJsave_News_articles();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
								'<div class="col-sm-12">'+    
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="name">Name<span class="required">*</span></label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input required="required" type="text" class="form-control" name="name" id="name" value="'+data.name+'" onKeyUp="createURI(\'name\', \'uri_value\', 0);" maxlength="100" />'+
											'<span id="error_name" class="errormsg"></span>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="uri_value">URI Value</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="uri_value" id="uri_value" value="'+data.uri_value+'" onKeyUp="createURI(\'name\', \'uri_value\', 1);" maxlength="100" />'+
											'<span id="error_uri_value" class="errormsg"></span>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="news_articles_date">News Date</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control DateField" name="news_articles_date" id="news_articles_date" value="'+data.news_articles_date+'" maxlength="10" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="created_by">Created By</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="created_by" id="created_by" value="'+data.created_by+'" maxlength="100" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="short_description">Short Description</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<textarea cols="40" rows="2" class="form-control" name="short_description" id="short_description">'+data.short_description+'</textarea>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="description">Description</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<textarea cols="40" rows="4" class="form-control" name="description" id="description">'+data.description+'</textarea>'+
										'</div>'+
									'</div>'+
								'</div>'+ 
								'<input type="hidden" name="frompage" id="frompage" value="'+frompage+'">'+
								'<input type="hidden" name="news_articles_id" value="'+news_articles_id+'">'+
							'</form>'+
						'</div>';
			
			form600dialog('News & Articles Information', formhtml, 'Save', AJsave_News_articles);
			
			setTimeout(function() {
				document.getElementById("name").focus();
				loadDateFunction();
			}, 500);			
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJget_News_articlesPopup, news_articles_id);
	});
	
	return true;
}

function AJsave_News_articles(){
	var error_Fields = document.getElementById("error_Fields");
	error_Fields.innerHTML = '';
	if(j("#name").val()==''){
		error_Fields.innerHTML = 'Missing Name';
		j("#name").focus();
		return false;
	}
	var requiredFields = document.getElementsByClassName("required");
	if(requiredFields.length>0){
		for(var l=0;l<requiredFields.length; l++){
			var oneFieldVal = requiredFields[l].value;
			if(oneFieldVal==''){
				error_Fields.innerHTML = requiredFields[l].title+' is missing.';
				requiredFields[l].focus();
				return false;
			}
		}
	}
				
	var news_articles_id = document.frmFields.news_articles_id.value;
	
	j(".btnmodel").html('Saving...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/News_articles/aJsave_News_articles/",
		data:j("#frmFields").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){
			window.location = '/'+data.login;
		}		
		if(data.savemsg !='error'){
			if(data.news_articles_id>0){
				window.location = '/News_articles/view/'+data.news_articles_id;
			}
			else{
				document.getElementById('error_Fields').innerHTML = data.message;
			}
		}
		else{						
			document.getElementById('error_Fields').innerHTML = data.message;
		}		
		j(".btnmodel").html('Save').prop('disabled', false);
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_News_articles);
	});
	return false;
}

/*===================Services Module=====================*/
function filter_Services_lists(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Services/aJgetPage/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
					
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Services_lists);
	});
}

function loadTableRows_Services_lists(){
	var jsonData = {};
	jsonData['sorting_type'] = j('#sorting_type').val();
	jsonData['keyword_search'] = j('#keyword_search').val();
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Services/aJgetPage",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Services_lists);
	});
}

function AJget_ServicesPopup(services_id){
	var frompage = j("#segment1").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');

	j.ajax({method: "POST", dataType: "json",
		url: '/Services/aJget_ServicesPopup',
		data: {"services_id":services_id}
	}).done(function( data ) {
		
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			
			var formhtml = '<div class="floatleft padding0 maxheight600">'+
							  '<div id="error_Fields" class="errormsg"></div>'+
							'<form action="#" name="frmFields" id="frmFields" onsubmit="return AJsave_Services();" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
								'<div class="col-sm-12">'+    
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="name">Name<span class="required">*</span></label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input required="required" type="text" class="form-control" name="name" id="name" value="'+data.name+'" onKeyUp="createURI(\'name\', \'uri_value\', 0);" maxlength="100" />'+
											'<span id="error_name" class="errormsg"></span>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="uri_value">URI Value</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="uri_value" id="uri_value" value="'+data.uri_value+'" onKeyUp="createURI(\'name\', \'uri_value\', 1);" maxlength="100" />'+
											'<span id="error_uri_value" class="errormsg"></span>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="font_awesome">Font Awesome</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<input type="text" class="form-control" name="font_awesome" id="font_awesome" value="'+data.font_awesome+'" maxlength="100" />'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="short_description">Short Description</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<textarea cols="40" rows="2" class="form-control" name="short_description" id="short_description">'+data.short_description+'</textarea>'+
										'</div>'+
									'</div>'+
									'<div class="form-group row">'+
										'<div class="col-sm-4" align="left">'+ 
											'<label for="description">Description</label>'+
										'</div>'+
										'<div class="col-sm-8" align="left">'+
											'<textarea cols="40" rows="4" class="form-control" name="description" id="description">'+data.description+'</textarea>'+
										'</div>'+
									'</div>'+
								'</div>'+ 
								'<input type="hidden" name="frompage" id="frompage" value="'+frompage+'">'+
								'<input type="hidden" name="services_id" value="'+services_id+'">'+
							'</form>'+
						'</div>';
			
			form600dialog('Services Information', formhtml, 'Save', AJsave_Services);
			
			setTimeout(function() {
				document.getElementById("name").focus();
			}, 500);			
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJget_ServicesPopup, services_id);
	});
	
	return true;
}

function AJsave_Services(){
	var error_Fields = document.getElementById("error_Fields");
	error_Fields.innerHTML = '';
	if(j("#name").val()==''){
		error_Fields.innerHTML = 'Missing Name';
		j("#name").focus();
		return false;
	}
	var requiredFields = document.getElementsByClassName("required");
	if(requiredFields.length>0){
		for(var l=0;l<requiredFields.length; l++){
			var oneFieldVal = requiredFields[l].value;
			if(oneFieldVal==''){
				error_Fields.innerHTML = requiredFields[l].title+' is missing.';
				requiredFields[l].focus();
				return false;
			}
		}
	}
				
	var services_id = document.frmFields.services_id.value;
	
	j(".btnmodel").html('Saving...').prop('disabled', true);
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Services/aJsave_Services/",
		data:j("#frmFields").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){
			window.location = '/'+data.login;
		}		
		if(data.savemsg !='error'){
			if(data.services_id>0){
				window.location = '/Services/view/'+data.services_id;
			}
			else{
				document.getElementById('error_Fields').innerHTML = data.message;
			}
		}
		else{						
			document.getElementById('error_Fields').innerHTML = data.message;
		}		
		j(".btnmodel").html('Save').prop('disabled', false);
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_Services);
	});
	return false;
}

/*============= Why choose us Module ============*/
function filter_Manage_Data_why_choose_us(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_why_choose_us/filter",
		data: jsonData,
	}).done(function(data){
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
			setTimeout(function(){
				loadPictureHover();
			}, 500);
		}

		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_why_choose_us);
	});
}

function loadTableRows_Manage_Data_why_choose_us(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_why_choose_us",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_why_choose_us);
	});
}

function AJsave_why_choose_us(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_why_choose_us/',
		data: j("#frmwhy_choose_us").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_why_choose_us();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_why_choose_us();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_why_choose_us);
	});
	return false;
}

function resetForm_why_choose_us(){
	document.getElementById('formtitle').innerHTML = 'Add New Why choose us';
	document.getElementById('why_choose_us_id').value = 0;
	document.getElementById('name').value = '';
	document.getElementById('description').value = '';
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*============= Pest Service Module ============*/
function filter_Manage_Data_pest_services(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_pest_services/filter",
		data: jsonData,
	}).done(function(data){
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
			setTimeout(function(){
				loadPictureHover();
			}, 500);
		}

		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_pest_services);
	});
}

function loadTableRows_Manage_Data_pest_services(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_pest_services",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_pest_services);
	});
}

function AJsave_pest_services(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_pest_services/',
		data: j("#frmpest_services").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_pest_services();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_pest_services();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_pest_services);
	});
	return false;
}

function resetForm_pest_services(){
	document.getElementById('formtitle').innerHTML = 'Add New Why choose us';
	document.getElementById('pest_services_id').value = 0;
	document.getElementById('name').value = '';
	document.getElementById('price').value = '';
	document.getElementById('description').value = '';
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}

/*=============Videos Module============*/
function filter_Manage_Data_videos(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_videos/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_videos);
	});
}

function loadTableRows_Manage_Data_videos(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_videos",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_videos);
	});
}

function AJsave_videos(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_videos/',
		data: j("#frmvideos").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_videos();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_videos();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_videos);
	});
	return false;
}

function resetForm_videos(){
	j("#formtitle").html('Add New Video');
	j("#videos_id").val(0);
	j("#name").val('');
	j("#youtube_url").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
}
/*=====================Commonly used=================*/

function fieldFocus(){
	loadPriceField();
	loadQtyField();
}

function loadPriceField(){
	j( ".pricefield" ).focus(function() {
		var price = j(this).val();
		if(price==0){j(this).val('');}
	});
	j( ".pricefield" ).blur(function() {
		var price = j(this).val();
		if(price==''){j(this).val(0);}
	});		
	j( ".pricefield" ).keyup(function() {
		var price = j(this).val();
		var ValidChars = ".0123456789-";
		var IsNumber=true;
		var Char;
		var validint = '';
		for (var i = 0; i < price.length && IsNumber == true; i++){ 
			Char = price.charAt(i);
			if ((i==0 && Char==0) || ValidChars.indexOf(Char) == -1){}
			else{
				validint = validint+Char;
			}
		}
		if(price.length > validint.length){
			j(this).val(validint);
		}
	});
}

function loadQtyField(){
	j( ".qtyfield" ).blur(function() {
		var price = j(this).val();
		if(price==''){j(this).val(0);}
	});		
	j( ".qtyfield" ).keyup(function() {
		var price = j(this).val();
		var ValidChars = "0123456789";
		var IsNumber=true;
		var Char;
		var validint = '';
		for (var i = 0; i < price.length && IsNumber == true; i++){ 
			Char = price.charAt(i);
			if ((i==0 && Char==0) || ValidChars.indexOf(Char) == -1){}
			else{
				validint = validint+Char;
			}
		}
		if(price.length > validint.length){
			j(this).val(validint);
		}
	});
}

function sendReturnData(uri, sendingData, returnID){
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: uri,
		data: sendingData,
	})
	.done(function( data ) {		
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			j('#'+returnID).html(data.returnStr);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(sendReturnData, uri, sendingData, returnID);
	});	
	return false;
}

function printbyurl(URL){
	var day = new Date();
	var id = day.getTime();
	var w = 900;
	var h = 600;
	var scrl = 1;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scrl+',toolbar=0,location=0,statusbar=0,menubar=0,resizable=0';
	eval("page" + id + " = window.open(URL, '" + id + "', winprops);");
}
				
function printbyuri(uri){
	var day = new Date();
	var id = day.getTime();
	var w = 900;
	var h = 600;
	var scrl = 1;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scrl+',toolbar=0,location=0,statusbar=0,menubar=0,resizable=0';
	
	eval("page" + id + " = window.open(uri, '" + id + "', winprops);");
}

function checkintpositive(price){
	var ValidChars = ".0123456789";
	var IsNumber=true;
	var Char;
	var validint = '';
	for (var i = 0; i < price.length && IsNumber == true; i++){ 
		Char = price.charAt(i);
		if ((i==0 && Char==0) || ValidChars.indexOf(Char) == -1){}
		else{
			validint = validint+Char;
		}
	}
	return validint;
}

function number_format(number){
	var decimals = 2;
	var dec_point = '.';
	var thousands_sep = ',';
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function roundnum(num){
	var bkNum = num; // taken backup original num
	if(num <0){num = 0-num;}
	var scale = 2;
	if(!("" + num).includes("e")) {
		num = +(Math.round(num + "e+" + scale)  + "e-" + scale);
	} else {
		var arr = ("" + num).split("e");
		var sig = ""
		if(+arr[1] + scale > 0) {
		  sig = "+";
		}
		num = +(Math.round(+arr[0] + "e" + sig + (+arr[1] + scale)) + "e-" + scale);
	}
	if(bkNum <0){num = 0-num;}
	return num;
}
		
function printbyuri(uri){
	var day = new Date();
	var id = day.getTime();
	var w = 900;
	var h = 600;
	var scrl = 1;
	var winl = (screen.width - w) / 2;
	var wint = (screen.height - h) / 2;
	winprops = 'height='+h+',width='+w+',top='+wint+',left='+winl+',scrollbars='+scrl+',toolbar=0,location=0,statusbar=0,menubar=0,resizable=0';
	
	eval("page" + id + " = window.open(uri, '" + id + "', winprops);");
}

function AJget_notesData(){
	var note_for = document.getElementById("note_forTable").value;
	var table_id = document.getElementById("table_idValue").value;
	if(note_for=='' || table_id==''){
		j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Could not add this note.</div></div>').fadeIn(500);
		setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
	}
	else{
		sendReturnData('/Common/AJget_notesData/', {"note_for":note_for, "table_id":table_id}, 'noteslist');
	}
}					

function AJget_notesPopup(){
	var args = Array.prototype.slice.call(arguments, 0);
	var notes_id = 0;
	var publics = 0;
	var note = '';
	if(args.length>0 && args.length==2){
		notes_id = args[0];
		publics = args[1];		
	}
	
	var publicsShow = document.getElementById("publicsShow").value;
	
	var formhtml = '<form action="#" name="frmnotes" id="frmnotes" onsubmit="return AJsave_notes();" enctype="multipart/form-data" method="post" accept-charset="utf-8">';
	
	if(publicsShow>0){	
		var yesselected = '';
		if(publics>0){
			yesselected = ' selected="selected"';
		}
		formhtml += '<div class="form-group row">'+
						'<div class="col-sm-2" align="left">'+ 
							'<label for="publics">Public</label>'+
						'</div>'+
						'<div class="col-sm-3" align="left">'+
							'<select class="form-control" name="publics" id="publics">'+
								'<option value="0">No</option>'+
								'<option value="1"'+yesselected+'>Yes</option>'+
							'</select>'+
						'</div>'+
					'</div>';
	}
	else{
		formhtml += '<input type="hidden" name="publics" id="publics" value="0">';
	}
	
	formhtml += '<div class="form-group row">'+
						'<div class="col-sm-12" align="left">';
						
	if(args.length>0 && args.length==2){
		
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: "/Common/AJget_notesPopup/",
			data: {notes_id:notes_id},
		})
		.done(function( data ) {
			if(data.login !=''){window.location = '/'+data.login;}
			else{
				formhtml += '<textarea required="required" name="note" id="note" class="form-control placeholder" rows="5" placeholder="Enter Note...">'+data.note+'</textarea>'+
								'</div>'+
								'</div>'+
								'<div class="form-group"><span class="error_msg" id="errmsg_note"></span></div>'+
								'<input type="hidden" name="notes_id" id="notes_id" value="'+data.notes_id+'">'+
							'</form>';
				form600dialog('Add New Note', formhtml, 'Save', AJsave_notes);
				
				setTimeout(function() {
					document.getElementById("note").focus();
			
					j(".placeholder").focus(function(){
						if(j(this).val()==''){
							j(this).prop('placeholder', '');
						}
					});
					
					j(".placeholder").blur(function(){
						if(j(this).val()==''){
							var altval = j(this).attr('alt');
							j(this).prop('placeholder', altval);
						}
					});
				}, 1000);
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {			
			callbackfunction.apply(this, args);
		});	
	}
	else{
		formhtml += '<textarea required="required" name="note" id="note" class="form-control placeholder" rows="5" placeholder="Enter Note...">'+note+'</textarea>';
		formhtml += '</div>'+
						'</div>'+
						'<div class="form-group"><span class="error_msg" id="errmsg_note"></span></div>'+
						'<input type="hidden" name="notes_id" id="notes_id" value="'+notes_id+'">'+
					'</form>';
		form600dialog('Add New Note', formhtml, 'Save', AJsave_notes);
		
		setTimeout(function() {
			document.getElementById("note").focus();
	
			j(".placeholder").focus(function(){
				if(j(this).val()==''){
					j(this).prop('placeholder', '');
				}
			});
			
			j(".placeholder").blur(function(){
				if(j(this).val()==''){
					var altval = j(this).attr('alt');
					j(this).prop('placeholder', altval);
				}
			});
		}, 1000);
	}	
}

function AJsave_notes(){
	var notes_id =  document.getElementById("notes_id").value;
	var publics =  document.getElementById("publics").value;
	var note_for = document.getElementById("note_forTable").value;
	var table_id = document.getElementById("table_idValue").value;
	
	var note = document.frmnotes.note;
	var oElement = document.getElementById('errmsg_note');
	oElement.innerHTML = "";
	if(note.value == ""){
		oElement.innerHTML = 'Missing note';
		note.focus();
		return(false);
	}
	else{
		
		j(".btnmodel").html('Saving...').prop('disabled', true);
		
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST", dataType: "json",
			url: "/Common/AJsave_notes/",
			data: {"notes_id":notes_id, "note_for":note_for, "table_id":table_id, "publics":publics, "note":note.value},
		})
		.done(function( data ) {
			if(data.login !=''){window.location = '/'+data.login;}
			else if(data.returnStr=='Add'){
				if(j("#stock_takessubmit").length){checkAndLoadData();}
				else{checkAndLoadFilterData();}
				
				if(j("#frmTimeClock").length){AJget_notesData();}
				j( "#form-dialog" ).html('').dialog( "close" );
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">'+data.returnStr+'</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 7000);				
				j(".btnmodel").html('Add Note').prop('disabled', false);
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {			
			connection_dialog(AJsave_notes);
		}); 
	}
	return false;
}

function runSignatureScript(){
	var SignaturesID = document.getElementsByClassName("Signature");
	for(var l=0; l<SignaturesID.length; l++){
		
		var digital_signature_id = SignaturesID[l].value;
		var showing_order = SignaturesID[l].getAttribute('name').replace('ff', '');
		if(digital_signature_id==''){
			
			j("#signatureID"+showing_order).html('<div class="signaturecontent">'+
				'<div class="signatureparent">'+
					'<div class="mainsignature" id="signature'+showing_order+'"></div>'+
				'</div>'+
				'<div id="tools'+showing_order+'"></div>'+										
			'</div>'+
			'<div class="scrollgrabber"></div>');
			
			var $sigdiv = j("#signature"+showing_order).jSignature({'UndoButton':true});
			var $tools = j('#tools'+showing_order);
			var $extraarea = j('#displayarea');
			var pubsubprefix = 'jSignature.demo.';
			
			var export_plugins = $sigdiv.jSignature('listPlugins','export');
			var chops = ['<input class="btn btn-primary" type="button" id="signatureSave'+showing_order+'" value="Save">'];
			var name;
			
			j(chops.join('')).bind('click', function(e){
				j("#signatureSave"+showing_order).slideUp('fast');
				var data = $sigdiv.jSignature("getData", "image");
				
				var note = '';
				if (typeof data === 'string'){
					var note = data;
				} else if(j.isArray(data) && data.length === 2){
					var note = data.join(',');
				} else {
					try {
						var note = JSON.stringify(data);
					} catch (ex) {
						j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">Not sure how to stringify this, likely binary, format.</div></div>').fadeIn(500);
						setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
					}
				}
				
				if(note != ""){
					var table_id = document.getElementById("forms_data_id").value;
					note = 'data:'+note;
					j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
					j.ajax({method: "POST", dataType: "json",
						url: "/Common/AJsave_digitalSignature/",
						data: {"for_table":"forms_data", "table_id":table_id, "note":note},
					}).done(function( data2 ) {
						if(data2.login !=''){window.location = '/'+data2.login;}
						else if(data2.savemsg=='Add'){
							document.getElementById("ff"+showing_order).value = data2.id;
							runSignatureScript();
						}
						else{
							j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">'+data2.message+'</div></div>').fadeIn(500);
							setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
						}
						if(j(".disScreen").length){j(".disScreen").remove();}
					})
					.fail(function() {
						j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">Internet connection problem. Retry again.</div></div>').fadeIn(500);
						setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
					});
				}
			}).appendTo($tools);

			j('<input class="btn btn-default mleft10" type="button" value="Reset">').bind('click', function(e){
				$sigdiv.jSignature('reset');
				j("#signatureSave"+showing_order).slideDown('fast');
			}).appendTo($tools);
			
		}
		else{
			AJget_signature(digital_signature_id, showing_order);
		}
	}
}

function AJget_signature(digital_signature_id, showing_order){
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Common/AJget_signature/",
		data: {digital_signature_id:digital_signature_id, showing_order:showing_order},
	}).done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else{
			j("#signatureID"+showing_order).html(data.returnStr);
			document.getElementById("ff"+showing_order).value = digital_signature_id;
			j(".signatureImages").hover(function() {
				var digital_signature_id = j(this).find("img").attr("alt");
				var showing_order = j(this).find("img").attr("title");
				j(this).append('<div class="deletedicon" onClick="removeSignature(\''+digital_signature_id+'\', \''+showing_order+'\')"></div>');
				
			}, function() {
				j(this).find(".deletedicon").remove();
			});
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJget_signature, digital_signature_id, showing_order);
	});	 
}

function upload_dialog(title, frompage, fileprename){
	var oldfilename = '';
	if(['news_articles', 'services'].includes(frompage)){
		if(j("#"+frompage+"_picture div").hasClass('currentPicture')){
			var picturepath = j("#"+frompage+"_picture div").find("img").attr("src");
			oldfilename = picturepath;
		}
	}

	j( "#form-dialog" ).html('');
	var formhtml = '<form name="frmupload" id="frmupload" action="/Common/uploadpicture" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<input name="filename" id="filename" type="file">'+
						'<input name="frompage" id="frompage" type="hidden" value="'+frompage+'">'+
						'<input type="hidden" name="MAX_FILE_SIZE" value="30000" />'+
						'<input name="fileprename" id="fileprename" type="hidden" value="'+fileprename+'">'+
						'<input name="oldfilename" id="oldfilename" type="hidden" value="'+oldfilename+'">'+
						'<span id="errmsg_filename" class="errormsg"></span>'+
					'</form>';
	
	j( "#form-dialog" ).html(formhtml);
	
	j( "#form-dialog" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width: j(window).width() > 400 ? 400 : j(window).width(),
		modal: true,
	  	buttons:{
				'Cancel':{
					text: 'Cancel', class: 'btn btn-default', click: function() {
						j( this ).dialog( "close" );
					},
				},
				'Upload':{
					text: 'Upload', class: 'btn btn-primary btnupload', click: function() {
						j("#frmupload").submit();
					},
				},
		},
    });
	
	var targetval = '#'+frompage+'_picture';
	var options = { target:  targetval,   // target element(s) to be updated with server response 
                 	beforeSubmit:  beforeSubmit,  // pre-submit callback 
					success:       afterSuccess1,  // post-submit callback 
					resetForm: true        // reset the form after successful submit 
				}; 
				
	j("#frmupload").submit(function(){
		j(this).ajaxSubmit(options);
	
		j( "#form-dialog" ).html('').dialog( "close" );
		if(['category'].includes(frompage)){
			setTimeout(function() {
				checkAndLoadFilterData();
			}, 2000);
		}
		return false; 
	});
	
	function beforeSubmit(){
	   //check whether browser fully supports all File API
	   if (window.File && window.FileReader && window.FileList && window.Blob)
		{
	   
			if( !j('#filename').val()) //check empty input filed
			{	
				j("#errmsg_filename").html('Missing picture');
				return false
			}
			
			var fsize = j('#filename')[0].files[0].size; //get file size
			var ftype = j('#filename')[0].files[0].type; // get file type
			
			//allow only valid image file types 
			switch(ftype)
			{
				case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
					break;
				default:
					j("#errmsg_filename").html("<b>"+ftype+"</b> Unsupported file type!");
					return false
			}
			
			//Allowed file size is less than 1 MB (1048576)
			if(fsize>4194304) 
			{
				j("#errmsg_filename").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
				return false
			}
			
			j(".btnupload").html('Uploading...').prop('disabled', true);
			j("#errmsg_filename").html("");  
		}
		else
		{
			j(".btnmodel").html('Uploading...').prop('disabled', false);
			//Output error to older unsupported browsers that doesn't support HTML5 File API
			j("#errmsg_filename").html('Please upgrade your browser, because your current browser lacks some new features we need!');
			return false;
		}
	}
	
	function afterSuccess1(){
		if(j("#"+frompage+"_picture div").hasClass('currentPicture')){
			j(".currentPicture").hover(function() {
				var picturepath = j(this).find("img").attr("src");
			}, function() {
				if(j("#"+frompage+"_picture div").hasClass('deletedicon')){
					j(this).find(".deletedicon").remove();
				}
			});	
		}
		
		if(frompage=='all_pages_header'){
			var picturepath = j(".currentPicture").find("img").attr("src");
			j("#web_logo").val(picturepath);
			headerPreview();
		}		

		if(['news_articles', 'services'].includes(frompage)){
			location.reload();
		}
		j( "#form-dialog" ).html('').dialog( "close" );
	}
}

function uploadImage(showing_order){
	var form_for = 'services';
	var table_id = document.frmFormsData.table_id.value;
	var forms_id = document.frmFormsData.forms_id.value;
	var forms_data_id = document.frmFormsData.forms_data_id.value;
	var fileprename = form_for+'_'+table_id+'_forms_'+forms_id+'_data_'+forms_data_id+'_ID_'+showing_order+'_';
	
	var formhtml = '<form name="frmupload" id="frmupload" action="/Common/uploadpicture" enctype="multipart/form-data" method="post" accept-charset="utf-8">'+
						'<input name="filename" id="filename" type="file">'+
						'<input name="frompage" id="frompage" type="hidden" value="fieldImages">'+
						'<input type="hidden" name="MAX_FILE_SIZE" value="30000" />'+
						'<input name="fileprename" id="fileprename" type="hidden" value="'+fileprename+'">'+
						'<input name="oldfilename" id="oldfilename" type="hidden" value="">'+
						'<span id="errmsg_filename" class="errormsg"></span>'+
					'</form>';
	
	j( "#dialog-confirm" ).html(formhtml);
	
	j( "#dialog-confirm" ).dialog({
		title: 'Upload Image',
		resizable: false,
		height: "auto",
		width: j(window).width() > 400 ? 400 : j(window).width(),
		modal: true,
		buttons:{
				'Cancel':{
					text: 'Cancel', class: 'btn btn-default', click: function() {
						j( this ).dialog( "close" );
					},
				},
				'Upload':{
					text: 'Upload', class: 'btn btn-primary btnupload', click: function() {
						j("#frmupload").submit();
					},
				},
		},
	});
	
	var targetval = '#UploadImageID'+showing_order;
	var options = { target:  targetval,   // target element(s) to be updated with server response 
					beforeSubmit:  beforeSubmit,  // pre-submit callback 
					success:       afterSuccess,  // post-submit callback 
					resetForm: true        // reset the form after successful submit 
				}; 						
	j("#frmupload").submit(function(){
		var updateReturn = j(this).ajaxSubmit(options);
		return false; 
	});
	
	function beforeSubmit(){
	   //check whether browser fully supports all File API
	   if (window.File && window.FileReader && window.FileList && window.Blob)
		{
	   
			if( !j('#filename').val()){	
				j("#errmsg_filename").html('Missing picture');
				return false
			}
			if( !j('#filename').val()){	
				j("#errmsg_filename").html('Missing picture');
				return false
			}
			var fsize = j('#filename')[0].files[0].size; //get file size
			var ftype = j('#filename')[0].files[0].type; // get file type
			
			switch(ftype){
				case 'image/png': case 'image/gif': case 'image/jpeg': case 'image/pjpeg':
					break;
				default:
					j("#errmsg_filename").html("<b>"+ftype+"</b> Unsupported file type!");
					return false
			}
			
			if(fsize>4194304){
				j("#errmsg_filename").html("<b>"+bytesToSize(fsize) +"</b> Too big Image file! <br />Please reduce the size of your photo using an image editor.");
				return false
			}
			
			j(".btnupload").html('Uploading...').prop('disabled', true);
			j("#errmsg_filename").html("");  
		}
		else{
			j(".btnmodel").html('Uploading...').prop('disabled', false);
			j("#errmsg_filename").html('Please upgrade your browser, because your current browser lacks some new features we need!');
			return false;
		}
	}
	
	function afterSuccess(){

		if(j(targetval+" div").hasClass('currentPicture')){
			j(".currentPicture").hover(function() {
				var picturepath = j(this).find("img").attr("src");
				j(this).append('<div class="deletedicon" onClick="AJremove_Picture(\''+picturepath+'\', \'fieldImages\')"></div>');
			}, function() {
				if(j(targetval+" div").hasClass('deletedicon')){
					j(this).find(".deletedicon").remove();
				}
			});	
		}
		
		var picturepath = j(targetval+" div").find("img").attr("src");
		j("#ff"+showing_order).val(picturepath);
		
		j( "#dialog-confirm" ).html('').dialog( "close" );
	}
}

function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Bytes';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
}

function loadPictureHover(){
	if(document.querySelectorAll(".currentPicture")){
		document.querySelectorAll(".currentPicture").forEach(oneClassObj=>{
			var targetval = false;
			if(oneClassObj.parentElement.hasAttribute('id'))
				targetval = oneClassObj.parentElement.id;

			oneClassObj.addEventListener('mouseenter',function(){
				let picturepath = this.querySelector("img").getAttribute("src");
				if(picturepath !='/assets/images/default.png'){
					let deletedicon = cTag('div', {class: "deletedicon"});
					deletedicon.addEventListener('click', function(){
						AJremove_Picture(picturepath, segment1, this.parentElement);
					});
					this.append(deletedicon);
				}
			});

			oneClassObj.addEventListener('mouseleave',function(){
				if(targetval && document.querySelector("#"+targetval)){
					this.querySelector(".deletedicon").remove();
				}
				else{
					this.querySelector(".deletedicon").remove();
				}
			});
		});
	}
}

function AJremove_Picture(picturepath, frompage, hoverObj = false){
	if(picturepath !='' && picturepath !='/assets/images/default.png'){
		j( "#dialog-confirm" ).html('<p class="txtleft">Do you sure want to remove this picture permanently?</p>');
		
		j( "#dialog-confirm" ).dialog({
			title: 'Remove Picture',
			resizable: false,
			height: "auto",
			width:400,
			modal: true,
			open: function(event, ui) {
				j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
			},
			buttons: {
				'Close': {
					text: 'Close', class: 'btn btn-default', click: function() {
						j( this ).dialog( "close" );
					},
				},
				'Confirm':{
					text: 'Confirm', class: 'btn btn-primary archive', click: function() {
						j( this ).dialog( "close" );
						
						j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
						j.ajax({method: "POST", dataType: "json",
							url: "/Common/AJremove_Picture",
							data: {"picturepath":picturepath},
						}).done(function( data ) {
							if(data.login !=''){window.location = '/'+data.login;}
							else if(data.returnStr=='Ok'){
								j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Picture removed successfully.</div></div>').fadeIn(500);
								
								if(frompage !=''){
									if(frompage=='banners'){
										const bannerIdIfo = picturepath.split('_');
										const banners_id = parseInt(bannerIdIfo[1]);
										if(!isNaN(banners_id) && banners_id>0){
											const ImagesTDID = document.getElementById("banner"+banners_id);
											if(ImagesTDID){
												ImagesTDID.innerHTML = '<button type="button" class="btn btn-default" onClick="upload_dialog(\'Upload Banner Picture\', \'banners\', \'bann_'+banners_id+'_\')">Upload</button>';
											}
										}
										j( "#dialog-confirm" ).dialog( "close" );
									}
									else if(frompage=='fieldImages'){
										var fieldImagesID = document.getElementsByClassName("fieldImages");
										for(var l=0; l<fieldImagesID.length; l++){
											
											var imagePath = fieldImagesID[l].value;
											var showing_order = fieldImagesID[l].getAttribute('name').replace('ff', '');
											if(imagePath==picturepath){
												j('#UploadImageID'+showing_order).html('<button type="button" class="ui-button ui-corner-all ui-widget" name="open" onClick="uploadImage(\''+showing_order+'\')">Upload</button>');
												j("#ff"+showing_order).val('');
											}
										}
										runImageScript();
										j( "#dialog-confirm" ).dialog( "close" );
									}

									if(['category', 'banners'].includes(frompage)){
										checkAndLoadFilterData();
									}
								}
								setTimeout(function() {j("#showmessagehere").slideUp(500)}, 5000);
							}
							else{
								j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">Could not remove picture.</div></div>').fadeIn(500);
								setTimeout(function() {j("#showmessagehere").slideUp(500)}, 5000);
							}
							if(j(".disScreen").length){j(".disScreen").remove();}
						})
						.fail(function() {							
							connection_dialog(AJremove_Picture, picturepath, frompage);
						});							 
					},
				}
			}
		});
		                               
	}
	else if(picturepath =='/assets/images/default.png'){
		alert_dialog('Alert message', 'Sorry! this is default Picture. You could not delete it.', 'Ok');
	}
}

function makedeletedicon(listidname){
	var countList = j("ul#"+listidname).children().length;
	var removeclass = 'r'+listidname;
	if(j( "#"+listidname ).hasClass(removeclass)){
		j( "."+removeclass ).remove();
	}
	
	if(countList>1){
		for(var l=0; l<countList; l++){
			var listno = parseInt(l+1);
			
			if(listno<=countList){				
				if(listidname=='rsListRow' && (l==1 || l==2)){}
                else{
					j("<a class=\"removeicon "+removeclass+"\" href=\"javascript:void(0);\" title=\"Remove this row\">"+
						"<img align=\"absmiddle\" alt=\"Remove this row\" title=\"Remove this row\" src=\"/assets/images/cross-on-white.gif\">"+
					"</a>").appendTo("ul#"+listidname+" li:nth-child("+l+")");
					
					j('.removeicon').click(function(){
						j(this).parent('li').slideUp(1000);
						j(this).parent('li').remove();
					});
				}
			}
		}
	}                                    
}

function showMessAndRedi(title, message){
	var redirectTo = j("#redirectTo").val();
	j( "#dialog-confirm" ).html(message);
	
	j( "#dialog-confirm" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width:400,
		modal: true,
		open: function(event, ui) {
			j(".ui-dialog-titlebar-close", ui.dialog | ui).hide();
		},
		buttons: {
			'Ok': {
				text: 'Ok', class: 'btn btn-primary', click: function() {
					window.location = redirectTo;
					j( this ).dialog( "close" );
				},
			}
		}
	});	
}

function getMobileOperatingSystem() {
  	var userAgent = navigator.userAgent || navigator.vendor || window.opera;

      // Windows Phone must come first because its UA also contains "Android"
    if (/windows phone/i.test(userAgent)) {
        return "Windows Phone";
    }

    if (/android/i.test(userAgent)) {
        return "Android";
    }

    // iOS detection from: http://stackoverflow.com/a/9039885/177710
    if (/iPad|iPhone|iPod/.test(userAgent) && !window.MSStream) {
		return "iOS";
    }

    return "unknown";
}

function emailcheck(str) {
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,10})?$/;
	if(!emailReg.test(str)) {
       	return false
    }
	
	return true					
}

function onClickPagination(){
	var setCurrentPage = function(url) {
		var loadTableRows = 'loadTableRows';
		if(url){
			var uri = j('#pageURI').val();
			var page = url.replace('/'+uri+'/', '');
			var pathArray = uri.split('/');
			loadTableRows = loadTableRows+'_'+pathArray[0]+'_'+pathArray[1];
		}
		else{var page = 1;}
		j("#page").val(page);
		var fn = window[loadTableRows];
		if(typeof fn === "function"){fn();}
	};
	
	if(j("#page").val()==1){
		var pathname = window.location.pathname;
		var targetUrl1 = '/'+j('#pageURI').val();
		var targetUrl = '/'+j('#pageURI').val()+'/'+j("#page").val();
		if(pathname !=targetUrl && pathname !=targetUrl1){
			window.history.pushState({url: "" + targetUrl + ""}, '', targetUrl);
		}
	}
	
	j("#Pagination").html(createLinks());
			
	j('.pagination li a').click(function(e){
		var disClassN = j(this).attr('class');
		if(disClassN=='disabled'){return false;}
		e.preventDefault();
		var targetUrl = j(this).attr('href'),
			targetTitle = j(this).attr('title');				
		window.history.pushState({url: "" + targetUrl + ""}, targetTitle, targetUrl);
		setCurrentPage(targetUrl);
	});

	window.onpopstate = function(e) {
		setCurrentPage(e.state ? e.state.url : null);
	};
}

function checkAndLoadData(){
	if (j('#pageURI').length){
		var pathArray = j('#pageURI').val().split('/');
		
		var loadData = 'loadData_'+pathArray[0]+'_'+pathArray[1];
		var fn = window[loadData];
		if (typeof fn === "function"){fn();}
	}
}

function checkloadTableRows(){
	if (j('#pageURI').length){
		var pathArray = j('#pageURI').val().split('/');
		
		var loadData = 'loadTableRows_'+pathArray[0]+'_'+pathArray[1];
		var fn = window[loadData];
		if (typeof fn === "function"){fn();}
	}
}

function checkAndLoadFilterData(){
	if (j('#pageURI').length){
		var pathArray = j('#pageURI').val().split('/');
		
		var loadData = 'filter_'+pathArray[0]+'_'+pathArray[1];
		var fn = window[loadData];
		if (typeof fn === "function"){fn();}
	}
}

function AJremoveData(tableName, tableId, nameVal){
	var message = 'Are you sure you want to archive this information?'+
				'<input type="hidden" name="tableName" id="tableName" value="'+tableName+'">'+
				'<input type="hidden" name="tableId" id="tableId" value="'+tableId+'">'+
				'<input type="hidden" name="nameVal" id="nameVal" value="'+nameVal+'">';
	confirm_dialog('Confirm Archive Information', message, confirmAJremoveData);
}

function confirmAJremoveData(){
	var jsonData = {};
	jsonData['tableName'] = j("#tableName").val();
	jsonData['tableId'] = j("#tableId").val();
	jsonData['nameVal'] = j("#nameVal").val();
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/AJremoveData/",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else if(data.returnStr=='archive-success'){
			if (j('#pageURI').length){
				var pathArray = j('#pageURI').val().split('/');
				var filterFunction = 'filter_'+pathArray[0]+'_'+pathArray[1];
				var fn = window[filterFunction];
				if (typeof fn === "function"){fn();}
				var resetFunction = 'resetForm_'+pathArray[1];
				var fn = window[resetFunction];
				if (typeof fn === "function"){fn();}
			}
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Archived successfully</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}
		else{
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well error_msg">'+data.returnStr+'</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500);},5000);
		}									
		j( "#dialog-confirm" ).html('').dialog( "close" );
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {
		connection_dialog(confirmAJremoveData);
	});
}

function getOneRowInfo(tableName, tableId){
	if(tableId>0){
		j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
		j.ajax({method: "POST",dataType: "json",
			url: "/Manage_Data/getOneRowInfo/",
			data: {tableName: tableName, tableId:tableId},
		})
		.done(function( data ) {
			if(data.login != ''){window.location = '/'+data.login;}
			else{
				var nameField = '';
				var nameVal = '';
				for (var field in data) {
					if(j("#"+field).length){
						j("#"+field).val(data[field]);
						if(j.inArray(field, ['category_name', 'name', 'brand']) !== -1){
							nameField = field;
							nameVal = data[field];
						}
						else if(j.inArray(field, ['is_single', 'is_required']) !== -1){
							let nameVal1 = parseInt(data[field]);
							if(nameVal1>0){document.getElementById(field).checked = true;}
							else{document.getElementById(field).checked = false;}
						}
					}
					else if(tableName=='users' && field=='users_roll'){
						setUserRoll(data[field]);
					}
					if(tableName=='pages' && document.getElementById('wysiwyrEditor') && field=='description'){
						let editor = document.getElementById('wysiwyrEditor');
						editor.querySelector("#description").innerHTML = editor.querySelector("#editingArea").contentWindow.document.body.innerHTML = data.description;
					}
				}
				if(j("#formtitle").length){j("#formtitle").html('Update');}
				if(j("#reset").length){j("#reset").fadeIn(500);}
				
				if(j("#archive").length && nameVal !=''){
					j("#archive").fadeIn(500);
					nameVal = nameVal.replace(/'/g, "\\'");
					j("#archive").attr('onClick', "AJremoveData('"+tableName+"', "+tableId+", '"+nameVal+"');");
				}
				if(nameField !='' && j("#"+nameField).length){
					setTimeout(function() {j("#"+nameField).focus();});
				}
				if(j("#is_admin").length){
					if(j("#is_admin").val()>0){j("#archive").removeAttr("onClick").fadeOut(500);}
				}

				
			}
			if(j(".disScreen").length){j(".disScreen").remove();}
		})
		.fail(function() {		
			connection_dialog(getOneRowInfo, tableName, tableId);
		});
	}
}

function gotoprevpage(previousList){
	window.location = previousList;
}

function cTag(tagName, attributesObject={}){
    let tagObject = document.createElement(tagName);
    if(Object.keys(attributesObject).length>0){
        for(const [key, value] of Object.entries(attributesObject)) {
            tagObject.setAttribute(key, value);        
        }
    }
    return tagObject;
}

function lefsidemenubar(){
	let width, nav, sideBar, sidebarWrapper;
	width = window.innerWidth;
	nav = document.getElementById('fanav');
	sideBar = document.getElementById('sideBar');
	sidebarWrapper = document.getElementById('settingsleftsidemenubar');
	if(sidebarWrapper){
		sidebarWrapper.addEventListener('click', function(){
			if(sideBar.classList.contains('hidden')){
				sideBar.classList.remove('hidden');
			}
			else{
				sideBar.classList.add('hidden');
			}
		})
	}
	if(nav){
		if(width>767  ){
			nav.classList.add('hidden');
			sideBar.classList.remove('hidden');
		}
		else{
			nav.classList.remove('hidden');
			sideBar.classList.add('hidden');
		}
	}
}

function leftsideHide(menuID, menuClass){

	if(document.querySelector('#'+menuID)){
		document.querySelector('#'+menuID).addEventListener('click', event=>{
			document.querySelectorAll('.'+menuClass).forEach(oneTag=>{
				oneTag.classList.toggle('settingslefthide');
			});
		});
	}

}

function wysiwyrEditor(fieldIdName='description'){
    let activeHtmlArea = false;
    let styles = {
        radiousBorder:{
            'border':'1px solid #ccc',
            'border-radius':'4px',
            'display':'flex'       
        },
        item:{
            'padding':'6px 12px',
            'cursor':'pointer',
            'display':'block'
        },
        nonSuppotredItem:{
            'padding':'6px 12px',
            'cursor':'not-allowed',
            'display':'block',
            'opacity':'0.4'
        },
        hoveredItem:{
            'background':'#e6e6e6',
            'box-shadow':'inset 0 2px 4px rgb(0 0 0 / 15%), 0 1px 2px rgb(0 0 0 / 5%)'
        },
        unhoveredItem:{
            'background':'transparent',
            'box-shadow':'none'
        },
        separator:{
            'border-right':'1px solid #ccc',
        },
        toolbar:{
            'position':'relative',
            'list-style': 'none',
            'padding': '0',
            'display': 'flex',
            'flex-wrap':'wrap',
            'gap': '10px',
            'margin-bottom':'10px'
        },
        editingArea:{
            'padding':'10px 15px',
            'height':'250px',
            'max-height':'40vh',
            'width':'95%',
            'box-shadow':'0 0 1px gray'
        },
        actionButtonWrapper:{
            'position': 'absolute',
            'height': '100%',
            'background': 'rgba(199, 199, 199, 0)',
            'width': '0',
            'z-index': '100'
        },
        activeButton:{
            'opacity':'1',
            'background':'transparent',
            'cursor':'pointer',
        },
        deactiveButton:{
            'opacity':'.4',
            'background':'#fbfbfb',
            'cursor':'not-allowed',
        }
    }
    let mouseHoverFunctions = {
        enter:function(){setStyles(this,styles.hoveredItem)},
        leave:function(){setStyles(this,styles.unhoveredItem)}
    }
   
    let editor = cTag('div',{'id':'wysiwyrEditor'});
    editor.appendChild(toolbar());
        let htmlArea = cTag('textarea',{ 'id':fieldIdName,'name':fieldIdName });
        setStyles(htmlArea,styles.radiousBorder);
        setStyles(htmlArea,styles.editingArea);
        setStyles(htmlArea,{'display':'none'}); // initially hidden
        htmlArea.addEventListener('keyup',()=>{
            updateEditBox();
        })
    editor.appendChild(htmlArea);
        let editingArea = cTag('iframe',{'id':'editingArea'});
        setTimeout(() => {
            editingArea.contentDocument.designMode = "on";
            editingArea.contentDocument.addEventListener('keyup',()=>{
                updateDescription();
            })
            editingArea.contentDocument.addEventListener('keydown',(event)=>{
                if(event.ctrlKey && event.keyCode==83){
                    editor.querySelector('[title="CTRL+S"]').click();
                    event.preventDefault();
                }
            })
            editingArea.contentDocument.addEventListener('selectionchange',()=>{
                let node = document.getElementById('editingArea').contentWindow.getSelection().anchorNode.parentNode;
                let tags = [
                    {tag:'b',id:'bold'},
                    {tag:'i',id:'italic'},
                    {tag:'u',id:'underline'},
                    {tag:'small',id:'small'},
                    {tag:'blockquote',id:'blockquote'},
                    {tag:'ul',id:'insertUnorderedList'},
                    {tag:'ol',id:'insertOrderedList'}
                ];                
                let activButtons = [];
                let inactivButtons = [];

                tags.forEach(item=>{
                    if(item.tag.toUpperCase()==node.tagName||node.closest(item.tag)) activButtons.push(item);
                    else inactivButtons.push(item);
                });
                
                activButtons.forEach(item=>{
                    let node = editor.querySelector('#'+item.id);
                    setStyles(node,styles.hoveredItem)
                    removeHoverStyle(node);
                })
                inactivButtons.forEach(item=>{
                    let node = editor.querySelector('#'+item.id);
                    setStyles(node,styles.hoveredItem)
                    setHoverStyle(node);
                })
            })
        }, 500);
        setStyles(editingArea,styles.radiousBorder);
        setStyles(editingArea,styles.editingArea);
    editor.appendChild(editingArea);

    function toolbar(){
		let li, a, div, span;
        let ul = cTag('ul',{ 'class':`toolbar` });
        setStyles(ul,styles.toolbar);
            li = cTag('li',{ 'class':`dropdown`,'id':'wysiwyrEditorDropdown' });
                a = cTag('a',{ 'class':`btn btn-default dropdown-toggle`,'data-toggle':`dropdown` });
                setStyles(a,styles.radiousBorder);
                setStyles(a,styles.item);
                setHoverStyle(a);
                a.appendChild(cTag('span',{ 'class':`fa fa-font` }));
                    let currentFont = cTag('span',{ 'class':`current-font` });
                    currentFont.innerHTML = ' Normal text';
                a.appendChild(currentFont);
                a.appendChild(cTag('b',{ 'class':`fa fa-caret-down`,'style':'margin-left:5px;font-size:12px' }));
            li.appendChild(a);
                let dropdownMenu = cTag('ul',{ 'class':`dropdown-menu`, 'style':'left: 0 !important;width: 20%;' });
                ['Normal text','Heading 1','Heading 2','Heading 3','Heading 4','Heading 5','Heading 6'].forEach(item=>{
                    let li1 = cTag('li');
                        a = cTag('a');
                        a.innerHTML = item;
                        a.addEventListener('click',()=>{
                            currentFont.innerHTML = ' '+item;
                            if(/Heading/.test(item)) actionButtonHandler(null,`H${item.match(/Heading (\d)/)[1]}`);
                            else actionButtonHandler(null,'P');
                        })
                    li1.appendChild(a);
                    dropdownMenu.appendChild(li1)
                })
            li.appendChild(dropdownMenu);
        ul.appendChild(li);
            li = cTag('li');
                div = cTag('div');
                setStyles(div,styles.radiousBorder);
                [
                    {type:'bold',style:'font-weight:bold'},
                    {type:'italic',style:'font-style:italic'},
                    {type:'underline',style:'text-decoration:underline'},
                    {type:'small',style:''},
                ].forEach((item,indx)=>{
                        let cmd = item.type;
                        if(cmd == 'small') cmd = 'decreaseFontSize';
                        let firstLetter = item.type[0];
                        firstLetter = firstLetter.toUpperCase();
                        span = cTag('span',{ 'id':item.type,'title':`CTRL+${firstLetter}`,'style':item.style });
                        checkForSupport(cmd,span,()=>actionButtonHandler(cmd));
                        if(indx!=3) setStyles(span,styles.separator);
                        span.innerHTML = firstLetter+item.type.slice(1);
                    div.appendChild(span);
                })
            li.appendChild(div);
        ul.appendChild(li);
            li = cTag('li');
                span = cTag('span',{ 'id':'blockquote','class':`fa fa-quote-left` });
                setStyles(span,styles.radiousBorder);
                checkForSupport("formatBlock",span,()=>actionButtonHandler(null,"BLOCKQUOTE"));
            li.appendChild(span);
        ul.appendChild(li);
            li = cTag('li');
                div = cTag('div');
                setStyles(div,styles.radiousBorder);
                [
                    {cmd:'insertUnorderedList',title:'Unordered list',class:'fa-list-ul'},
                    {cmd:'insertOrderedList',title:'Ordered list',class:'fa-list-ol'},
                    {cmd:'Outdent',title:'Outdent',class:'fa-outdent'},
                    {cmd:'Indent',title:'Indent',class:'fa-indent'},
                ].forEach((item,indx)=>{
                        span = cTag('span',{ 'id':item.cmd, 'class':`fa ${item.class}`,'title':item.title, });
                        if(indx!=3) setStyles(span,styles.separator);
                        checkForSupport(item.cmd,span,()=>actionButtonHandler(item.cmd));                       
                    div.appendChild(span);
                })
            li.appendChild(div);
        ul.appendChild(li);
            li = cTag('li');
                div = cTag('div');
                    span = cTag('span',{ 'title':`Edit HTML` });
                    span.appendChild(cTag('span',{'class':`fa fa-pencil`,'style':'font-size:12px'}))
                    span.append(' HTML');
                    span.addEventListener('click',htmlEditorHandler)
                    setStyles(span,styles.radiousBorder);
                    setStyles(span,styles.item);
                    setHoverStyle(span);
                div.appendChild(span);
            li.appendChild(div);
        ul.appendChild(li);
        return ul;
    }
    function htmlEditorHandler(){
        activeHtmlArea = !activeHtmlArea;
        if(activeHtmlArea){
            editingArea.style.display = 'none';
            htmlArea.style.display = 'block';
            editor.querySelectorAll('.action-button').forEach(buttonItem=>{
                setStyles(buttonItem,styles.deactiveButton);
                removeHoverStyle(buttonItem);
            });
        }else{
            editingArea.style.display = 'block';
            htmlArea.style.display = 'none';
            editor.querySelectorAll('.action-button').forEach(buttonItem=>{
                setStyles(buttonItem,styles.activeButton);
                setHoverStyle(buttonItem);
            });
        }
    }
    function actionButtonHandler(cmd,tag){
        let doc = editingArea.contentDocument;
        if(cmd) doc.execCommand(cmd)
        else doc.execCommand('formatBlock',false,`<${tag}>`);     
        updateDescription();
    }
    function updateDescription(){
        let fieldId = editor.querySelector('#'+fieldIdName);
        fieldId.value = editingArea.contentDocument.body.innerHTML;
    }
    function updateEditBox(){
        editingArea.contentDocument.body.innerHTML = editor.querySelector('#'+fieldIdName).value;
    }
    function checkForSupport(cmd,node,cbf){
        if(document.queryCommandSupported(cmd)){
            setStyles(node,styles.item);
            setHoverStyle(node);
            node.addEventListener('click',cbf);
            node.classList.add('action-button');
        }
        else{
            setStyles(node,styles.nonSuppotredItem);
        }
    }

    function setStyles(node,stylesObj){
		for (const property in stylesObj) {
			node.style[property] = stylesObj[property];
		}
	}
    function setHoverStyle(node){
        setStyles(node,styles.unhoveredItem);
        node.addEventListener('mouseenter',mouseHoverFunctions.enter)
        node.addEventListener('mouseleave',mouseHoverFunctions.leave)
    }
    function removeHoverStyle(node){
        node.removeEventListener('mouseenter',mouseHoverFunctions.enter)
        node.removeEventListener('mouseleave',mouseHoverFunctions.leave)
    }
    return editor;
}



/*============Pagination=========*/
function createLinks(){
	var rowHeight = j("#rowHeight").val();
	var total = j("#totalTableRows").val();
	var page = j("#page").val();
	var limit = j("#limit").val();
	if(limit=='' || limit=='auto'){
		var screenHeight = getCookie('screenHeight');
		if(screenHeight==''){
			screenHeight = window.innerHeight;
		}
		var headerHeight = getCookie('headerHeight');
		if(headerHeight==''){
			headerHeight = Math.floor(j("#topheaderbar").height()+240);
		}
		var bodyHeight = screenHeight-headerHeight;
		if(bodyHeight<=0 || bodyHeight<=rowHeight){limit = 1;}
		else{
			limit = Math.floor(bodyHeight/rowHeight);
		}
	}
	var uri = j('#pageURI').val();
	
	if(total==0 || limit==0 || limit=='' || isNaN(parseInt(limit))){
		j('#fromtodata').html('0-0/0');
		return '';
	}
	//alert('screenHeight:'+screenHeight+'-headerHeight:'+headerHeight+'=bodyHeight:'+bodyHeight+'/rowHeight:'+rowHeight+'='+limit);
	var num_edge = 2;	
	var np = Math.ceil(total/limit);
	var page = Math.floor(page);
	if(page>np){page = np;}
	var start1 = 1;
	var end1 = np;
	var start2 = 0;
	var end2 = 0;
	var start3 = 0;
	var end3 = 0
	if(np>num_edge){
		end1 = end2 = Math.floor(num_edge);
		if(np>Math.floor(end1+num_edge) && page>end1 && page<=parseInt(np-num_edge)){
			start2 = page;
			end2 = Math.floor(page+num_edge);
			if(Math.floor(page-1)>end1){
				start2 = Math.floor(page-1);
				end2 = Math.floor(page+1);
			}		
		}
		if(np>end2){
			start3 = end3 = np;
			if(Math.floor(np-num_edge)>=end2){
				start3 = Math.floor(np-num_edge+1);
			}
		}
	}
	//alert('First:'+start1+'-'+end1+', Center:'+start2+'-'+end2+', Last:'+start3+'-'+end3+', total:'+total+', page:'+page+', np:'+np);
	
	var fromPag = Math.floor(Math.floor(Math.floor(page-1)*limit)+1);
	var toPag = Math.floor(page*limit);
	if(toPag>total){toPag = total;}
	if(fromPag>total){fromPag = 1;}
	
	j('#fromtodata').html(fromPag+'-'+toPag+'/'+total);
	
	var html = '<ul class="pagination">';
	if(page > 1){
		html += '<li class="prev"><a href="/'+uri+'/'+(page - 1)+'">&laquo;</a></li>';
	}
	else{
		html += '<li class="disabled prev"><a class="disabled" href="javascript:void(0);">&laquo;</a></li>';
	}
	 
	for ( var i = start1; i <= end1; i++ ) {
		var activeCls = '';
		if(page==i){activeCls = ' class="active"';}
		html   += '<li'+activeCls+'><a href="/'+uri+'/'+i+'">'+i+'</a></li>';
	}
	if ( end1 < start2 ) {
		if ( Math.floor(end1+1) < start2 ) {
			html   += '<li class="disabled"><span>..</span></li>';
		}
		for ( var i = start2; i <= end2; i++ ) {
			var activeCls = '';
			var disabledCls = '';
			if(page==i){
				activeCls = ' class="active"';
				disabledCls = ' class="disabled"';
			}
			html   += '<li'+activeCls+'><a'+disabledCls+' href="/'+uri+'/'+i+'">'+i+'</a></li>';
		}
	}
	if ( end2 < start3 ) {
		if ( Math.floor(end2+1) < start3 ) {
			html   += '<li class="disabled"><span>..</span></li>';
		}
		for ( var i = start3; i <= end3; i++ ) {
			var activeCls = '';
			var disabledCls = '';
			if(page==i){
				activeCls = ' class="active"';
				disabledCls = ' class="disabled"';
			}
			html   += '<li'+activeCls+'><a'+disabledCls+' href="/'+uri+'/'+i+'">'+i+'</a></li>';
		}
	}
	if(np>page){
		html += '<li class="next"><a href="/'+uri+'/'+(page + 1)+'">&raquo;</a></li>';
	}
	else{
		html += '<li class="disabled next"><a class="disabled" href="javascript:void(0);">&raquo;</a></li>';
	}	
	
	html += '</ul>';	 
	return html;
}

function clickPagination(){
	var setCurrentPage = function(url) {
		if(url){
			var uri = j('#pageURI').val();
			var page = url.replace('/'+uri+'/', '');
		}
		else{var page = 1;}
		j("#page").val(page);
		loadTableRows();
	};
	
	if(j("#page").val()==1){
		var pathname = window.location.pathname;
		var targetUrl1 = '/'+j('#pageURI').val();
		var targetUrl = '/'+j('#pageURI').val()+'/'+j("#page").val();
		if(pathname !=targetUrl && pathname !=targetUrl1){
			window.history.pushState({url: "" + targetUrl + ""}, '', targetUrl);
		}
	}
	
	j("#Pagination").html(createLinks());
			
	j('.pagination li a').click(function(e){
		var disClassN = j(this).attr('class');
		if(disClassN=='disabled'){return false;}
		e.preventDefault();
		var targetUrl = j(this).attr('href'),
			targetTitle = j(this).attr('title');				
		window.history.pushState({url: "" + targetUrl + ""}, targetTitle, targetUrl);
		setCurrentPage(targetUrl);
	});

	window.onpopstate = function(e) {
		setCurrentPage(e.state ? e.state.url : null);
	};
}

function connection_dialog(callbackfunction){
	
    var args = Array.prototype.slice.call(arguments, 1);
	
	if(document.cookie.indexOf("connection_retries=") >= 0) {
		var connection_retries = getCookie('connection_retries');
   		if (connection_retries > 4 ) {
			if(j(".disScreen").length){j(".disScreen").remove();}
				
			j( "#dialog-confirm" ).html('<p class="txtleft">Internet connection problem. Retry again.</p>');
	
			j( "#dialog-confirm" ).dialog({
				title: 'Could not connect to internet',
				resizable: false,
				height: "auto",
				width:400,
				modal: true,
				buttons: {
					'Retry':{
						text: 'Retry', class: 'btn btn-primary archive', click: function() {
							j( this ).dialog( "close" );
							if(args.length>0){
								callbackfunction.apply(this, args);
							}
							else{
								callbackfunction();
							}
						},
					}
				}
			});
			
			document.cookie = "failcall="+window.location.href+"; path=/";
			
			var connection_retries = 0;
			var d = new Date();
			d.setTime(d.getTime() + 60000);
			var expires = "expires="+ d.toUTCString();
			document.cookie = "connection_retries="+connection_retries+";" + expires + ";";
			
		}
   		else{
          	setTimeout(function() {
				if(j(".disScreen").length){j(".disScreen").remove();}
				connection_retries++;
				var d = new Date();
				d.setTime(d.getTime() + 60000);
				var expires = "expires="+ d.toUTCString();
				document.cookie = "connection_retries="+connection_retries+";" + expires + ";";
				
				if(args.length>0){
					callbackfunction.apply(this, args);
				}
				else{
					callbackfunction();
				}
			}, 4000);
		}
   	}
	else{
        var connection_retries = 1;
		var d = new Date();
		d.setTime(d.getTime() + 60000);
		var expires = "expires="+ d.toUTCString();
		document.cookie = "connection_retries="+connection_retries+";" + expires + ";";
		
		if(args.length>0){
			callbackfunction.apply(this, args);
		}
		else{
			callbackfunction();
		}
	}
}

function getCookie(cname) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}

function confirm_dialog(title, message, callbackfunction){
    let p = cTag('p',{"class":"txtleft"});
    p.innerHTML = '';
    if(typeof message =='string'){p.innerHTML = message;}
    else{p.appendChild(message);}
    let dialogConfirm = document.querySelector("#dialog-confirm" );
	dialogConfirm.innerHTML = '';
	dialogConfirm.appendChild(p);
	
	j( "#dialog-confirm" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width:400,
		modal: true,
	  	buttons: {
			'Close': {
				text: 'Close', class: 'btn btn-default', click: function() {
					j( this ).dialog( "close" );
				},
			},
			'Confirm':{
				text: 'Confirm', class: 'btn btn-primary archive', click: function() {
					callbackfunction();
				},
			}
      	}
    });
}

function alert_dialog(title, message, btnname){
    
	j( "#dialog-confirm" ).html(message);
	
	j( "#dialog-confirm" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width:500,
		modal: true,
	  	buttons: {
			btnname: {
				text: btnname, class: 'btn btn-primary', click: function() {
					j( this ).dialog( "close" );
				},
			}
      	}
    });
}

function form600dialog(title, formhtml, actionbutton, callbackfunction){
   	j( "#form-dialog" ).html(formhtml);
	
	j( "#form-dialog" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width: j(window).width() > 600 ? 600 : j(window).width(),
		modal: true,
	  	buttons: {
			'Cancel': {
				text: 'Cancel', class: 'btn btn-default', click: function() {
					j( this ).dialog( "close" );
				},
			},
			actionbutton:{
				text: actionbutton, class: 'btn btn-primary btnmodel', click: function() {
					callbackfunction();
				},
			}
      	}
    });
}

function form1000dialog(title, formhtml, callbackfunction){
   	j( "#form-dialog" ).html(formhtml);
	
	j( "#form-dialog" ).dialog({
		title: title,
		resizable: false,
		height: "auto",
		width: j(window).width() > 1000 ? 1000 : j(window).width(),
		modal: true,
	  	buttons: {
			'Cancel': {
				text: 'Cancel', class: 'btn btn-default', click: function() {
					j( this ).dialog( "close" );
				},
			},
			'Save':{
				text:'Save', class: 'btn btn-primary btnmodel', click: function() {
					callbackfunction();
				},
			}
      	}
    });
}

function multiSelectAction(idName11){
	let parent = document.querySelector('#'+idName11);
	if(parent){
		window.addEventListener('click', function(e){
			if (document.querySelector('#'+idName11) && !document.querySelector('#'+idName11).contains(e.target) && document.querySelector('#'+idName11).classList.contains('open')){
				document.querySelector('#'+idName11).classList.remove('open');
				document.querySelector('#'+idName11).querySelector('.dropdownToggle').ariaExpanded = false;
			}
		});
		parent.addEventListener('click',function(){
			parent.classList.toggle("open");
			if(document.querySelector('#'+idName11))
				document.querySelector('#'+idName11).querySelector('.dropdownToggle').ariaExpanded = true;
		})
	}
}		

function htmlRowRemoveById(dynName){
	if(document.getElementById(dynName)){
		j('#'+dynName).remove();
	}
}

function checkNumericInputOnKeydown(field){
    field.addEventListener('keydown',function(){
        if(this.getAttribute('max')||this.getAttribute('min')){
            let oldValue = this.value;
			let pattern = /^-?\d*(\.\d{0,15})?$/;
			if(this.getAttribute('step')==='0.001') pattern = /^-?\d*(\.\d{0,15})?$/;
            setTimeout(() => {
                let invalidPattern = false;
                invalidPattern = !RegExp(pattern).test(this.value);
                let aboveMax = false;
                if(this.getAttribute('max')) aboveMax = Number(this.value) > Number(this.getAttribute('max'));
                let underMin = false;
                if(this.getAttribute('min')) underMin = Number(this.value) < Number(this.getAttribute('min'));
                
                if(invalidPattern||aboveMax||underMin) this.value = oldValue;
            }, 0);
        }
    })
}

async function changeNotification(notifications){
	const appointments_id = document.getElementById("appointments_id").value;
	const jsonData = {appointments_id, notifications};
	const options = {method: "POST", body:JSON.stringify(jsonData), headers:{'Content-Type':'application/json'}};
	const url = '/Appointments/changeNotification';
	let data = await (await fetch(url, options).catch(handleErr)).json();

	if (data.code && data.code == 400){
		connection_dialog(changeNotification);
	}
	else{
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			location.reload();
		 }
	}
}

function emailthispage(){
	j(".emailform").fadeIn("slow");
	j(".sendbtn").val(_Email).prop('disabled', false);
	document.getElementById("email_address").focus();
}

function emaildetails(uri){
	
	var oField = document.getElementById("email_address");
	var email_address = oField.value;
	var appointments_id = j("#appointments_id").val();
	j(".sendbtn").val(_Sending+'...').prop('disabled', true);
	var frompage = j("#frompage").val();
	var amount_due = 0;
	var noteCrOrNot = 1;
	if(jQuery.inArray( frompage, ['POS', 'Repairs', 'Orders'] ) !=-1){
		var amountDue = parseFloat(j("#amount_due").val());
		if(amountDue<0){
			var amount_due = amountDue;
		}
	}
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST",dataType: "json",
		url: uri,
		data: {"appointments_id":appointments_id, "email_address":email_address, amount_due:amount_due, noteCrOrNot: noteCrOrNot}
	}).done(function( data ) {

		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.returnStr !='Ok'){
			j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">'+data.returnStr+'</div></div>').fadeIn(500);
			setTimeout(function() {j("#showmessagehere").slideUp(500)}, 5000);
		}
		else{
			j(".emailform").fadeOut("slow");
			if(uri=='/Appointments/AJsend_AppointmentsEmail' || uri=='/Appointments/jquery_sendMail'){
				checkAndLoadFilterData();
			}
		}

		j(".sendbtn").val(_Email).prop('disabled', false);
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(emaildetails, uri);
	});
	return false;
}

function cancelemailform(){
	j(".emailform").fadeOut("slow");
	j(".sendbtn").val(_Email).prop('disabled', false);
}

function getNotification(){
	fetch('/Home/checkNotification').then(response=>response.json()).then(data=>{
		 if(data.notifications>0){
			  const notifier = document.getElementById('notifier');
			  
			  notifier.play();  

			  const showmessagehere = document.getElementById('showmessagehere');
			  showmessagehere.innerHTML = '<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well alert_msg">You have '+data.notifications+' new orders <a href="/Appointments">check</a></div></div>';
			  showmessagehere.classList.add('display');
			  let timeoutID = setTimeout(()=>showmessagehere.classList.remove('display'),5000);
			  showmessagehere.addEventListener('mouseover',()=>{
					console.log('up')
					clearTimeout(timeoutID);
			  })
			  showmessagehere.addEventListener('mouseleave',()=>{
					console.log('out')
					timeoutID = setTimeout(()=>showmessagehere.classList.remove('display'),3000);
			  })
		  if(segment1==='Invoices'){
			  showmessagehere.querySelector('a').style.display = 'none';
		  }

		 }
	})
}

function loadDateFunction(){
	
	if(j(".DateField").length){
		var dateformat='dd-mm-yy';
		j('.DateField').datepicker({
			dateFormat: dateformat,
			autoclose: true,
			onSelect: function (date, inst) {
				
				var selectedDay = inst.selectedDay.replace('<font style="vertical-align: inherit;">', '').replace('</font>', '');
				selectedDay = selectedDay.replace('<font style="vertical-align: inherit;">', '').replace('</font>', '');
				selectedDay = ('0' + selectedDay).slice(-2);
				
				var selectedMonth = inst.selectedMonth+1;
				selectedMonth = ('0' + selectedMonth).slice(-2);
				
				var selectedYear = inst.selectedYear;
				if(calenderDate.toLowerCase()=='dd-mm-yyyy'){
					var dateVal=selectedDay+'-'+selectedMonth+'-'+selectedYear;
				}
				else{
					var dateVal=selectedMonth+'/'+selectedDay+'/'+selectedYear;
				}
				j(this).val(dateVal);
			}
		});
	}
	
}



/*=============SEO Info Module============*/
function filter_Manage_Data_seo_info(){
	var limit = j('#limit').val();
	var page = 1;
	j("#page").val(page);
	
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();
	jsonData['limit'] = limit;
	jsonData['page'] = page;
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_seo_info/filter",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			j("#totalTableRows").val(data.totalRows);
			
			if(j.inArray(limit, [15, 20, 25, 50, 100, 500])){j("#limit").val(limit);}
			else{j("#limit").val('auto');}
			
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function(){
		connection_dialog(filter_Manage_Data_seo_info);
	});
}

function loadTableRows_Manage_Data_seo_info(){
	var jsonData = {};
	jsonData['keyword_search'] = j('#keyword_search').val();			
	jsonData['totalRows'] = j('#totalTableRows').val();
	jsonData['rowHeight'] = j('#rowHeight').val();	
	jsonData['limit'] = j('#limit').val();
	jsonData['page'] = j('#page').val();
	
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: "/Manage_Data/aJgetPage_seo_info",
		data: jsonData,
	}).done(function( data ) {
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			j("#tableRows").html(data.tableRows);
			onClickPagination();
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {			
		connection_dialog(loadTableRows_Manage_Data_seo_info);
	});
}

function AJsave_seo_info(){
	j("#submit").val('Saving...').prop('disabled', true);
	j("body").append('<div class="disScreen"><img src="/assets/images/ajax-loader.gif"></div>');
	j.ajax({method: "POST", dataType: "json",
		url: '/Manage_Data/AJsave_seo_info/',
		data: j("#frmseo_info").serialize(),
	})
	.done(function( data ) {
		if(data.login !=''){window.location = '/'+data.login;}
		else if(data.savemsg !='error' && (data.returnStr=='Add' || data.returnStr=='Update')){
			resetForm_seo_info();
			if(data.returnStr=='Add'){
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Added successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			else{
				j("#showmessagehere").html('<div class="col-sm-12 col-sm-12 col-md-12"><div class="bs-callout bs-callout-info well success_msg">Updated successfully.</div></div>').fadeIn(500);
				setTimeout(function() {j("#showmessagehere").slideUp(500);}, 5000);
			}
			filter_Manage_Data_seo_info();	
			j("#submit").val('Add').prop('disabled', false);			
		}
		else{
			alert_dialog('Alert message', data.returnStr, 'Ok');			
			j("#submit").val('Add').prop('disabled', false);
		}
		if(j(".disScreen").length){j(".disScreen").remove();}
	})
	.fail(function() {		
		connection_dialog(AJsave_seo_info);
	});
	return false;
}

function resetForm_seo_info(){
	j("#formtitle").html('Add New SEO Info');
	j("#seo_info_id").val(0);
	j("#seo_title").val('');
	j("#uri_value").val('');
	j("#seo_keywords").val('');
	j("#description").val('');
	j("#video_url").val('');
	j("#reset").fadeOut(500);
	j("#archive").fadeOut(500);
	//let editor = document.getElementById('wysiwyrEditor');
	//editor.querySelector("#description").innerHTML = editor.querySelector("#editingArea").contentWindow.document.body.innerHTML = '';
}


/*============================*/
async function checkWidget(){
	let limit = document.getElementById('limit').value;
	let page = document.getElementById('page').value;
	let keyword_search = document.getElementById('keyword_search').value;
	const jsonData = {limit, page, keyword_search};
	const options = {method: "POST", body:JSON.stringify(jsonData), headers:{'Content-Type':'application/json'}};
	const url = '/widget/getAllServices';
	let data = await (await fetch(url, options).catch(handleErr)).json();

	if (data.code && data.code == 400){
		connection_dialog(changeNotification);
	}
	else{
		if(data.login != ''){window.location = '/'+data.login;}
		else{
			//location.reload();
		 }
	}
}



j(document).ready(function(){
	if(segment1 != 'Login'){
		getNotification();
		setInterval(getNotification,60000);
	}
	loadDateFunction();
   //j('.dropdown-toggle').dropdown();
	j(window).click(function() {
		if(document.querySelector(".dropdown-menu") && document.querySelector(".dropdown-menu").classList.contains('active')){
			j(".dropdown-menu").toggle().toggleClass('active');
		}
	 });
	j('.dropdown-toggle').on("click", function(e){
		j(this).next('ul').toggle().toggleClass('active');
		e.stopPropagation();
		e.preventDefault();
	 });

	if (j('#pageURI').length){
		var pathArray = j('#pageURI').val().split('/');
		var loadTableRows = 'loadTableRows_'+pathArray[0]+'_'+pathArray[1];
		var fn = window[loadTableRows];
		if (typeof fn === "function"){onClickPagination();}
		
		checkAndLoadData();
	
		if (j('#keyword_search').length){
			j('#keyword_search').keypress(function (e) {
				if (e.which == 13) {
					var filter = 'filter_'+pathArray[0]+'_'+pathArray[1];
					var fn = window[filter];
					if (typeof fn === "function"){fn();}
				}
			});
		}
	}
	
    setTimeout(function() {
		j("#showmessagehere").slideUp(500);
	},5000);
	if(j("#mathCaptcha").length || j("#frmForgotPass").length){
		j("#users_email").focus();		
	}
	if(j("#mathCaptcha").length){mathCaptcha();}
    // Add active state to sidbar nav links
    var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
	j("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
		if (this.href === path) {
			j(this).addClass("active");
		}
	});

	lefsidemenubar();
	leftsideHide("settingsleftsidemenu",'leftsidemenu');
	leftsideHide("settingsleftsidemenubar",'sidebar-nav');
	leftsideHide('settingsleftsidemenubar','sidebar-nav1');
	multiSelectAction('mydropdown');

	if(segment1 == 'Home'){
		document.getElementById('sideNav').classList.remove('settingslefthide');
	}
	
	if(document.getElementById('fanav') && segment1 != 'Home'){
		lefsidemenubar();
	}

	if(segment1 != 'Home'){
		window.addEventListener('resize', function(){
			if(document.getElementById('fanav')){
				let width,nav, sideBar, sidenav;
				width = window.innerWidth;
				nav = document.getElementById('fanav');
				sideBar = document.getElementById('sideBar');
				sidenav = document.getElementById('sideNav');
	
				if(width >767  ){
					nav.classList.add('hidden');
					sideBar.classList.remove('hidden');
					if(!(sidenav.classList.contains('settingslefthide'))){
						sidenav.classList.add('settingslefthide');
					}
				}
				else{
					nav.classList.remove('hidden');
					sideBar.classList.add('hidden');
					if((sideBar.classList.contains('hidden')) && !(sidenav.classList.contains('settingslefthide'))){
						sideBar.classList.remove('hidden');
					}
					else if(!(sideBar.classList.contains('hidden')) && !(sidenav.classList.contains('settingslefthide'))){
						sideBar.classList.add('hidden');
					}
				}
			}
		});
	}

	if (j('#archive_Data').length){

		if(j("#customer_name").length){
			j('#customer_name').keypress(function (e) {
				if (e.which == 13) {archiveCustomers();return false;}
			});
		}
	}

	if(j("#rememberPassword").length){
		j("#rememberPassword").on("click", function(e) {
			if(this.checked){
				localStorage.setItem("ue", j("#users_email").val());
				localStorage.setItem("up", j("#users_password").val());
				localStorage.setItem("rp", true);
			}
			else{
				localStorage.removeItem('ue');
				localStorage.removeItem('up');
				localStorage.removeItem('rp');
			}
		});
		
		if (localStorage.getItem("ue")  !== null) {
			j("#users_email").val(localStorage.getItem("ue"));
		}
		if (localStorage.getItem("up")  !== null) {
			j("#users_password").val(localStorage.getItem("up"));
		}
		if (localStorage.getItem("rp")  !== null) {
			j("#rememberPassword").prop("checked", true);
		}
	}
	
	if(document.querySelectorAll(".currentPicture")){
		loadPictureHover();
	}
});
