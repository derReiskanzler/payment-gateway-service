#!/bin/bash

function params(){
	echo "Pick an Option:"
	echo " - setup -- creates the payment-gateway-service and test db"
	echo " - shell -- to get access to the shell"
	exit
}

if [ "$#" -eq 0 ]; then
	params
fi
case "$1" in
	setup)
		docker exec -i -t payment-gateway-service-app bash -c 'export PGPASSWORD=$DB_EVENT_STORE_PASSWORD; psql -h $DB_EVENT_STORE_HOST -p $DB_EVENT_STORE_PORT -U $DB_EVENT_STORE_USERNAME -d $DB_EVENT_STORE_DATABASE -c "CREATE DATABASE \"event-store_test\""'
		docker exec -i -t payment-gateway-service-app bash -c 'export PGPASSWORD=$DB_PASSWORD; psql -h $DB_HOST -p $DB_PORT -U $DB_USERNAME -d $DB_DATABASE -c "CREATE DATABASE \"payment-gateway-service_test\""'
		if [ ! -f .env ]; then cp .env.example .env; fi
		docker exec -t payment-gateway-service-app bash -c 'composer install --no-interaction'
		docker exec -t payment-gateway-service-app bash -c '/var/www/html/artisan migrate'
		;;
	shell)
		HOST=""
		if [ "$(uname)" != "Darwin" ]; then
			docker exec -t payment-gateway-service-app /bin/bash -c "id -u host &>/dev/null || \
				addgroup -g $(id -u) host && id -u host &>/dev/null || \
				adduser -u $(id -g) -G host host -D; cp /root/.bashrc /home/host"
			$HOST="--user host"
		fi
		docker exec -e COLUMNS="`tput cols`" -e LINES="`tput lines`" -ti $HOST payment-gateway-service-app /bin/bash
		;;
	*)
		params
		;;
esac
