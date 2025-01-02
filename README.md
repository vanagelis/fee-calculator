# Commission Calculator

Project documentation

## Installation

In case you don't have docker, you can find setup instructions for your OS distribution here: https://docs.docker.com/compose/install/

Initialize a local container using (First initialization)

`$ make init `


To start a local container run

`$ make start `



## Running application

Open docker container bash

`$ docker compose exec php bash`

Run application inside docker container

`$ php bin/console commission:calculate <file path>`


## Testing

Run `$ make tests` or `$ php bin/phpunit` inside docker container to be sure everything is working correctly
