import sys
sys.path.append("..")
import Const

ui = {
    ###################################
    # User data should be added below #
    ###################################

    # e.g. #
    "uidTitle": { 
        Const.propString: "sidTitle" 
    },
    "uidCopyright": {
        Const.propString: "sidCopyright"
    },
    "uidNaviDebug": {
        Const.propType: Const.typeLink,
        Const.propBody: "sidNaviDebug",
        Const.propApi: {
            Const.apiID: "aidPage",
            Const.apiParam1: "uidPageDebug"
        }
    },
    "uidPageDebug": {
        Const.propType: Const.typePage,
        Const.propFile: "html/PageDebug.html"
    },
    "uidPageError": {
        Const.propType: Const.typePage,
        Const.propFile: "html/PageError.html"
    },
    "uidButtonBack": {
        Const.propType: Const.typeButton,
        Const.propBody: "sidButtonBack",
        Const.propEvent: {
            Const.eventClick: "GoBack()"
        }
    },
    "uidPageErrorBackButton": {
        Const.propPrototype: "uidButtonBack"
    }
    
    ###################################
    # User data should be added above #
    ###################################
}
