# Giggle Pig: Back-end Framework (PHP)

<p align="center">
  <img width="460" height="300" src="https://raw.githubusercontent.com/srisar/giggle-pig/master/public/images/giggle-pig-full.svg">
</p>
Giggle Pig framework is an **highly opinionated** framework/template to develop monolithic applications with PHP backend and Vue.js-based front-end SPA. 

Vue front-end communicates with PHP backend using api calls, which can also be guarded with authentication and authorization. Because of the monolithic nature, authenticated api calls carry api-key for auth.

This project is developed mostly for rapid development for low-budget php hostable application development.

PHP codebase require at least PHP 7.2, Vue.js 2 is used on the front-end.

### How to set up

1. Once cloned the repo, make sure the public folder inside the project is pointed as serving folder for the http server. ([Laragon](https://laragon.org/download/index.html) is recommended for local development)
2. Run the SQL file in the project to set up the user table with admin user for login/authentication.
3. `bootstrap.php` file contains the connection details for the database server. Update the username, password and database name if needed.
4. run `npm install` to set up Vue building workflow. The project comes with Laravel-mix for building Vue SPA. After running `npm install` you can use `npx mix`
   to build Vue code, or use `npx mix watch` to run watch process that watch for any source code modification and build on the fly. more details [here.](https://laravel-mix.com/docs/6.0/upgrade#update-your-npm-scripts)

### Login details

Once you run the SQL file, you will have a single user (Administrator) in the database. You can log in to the app using the following credentials to access the admin area. 

```
username: admin
password: admin
```

------

## Features

Giggle Pig is highly opinionated. It comes with pre-built custom components for both PHP and Vue.js.

### Dialog boxes and input boxes

Giggle Pig uses Bootloks to provide Bootstrap's modal based programmatically callable dialog boxes. Bootloks is a standalone lib. https://github.com/srisar/bootloks

### Modal windows

Vue component that creates modal windows based on Bootstap 5 modals.

### File uploading features





### Libraries Used

**PHP**:

- nesbot/carbon

**JavaScript:**

- Vue.js, Vuex, Vue-Router
- axios
- lodash

**CSS**

- Bootstrap 5

**Build tools**

- Laravel-mix

