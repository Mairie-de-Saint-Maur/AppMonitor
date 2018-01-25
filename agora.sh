DESTINATAIRES="camus.lejarre@mairie-saint-maur.com,blaise.thauvin@mairie-saint-maur.com"
LOG=/tmp/agora.log
cd /opt/AppMonitor
/usr/bin/date >$LOG
/usr/bin/php agora.php >>$LOG
if [$? -ne 0 ]; then
   i/usr/bin/date >>$LOG
   /usr/bin/mailx -a screenshot*.png -s "Echec Agora" -r "Supervision Applicative Agora" $DESTINATAIRES <$LOG
   rm -f screenshot*.png
fi
rm  $LOG

