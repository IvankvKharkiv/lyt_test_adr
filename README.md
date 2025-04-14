Test Project <br>
This is attempt to implement ADR (Action–domain–responder) architecture <br>
To start the project: docker compose up -d --build <br>
To enter the container: docker compose exec php bash <br>
To install composer dependencies:docker compose exec php composer install <br>
To see the main page: http://localhost:8080/get-weather <br>
Choose the city and submit. <br>
To run tests: docker compose exec php vendor/bin/phpunit tests

.env.prod should contain keys and be only deployed on prod server <br>

Wiremock is used to emulate weather endpoint. <br>

Php-cs-fixer is used to standardize code.

Symfony Logger is used to save log files. Files can be found at /var/log in container.

