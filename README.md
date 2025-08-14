# DocuPet - Pet Registration System

Hey there! This is a simple pet registration app I built using Symfony. It helps you register pets with all their details like breed, age, and automatically flags dangerous animals for safety compliance.

## What it does

This app lets you register pets step by step. You pick the pet type (dog or cat), then choose from a list of breeds that updates automatically. It's pretty smart about dangerous breeds too - it'll warn you if you're registering something like a Pit Bull or Rottweiler.

The cool thing is you can enter age either as a birth date or just say "about 2 years old" if you're not sure. It also handles mixed breeds and custom entries when the exact breed isn't in the list.

## Tech stuff

Built with Symfony 6.4 and PHP 8.2. Uses MySQL for the database, Tailwind CSS for styling, and Docker to make everything easy to run. There are proper tests too.

## Getting it running on macOS

You'll need Docker Desktop for Mac and Git (which comes with Xcode command line tools).

If you don't have Docker Desktop yet:
1. Download it from [docker.com](https://www.docker.com/products/docker-desktop)
2. Install and start it up
3. Make sure it's running (you'll see the whale icon in your menu bar)

First, grab the code:
```bash
git clone https://github.com/itech-sniper/hll_docupet.git
cd hll_docupet
```

Copy the environment file:
```bash
cp .env.example .env
```

Start everything up (this might take a few minutes the first time):
```bash
docker-compose up --build -d
```

You can watch the progress with:
```bash
docker-compose logs -f app
```

Once everything's running, set up the database:
```bash
docker-compose exec app php bin/console doctrine:database:create --if-not-exists
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

Build the frontend assets:
```bash
docker-compose exec app npm install
docker-compose exec app npm run build
```

That's it! Open http://localhost:8001 and you should see the app running.

**Note for M1/M2 Macs**: If you run into any issues, the containers should work fine on Apple Silicon, but let me know if you see any weird errors.

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

## Development on macOS

If you want to work on the code, you can watch for changes (this will automatically rebuild CSS when you save files):
```bash
docker-compose exec app npm run watch
```

Check the logs if something goes wrong:
```bash
docker-compose logs -f app
```

Get into the container to run commands (useful for debugging):
```bash
docker-compose exec app bash
```

**Pro tip**: You can edit the code directly on your Mac using any editor (VS Code, PHPStorm, etc.) and the changes will sync automatically to the Docker container thanks to volume mounting.

## Database stuff

If you need to reset everything (useful if you mess up the data):
```bash
docker-compose exec app php bin/console doctrine:database:drop --force
docker-compose exec app php bin/console doctrine:database:create
docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction
```

You can also connect to the database directly using any MySQL client:
- **Host**: localhost
- **Port**: 3307 (not the usual 3306!)
- **Username**: root
- **Password**: password
- **Database**: hll_docupet

I like using [Sequel Pro](https://www.sequelpro.com) or [TablePlus](https://tableplus.com) on macOS.

## Troubleshooting on macOS

If something's not working:

**Docker Desktop not running?** Make sure you see the whale icon in your menu bar and it says "Docker Desktop is running".

**Ports already in use?** If you get port conflicts (like if you're running MAMP or another local server):
```bash
docker-compose down
# Edit docker-compose.yml and change "8001:80" to something like "8002:80"
docker-compose up -d
```

**Database won't connect?** Try restarting it:
```bash
docker-compose restart db
```

**Frontend looks broken?** Rebuild the assets:
```bash
docker-compose exec app npm run build
```

**Permission issues?** Sometimes on macOS you might need to fix file permissions:
```bash
sudo chown -R $(whoami) .
```

Check the logs if you're stuck:
```bash
docker-compose logs app
```

**Still having trouble?** Try the nuclear option:
```bash
docker-compose down
docker system prune -f
docker-compose up --build -d
```
