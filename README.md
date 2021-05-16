# Little framework

## Directories

The name and location of the directories are based on common accepted application development standards

1. app - application classes, all MVC components
    1. Controllers - application controllers. Processing data from the database via Models.
    1. Extensions - additional classes that are responsible for the correct operation of MVC. For example: routes, DB
       connection, etc.
    1. Middlewares - intermediary classes for performing additional actions before starting the application
    1. Models - application models. Equivalent to DB entities
    1. Views - layout and display of data processed in controllers
1. config - configuration files, application constants
1. public - application entry point
    1. assets - JavaScript, CSS, and other UI files

## Files

The name of the files describes their functionality

1. app/autoload.php - implementation of the spl_autoload_register method. Auto-loading classes
1. app/Controllers/Controller.php - general class for controllers. Empty inside. Needed to assign all controllers to the
   same family
1. app/Controllers/HomeController.php - home page output
1. app/Controllers/UserController.php - authorization, registration, and adding users. As well as the output of the
   corresponding View
1. app/Extensions/Config.php - loading all configs from the corresponding directory
1. app/Extensions/Database.php - connecting to the database. Mysqli wrapper
1. app/Extensions/Request.php - processing the $_REQUEST magic variable and retrieving the data
1. app/Extensions/Route.php - processing uris via controllers
1. app/Extensions/View.php - render and working with View
1. app/Middlewares/MiddlewareAbstract.php - an abstract class for the uniformity of Middlewares. Contains the main run
   method
1. app/Middlewares/Middleware.php - distributes classes by route or by general purpose
1. app/Middlewares/AccessOnlyAdminsMiddleware.php - prohibits non-administrators from accessing certain routes
1. app/Middlewares/AccessOnlyLoggedUser.php - prohibits unauthorized users from accessing certain routes
1. app/Middlewares/AddUserToViewMiddleware.php - adds user data to the View
1. app/Middlewares/DisableAuthPagesForLoggedUserMiddleware.php - disabling authorization and registration for authorized
   users
1. /app/Models/Model.php - the main class of models. Implement functions for manipulating entities in the database, is
   similar to ORM
