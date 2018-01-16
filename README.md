# w3
Windows like web site workspace

* UI Body
It's W3Const.w3UIBody and it must be defined as "uid" in UI define file.
And there are two properties also must be defined for w3UIBody:
    1. W3Const.w3PropDefaultPage
    2. W3Const.w3PropDefaultErrorPage

* UI Page
UI type "Page" is bound to API "page".
In UI "Page" it will always display the page specifyied in get page API by pid.
Refer below section "HTML UI Logic" for page loading

* Datetime format
It's "yy-mm-dd"

* Log level
It's defined in W3Const.w3LogLevel

* HTML UI Logic
index.html
    W3Main.php
        "Is empty request?"
	    "Yes" -------------------------------------------------------,
	    "No"  -> W3HandleRequest@W3RequestHandler.php                |
	    	         "Is page request?"                              |
			     "No"  -> W3APIHandleRequest@api.php         |
			     "Yes" -> W3OnRequestPage@W3Util.php <-------`
			                  W3Main.html 
        				      W3LoadPage@W3PageHandler.php
					          W3CreateUI@W3.php
					              w3TypePage -> W3CreatePage@W3Util.php
						                        W3SelectPage@W3PageHandler.php
							                 |
* UI Structure							         |
w3UIBody = "uibody": w3TypePanel				         |
    "uidMain": w3TypePanel					         |
        "uidPage": w3TypePage					         |
	    "uidPageXXX": w3TypePanel <----------------------------------`
	    ...

* API Binding
    1. Trigger API on control event
        a. In w3PropTriggerApi define API array, and each API array item is to define how API parameters are filled
	b. In w3PropEvent.w3EventXXX define which API will be tiggered in this event by w3PlaceHolder_X ("X" is the API offset in w3PropTriggerApi array)
	c. When create control event in PHP, above relationship will be used and insert code into JS automatically
    2. Sink API result to display by control
        a. Define w3PropSinkApi in control
	b. Then metadata handler will handle this and insert listener to API define

* Variable Binding
    1. Define w3PropBindingVar in control
    2. Then metadata UI handler will add the control to variable listener list
    3. When W3SetVariable is called, the control will be updated automatically
    4. There is default variable "session" and do not bind to it
    5. The variable could not cross page except "session"
    
* W3 API
JS:				PHP:

/*------------------ Logger ------------------*/
W3LogDebug			W3LogDebug
W3LogInfo			W3LogInfo
W3LogWarning			W3LogWarning
W3LogError			W3LogError
W3LogFatal			W3LogFatal

/*------------------ Language ------------------*/
W3GetLanguage			W3GetLanguage
W3GetStringValue		W3GetStringValue

/*------------------ API ------------------*/
W3GetAPIDef			W3GetAPIDef
W3GetAPIParamCount		W3GetAPIParamCount
				W3GetAPIParamIndex
W3CreateAPI			W3CreateAPI
				W3CreateSuccessfulResult
				W3CreateFailedResult
				W3CreateAuthenticationResult
W3CallAPI			
W3CallAPIAsync
W3CallAPISync

/*------------------ UI ------------------*/
W3GetUIDef			W3GetUIDef
W3GetUIValue
W3TryGetUIProperty		W3TryGetUIProperty
W3DisplayUI
W3HideUI

/*------------------ Variable ------------------*/
W3GetVariable
W3SetVariable

/*------------------ Session ------------------*/
				W3GetSession

/*------------------ String ------------------*/
				W3MakeString
				W3MakeDateString

/*------------------ Formatter ------------------*/
W3FormatCurrency
W3FormatCurrencyColor
W3FormatDatetime

/*------------------ Graphic ------------------*/
W3DrawPercentageReport

/*------------------ Other ------------------*/
W3GoBack

