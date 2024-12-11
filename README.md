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
   1. run `ddev yarn install`
   2. DEV watch: `ddev yarn run watch`
   3. DEV server: `ddev yarn run dev-server`
   


# Messenger

Start async message consumer

```bash
ddev php bin/console messenger:consume async
```

# Clear cache
```bash
ddev php bin/console cache:clear
```


Problems 

-- upload file , form errors aren`t displayed 