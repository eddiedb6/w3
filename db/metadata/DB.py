import sys
sys.path.append("..")
import Const

db = {
    #############################################
    # User DB description should be added below #
    #############################################

    # e.g. #
    "ej": { # DB name
        "config": { # Table name
            Const.tbSchema: { # Table description
                "Name": ["varchar(128)", "not null", "primary key"],
                "Value": ["varchar(128)"]
            },
            Const.tbValues: [ # Table initial values if needed
                {"Name": "'version'", "Value": "'0.1'"}
            ]
        }
    }
    
    #############################################
    # User DB description should be added above #
    #############################################
}
