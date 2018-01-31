#!/bin/bash
#####################################################################
#                                                                   #
# Script de supervision applicative                                 #
#                                                                   #
# parameters: Scenario applicatif                                   #
# input: none                                                       #
# output: none                                                      #
#                                                                   #
# Author: Blaise Thauvin                                            #
# Version 1.0                                                       #
#                                                                   #
#  History                                                          #
#  1.0 26/01/18, BLT Version initiale                               #
#                                                                   #
#####################################################################
source /opt/smdf/common-lib.sh
if [ $? -ne 0 ]; then echo "Cannot load common library" >&2; exit 99; fi

# Script needs at least common-lib version 1.19 
check_version "1.19"
if [ $? -ne 0 ]; then exit_error "Script could not check common-lib version, likely reason is version is lower than 1.19 and obsolete"; fi

# Use this function if your script cannot be run twice simultaneously
#check_script 
      
# Define the constants and variables to be used later in your script
DESTINATAIRES="camus.lejarre@mairie-saint-maur.com,blaise.thauvin@mairie-saint-maur.com"

# Variables

# Check parameters passed to your script. The common-lib provides several parameter checking functions.
#Test number of parameters passed to the script
if [ $# -ne 1 ] 
   then 
      echo "Usage: $CALLED_NAME application"
      exit_error "Wrong number of parameters, expecting 1, got $#" 
fi
SCENARIO=$1

# Add tests to check parameters validity, ranges, consistency....


# Main
# All internal steps are checked for success and the script exits if not.
cd /opt/AppMonitor
if [ $? -ne "0" ]; then exit_error "Cannot change to directory /opt/AppMonitor" 3; fi

# Call to common_lib to get a temp file name in standard format and location
FILE=`tmpfile`
if [ $? -ne "0" ]; then exit_error "Cannot create temporary working file $FILE" 4; fi
touch $FILE


/usr/bin/date >$FILE
/usr/bin/php $SCENARIO.php >>$FILE
if [ $? -ne 0 ]; then
   warning "Le script $SCENARIO a échoué"
   /usr/bin/date >>$FILE
   /usr/bin/mailx -a screenshot-$SCENARIO*.png -s "ECHEC Scenario $SCENARIO" -r "Supervision_Applicative" $DESTINATAIRES <$FILE
   rm -f screenshot-$SCENARIO*.png
   exit_error "Echec $SCENARIO"
fi

# Script ends nicely, clean-up of temp files, lock file, time stamping log file are taken care of by the common library.
exit_ok "Scenario $SCENARIO  OK"

# Just to make sure we add a line that is never to be used. Exit code 99 should help.
# This should never happen but could if loading the standard lib fails.
exit 99

