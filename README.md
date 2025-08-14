# DocuPet - Pet Registration System

Hey there! This is a simple pet registration app I built using Symfony. It helps you register pets with all their details like breed, age, and automatically flags dangerous animals for safety compliance.

## What it does

This app lets you register pets step by step. You pick the pet type (dog or cat), then choose from a list of breeds that updates automatically. It's pretty smart about dangerous breeds too - it'll warn you if you're registering something like a Pit Bull or Rottweiler.

The cool thing is you can enter age either as a birth date or just say "about 2 years old" if you're not sure. It also handles mixed breeds and custom entries when the exact breed isn't in the list.

## Tech stuff

Built with Symfony 6.4 and PHP 8.2. Uses MySQL for the database, Tailwind CSS for styling, and Docker to make everything easy to run. There are proper tests too.

## Getting it running

You'll need Docker Desktop and Git installed.

First, grab the code:
```bash
git clone https://github.com/itech-sniper/hll_docupet.git
cd hll_docupet
```

Copy the environment file:
```bash
cp .env.example .env
```

Start everything up:
```bash
docker-compose up --build -d
```

Wait a minute for everything to start, then set up the database:
```bash
docker-compose exec app php bin/console doctrine:database:create --if-not-exists
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

Build the frontend:
```bash
docker-compose exec app npm install
docker-compose exec app npm run build
```

That's it! Open http://localhost:8001 and you should see the app running.

## How to use it

Just go to the homepage and click "Register Your Pet". The form walks you through everything step by step. Pick your pet type first, then the breed list updates automatically. If you can't find the exact breed, there's an option for that too.

The age thing is flexible - if you know the exact birthday, great. If not, just pick "about X years old" from the dropdown.

## Running tests

To run the tests:
```bash
docker-compose exec app php bin/phpunit
```

Or run specific parts:
```bash
docker-compose exec app php bin/phpunit tests/Service/
docker-compose exec app php bin/phpunit tests/Controller/
```

## Development

If you want to work on the code, you can watch for changes:
```bash
docker-compose exec app npm run watch
```

Check the logs if something goes wrong:
```bash
docker-compose logs -f app
```

Get into the container to run commands:
```bash
docker-compose exec app bash
```

## Database stuff

If you need to reset everything:
```bash
docker-compose exec app php bin/console doctrine:database:drop --force
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

## Troubleshooting

If something's not working:

**Ports already in use?** Stop the containers and edit docker-compose.yml to use different ports.

**Database won't connect?** Try restarting it:
```bash
docker-compose restart db
```

**Frontend looks broken?** Rebuild the assets:
```bash
docker-compose exec app npm run build
```

Check the logs if you're stuck:
```bash
docker-compose logs app
```
