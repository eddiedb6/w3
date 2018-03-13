# Folder and file def
w3DirServer = "htdocs"
w3DirGenerated = "generated"
w3DirUserFile = "user"

w3FileStringPHP = "language.php"
w3FileStringJS = "language.js"
w3FileAPIPHP = "api.php"
w3FileAPIJS = "api.js"
w3FileUIPHP = "ui.php"
w3FileUIJS = "ui.js"
w3FileUICSS = "ui.css"
w3FileConstPHP = "const.php"
w3FileConstJS = "const.js"
w3FileUserPHP = "user.php"

# Server output folders
w3DirPHP = "php"
w3DirJS = "js"
w3DirCSS = "css"

# Default UI
w3UIBody = "uibody"

# Session Variable
w3Session = "w3globalsessionvar"

# W3 element types
w3ElementType = "elementtype"
w3TypeApi = "typeapi"
w3TypeClass = "typeclass"
# Define "ui" types
w3TypeLink = "typelink"
w3TypeTable = "typetable"
w3TypeTableHeader = "typetableheader"
w3TypeCheckbox = "typecheckbox"
w3TypeLabel = "typelabel"
w3TypeDatePicker = "typedatepicker"
w3TypeMonthPicker = "typemonthpicker"
w3TypeButton = "typebutton"
w3TypeText = "typetext"
w3TypePassword = "typepassword"
w3TypeCombobox = "typecombobox"
w3TypeTab = "typetab"
w3TypePanel = "typepanel"
w3TypeCanvasPanel = "typecanvaspanel"
w3TypeHeadline = "typeheadline"
w3TypeLine = "typeline"
w3TypeLineBreak = "typelinebreak"
w3TypeParagraph = "typeparagraph"
w3TypeCanvas = "typecanvas"
w3TypePage = "typepage"
w3TypeTextEditor = "typetexteditor"
# Collect types
w3ElementTypeCollection = [
    w3TypeApi,
    w3TypeClass,

    w3TypeLink,
    w3TypeTable,
    w3TypeTableHeader,
    w3TypeCheckbox,
    w3TypeLabel,
    w3TypeDatePicker,
    w3TypeMonthPicker,
    w3TypeButton,
    w3TypeText,
    w3TypePassword,
    w3TypeCombobox,
    w3TypeTab,
    w3TypePanel,
    w3TypeCanvasPanel,
    w3TypeHeadline,
    w3TypeLine,
    w3TypeLineBreak,
    w3TypeParagraph,
    w3TypeCanvas,
    w3TypePage,
    w3TypeTextEditor
]

# API properties
w3ApiID = "apiid"
w3ApiName = "apiname"
w3ApiParams = "apiparams"
w3ApiData = "apidata"
w3ApiDataType = "apidatatype"
w3ApiDataValue = "apidatavalue"
# API data types
w3ApiDataTypeNone = "apivaluetypenone"
w3ApiDataTypeString = "apivaluetypestring"
w3ApiDataTypeNum = "apivaluetypenum"
w3ApiDataTypeUID = "apivaluetypeuid"
w3ApiDataTypeSID = "apivaluetypesid"
w3ApiDataTypeVar = "apivaluetypevar"
w3ApiDataTypeCollection = [
    w3ApiDataTypeNone,
    w3ApiDataTypeString,
    w3ApiDataTypeNum,
    w3ApiDataTypeUID,
    w3ApiDataTypeSID,
    w3ApiDataTypeVar
]
w3ApiResult = "apiresult"
w3ApiResultStatus = "apiresultstatus"
w3ApiResultData = "apiresultdata"
w3ApiResultSuccessful = "apiresultsuccessful"
w3ApiResultFailed = "apiresultfailed"
w3ApiResultAuthentication = "apiresultauthentication"
w3ApiHandler = "apihandler"
w3ApiListener = "apilistener"
# API call
w3ApiCall = "apicall"
w3ApiDirect = "apidirect"
w3ApiAsync = "apiasync"
w3ApiSync = "apisync"
w3ApiCallCollection = [
    w3ApiDirect,
    w3ApiSync,
    w3ApiSync
]

# Define "ui" properties
w3PropDefaultPage = "propdefaultpage"
w3PropDefaultErrorPage = "propdefaulterrorpage"
w3PropDefaultAuthenticationPage = "propdefaultauthenticationpage"
w3PropType = "elementtype" # w3ElementType
w3PropID = "propid"
w3PropSubUI = "propsubui"
w3PropString = "propstring"
w3PropEvent = "propevent"
w3PropFunc = "propfunc"
w3PropAttr = "propattr"
w3PropClass = "propclass"
w3PropPrototype = "propprototype"
w3PropCSS = "propcss"
w3PropBindingVar = "propbindingvar"
w3PropSinkApi = "propsinkapi"
w3PropTriggerApi = "proptriggerapi"
# Collect UI properties
w3PropCollection = [
    w3PropDefaultPage,
    w3PropDefaultErrorPage,
    w3PropDefaultAuthenticationPage,
    w3PropType,
    w3PropID,
    w3PropSubUI,
    w3PropString,
    w3PropEvent,
    w3PropFunc,
    w3PropAttr,
    w3PropClass,
    w3PropPrototype,
    w3PropCSS,
    w3PropBindingVar,
    w3PropSinkApi,
    w3PropTriggerApi
]

# Define trigger
w3TriggerApi = "triggerapi"

# Define sinker
w3SinkRow = "sinkrow"
w3SinkMatrix = "sinkmatrix"

# Define binding
w3BindingVarName = "bindingvarname"
w3BindingType = "bindingtype"
w3BindingFormat = "bindingformat"

w3BindingUIDisplay = "bindinguidisplay"
w3BindingTypeCollection = [
    w3BindingUIDisplay
]

# Define variable
w3VariableValue = "variablevalue"
w3VariableListeners = "variablelisteners"
w3VariableFormat = "variableformat"

# Define "ui" attributes
w3AttrHeadlineLevel = "attrheadlinelevel"

# Define generic uid
w3UIDUndefined = "uidUndefined"

# Define event
w3EventClick = "onclick" # It's js event name and DO NOT change it
w3EventCollection = [
    w3EventClick
]

# Define function
w3FuncCreator = "funccreator"
w3FuncProcessor = "funcprocessor"
w3FuncCollection = [
    w3FuncCreator,
    w3FuncProcessor
]

# Define function parameter holder
w3PlaceHolder_1 = "w3PlaceHolder_1"
w3PlaceHolder_2 = "w3PlaceHolder_2"
w3PlaceHolder_3 = "w3PlaceHolder_3"
w3PlaceHolder_4 = "w3PlaceHolder_4"
w3PlaceHolder_5 = "w3PlaceHolder_5"
w3PlaceHolder_6 = "w3PlaceHolder_6"
w3PlaceHolder_7 = "w3PlaceHolder_7"
w3PlaceHolder_8 = "w3PlaceHolder_8"
w3PlaceHolder_9 = "w3PlaceHolder_9"

# Define language
w3LanEnglish = "lanenglish"

# Datetime format
# Keep the same as MySQL and DO NOT change it
w3DatetimeFormat = "YYYY-MM-DD HH:MM:SS"
w3DateFormat = "YYYY-MM-DD"
w3MonthFormat = "YYYY-MM"

# Define log level
w3LogDebug = 0
w3LogInfo = 1
w3LogWarning = 2
w3LogError = 3
w3LogFatal = 4
