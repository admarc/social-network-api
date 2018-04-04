Social network api
==================
RESTful API on Symfony 4 with Symfony Flex

Api documentation
-----------------
Api documentation is available at http://your_host/api/doc (only in dev environment)

![Api](/doc/api_swagger.png)

Quality check
-------------
Application was created using Behavior-driven development (**BDD**) approach.

**Behat** and **phpspec** were used to test business logic.

**PHP CS Fixer**, **PHP Mess Detector**, **PHP CodeSniffer**, **SensioLabs Security Checker** were use to test code quality.

To run all quality checks and tests:
```
ant quality-check
```

Running application
-------------
* Install all dependencies (you have to have composer installed):
```
composer install
```
* Generate the SSH keys ant put them in var/jwt/. You can follow instructions on [LexikJWTAuthenticationBundle documentation](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/master/Resources/doc/index.md#configuration)

* Set application environment variables (most of them can use default values except DATABASE_URL). You can find all needed environment variables in .env.dist file. Alternatively if you don't want to set them manually you can use Dotenv extension. [More information](https://symfony.com/doc/current/components/dotenv.html) 


* Run Symfony built-in Web Server (only for development and testing environments):
```
bin/console server:start
```

* To ensure that everything is working run:

```
ant quality-check
```
