{
    ###################################
    # User data should be added below #
    ###################################

    # e.g. #
    "uidButtonBack": {
        W3Const.w3PropType: W3Const.w3TypeButton,
        W3Const.w3PropString: "sidButtonBack",
        W3Const.w3PropEvent: {
            W3Const.w3EventClick: "W3GoBack()"
        }
    },
    "uidLine": {
        W3Const.w3PropType: W3Const.w3TypeLine
    },
    "uidLineBreak": {
        W3Const.w3PropType: W3Const.w3TypeLineBreak
    },

    # Main Page
    "uidBody": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidHeader",
            "uidMain",
            "uidFooter"
        ]
    },
    "uidHeader": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidTitle",
            "uidLine"
        ],
        W3Const.w3PropCSS: {
            "text-align": "center",
        }
    },
    "uidMain": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidNavigation",
            "uidContent"
        ]
    },
    "uidNavigation": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidNaviDebug",
            "uidLineBreak"
        ],
        W3Const.w3PropCSS: {
            "border-left": "1px solid",
            "padding-left": "5px",
            "float": "left",
        }
    },
    "uidContent": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropClass: "cidLRPadding",
        W3Const.w3PropFunc: {
            W3Const.w3FuncCreator: "W3SelectPage"
        },
        W3Const.w3PropCSS: {
            "float": "left"
        }
    },
    "uidFooter": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidLine",
            "uidCopyright"
        ],
        W3Const.w3PropCSS: {
            "text-align": "center",
            "clear": "both",
            "padding-top": "5px",
        }
    },
    "uidTitle": {
        W3Const.w3PropType: W3Const.w3TypeHeadline,
        W3Const.w3PropAttr: "level=1",
        W3Const.w3PropString: "sidTitle" 
    },
    "uidCopyright": {
        W3Const.w3PropType: W3Const.w3TypeParagraph,
        W3Const.w3PropString: "sidCopyright"
    },
    "uidNaviDebug": {
        W3Const.w3PropType: W3Const.w3TypeLink,
        W3Const.w3PropString: "sidNaviDebug",
        W3Const.w3PropApi: {
            W3Const.w3ApiID: "aidPage",
            W3Const.w3ApiParams: [
            {
                W3Const.w3ApiDataType: W3Const.w3ApiDataTypeUID,
                W3Const.w3ApiDataValue: "uidPageDebug"
            }]
        }
    },
    "uidPageDebug": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidDebugContent",
            "uidLineBreak",
            "uidPageErrorBackButton"
        ],
        W3Const.w3PropCSS: {
            "text-align": "center",
        }
    },
    "uidDebugContent": {
        W3Const.w3PropType: W3Const.w3TypeParagraph,
        W3Const.w3PropString: "sidDebugContent"
    },
    "uidPageError": {
        W3Const.w3PropType: W3Const.w3TypePanel,
        W3Const.w3PropSubUI: [
            "uidErrorContent",
            "uidLineBreak",
            "uidPageErrorBackButton"
        ],
        W3Const.w3PropCSS: {
            "text-align": "center",
        }
    },
    "uidErrorContent": {
        W3Const.w3PropType: W3Const.w3TypeParagraph,
        W3Const.w3PropString: "sidErrorContent"
    },
    "uidPageErrorBackButton": {
        W3Const.w3PropType: W3Const.w3TypeButton,
        W3Const.w3PropPrototype: "uidButtonBack"
    }

    ###################################
    # User data should be added above #
    ###################################
}
