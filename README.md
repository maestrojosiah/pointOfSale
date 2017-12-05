# pointOfSale

pointOfSale is a symfony 3 application that is designed for small businesses who are tired of the heavy POS softwares with a lot of things that they don't need. It helps keep track of sales, products, stock movement and much more. It generates reports in pdf format for printing. mpesa, cheque and credit card reports are included.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

You need to have some knowledge of symfony 3 framework to use this.
You also need:
1. To enable javascript in your browser
2. At least 3GB ram computer(for good speed)
3. Works best in a linux machine (speed and printer integration)
4. Composer


### Installing

A step by step series of examples that tell you how to get a development env running

Clone project from github

```
$ git clone https://github.com/maestrojosiah/pointOfSale.git
```

Install dependencies

```
$ cd pointOfSale
$ composer install
```

Install ckeditor

```
$ php bin/console ckeditor:install
```

Install assets

```
$ php bin/console assets:install web
```

Connect to mysql and create database with the details you provided in the step above

```
mysql: // replace my_db with the name of database you provided above
create database my_db 
```

Take care of some important doctrine steps

```
$ php bin/console doctrine:schema:update --force
```

Start the server

```
php bin/console server:run
```

Visit the site by CTRL+click the address given by symfony on the terminal after running the above command.

You will need to create a first user as admin, then the admin will create other users at will.

It is very easy to use. See the doc on the left sidebar after installing.


## Deployment

For deployment, see tutorials on deploying symfony apps, especially from digital ocean website
You can contact me with my username at Gmail.

## Built With

* [Symfony](http://symfony.com/doc/current/index.html) - The web framework used
* [Javascript] & [PHP] = [AJAX]


## Authors

* **Maestro Josiah** - *Initial work* - [Maestrojosiah](https://github.com/maestrojosiah)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments

* My wife for being comfortable with my silence, though she can't take it sometimes :)
