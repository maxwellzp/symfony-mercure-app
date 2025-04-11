# Symfony mercure application

This is a Symfony project that demonstrate the integration of Symfony, Stimulus and Mercure Protocol.


## Requirements
* PHP 8.2 or higher
* Symfony CLI binary
* Docker Compose
* NPM

## Installation
* Clone the repository to your computer
```bash
git clone git@github.com:maxwellzp/symfony-mercure-app.git
```
* Change your current directory to the project directory
```bash
cd symfony-mercure-app
```
* Install Composer dependencies
```bash
composer install
```
* Install node modules dependencies and build them in dev
```bash
npm install
npm run dev
```
* Start Mercure in Docker container
```bash
docker compose up -d
```

* Start Symfony development server
```bash
symfony server:start --no-tls -d
```
* Start websoket service
```bash
symfony console app:binance-websocket
```

### Usage
* Access the application in any browser at the given URL http://127.0.0.1:8000/

### Running Tests

To run tests, run the following command

```bash
./bin/phpunit
```


## License

[MIT](https://choosealicense.com/licenses/mit/)

