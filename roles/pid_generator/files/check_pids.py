#!/usr/bin/python
import datetime
import MySQLdb
import sys
import requests

# This script checks pids stored in "source_csv_name". It tests that
# * the pidNumber exists in the mapping database (to avoid futile updates)
# * the pidNumber resolves URN and Handle to the same URL specified in the source file.

from gen_pids_conf import \
    PID_DB_NAME, \
    PID_DB_USER, \
    PID_DB_PASSWORD, \
    SOURCE_CSV_URL



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

def expand_handle(pidNumber):
    return "11113/lb-"+pidNumber

def get_handle(pidNumber):
    r = requests.get("http://hdl.handle.net/11113/lb-"+pidNumber, allow_redirects=False)
    if "location" in r.headers:
        return r.headers['location']
    else:
        return None

def get_urn(pidNumber):
    r = requests.get("http://urn.fi/urn:nbn:fi:lb-"+pidNumber, allow_redirects=False)
    if "location" in r.headers:
        return r.headers['location']
    else:
        return None


def verify(db_cur, pidNumber,url):
    #print "Testing: %s" % pidNumber
    
    db_cur.execute("SELECT pid,url from pid_map WHERE pid = %s", (pidNumber))
    handle_destination_url = get_handle(pidNumber)
    urn_destination_url = get_urn(pidNumber)
    if db_cur.rowcount == 1:
        (db_pid, db_url) = db_cur.fetchone()
        if (url != db_url):
            print "WARN: %s -> %s differs in database  (there: %s)" % (pidNumber, url, db_url)
        if (handle_destination_url != url):
            print "WARN: %s -> %s Handle not identical (there: %s)" % (pidNumber, url, handle_destination_url)
        if (urn_destination_url != url):
            print "WARN: %s -> %s URN not identical    (there: %s)" % (pidNumber, url, urn_destination_url)
    else:
        print "WARN: %s -> %s not in database" % (pidNumber,url)

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

# put "True" here to get more verosity
DEBUG=True

try:
    print("Content-type: text/html\n\n")
    print("<pre>")
    (db_con, db_cur) = openLocalPidDB()

    r = requests.get(source_csv_url)
    for line in  r.iter_lines():
        
        row = line.split()
        if not row or row[0].startswith('#'):
            continue
        elif not ( len(row) == 2 ):
            raise Exception ('Illegal line: '+str(row))
        elif not plausibleUrnNumber(row[0]):
            raise Exception ('Implausible URN Number: '+row[0])
        elif not plausibleURL(row[1]):
            raise Exception ('Implausible URL: '+row[1])
        else:
            pidNumber= row[0]
            url = row[1]
            verify(db_cur,pidNumber,url)

    print ("</pre>")
    print ("PID check finnished.")
finally:
    
    if db_con:
        db_con.close()

