#!/usr/bin/python3
from lxml import etree
from lxml import objectify
import datetime
import subprocess
import MySQLdb
import sys
import os
import requests
import json
import logging

from gen_lat_pids_conf import \
    EPIC_SERVICE_URL, \
    EPIC_SERVICE_KEYFILE, \
    EPIC_SERVICE_CRTFILE, \
    EPIC_SERVICE_PREFIX, \
    PID_DB_NAME, \
    PID_DB_USER, \
    PID_DB_PASSWORD, \
    URN_PATH, \
    RAW_FILENAME, \
    SOURCE_CSV_URL, \
    AUTH_TOKEN


# This script registers manually assigned PIDs. It supports both URNs (via urn.fi) and Handles (via handle.net)
# The mappings are in the file specified in "source_csv_name".
# The scripts outputs an XML file with URNs for later harvesting and registers the Handles directly via EPIC.

#######
##
## Functions

# Do some basic checks to warn about implausible URN numbers
def plausibleUrnNumber(numberString):
    try:
        datetime.datetime.strptime(numberString[0:8], '%Y%m%d')
        returnValue = numberString.isdigit()
    except:
        return False
    return returnValue 

# Do some basic checks for URLs. We don't want to be perfect here, but
# catch obvious typos
def plausibleURL(url):
    return "://" in url

def expand_handle(urnNumber):
    return EPIC_SERVICE_PREFIX+"/lb-"+urnNumber

def update_handle(handle,url):
    send_request(handle,url,False)
    logging.info("UPDATE %s -> %s" % (handle,url))

def register_handle(handle,url):
    send_request(handle,url,True)
    logging.info("REGISTER %s -> %s" % (handle,url))

def send_request (handle,url,register):
    service_url      = EPIC_SERVICE_URL
    service_user     = EPIC_SERVICE_PREFIX
    service_keyfile = EPIC_SERVICE_KEYFILE
    service_crtfile = EPIC_SERVICE_CRTFILE

    # exit if service url is empty (no Handle updates)
    if not service_url:
        return
    
    json_obj = create_url_json(url)

    #create the headers
    headers = {'Content-Type': 'application/json', 'Authorization': 'Handle clientCert="true"','Content-Length': len(json_obj)}
 
    r = requests.put(service_url+handle, data=json_obj, cert=(service_crtfile, service_keyfile), headers=headers, verify="epic5-storage-surfsara-nl-chain.pem" )
    

    if register:
        if r.status_code == 201:
            logging.info("REGISTER OK %s -> %s" % (handle,url))
        else:
            if r.status_code == 200:
                # let's recover from the attempt to register an existing Handle.
                logging.warn("REGISTER: PID already existed (%s)."  % r.status_code)
            else:
                raise requests.exceptions.HTTPError("Unexpected registration error. (%s)"  % r.status_code)
    else:
        if r.status_code == 200:
            logging.info("UPDATE OK %s -> %s" % (handle,url))
        else:
            if r.status_code == 201:
                # PID should have existed, but did not.
                logging.warn("UPDATE: PID did not exist (%s)."  % r.status_code)
            else:
                raise requests.exceptions.HTTPError("Unexpected update error. (%s)"  % r.status_code)
        


def create_url_json(url):
    return json.dumps({"values": [
                {"index":1,
                 "type":"URL",
                 "data":{"format":"string","value":url}},
                {"index":100,
                 "type":"HS_ADMIN",
                 "data":{"format":"admin","value":{"handle":"0.NA/11113","index":200,"permissions":"011111110011"}}}
                ]})
    
# construct XML describing one URN->URL mapping.
def recordAsXML(urnNumber, url, dateStamp):
    E = objectify.E
    record=  E.record (
        E.header (
            E.dateStamp(dateStamp, type = "modified"),
            E.identifier("urn:nbn:fi:lb-"+urnNumber),
            E.destinations(
                E.destination (
                    E.dateStamp( type = "activated"),
                    E.url(url) 
                    )
                )
            )
        )
    objectify.deannotate(record, xsi_nil=True)
    etree.cleanup_namespaces(record)
    # pretty print string to keep XML somewhat human readable
    return etree.tostring(record, pretty_print=True)

def getPidData(line):
    row = line.split()

    if not ( len(row) == 2 ):
        raise Exception ('Illegal line: '+str(row))
    elif not plausibleUrnNumber(row[0]):
        raise Exception ('Implausible URN Number: '+row[0])
    elif not plausibleURL(row[1]):
        raise Exception ('Implausible URL: '+row[1])

    urnNumber= row[0]
    url = row[1]
    return (urnNumber, url)

