#!/bin/bash
#####################################################################
#                                                                   #
# Counts close_wait lines and reboots if necessary                  #
#                                                                   #
# parameters: none                                                  #
# input: none                                                       #
# output: creates a rrd file                                        #
#                                                                   #
# Author: Blaise Thauvin                                            #
# Version 1.0                                                       #
#                                                                   #
#  History                                                          #
#  1.0 01/04/18, BLT Initial version                                #
#                                                                   #
#####################################################################
source /opt/smdf/common-lib.sh
if [ $? -ne 0 ]; then echo "Cannot load common library" >&2; exit 99; fi

# Script needs at least common-lib version 1.15 
check_version "1.15"
if [ $? -ne 0 ]; then exit_error "Script could not check common-lib version, likely reason is version is lower than 1.15 and obsolete"; fi

# Use this function if your script cannot be run twice simultaneously
check_script 

# Fichier de résultat
FILE_CW=rrd/close_wait.rrd      
FILE_TW=rrd/time_wait.rrd      
FILE_CHROME=rrd/nb_chrome.rrd

# Check parameters passed to your script. The common-lib provides several parameter checking functions.
#Test number of parameters passed to the script
if [ $# -ne 0 ] 
   then 
      echo "Usage: $CALLEDNAME"
      exit_error "Wrong number of parameters, expecting 0, got $#" 
fi




# Main
cd /opt/AppMonitor

#Compte le nombre de "close_wait" en attente" sur machine distante
NB_CW=`ssh test01-x.saintmaur.local netstat -np|grep 127.0.0.1|grep CLOSE_WAIT|wc -l`
#Compte le nombre de "time_wait" en attente" sur machine distante
NB_TW=`ssh test01-x.saintmaur.local netstat -np|grep 127.0.0.1|grep TIME_WAIT|wc -l`
#Compte le nombre d'instance de chrome en cours d'execution
NB_CHROME=`ssh test01-x.saintmaur.local ps -ef | grep chrome | wc -l`
#Verifie si la connexion au hub est OK
TIMEDOUT=`curl -Ss http://test01-x.saintmaur.local:4444/grid/console# --max-time 15 | grep "Operation timed out after 15"`
#Verifie si le hub n'est pas en surcharge
OVERLOAD=`curl -Ss http://test01-x.saintmaur.local:4444/grid/console# --max-time 15 | grep "waiting for a slot to be free"`


# Vérifie si le fichier RRD close_wait est créé, s'il ne l'est pas, on le crée
if [ ! -e $FILE_CW ]; then
   rrdtool create $FILE_CW --step 60 --no-overwrite DS:nb:GAUGE:120:0:60000 \
           RRA:AVERAGE:0.5:1:2880 \
           RRA:AVERAGE:0.5:5:2304 \
           RRA:AVERAGE:0.5:30:700 \
           RRA:AVERAGE:0.5:120:775 \
           RRA:AVERAGE:0.5:1440:3700 \
           RRA:MIN:0.5:1:2880 \
           RRA:MIN:0.5:5:2304 \
           RRA:MIN:0.5:30:700 \
           RRA:MIN:0.5:120:775 \
           RRA:MIN:0.5:1440:3700 \
           RRA:MAX:0.5:1:2880 \
           RRA:MAX:0.5:5:2304 \
           RRA:MAX:0.5:30:700 \
           RRA:MAX:0.5:120:775 \
           RRA:MAX:0.5:1440:3700 \
           RRA:LAST:0.5:1:2880 \
           RRA:LAST:0.5:5:2304 \
           RRA:LAST:0.5:30:700 \
           RRA:LAST:0.5:120:775 \
           RRA:LAST:0.5:1440:3700
fi
# Vérifie si le fichier RRD time_wait est créé, s'il ne l'est pas, on le crée
if [ ! -e $FILE_TW ]; then
   rrdtool create $FILE_TW --step 60 --no-overwrite DS:nb:GAUGE:120:0:60000 \
           RRA:AVERAGE:0.5:1:2880 \
           RRA:AVERAGE:0.5:5:2304 \
           RRA:AVERAGE:0.5:30:700 \
           RRA:AVERAGE:0.5:120:775 \
           RRA:AVERAGE:0.5:1440:3700 \
           RRA:MIN:0.5:1:2880 \
           RRA:MIN:0.5:5:2304 \
           RRA:MIN:0.5:30:700 \
           RRA:MIN:0.5:120:775 \
           RRA:MIN:0.5:1440:3700 \
           RRA:MAX:0.5:1:2880 \
           RRA:MAX:0.5:5:2304 \
           RRA:MAX:0.5:30:700 \
           RRA:MAX:0.5:120:775 \
           RRA:MAX:0.5:1440:3700 \
           RRA:LAST:0.5:1:2880 \
           RRA:LAST:0.5:5:2304 \
           RRA:LAST:0.5:30:700 \
           RRA:LAST:0.5:120:775 \
           RRA:LAST:0.5:1440:3700
fi
# Vérifie si le fichier RRD nb chrome est créé, s'il ne l'est pas, on le crée
if [ ! -e $FILE_CHROME ]; then
   rrdtool create $FILE_CHROME --step 60 --no-overwrite DS:nb:GAUGE:120:0:60000 \
           RRA:AVERAGE:0.5:1:2880 \
           RRA:AVERAGE:0.5:5:2304 \
           RRA:AVERAGE:0.5:30:700 \
           RRA:AVERAGE:0.5:120:775 \
           RRA:AVERAGE:0.5:1440:3700 \
           RRA:MIN:0.5:1:2880 \
           RRA:MIN:0.5:5:2304 \
           RRA:MIN:0.5:30:700 \
           RRA:MIN:0.5:120:775 \
           RRA:MIN:0.5:1440:3700 \
           RRA:MAX:0.5:1:2880 \
           RRA:MAX:0.5:5:2304 \
           RRA:MAX:0.5:30:700 \
           RRA:MAX:0.5:120:775 \
           RRA:MAX:0.5:1440:3700 \
           RRA:LAST:0.5:1:2880 \
           RRA:LAST:0.5:5:2304 \
           RRA:LAST:0.5:30:700 \
           RRA:LAST:0.5:120:775 \
           RRA:LAST:0.5:1440:3700
fi

# Met à jour le fichier RRD
rrdupdate $FILE_CW -t nb N:$NB_CW
rrdupdate $FILE_TW -t nb N:$NB_TW
rrdupdate $FILE_CHROME -t nb N:$NB_CHROME


# Nouvelle condition
if [ -n $TIMEDOUT ] || [ -n $OVERLOAD ] ; then 
   warning "Le hub ne repond plus et/ou est en surcharge. Redemarrage."
   # On kill
   #ssh test01-x.saintmaur.local reboot
   #sleep 15;
   #ssh test02-x.saintmaur.local reboot
fi


# Ancienne condition
if [ $NB_CW -gt 100 ]; then 
   warning "$NB_CW close_wait en attente et $NB_TW time wait en attente. Redémarrage du serveur selenium."
   # On kill
   ssh test01-x.saintmaur.local ps -eF | grep selenium | grep jar | awk '{print $2}' | xargs kill
   ssh test01-x.saintmaur.local /usr/bin/su - blaise -c "export DISPLAY=:99;/usr/bin/java -jar /opt/selenium/selenium-server-standalone-3.9.1.jar >/var/log/selenium.log 2>/var/log/selenium-errors.log &"
else 
   exit_ok "$NB_CW close_wait en attente"
fi


# Just to make sure we add a line that is never to be used. Exit code 99 should help.
# This should never happen but could if loading the standard lib fails.
exit 99

