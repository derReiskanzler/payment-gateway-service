#!/bin/bash

function error(){
	echo "Please enter a parameter that only contains letters/numbers/dashes/underscores"
	exit 1
}

if [ "$#" -eq 1 ]; then
	if [[ $1 =~ ^[A-Za-z0-9_-]+$ ]]; then
		APPNAME=$1
		UAPPNAME=`echo ${APPNAME:0:1} | tr '[a-z]' '[A-Z]'`${APPNAME:1}
		for FILE in \
			.env.example \
			.env.testing \
			.ops/docker/Dockerfile \
			.ops/sonar-project.properties \
			docker-compose.yaml \
			toolset.sh;
		do
			sed -i '' "s|boilerplate-laravel|$APPNAME|g" $FILE
			sed -i '' "s|boilerplate|$APPNAME|g" $FILE
			sed -i '' "s|Boilerplate|$UAPPNAME|g" $FILE
		done
    sed -i '' "s|boilerplate|$APPNAME|g" composer.json
    sed -i '' "s|Boilerplate to create|$UAPPNAME to create|g" composer.json
		sed -i '' "s|boilerplate-db|${APPNAME}-db|g" .gitlab-ci.yml
		sed -i '' "s|boilerplate_test|${APPNAME}_test|g" .gitlab-ci.yml
        sed -i '' "s|<Service_Name>|${APPNAME}|g" Contracts/Providing/api-unparsed.yaml
        sed -i '' "s|<Service_Name>|${APPNAME}|g" Contracts/Providing/api.yaml
	else
		error
	fi
else
	error
fi
