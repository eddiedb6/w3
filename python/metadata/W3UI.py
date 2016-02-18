import sys
sys.path.append("..")
import W3Const

w3UI = {
    ###################################
    # User data should be added below #
    ###################################

    # e.g. #
    "uidHeader": {
        W3Const.w3PropCSS: {
            "text-align": "center"
        }
    },

    "uidNavigation": {
        W3Const.w3PropCSS: {
            "width": "20%",
            "float": "right",
            "border-style": "solid",
            "border-left-width": "1px",
            "padding-left": "5px",
            "border-top-width": "0px",
            "border-right-width": "0px",
            "border-bottom-width": "0px"
        }
    },

    "uidPage": {
        W3Const.w3PropCSS: {
            "width": "80%"
        }
    },
    
    "uidFooter": {
        W3Const.w3PropCSS: {
            "text-align": "center"
        }
    },
    
    "uidTitle": { 
        W3Const.w3PropString: "sidTitle" 
    },
    "uidCopyright": {
        W3Const.w3PropString: "sidCopyright"
    },
    "uidNaviDebug": {
        W3Const.w3PropType: W3Const.w3TypeLink,
        W3Const.w3PropBody: "sidNaviDebug",
        W3Const.w3PropApi: {
            W3Const.w3ApiID: "aidPage",
            W3Const.w3ApiParam1: "uidPageDebug"
        }
    },
    "uidPageDebug": {
        W3Const.w3PropType: W3Const.w3TypePage,
        W3Const.w3PropFile: "html/W3PageDebug.html"
    },
    "uidPageError": {
        W3Const.w3PropType: W3Const.w3TypePage,
        W3Const.w3PropFile: "html/W3PageError.html"
    },
    "uidButtonBack": {
        W3Const.w3PropType: W3Const.w3TypeButton,
        W3Const.w3PropBody: "sidButtonBack",
        W3Const.w3PropEvent: {
            W3Const.w3EventClick: "W3GoBack()"
        }
    },
    "uidPageErrorBackButton": {
        W3Const.w3PropPrototype: "uidButtonBack"
    }
    
    ###################################
    # User data should be added above #
    ###################################
}
