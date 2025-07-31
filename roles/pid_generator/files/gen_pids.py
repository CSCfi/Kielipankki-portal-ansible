#!/usr/bin/python3
from lxml import etree
from lxml import objectify
import datetime
import subprocess
import pymysql
import sys
import os
import requests
import json
import logging
import re

from gen_pids_conf import (
    EPIC_SERVICE_URL,
    EPIC_SERVICE_KEYFILE,
    EPIC_SERVICE_CRTFILE,
    EPIC_SERVICE_PREFIX,
    PID_DB_NAME,
    PID_DB_USER,
    PID_DB_PASSWORD,
    URN_PATH,
    RAW_FILENAME,
    URN_XML_FILENAME,
    SOURCE_CSV_URL,
    AUTH_TOKEN,
    DEBUG_EMAIL_ADDRESS
)


# This script registers manually assigned PIDs. It supports both URNs (via urn.fi) and Handles (via handle.net)
# The mappings are in the file specified in "source_csv_name".
# The scripts outputs an XML file with URNs for later harvesting and registers the Handles directly via EPIC.

# It uses a small local database to keep track on already registered PIDs to avoid unneccessary Handle requests.

# The script is normally invoked as CGI without parameters and configured using "gen_pids_conf.py".
# If invoked with the parameter "init" the local PID db is initialized with presently known PIDs, but without requesting Handles. This is only useful from Ansible. Note:
# - if the local PID DB contains PIDs not yet registered in production (because the PID list was updated right before "init"), this should not be a problem, since PIDs will be registered in production soon after.
# - having the PID Generator idle on pre-production for a longer period will cause the latest Handle-PIDs to be registered twice, since the database will be out-of-sync. That should normally not be a problem.

#######
##
## Functions

# BEGIN Functions related to Handle handling
# We use the same Handles prefix as EUDAT and have agreed with them that our suffixes always start with "lb-"


# expand urnNumber to a proper Handle
def expand_handle(urnNumber):
    return EPIC_SERVICE_PREFIX + "/lb-" + urnNumber


# Update existing Handle via EPIC
def update_handle(handle, url):
    send_request(handle, url, False)
    logging.info("UPDATE %s -> %s" % (handle, url))


# Register new Handle via EPIC
def register_handle(handle, url):
    send_request(handle, url, True)
    logging.info("REGISTER %s -> %s" % (handle, url))


# common code for update/register via EPIC
def send_request(handle, url, register):
    service_url = EPIC_SERVICE_URL
    service_user = EPIC_SERVICE_PREFIX
    service_keyfile = EPIC_SERVICE_KEYFILE
    service_crtfile = EPIC_SERVICE_CRTFILE

    # exit if service url is empty (no Handle updates)
    if not service_url:
        return

    json_obj = create_url_json(url)

    # create the headers
    headers = {
        "Content-Type": "application/json",
        "Authorization": 'Handle clientCert="true"',
        "Content-Length": str(len(json_obj)),
    }

    r = requests.put(
        service_url + handle,
        data=json_obj,
        cert=(service_crtfile, service_keyfile),
        headers=headers,
        verify= True,
    )

    if register:  # Register
        if r.status_code == 201:
            logging.info("REGISTER OK %s -> %s" % (handle, url))
        else:
            if r.status_code == 200:
                # let's recover from the attempt to register an existing Handle.
                logging.warn("REGISTER: PID already existed (%s)." % r.status_code)
            else:
                raise requests.exceptions.HTTPError(
                    "Unexpected registration error. (%s)" % r.status_code
                )
    else:  # Update
        if r.status_code == 200:
            logging.info("UPDATE OK %s -> %s" % (handle, url))
        else:
            if r.status_code == 201:
                # PID should have existed, but did not.
                logging.warn("UPDATE: PID did not exist (%s)." % r.status_code)
            else:
                raise requests.exceptions.HTTPError(
                    "Unexpected update error. (%s)" % r.status_code
                )


# construct payload for update/register
def create_url_json(url):
    return json.dumps(
        {
            "values": [
                {"index": 1, "type": "URL", "data": {"format": "string", "value": url}},
                {
                    "index": 100,
                    "type": "HS_ADMIN",
                    "data": {
                        "format": "admin",
                        "value": {
                            "handle": "0.NA/11113",
                            "index": 200,
                            "permissions": "011111110011",
                        },
                    },
                },
            ]
        }
    )


# register/update Handle if needed. Do nothing in case of no change.
# keep track of new/changed PIDs in local DB
def setHandle(urnNumber, url, db_con, db_cur):

    db_cur.execute("SELECT pid,url from pid_map WHERE pid = %s", (urnNumber,))
    handle = expand_handle(urnNumber)

    # if urn number not in DB, insert / register
    if db_cur.rowcount == 0:
        register_handle(handle, url)
        db_cur.execute("INSERT INTO pid_map(pid,url) VALUES (%s,%s)", (urnNumber, url))
        db_con.commit()
    # if urn number alread in DB and url changed, update
    elif db_cur.rowcount == 1:
        (db_pid, db_url) = db_cur.fetchone()
        if db_url != url:
            update_handle(handle, url)
            db_cur.execute(
                "UPDATE pid_map SET url = %s WHERE pid = %s", (url, urnNumber)
            )
            db_con.commit()


# END Functions related to Handle handling

# BEGIN Functions related to URN handling
# URNs are all written into a XML file which is read by the national library, currently once per day at 10pm.
# The XML files has a header, all PIDs as records and a footer.


