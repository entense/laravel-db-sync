# Laravel DB Sync

![DB Dync](https://repository-images.githubusercontent.com/506690782/a5b01352-4869-4e6d-8e46-d44e93c960df)

## Introduction
Sync remote database to a local database

> A word of warning you should only sync a remote database into a local database if you have permission to do so within your organisation's policies. I'm syncing during early phases of development where the data is largely test data and not actual customer data.

Connection can be made over SSH or using a remote MySQL connection.

## Install

Install the package.

```bash
composer require dcblogdev/laravel-db-sync
```

## Config

You can publish the config file with:

```
php artisan vendor:publish --provider="Dcblogdev\DbSync\DbSyncServiceProvider" --tag="config"
``` 

## .env 

Set the remote database credentials in your .env file

When using SSH Add:
```
REMOTE_USE_SSH=true
REMOTE_SSH_PORT=22
REMOTE_SSH_USERNAME=
REMOTE_DATABASE_HOST=

REMOTE_DATABASE_USERNAME=
REMOTE_DATABASE_NAME=
REMOTE_DATABASE_PASSWORD=
REMOTE_DATABASE_IGNORE_TABLES=''

REMOTE_REMOVE_FILE_AFTER_IMPORT=true
REMOTE_IMPORT_FILE=true
```

For only MySQL remote connections:
```
REMOTE_DATABASE_HOST=
REMOTE_DATABASE_USERNAME=
REMOTE_DATABASE_NAME=
REMOTE_DATABASE_PASSWORD=
REMOTE_DATABASE_IGNORE_TABLES=''

REMOTE_REMOVE_FILE_AFTER_IMPORT=true
REMOTE_IMPORT_FILE=true
```

Set a comma seperate list of tables NOT to export in `REMOTE_DATABASE_IGNORE_TABLES`

## Usage

To export a remote database to OVERRIDE your local database by running:

```bash
php artisan db:production-sync
```
