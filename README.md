# JESHOP API Laravel

![](/public/assets/images/logo-jr-mobile.png)

`PHP Version - Using PHP 8.1`

### Create Models

```
php artisan make:model ModelName -cmf
```

##### Options

-   c = controller
-   m = model
-   f = factory
-   s = seed
-   r = resource
-   R = requests
-   a = all

### Create a seed

```
php artisan make:seeder UserSeeder
```

### Run migrations

```
php artisan migrate
```

### Create a specific seeder

```
php artisan make:seeder UserSeeder
```

### Run a specific seeder

```
php artisan db:seed --class=UserSeeder
```

### Run seeders

```
php artisan migrate --seed
```

### Force rerun seeds (replace all data in database)

```
php artisan migrate:fresh --seed
```

### Example migration add role_id to user table

```
php artisan make:migration add_role_id_in_users_table --table=users
```

### Get all routes

```
php artisan route:list
```

### Docker Commands

```
docker-compose build --no-cache
docker-compose up -d --build --force-recreate
```
