# Folder and file def
w3DirMeta = "python/metadata"
w3DirServer = "htdocs"
w3DirHelper = "python"
w3DirDB = "db"
w3DirGenerated = "generated"

w3FileStringPHP = "language.php"
w3FileStringJS = "language.js"
w3FileAPIPHP = "api.php"
w3FileAPIJS = "api.js"
w3FileUIPHP = "ui.php"
w3FileUIJS = "ui.js"
w3FileUICSS = "ui.css"
w3FileConstPHP = "const.php"
w3FileConstJS = "const.js"

# Server output folders
w3DirPHP = "php"
w3DirJS = "js"
w3DirCSS = "css"

# W3 element types
w3ElementType = "elementtype"
w3TypeApi = "typeapi"
w3TypeClass = "typeclass"
# Define "ui" types
w3TypeLink = "typelink"
w3TypeTable = "typetable"
w3TypeCheckbox = "typecheckbox"
w3TypeLabel = "typelabel"
w3TypeDatePicker = "typedatepicker"
w3TypeMonthPicker = "typemonthpicker"
w3TypeButton = "typebutton"
w3TypeForm = "typeform"
w3TypeSubmit = "typesubmit"
w3TypeText = "typetext"
w3TypeCombobox = "typecombobox"
w3TypeTab = "typetab"
w3TypePanel = "typepanel"
w3TypeHeadline = "typeheadline"
w3TypeLine = "typeline"
w3TypeLineBreak = "typelinebreak"
w3TypeParagraph = "typeparagraph"
w3TypeCanvas = "typecanvas"
# Collect types
w3ElementTypeCollection = [
    w3TypeApi,
    w3TypeClass,

    w3TypeLink,
    w3TypeTable,
    w3TypeCheckbox,
    w3TypeLabel,
    w3TypeDatePicker,
    w3TypeMonthPicker,
    w3TypeButton,
    w3TypeForm,
    w3TypeSubmit,
    w3TypeText,
    w3TypeCombobox,
    w3TypeTab,
    w3TypePanel,
    w3TypeHeadline,
    w3TypeLine,
    w3TypeLineBreak,
    w3TypeParagraph,
    w3TypeCanvas
]

# API properties
w3ApiID = "apiid"
w3ApiName = "apiname"
w3ApiParams = "apiparams"
w3ApiData = "apidata"
w3ApiDataType = "apidatatype"
w3ApiDataValue = "apidatavalue"
# API data types
w3ApiDataTypeString = "apivaluetypestring"
w3ApiDataTypeNum = "apivaluetypenum"
w3ApiDataTypeUID = "apivaluetypeuid"
w3ApiDataTypeCollection = [
    w3ApiDataTypeString,
    w3ApiDataTypeNum,
    w3ApiDataTypeUID
]
w3ApiResult = "apiresult"
w3ApiResultStatus = "apiresultstatus"
w3ApiResultData = "apiresultdata"
w3ApiResultSuccessful = "apiresultsuccessful"
w3ApiResultFailed = "apiresultfailed"

# Define "ui" properties
w3PropType = "elementtype" # w3ElementType
w3PropSubUI = "propsubui"
w3PropString = "propstring"
w3PropApi = "propapi"
w3PropEvent = "propevent"
w3PropFunc = "propfunc"
w3PropAttr = "propattr"
w3PropClass = "propclass"
w3PropPrototype = "propprototype"
w3PropCSS = "propcss"
# Collect UI properties
w3PropCollection = [
    w3PropType,
    w3PropSubUI,
    w3PropString,
    w3PropApi,
    w3PropEvent,
    w3PropFunc,
    w3PropAttr,
    w3PropClass,
    w3PropPrototype,
    w3PropCSS
]

# Define event
w3EventClick = "eventclick"
w3EventCollection = [
    w3EventClick
]

# Define function
w3FuncCreator = "funccreator"
w3FuncCollection = [
    w3FuncCreator
]

# Define language
w3LanEnglish = "lanenglish"

# Define log level
w3LogDebug = 0
w3LogInfo = 1
w3LogWarning = 2
w3LogError = 3
w3LogFatal = 4
w3LogLevel = 0