def setHandle (urnNumber, url, db_con, db_cur):

    db_cur.execute("SELECT pid,url from pid_map WHERE pid = %s", (urnNumber,))
    handle = expand_handle(urnNumber)

    # if urn number not in DB, insert / register
    if db_cur.rowcount == 0:
        register_handle(handle,url)
        db_cur.execute("INSERT INTO pid_map(pid,url) VALUES (%s,%s)", (urnNumber, url))
        db_con.commit()
    # if urn number alread in DB and url changed, update
    elif db_cur.rowcount == 1:
        (db_pid, db_url) = db_cur.fetchone()
        if (db_url != url):
            update_handle(handle,url)
            db_cur.execute("UPDATE pid_map SET url = %s WHERE pid = %s",(url,urnNumber))
            db_con.commit()

                

def xmlPrintHeader(outFile):
    print >> outFile, '<?xml version="1.0" encoding="ASCII"?>'
    print >> outFile, '<records xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:nbn:se:uu:ub:epc-schema:rs-location-mapping http://urn.kb.se/resolve?urn=urn:nbn:se:uu:ub:epc-schema:rs-location-mapping&amp;godirectly">'
    print >> outFile, ' <protocol-version>3.0</protocol-version>'
    print >> outFile, ' <datestamp type="modified">'+dateStamp+'</datestamp>'
    print >> outFile, ''

def xmlPrintFooter(outFine):
    print >> outFile, "</records>"

def openLocalPidDB():
    pid_db_name = PID_DB_NAME
    pid_db_user = PID_DB_USER
    pid_db_password = PID_DB_PASSWORD
    con = MySQLdb.connect(host="localhost",
                          user= pid_db_user,
                          passwd= pid_db_password,
                          db= pid_db_name)
    cur = con.cursor()
    return (con, cur)

    
#### MAIN
## The variables
##

dateStamp = datetime.datetime.utcnow().replace(microsecond=0).isoformat()+"Z"

# the source of mappings
source_csv_url = SOURCE_CSV_URL
urn_path= URN_PATH
# The file containing the unmodified PID file (copied from Github)
raw_filename= RAW_FILENAME
auth_token = AUTH_TOKEN

# the URN xml for harvesting
urn_target_xml_name  = urn_path + "/urn_nbn_fi_lb-10011001.xml"
raw_target_name  = urn_path + "/" + raw_filename

# put "True" here to get more verbosity
DEBUG=False #True

logging.basicConfig(filename='/tmp/gen_pids_lastlog.txt', level=logging.INFO, filemode='w')

try:
    (db_con, db_cur) = openLocalPidDB()
    outFile = open(urn_target_xml_name, "wb+")
    rawFile = open(raw_target_name, "wb+")

    xmlPrintHeader(outFile)
    r = requests.get(source_csv_url, auth = (auth_token, "") )

    # at init sync db with pid db without registering handles
    # unset EPIC_SERVICE_URL

    init = ( len(sys.argv) == 2 and sys.argv[1] == "init" )

    if (init == True) :
        EPIC_SERVICE_URL=""

    for line in  r.iter_lines():
        # copy content as-is to rawFile
        print >> rawFile, line
        if not line.strip() or line.startswith('#'):
            continue
        (urnNumber,url) = getPidData(line)
        print >> outFile, recordAsXML(urnNumber, url, dateStamp)
        setHandle (urnNumber, url, db_con, db_cur)

    xmlPrintFooter(outFile)
    outFile.close()
    rawFile.close()

    if init:
        logging.info ("Script installed, database initialized.")
    else:
        logging.info ("URN update finished.")
        if not EPIC_SERVICE_URL:
            logging.info ("Note: Handle generation disabled.")
        else:
            logging.info ("Handle update finished.")

except Exception as e:
    if DEBUG:
        logging.exception("Exception found. Script haltet.")
	raise # re-raise the exception                                          
	      # traceback gets printed                                          
    else:
        logging.error(e.args)
        logging.error("Script halted. Fix the error and try again.")

finally:
    print("Content-type: text/html\n\n")
    print("<pre>")
    with open("/tmp/gen_pids_lastlog.txt", 'r') as log:
        print log.read()
    print("</pre>")
    os.system('cat /tmp/gen_pids_lastlog.txt | mailx -r kielipankki@csc.fi -s "LAT Tombstone PID Generator output" matthies@csc.fi')
    if db_con:
        db_con.close()
    


