# PID Generator

This role installs and configures the PID Generator for the Language Bank.

# Installation Overview

The files/gen_pids.py is copied to an Apache server and configured via
templates/gen_pids_conf.j2. The Apache directory itself is configured
via templates/pid.conf.j2

# Running the script

The script `gen_pids.py` registers/updates Handles instantly and
constructs an XML file which is polled regularily by the National
Library of Finlands urn.fi service. Current path of the XML file:
`/pid/urn_nbn_fi_lb.xml`

The PIDs are maintained in GitHub (for the path see
`templates/gen_pids_conf.j2`). `gen_pids.py` makes the raw PID file
available at the path `/pid/lb_pid.txt`.

For end user documentation see [FIN-CLARIN-Administration in GitHub](/CSCfi/Kielipankki/master/FIN-CLARIN-Administration)

