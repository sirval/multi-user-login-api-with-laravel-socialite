## About Multi-user-login-api-with-laravel-socialite

Multi-user-login-with-laravel-socialite is a login API built with laravel. It employs the native registration and login that is; username, email, password and confirm password. And also Laravel social application called Laravel Socialiate https://laravel.com/docs/8.x/socialite#main-content. This package enable developers implement login on click of a particular social media button like Facebook, Github, Google etc.

## How to Install 

To use this application
- clone the project from my github repo at https://github.com/sirval/multi-user-login-api-with-laravel-socialite
- once cloned, navigate to the folder in your terminal
- run <composer update> to get all the required dependencies
- create a .env file
- goto .env.example, copy and paste its content into the .env file you created.
- setup your social media client_id, client_secret_key and the redirect_uri all gotten from the         respective socail media developers platform.


## How to Run

To check out this API, open your postman and copy and paste the callback uri from your .env file. Remember it most be a GET request. If successful, it'll return a 200 OK status on your postman.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