1. /app/Models/User.php - the model of the corresponding entity in the database
1. /app/Views/*.* - all the View and its components
1. /config/*.* - files with constants for the correct operation of the application
1. /public/*.* - public directory, the entry point to the application
1. /public/assets/*.* - frontend styles, scripts, and images
1. /routes/web.php - application routes. Use the Route extension and controllers
1. /storage/*.* - user-uploaded files

## Backend

### Application Entry Point

All requests are first redirected to the /public directory. Then they are redirected to index.php

Two .htaccess files are required to share responsibility between the entire application and the public directory. In
other words, you should not change the first one .htaccess, but you can use the second one

```
<IfModule mod_rewrite.c>
    RewriteEngine on
    RewriteCond %{REQUEST_URI} !^public
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

```
<IfModule mod_rewrite.c>
    RewriteEngine On

#    Redirect Trailing Slashes If Not A Folder...
#    RewriteCond %{REQUEST_FILENAME} !-d
#    RewriteCond %{REQUEST_URI} (.+)/$
#    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

As mentioned, the input point is /public/index.php - this file is responsible for the operation of the entire
application. This is where all queries refer to.

As mentioned, the input point is /public/index.php - this file is responsible for the operation of the entire
application. This is where all queries refer to. However, there are only a few lines inside the file that launch all the
important functions of the application:

1. `(new Config ())` - loading configs
1. `Middleware:: run ()` - loading Middleware
2. `Route:: run ()` - start routes

There is also `"require_once __DIR__.'/../app/autoload.php'"` - this method connects a file with class autoloading. All
classes in the app start with" App", which is equivalent to the" app "directory - this conversion takes place in "App".
autoload.php"

### Middlewares

#### Middleware.php

First, Middlewares are loaded into the application. This function is implemented by the App\Middleware\Middleware class.
This class uses the run method assigned in the abstract class to load certain routes, depending on certain conditions.

The loading conditions are 2 variables:

1. `$middleware-responsible` for loading Middleware on any route
1. `$middleware_uris-responsible` for loading Middleware on certain routes

First, the general Middleware is processed, then the assignment to specific routes is processed. Therefore, the
middleware in `$middleware_uris` can use the data left in the static properties/classes of `$middleware`. Thus, for
example, first the user data is loaded everywhere in the static View property, then this variable is used in other
Middleware to check the rights and authorization of the user on certain routes

#### MiddlewareAbstract.php

As mentioned earlier, all Middleware uses the abstract class App\Middlewares\MiddlewareAbstract, which defines the
method ::run()
This method is the main one and runs in App\Middlewares\Middleware when loading Middlewares

#### About All Middlewares

1. AccessOnlyAdminsMiddleware.php - through the user controller, it checks whether the current user belongs to the
   administrator. If it doesn't belong, it redirects to the main page
1. AccessOnlyLoggedUser.php - through the user controller and View checks the authorization of the current user. If not
   logged in, then redirects to the main page
1. DisableAuthPagesForLoggedUserMiddleware.php - redirects authorized users to the home page. The check goes through the
   View and the controller
1. AddUserToViewMiddleware.php - adds user data to the static View property

### Route

After Middleware, the routes are started. They are needed to assign a controller and method to a specific uri

All routes are listed in **routes/web.php**

Routes use the `App\Extensions\Route` extension class. This class contains a set of methods for working with controllers
and searching for routes to controllers. Similarly, the class defines the type of request and is used in other classes
to get a uri without a GET request via the function `::getURI()`

Methods:

1. `run()` - the main method that starts scanning the routes and executing the controller for the found route
1. `add()` - adding a route with a uri, a controller with a method, and a request type
1. `get()` - adding a route with a specified uri, a controller with a method. Used for GET requests
1. `post()` - adding a route with a specified uri, a controller with a method. Used for POST requests
1. `getURI()` - getting a uri without a GET request
1. `scanRoutes()` - scanning the routes directory and using "require_once" on them
1. `showCurrentRouteView()` - using the controller and its method if the router was found. Route by comparing the
   elements in the uri and the path property of the route. Then, if necessary, variables are entered in the array of
   variables for the controller and as a result, the specified method of the specified controller executes

### Controllers

The application uses defaults controllers that contain logic for processing data from the database and then passing it
to the View

Some controllers contain the **ENDPOINTS** constant, which is necessary to specify the endpoints of the application
where the user will be redirected in case of successful / unsuccessful requests. This constant is used primarily in
methods related to data manipulation and in methods used in POST requests

#### Controller.php

An empty class is the parent of all controllers. Used to create connections between controllers

#### HomeController.php

Responsible for the output of the home page

#### UserController.php

Uses the User.php model. Responsible for manipulating the model and displaying registration and authorization forms. It
also has additional methods for third-party classes.

Methods

1. registerUser() - user authorization based on post data, including verification of this data. In case of success,
   redirection
1. loginUser() - user authorization based on POST data, including verification. In case of success, redirection
1. logoutUser() - the user's cookie is reset to zero
1. getUser() - getting a user by condition, or by mail
1. getCurrentUser() - Getting a user by cookies
1. getCookiesUser() - Getting a user by cookies via current Controller
1. isAdmin() - checking the admin rights of the user
1. isRoot() - checking the root rights of the user
1. showLoginForm() - displaying the authorization form
1. showRegisterForm() - displaying the registration form

### Views

Views are closer to frontend, but still interact with controllers and are closely related to server logic.

To display the View, use the `App\Extensions\View` class of the same name. This class in the constructor returns the
specified view from the root of the **"app/Views"** directory, or through the static method `::include()` returns a part of
the template relative to the **"app/Views/template-parts"** directory.

A view can contain properties specified via the `::addData()` static method, which adds a new value to the static
property. This value can be taken using the static `::getData()` method, which takes a key, and a default value. The data
in the View is used in Controllers and Middlewares

The parts of the View template can be divided into 2 groups: general and normal. Common blocks are footer.php,
header.php, and meta.php. The general ones are all the others
# Frontend
The application frontend is closely related to the backend. However, there are parts of the frontend that are not affected by the backend. For example assets (“public/assets”) and plug-in styles.
The site uses styles+js Bootstrap and JQuery

