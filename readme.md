
# Slim Setup

Go inside Docker to container /var/www/html directory and run 

        require slim/slim "^3.0"

## A) Manual app creation

Exit container and give rights to your user

        chown -R user:group *

Add `.htaccess` to html `directory` file with:

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]

## B) Automatic app creation

Add your app location to Dockerfile, changing Apache root

Go inside Docker to container /var/www/html directory and run 

        composer create-project slim/slim-skeleton app

Exit container and give rights to your user

        chown -R user:group *


# TODO

- Move app and document root one level down to /var/www/app/public ??

