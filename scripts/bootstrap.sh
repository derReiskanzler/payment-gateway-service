#!/bin/bash

############################################################
# Clone the GitLab repository
############################################################
echo -e "\e[92m Cloning the laravel-api-boilerplate GitLab repository..."

BOILERPLATE="$HOME/Desktop/bootstrap_boilerplate"
cd $BOILERPLATE

OAUTH_KEY_FILE1=$(find ./keys/oauth-public.key)
OAUTH_KEY_FILE2=$(find ./keys/test-oauth-private.key)
OAUTH_KEY_FILE3=$(find ./keys/test-oauth-public.key)

if [[ ("$OAUTH_KEY_FILE1" == "") || ("$OAUTH_KEY_FILE2" == "") || ("$OAUTH_KEY_FILE3" == "") ]];then
    echo -e "\e[31m oauth keys need to be placed on the Desktop ..."
    exit 2
fi

# clone the GitLab repository
git clone https://gitlab.smartexpose.com/allmyhomes/laravel-api-boilerplate.git boilerplate
cd boilerplate

############################################################
# Start the Docker container
############################################################
echo -e "\e[92m Starting the docker container..."

docker-compose up -d

############################################################
# Set up the docker environment and test db
############################################################
echo -e "\e[92m Setting up the environment variables and database ..."

cp .env.example .env
cp $BOILERPLATE/keys/oauth-public.key src/Infrastructure/Boilerplate/Laravel/storage/oauth-public.key
cp $BOILERPLATE/keys/test-oauth-private.key src/Infrastructure/Boilerplate/Laravel/storage/test-oauth-private.key
cp $BOILERPLATE/keys/test-oauth-public.key src/Infrastructure/Boilerplate/Laravel/storage/test-oauth-public.key

sh toolset.sh shell
composer clearcache
composer install
