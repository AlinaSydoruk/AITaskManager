# Alina's playground :)


# Setup Project

1. Setup Env Vars
   1. Duplicate ".env" and rename to ".env.local"
   2. configure missing variables
2. `ddev start`
3. Setup Bundles
   1. run `ddev composer install`
4. Setup DB
   1. run `ddev php bin/console doctrine:database:create`
   2. run `ddev php bin/console doctrine:migrations:migrate`
   3. run `ddev php bin/console doctrine:fixtures:load`
5. Setup Asset Build
   1. run `ddev php bin/console importmap:install`
   2. run `ddev php bin/console tailwind:build`


## Create port forwarding to ngrok 
   1. run in PowerShell `ngrok http <ddev_port>`
   2. run `Invoke-WebRequest -Uri "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/setWebhook" -Method POST -Body @{ url = 'https://<YOUR_NUMBER>.ngrok-free.app/bot/webhook' }`


## watch for changes to your assets/styles

```
ddev php bin/console tailwind:build --watch
```
## If you work on Windows and your app is running in a Docker container, and you are having trouble with the --watch option
```
ddev php bin/console tailwind:build --watch --poll
```

# Messenger

Start async message consumer

```bash
ddev php bin/console messenger:consume async
```

# Clear cache
```bash
ddev php bin/console cache:clear
```

