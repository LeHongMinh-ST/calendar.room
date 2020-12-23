## Requirements

- PHP >= 7.1.3

## Usage

1. Clone project.
	$ git clone git@st-dse.vnua.edu.vn:minhlh/qlthoikhoabieuphongmay.git
2. cd 04-Source/code ,Create .env file, copy content from .env.example to .env file and config your database in .env:
``` bash
	DB_CONNECTION=mysql
	DB_HOST=database_server_ip
	DB_PORT=3306
	DB_DATABASE=database_name
	DB_USERNAME=username
	DB_PASSWORD=password

	START_TIME_1 = "07:00:00"
	START_TIME_6 = "12:45:00"
	START_TIME_13 = "18:00:00"
	PASSWORD_USER = default password
```
3. Run (cd 04-Source/code)
``` bash
	$ composer install
	$ composer require maatwebsite/excel
	$ php artisan key:generate
	$ php artisan migrate
	$ php artisan db:seed --class=DatabaseSeeder
	$ npm install
	$ npm run production
	$ php artisan storage:link
	$ php artisan vendor:publish --provider="Maatwebsite\Excel\ExcelServiceProvider" --tag=config
	$ php artisan route:clear
	$ php artisan config:clear
```
4. Local development server
- Run (cd 04-Source/code)
``` bash
	$ php artisan serve
```
- In your browser, go to [http://127.0.0.1:8000/admin/login](http://127.0.0.1:8000/admin/login) to run your project.
- Login with default admin acount email: admin@gmail.com and password: 12345678

