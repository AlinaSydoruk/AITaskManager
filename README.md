# Alina's playground :)


# Setup Project

1. Setup Env Vars
   1. Duplicate ".env" and rename to ".env.local"
   2. configure missing variables
   3. Copy EPD certs & key into `.ddev/certs` directory (see values with `EPD_*` prefix in `.env` for reference)
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
   4. PROD: `ddev yarn run build`
6. Setup SAML (SSO)
   1. Open http://localhost:8080/simplesaml/saml2/idp/metadata.php?output=xhtml in browser
   2. Copy value from `<ds:X509Certificate>` to `IDP_X509_CERT` in `.env.local`
   3. Login with `admin@ongoing.ch` and `test` to https://post-sanela-onetouch.ddev.site/admin


# Messenger

Start async message consumer

```bash
ddev php bin/console messenger:consume async
```

Start scheduler 
- Check HPD reminder email


```bash
ddev php bin/console messenger:consume scheduler_default
```

# Translations (Intl ICU)

```bash
ddev php bin/console jms:translation:extract --dir "src" --output-dir "translations" --default-output-format yaml --intl-icu
```

## Pull translations from Crowdin
```bash
ddev php bin/console translation:pull crowdin --locales en_US --locales fr_CH --locales it_CH --force --format=yaml --intl-icu
```

## Push translations to Crowdin
```bash
ddev php bin/console trans:push crowdin --locales=de_CH --force
```

## React translations
The `react` domain translations are cached:

```bash
ddev php bin/console cache:clear
```

# run tests
Setup test DB if you haven't already
```bash
ddev php bin/console doct:database:create --env=test
ddev php bin/console doct:schema:update --force --env=test
```

Run PHPUnit tests
```bash
ddev test
```

# check code, deploy, connect

Use the following DDEV command to check the code with rector and ESLint
```bash
ddev check
```
