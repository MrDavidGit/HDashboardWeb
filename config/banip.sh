#!/bin/bash

# Tiempo de bloqueo en horas (puedes cambiarlo fácilmente aquí)
TIEMPO_BLOQUEO_HORAS=12

# La IP se pasa como el primer argumento del script
ip="$1"

# Bloquear la IP en los puertos 80 (HTTP) y 443 (HTTPS)
echo "Bloqueando la IP $ip en los puertos 80 y 443..."
sudo ufw deny from $ip to any port 80,443

# Calcular la hora exacta dentro del tiempo de bloqueo (usando la variable TIEMPO_BLOQUEO_HORAS)
cron_time=$(date -d "+${TIEMPO_BLOQUEO_HORAS} hours" '+%M %H %d %m')

# Añadir la tarea de desbloqueo de la IP en los puertos 80 y 443 al crontab
(crontab -l; echo "$cron_time * sudo ufw delete deny from $ip to any port 80,443") | crontab -

echo "La IP $ip ha sido bloqueada temporalmente en los puertos 80 y 443. Se desbloqueará en $TIEMPO_BLOQUEO_HORAS horas."
