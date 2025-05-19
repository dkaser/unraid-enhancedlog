if [ ! -f /boot/config/plugins/enhanced.log/enhanced.log.cfg ]; then
  cp -a /usr/local/emhttp/plugins/enhanced.log/default/enhanced.log.cfg /boot/config/plugins/enhanced.log/enhanced.log.cfg
fi

if [ ! -f /boot/config/plugins/enhanced.log/custom_syslog.conf ]; then
  cp -a /usr/local/emhttp/plugins/enhanced.log/default/custom_syslog.conf /boot/config/plugins/enhanced.log/custom_syslog.conf
fi

if [ ! -f /boot/config/plugins/enhanced.log/syslog_filter.cfg ]; then
  cp -a /usr/local/emhttp/plugins/enhanced.log/default/syslog_filter.cfg /boot/config/plugins/enhanced.log/syslog_filter.cfg
fi

/usr/local/emhttp/plugins/enhanced.log/scripts/rc.enhanced.log