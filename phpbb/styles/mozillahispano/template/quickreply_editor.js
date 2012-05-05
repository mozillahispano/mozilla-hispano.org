// Check for Browser & Platform for PC & IE specific bits
// More details from: http://www.mozilla.org/docs/web-developer/sniffer/browser_type.html
var clientPC = navigator.userAgent.toLowerCase(); // Get client info
var clientVer = parseInt(navigator.appVersion); // Get browser version

var is_ie = ((clientPC.indexOf('msie') != -1) && (clientPC.indexOf('opera') == -1));
var is_win = ((clientPC.indexOf('win') != -1) || (clientPC.indexOf('16bit') != -1));


//Quick Reply
var isNav4Min = (navigator.appName == "Netscape" && parseInt(navigator.appVersion) >= 4)
var isIE4Min = (navigator.appName.indexOf("Microsoft") != -1 && parseInt(navigator.appVersion) >= 4)
function quoteSelection() 
{
	var userSelection = false;
	var textarea = document.postform.message;

	if (isNav4Min && window.getSelection() !=  '') {
		userSelection = window.getSelection();
	}
	else if (isIE4Min && document.selection) {
		userSelection = document.selection.createRange().text;
	}
			
	if (userSelection) {
		insert_text( '[quote]\n' + userSelection + '\n[/quote]\n', true, false);
		textarea.focus();
		userSelection = '';
		return;
	}
	else
	{
		alert(LANG_L_NO_TEXT_SELECTED);
	}
}

function checkQuickForm() {
	formErrors = false;
		
	if (document.postform.message.value.length <= 2) {
		formErrors = LANG_TOO_FEW_CHARS;
	}

	if (formErrors)
	{
		alert(formErrors);
		return false;
	} 
	else
	{
		if (document.postform.quote_last_msg.checked) 
		{
			document.postform.message.value = document.postform.last_post.value + document.postform.message.value;
		} 
		return true;
	}
}
//Quick Reply
