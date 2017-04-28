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
            HasKey(W3Const.w3ApiName)
        ]
    },
    W3Const.w3ApiID: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiName: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam1: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam2: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam3: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam4: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam5: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam6: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam7: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam8: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3ApiParam9: {
        SchemaType: SchemaTypeString
    },

    # Class
    W3Const.w3TypeClass: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3PropCSS)
        ]
    },

    # UI
    W3Const.w3TypeButton: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3PropCollection)
        ]
    },
    W3Const.w3TypeLink: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeTable: {
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
    W3Const.w3TypeForm: {
        SchemaInherit: W3Const.w3TypeButton
    },
    W3Const.w3TypeSubmit: {
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
    W3Const.w3PropApi: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            HasKey(W3Const.w3ApiID)
        ]
    },
    W3Const.w3PropEvent: {
        SchemaType: SchemaTypeDict,
        SchemaRule: [
            KeyIn(W3Const.w3EventCollection)
        ]
    },
    W3Const.w3PropFunc: {
        SchemaType: SchemaTypeDict        
    },
    W3Const.w3PropClass: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropPrototype: {
        SchemaType: SchemaTypeString
    },
    W3Const.w3PropAttr: {
        SchemaType: SchemaTypeString
    },

    # Event
    W3Const.w3EventClick: {
        SchemaType: SchemaTypeString
    },

    # Functor
    W3Const.w3FuncCreator: {
        SchemaType: SchemaTypeString
    }
}
