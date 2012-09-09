var isIE = navigator.appName == 'Microsoft Internet Explorer' && navigator.userAgent.indexOf('Opera') < 1 ? true : false;

function getHTTPRequestObject() {
	if(window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if (window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		return null
	}		
}

function getAllStyleSheets() {
	var result = new Array();
	var links = document.getElementsByTagName("link");

	for (var i = 0; i < links.length; i++) {
		if ((links[i].rel.toLowerCase() == "stylesheet" || links[i].rel.toLowerCase() == "alternate stylesheet") && links[i].title != "") {
			result[result.length] = links[i];
		}
	}
	
	return result;
}

function changeStyle(stylename) {
	var stylesheets = getAllStyleSheets();

	for (var i = 0; i < stylesheets.length; i++) {
		stylesheets[i].disabled = true;
		if (stylesheets[i].title == stylename) {
			stylesheets[i].disabled = false;
		}
	}
}

function styleChangerHandler(styleID) {
	this.styleID = styleID;

	this.handleEvent = function(e) {
		var requestObject = getHTTPRequestObject();
		requestObject.open("GET", "changestyle.php?style=" + this.styleID + "&mode=ajax");
		requestObject.send(null);
		changeStyle(stylesNames[this.styleID]);
		e.preventDefault();
	};
}

function clearElement(element) {
	while (element.hasChildNodes()) {
		element.removeChild(element.firstChild);
	}
}

var newerPostCheckIntervalID = 0;

if (isIE) {
	var newMessagesRequestObject;
}

function checkForNewerPostHandler() {
	var requestObject = this;
	if (isIE) {
		requestObject = newMessagesRequestObject;
	}
	if (requestObject.readyState == 4 && requestObject.status == 200 && requestObject.responseXML != null) {
		var links = requestObject.responseXML.getElementsByTagName('link')
		if (links.length > 0) {
			var newerPostLink = links[0];

			var newerPostMessageContainer = document.getElementById("newerTopicMessage");
			clearElement(newerPostMessageContainer);
			newerPostMessageContainer.style.display = "block";
			
			var newerPostMessage = document.createElement("p");
			newerPostMessage.appendChild(document.createTextNode(newMessageString));
			newerPostMessageContainer.appendChild(newerPostMessage);

			var actionsContainer = document.createElement("p");
			var reloadPageLink = document.createElement("a");
			reloadPageLink.href = currentResourceLink;
			reloadPageLink.appendChild(document.createTextNode(reloadCurrentPageString));
			actionsContainer.appendChild(reloadPageLink);
			actionsContainer.appendChild(document.createElement("br"));
			var goToPostLink = document.createElement("a");
			goToPostLink.href = newerPostLink.firstChild.nodeValue;
			goToPostLink.appendChild(document.createTextNode(goToPostString));
			actionsContainer.appendChild(goToPostLink);
			newerPostMessageContainer.appendChild(actionsContainer);
			actionsContainer.appendChild(document.createElement("br"));
			var closeLink = document.createElement("span");
			closeLink.appendChild(document.createTextNode(closeThisBoxString));
			closeLink.className = "link";
			EventUtils.addEventHandler(closeLink, "click", {
					handleEvent: function(e) {
						newerPostMessageContainer.style.display = "none";
						clearElement(newerPostMessageContainer);
					}
				});
			actionsContainer.appendChild(closeLink);

			clearInterval(newerPostCheckIntervalID);
		}
	}
}

function checkForNewerPost() {
	var requestObject = getHTTPRequestObject();
	if (isIE) {
		newMessagesRequestObject = requestObject;
	}
	requestObject.onreadystatechange = checkForNewerPostHandler;
	requestObject.open("GET", "getnewerpost.php?topicid=" + topicID + "&postid=" + lastPostID + "&postedTime=" + lastPostPostedTime);
	requestObject.send(null);
}

function init() {
	var styleChangerLinks = document.getElementById("styleChooser").getElementsByTagName("a");

	for (var i = 0; i < styleChangerLinks.length; i++) {
		var styleID = styleChangerLinks[i].href.split("changestyle.php?style=")[1];
		EventUtils.addEventHandler(styleChangerLinks[i], "click", new styleChangerHandler(styleID));
	}
	
	if (viewingTopic) {
		newerPostCheckIntervalID = setInterval(checkForNewerPost, 20000);
	}
}
