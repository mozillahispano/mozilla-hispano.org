function get_ajax()
{
	try { return new XMLHttpRequest(); } catch (e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch(e) {}
	try { return new ActiveXObject("Microsoft.XMLHTTP"); } catch (e) {}
	return null;
}

// Emulate PHP's urlencode function
function urlencode(s)
{
	s = encodeURIComponent(s);
	s = s.replace('%20', '+');
	s = s.replace('*', '%2A');
	s = s.replace("!", '%21');
	s = s.replace("'", '%27');
	s = s.replace('(', '%28');
	s = s.replace(')', '%29');
	s = s.replace('~', '%7E');
	return s;
}

function urldecode(s)
{
	//s = s.replace('+', '%20'); //php response has literal plus signs
	s = s.replace('%2A', '*');
	s = s.replace('%21', "!");
	s = s.replace('%27', "'");
	s = s.replace('%28', '(');
	s = s.replace('%29', ')');
	s = s.replace('%7E', '~');
	try {
		s = decodeURIComponent(s);
	} catch(e) {};
	return s;
}

function prime_subject_check(wait)
{
	if (!(ajax = get_ajax()))
	{
		return true;
	}
	var subject	= postform.subject.value.replace(/^\s+|\s+$/g, '');
	if (subject == '' || subject == postform.subject_checked.value)
	{
		return (subject == '' || parseInt(postform.subject_check_bypass.value)) ? true : false;
	}
	var obj_r = document.getElementById('prime_subject_check_ajax_results');
	var obj_d = document.getElementById('prime_subject_check_ajax_display');
	obj_r.innerHTML = subject_check_working;
	obj_d.style.display = '';
	postform.subject_checked.value = subject;

	ajax.onreadystatechange = wait ? null : function() { if (ajax.readyState == 4) { prime_subject_check_response(obj_r, obj_d); } };
	ajax.open("GET", subject_check_action + '&subject=' + urlencode(subject), !wait);
	ajax.send(null);
	return (wait ? prime_subject_check_response(obj_r, obj_d) : false);
}

function prime_subject_check_response(obj_r, obj_d)
{
	var topic_list = '';
	if (ajax.responseText)
	{
		topic_list = ajax.responseText;
		var s_str = '&subject=', s_pos = topic_list.lastIndexOf(s_str);
		var b_str = '&bypass=', b_pos = topic_list.lastIndexOf(b_str);
		if (s_pos >= 0 && b_pos > 0)
		{
			postform.subject_check_bypass.value = topic_list.substr(b_pos + b_str.length);
			topic_list = topic_list.substr(0, b_pos);
			postform.subject_checked.value = urldecode(topic_list.substr(s_pos + s_str.length));
			topic_list = topic_list.substr(0, s_pos);
		}
	}
	if (topic_list)
	{
		obj_r.innerHTML = topic_list;
		obj_d.style.display = '';
		return false;
	}
	obj_d.style.display = 'none';
	return true;
}

var ajax = null, postform = document.getElementById('postform') || document.getElementsByName('postform')[0];
if (postform)
{
	postform.onsubmit = function() {
		var result = prime_subject_check(true), msg = document.getElementById('prime_subject_check_ajax_results').innerHTML;
		if (!result && msg && (msg = msg.replace(/<br ?\/?>.*/i, "").replace(/(<([^>]+)>)/ig, "")))
		{
			alert(msg)
		}
		return result;
	}
	var subject_obj = document.getElementById('subject') || document.getElementsByName('subject')[0];
	if (subject_obj)
	{
		subject_obj.onblur = function() { prime_subject_check(false); }
	}
}