Using PHP 8.1

Commands
#Create Models
php artisan make:model ModelName -cmf
c = controller
m = model
f = factory
s = seed
r = resource
R = requests
a = all

#create a seed
php artisan make:seeder UserSeeder

#run migrations
php artisan migrate

#create a specific seeder
php artisan make:seeder UserSeeder

#run a specific seeder
php artisan db:seed --class=UserSeeder

#Run seeders
php artisan migrate --seed

#Force rerun seeds (replace all data in database)
php artisan migrate:fresh --seed

#example migration add role_id to user table
php artisan make:migration add_role_id_in_users_table --table=users
