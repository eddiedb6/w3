{
    SchemaConfigRoot: {
        SchemaType: SchemaTypeDict
    },
    SchemaAnyOther: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            CheckAsTypeFromKey(W3Const.w3ElementType)
        ]
    },
    W3Const.w3ElementType: {
        SchemaType: SchemaTypeString,
        SchemaRule: [
            ValueIn(W3Const.w3ElementTypeCollection)
        ]
    },

    # String 
    W3Const.w3LanEnglish: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            IgnoreChildSchema()
        ]
    },

    # API
    W3Const.w3TypeApi:
    {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3ApiName, W3Const.w3ApiHandler),
            KeyIn([W3Const.w3ElementType, W3Const.w3ApiName, W3Const.w3ApiParams, W3Const.w3ApiResult, W3Const.w3ApiHandler, W3Const.w3ApiListener]) 
        ]
    },
    W3Const.w3ApiID: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiName: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiData: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3ApiDataType, W3Const.w3ApiDataValue),
            KeyIn([W3Const.w3ApiDataType, W3Const.w3ApiDataValue])
        ]
    },
    W3Const.w3ApiDataType: {
        SchemaType: SchemaTypeString,
        SchemaRule: [
            ValueIn(W3Const.w3ApiDataTypeCollection)
        ]
    },
    W3Const.w3ApiDataValue: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParams: {
        SchemaType: SchemaTypeArray,
        SchemaRule: [
            CheckForeachAsType(W3Const.w3ApiData)
        ]
    },
    W3Const.w3ApiResult: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn([W3Const.w3ApiResultStatus, W3Const.w3ApiResultData])
        ]
    },
    W3Const.w3ApiResultStatus: {
        SchemaType: SchemaTypeArray
    },
    W3Const.w3ApiResultData: {
        SchemaType: SchemaTypeArray,
        SchemaRule: [
            CheckForeachAsType(W3Const.w3ApiData)
        ]
    },
    W3Const.w3ApiHandler: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiListener: {
        SchemaType: SchemaTypeArray
    },
    W3Const.w3ApiCall: {
        SchemaType: SchemaTypeString,
        SchemaRule: [
            ValueIn(W3Const.w3ApiCallCollection)
        ]
    },

    # Class
    W3Const.w3TypeClass: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3PropCSS),
            KeyIn([W3Const.w3PropCSS, W3Const.w3PropType])
        ]
    },

    # Default UI
    W3Const.w3UIBody: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            CheckAsTypeFromKey(W3Const.w3ElementType),
            HasKey(W3Const.w3PropDefaultPage, W3Const.w3PropDefaultErrorPage)
        ]
    },

    # UI Type
    W3Const.w3TypeButton: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3PropCollection)
        ]
    },
    W3Const.w3TypeLink: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3PropCollection),
            HasKey(W3Const.w3PropTriggerApi)
        ]
    },
    W3Const.w3TypeTable: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeTableHeader: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeCheckbox: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeLabel: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeDatePicker: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeMonthPicker: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeText: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeCombobox: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeTab: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypePanel: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeCanvasPanel: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypePassword: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeHeadline: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeLine: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeLineBreak: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeParagraph: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeCanvas: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypePage: {
        SchemaInherit: W3Const.w3TypeButton
    },

    # UI Prop
    W3Const.w3PropDefaultPage: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropDefaultErrorPage: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropDefaultAuthenticationPage: {
        SchemaType: SchemaTypeString
    },    
    W3Const.w3PropCSS: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            IgnoreChildSchema()
        ]
    },
    W3Const.w3PropSubUI: {
        SchemaType: SchemaTypeArray
    },
    W3Const.w3PropString: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropEvent: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3EventCollection)
        ]
    },
    W3Const.w3PropFunc: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3FuncCollection)
        ]
    },
    W3Const.w3PropClass: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropPrototype: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropAttr: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            IgnoreChildSchema()
        ]
    },
    W3Const.w3PropBindingVar: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3BindingVarName),
            KeyIn([W3Const.w3BindingVarName, W3Const.w3BindingFormat])
        ]
    },
    W3Const.w3PropSinkApi: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3ApiID),
            KeyIn([W3Const.w3ApiID, W3Const.w3SinkRow, W3Const.w3SinkMatrix])
        ]
    },
    W3Const.w3PropTriggerApi: {
        SchemaType: SchemaTypeArray,
        SchemaRule: [
            CheckForeachAsType(W3Const.w3TriggerApi)
        ]
    },

    # Trigger & Binding
    W3Const.w3TriggerApi: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3ApiID),
            KeyIn([W3Const.w3ApiID, W3Const.w3ApiParams, W3Const.w3ApiCall])
        ]
    },
    W3Const.w3SinkRow: {
        SchemaType: SchemaTypeArray,
        SchemaRule: [
            CheckForeachAsType(W3Const.w3ApiData)
        ]
    },
        
    W3Const.w3SinkMatrix: {
        SchemaType: SchemaTypeArray,
        SchemaRule: [
            CheckForeachAsType(W3Const.w3SinkRow)
        ]
    },
    W3Const.w3BindingVarName: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3BindingFormat: {
        SchemaType: SchemaTypeString
    },

    # Event
    W3Const.w3EventClick: {
        SchemaType: SchemaTypeArray
    },

    # Functor
    W3Const.w3FuncCreator: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3FuncProcessor: {
        SchemaType: SchemaTypeArray
    }
    
}
