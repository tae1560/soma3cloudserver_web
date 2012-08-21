/**
 * @author Tae-ho Lee
 */


function post_to_url(path, params, method) {
    method = method || "post"; // Set method to post by default, if not specified.
 
    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
 
    for(var key in params) {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", key);
        hiddenField.setAttribute("value", params[key]);
 
        form.appendChild(hiddenField);
    }
 
    document.body.appendChild(form);
    form.submit();
}

function basename(path) {
    return path.replace(/\\/g,'/').replace( /.*\//, '' );
}
 
function dirname(path) {
    return path.replace(/\\/g,'/').replace(/\/[^\/]*$/, '');;
}

////////////////// ajax util start ////////////////////

// ajax util function
function sendRequest(url, method, paramString, callBack) {
	var webServiceUrl = url;
	var requestUrl = webServiceUrl + '?method=' + method;
	var params = json_parse(paramString);
	for(var i = 0; i < params.length; i++)
	requestUrl = requestUrl + "&" + params[i].key + "=" + params[i].value;
	//window.alert(requestUrl);
	try {
		var asyncRequest = null;
		if(window.XMLHttpRequest) {
			asyncRequest = new XMLHttpRequest();
		} else if(window.ActiveXObject) {
			asyncRequest = new ActiveXObject("Microsoft.XMLHTTP");
		}
		asyncRequest.onreadystatechange = function() {
			callBack(asyncRequest, method);
		};
		asyncRequest.open('GET', requestUrl, true);
		//				asyncRequest.setRequestHeader('Accept', 'application/json; charset=utf-8');
		asyncRequest.send();
	} catch (exception) {
		alert('Request Failed');
	}
}

function json_parse(str) {
	return eval('(' + str + ')');
	// or JSON.parse(paramString) or paramString.parseJSON()
}

////////////////// ajax util end ////////////////////