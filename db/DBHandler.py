import os
import datetime

import Const

from metadata import DB
from metadata import Def

# Generate DB drop script
dbDropScriptPath = os.path.join(Def.dirBase,
                                Const.dirGenerated,
                                Def.fileDropDB)
dbDropScript = open(dbDropScriptPath, "w")
for dbName in DB.db.keys():
    dbDropScript.write("drop database " + dbName + ";\n")
dbDropScript.close()

#
# Generate DB init script
#

dbInitScriptPath = os.path.join(Def.dirBase,
                                Const.dirGenerated,
                                Def.fileInitDB)
dbInitScript = open(dbInitScriptPath, "w")

# First create DB
for dbName in DB.db.keys():
    dbInitScript.write("## Create Database " + dbName + " ##\n")
    dbInitScript.write("create database " + dbName + ";\n")
    dbInitScript.write("\n")
    dbInitScript.write("use " + dbName + ";\n")
    
    # Then create table 
    for tableName in DB.db[dbName].keys():
        dbInitScript.write("## Create Table " + tableName + " ##\n")
        dbInitScript.write("create table " + tableName + " (\n")

        # Handle "PRIMARY KEY" first
        primaryKeys = None
        if "PRIMARY KEY" in DB.db[dbName][tableName][Const.tbSchema].keys():
            primaryKeys = DB.db[dbName][tableName][Const.tbSchema]["PRIMARY KEY"]
            del DB.db[dbName][tableName][Const.tbSchema]["PRIMARY KEY"]
        
        # Add column description
        index = len(DB.db[dbName][tableName][Const.tbSchema])
        for column in DB.db[dbName][tableName][Const.tbSchema].keys():
            index = index - 1
            if len(DB.db[dbName][tableName][Const.tbSchema][column]) > 1:
                dbInitScript.write("    " +
                                   column +
                                   " " +
                                   " ".join(DB.db[dbName][tableName][Const.tbSchema][column]))
            else:
                dbInitScript.write("    " +
                                   column +
                                   " " +
                                   DB.db[dbName][tableName][Const.tbSchema][column][0])
            if index == 0:
                if primaryKeys is not None:
                    dbInitScript.write(",\n")
                    dbInitScript.write("    PRIMARY KEY(" + ", ".join(primaryKeys) + ")")
                dbInitScript.write(");\n")
            else:
                dbInitScript.write(",\n")

        # Insert initial values
        if not  DB.db[dbName][tableName].has_key(Const.tbValues):
            continue
        for row in  DB.db[dbName][tableName][Const.tbValues]:
            columns = ", ".join(row.keys())
            value = ", ".join(row.values())
            dbInitScript.write("insert into " + tableName + " (" + columns + ") values (" + value + ");\n")
                               
        dbInitScript.write("\n")
    dbInitScript.write("\n")                               
                               
dbInitScript.close()

# Generate init shell script for MySQL
dbInitShellPath = os.path.join(Def.dirBase,
                               Const.dirGenerated,
                               Def.fileInitDBShell)
dbInitShell = open(dbInitShellPath, "w")
dbInitShell.write("# Usage: sh " + dbInitShellPath + " DB_USERNAME\n")
dbInitShell.write("if test $# -lt 1\n")
dbInitShell.write("then\n")
dbInitShell.write("    echo \"sh " + Def.fileInitDBShell + " DB_USERNAME\"\n")
dbInitShell.write("    exit\n")
dbInitShell.write("fi\n")
dropCommand = Def.cmdMysql + " -u $1 -p < " + dbDropScriptPath
dbInitShell.write(dropCommand + "\n")
dbInitShell.write("echo \"" + dropCommand + "\"\n");
initCommand = Def.cmdMysql + " -u $1 -p < " + dbInitScriptPath
dbInitShell.write(initCommand + "\n")
dbInitShell.write("echo \"" + initCommand + "\"\n")
dbInitShell.close()

# Generate DB backup shell script
dbBackupShellPath = os.path.join(Def.dirBase,
                                 Const.dirGenerated,
                                 Def.fileBackupDBShell)
dbBackupShell = open(dbBackupShellPath, "w")
dbBackupShell.write("# Usage: sh " + dbBackupShellPath + " DB_USERNAME DB_NAME PW\n")
dbBackupShell.write("if test $# -lt 3\n")
dbBackupShell.write("then\n")
dbBackupShell.write("    echo \"" + Def.fileBackupDBShell + " DB_USERNAME DB_NAME PW\"\n")
dbBackupShell.write("    exit\n")
dbBackupShell.write("fi\n")
dbBackupDir = os.path.join(Def.dirBase,
                           Const.dirBackup)
dbBackupFilePath = os.path.join(dbBackupDir, "$2_$(date +'%Y%m%d').bak")
backupCommand = Def.cmdMysqldump + " -u $1 -p $2 > " + dbBackupFilePath
dbBackupShell.write(backupCommand + "\n")
dbBackupShell.write("echo \"" + backupCommand + "\"\n");
dbBackupShell.write("sudo 7z a -p$3 " +
                    dbBackupFilePath.replace(".bak", ".7z") +
                    " " +
                    dbBackupFilePath +
                    "\n")
dbBackupShell.write("sudo rm -rf " + dbBackupFilePath + "\n")

dbBackupShell.close()

# Generate DB restore shell script
dbRestoreShellPath = os.path.join(Def.dirBase,
                                  Const.dirGenerated,
                                  Def.fileRestoreDBShell)
dbRestoreShell = open(dbRestoreShellPath, "w")
dbRestoreShell.write("# Usage: sh " + dbRestoreShellPath + " DB_USERNAME DB_NAME BAK_FILE_NAME\n")
dbRestoreShell.write("if test $# -lt 3\n")
dbRestoreShell.write("then\n")
dbRestoreShell.write("    echo \"" +
                     Def.fileBackupDBShell +
                     " DB_USERNAME DB_NAME BAK_FILE_NAME\"\n")
dbRestoreShell.write("    exit\n")
dbRestoreShell.write("fi\n")
dbBackupDir = os.path.join(Def.dirBase,
                           Const.dirBackup)
dbRestoreFilePath = os.path.join(dbBackupDir, "$3")
dbRestoreShell.write("dbBakFile=`echo " + dbRestoreFilePath + " | sed -e 's/.7z/.bak/'`\n")
dbRestoreShell.write("cd " + dbBackupDir + "\n")
dbRestoreShell.write("sudo 7z e " + dbRestoreFilePath + "\n")
restoreCommand = Def.cmdMysql + " -u $1 -p $2 < $dbBakFile"
dbRestoreShell.write(restoreCommand + "\n")
dbRestoreShell.write("echo \"" + restoreCommand + "\"\n");
dbRestoreShell.write("sudo rm -rf $dbBakFile\n")
dbRestoreShell.close()
