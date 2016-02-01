import sys
sys.path.append("..")
import W3Const

w3UI = {
    ###################################
    # User data should be added below #
    ###################################

    # e.g. #
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