# construct XML describing one URN->URL mapping.
def recordAsXML(urnNumber, url, dateStamp):
    E = objectify.E
    record = E.record(
        E.header(
            E.dateStamp(dateStamp, type="modified"),
            E.identifier("urn:nbn:fi:lb-" + urnNumber),
            E.destinations(E.destination(E.dateStamp(type="activated"), E.url(url))),
        )
    )
    objectify.deannotate(record, xsi_nil=True)
    etree.cleanup_namespaces(record)
    # pretty print string to keep XML somewhat human readable
    return etree.tostring(record, pretty_print=True, encoding="unicode")


def xmlPrintHeader(outFile):
    outFile.write('<?xml version="1.0" encoding="ASCII"?>')
    outFile.write(
        '<records xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:nbn:se:uu:ub:epc-schema:rs-location-mapping http://urn.kb.se/resolve?urn=urn:nbn:se:uu:ub:epc-schema:rs-location-mapping&amp;godirectly">'
    )
    outFile.write(" <protocol-version>3.0</protocol-version>")
    outFile.write(' <datestamp type="modified">' + dateStamp + "</datestamp>")
    outFile.write("")


def xmlPrintFooter(outFile):
    outFile.write("</records>")


# END Functions related to URN handling

# BEGIN General functions


# Do some basic checks to warn about implausible URN numbers
def plausibleUrnNumber(numberString):
    try:
        # check that first eight digits are a reverse date, return false otherwise
        datetime.datetime.strptime(numberString[0:8], "%Y%m%d")
        # check that the whole string contains only numbers.
        returnValue = numberString.isdigit()
    except:
        return False
    return returnValue


# Do some basic checks for URLs. We don't want to be perfect here, but
# catch obvious typos, like http:/xxx
def plausibleURL(url):
    pattern = r'://(?!/)' # to exactly match '://'
    return re.search (pattern, url)


# parse line from source file and do basic checks
def getPidData(line):
    row = line.split()

    if not (len(row) == 2):
        raise Exception("Illegal line: " + str(row))
    elif not plausibleUrnNumber(row[0]):
        raise Exception("Implausible URN Number: " + row[0])
    elif not plausibleURL(row[1]):
        raise Exception("Implausible URL: " + row[1])

    urnNumber = row[0]
    url = row[1]
    return (urnNumber, url)


# open local DB which keeps track on already registered pids
# Otherwise each run would generate thousands of Handle register requests.
def openLocalPidDB():
    pid_db_name = PID_DB_NAME
    pid_db_user = PID_DB_USER
    pid_db_password = PID_DB_PASSWORD
    con = pymysql.connect(
        host="localhost", user=pid_db_user, passwd=pid_db_password, db=pid_db_name
    )
    cur = con.cursor()
    return (con, cur)


# END General functions


#### MAIN
## The variables
##

dateStamp = datetime.datetime.utcnow().replace(microsecond=0).isoformat() + "Z"

# the source of mappings
source_csv_url = SOURCE_CSV_URL
urn_path = URN_PATH
# The file containing the unmodified PID file (copied from Github)
# useful for quickly reverse matching PIDs.
raw_target_name = urn_path + "/" + RAW_FILENAME

# The auth token to access the source file in Github
auth_token = AUTH_TOKEN

# name and location of the URN xml for harvesting by NLF's URN service
urn_xml_filename = URN_XML_FILENAME
urn_target_xml_name = urn_path + "/" + urn_xml_filename

debug_email_address = DEBUG_EMAIL_ADDRESS

# Debug settings
# put "True" here to get more verbosity
DEBUG = False
# DEBUG=True


# a simple log of the last run.
logging.basicConfig(
    filename="/tmp/gen_pids_lastlog.txt", level=logging.INFO, filemode="w"
)

try:
    (db_con, db_cur) = openLocalPidDB()
    # at init sync db with pid db without registering handles
    # unset EPIC_SERVICE_URL
    init = len(sys.argv) == 2 and sys.argv[1] == "init"
    if init == True:
        EPIC_SERVICE_URL = ""

    with open(urn_target_xml_name, "w") as outFile, open(
        raw_target_name, "w"
    ) as rawFile:
        xmlPrintHeader(outFile)
        # connect to Github
        r = requests.get(source_csv_url, auth=(auth_token, ""))

        for line in r.iter_lines():
            # copy content as-is to rawFile
            line = line.decode("utf-8")
            rawFile.write(line + "\n")
            if not line.strip() or line.startswith("#"):
                continue
            (urnNumber, url) = getPidData(line)
            outFile.write(recordAsXML(urnNumber, url, dateStamp))
            setHandle(urnNumber, url, db_con, db_cur)
        xmlPrintFooter(outFile)

    if init:
        logging.info("Script installed, database initialized.")
    else:
        logging.info("URN update finished.")
        if not EPIC_SERVICE_URL:
            logging.info("Note: Handle generation disabled.")
        else:
            logging.info("Handle update finished.")

except Exception as e:
    if DEBUG:
        logging.exception("Exception found. Script halted.")
        raise  # re-raise the exception
    # traceback gets printed

    else:
        logging.error(e.args)
        logging.error("Script halted. Fix the error and try again.")

finally:
    # Show debug output in browser and mail to hardcoded email
    print("Content-type: text/html\n\n")
    print("<pre>")
    with open("/tmp/gen_pids_lastlog.txt", "r") as log:
        print(log.read())
    print("</pre>")
    if debug_email_address:
        os.system(
            'cat /tmp/gen_pids_lastlog.txt | mailx -r kielipankki@csc.fi -s "PID Generator output" ' + debug_email_address
        )
    if db_con:
        db_con.close()
