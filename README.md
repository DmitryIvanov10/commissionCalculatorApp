# commission-calculator-app
Test app to calculate commissions

## Versions:
**Symfony 4.4**

**PHP 7.4.6**

## Install and run:
1. ```git clone git@github.com:DmitryIvanov10/commissionCalculatorApp.git```
2. ```cd commissionCalculatorApp```
3. ```docker-compose up --build -d```
4. You are able to work with PHP from inside the container```docker exec -it php sh```
5. Run ```composer install```
6. To test the application run the command ```php bin/console app:calculate-commissions input.txt```
7. The result will be output in the console
8. Run tests with ```./bin/phpunit```
