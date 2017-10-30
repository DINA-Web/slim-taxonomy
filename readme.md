
# Slim Setup

Go inside Docker to container /var/www/html directory and run 

        require slim/slim "^3.0" --no-dev

Exit container and give rights to your user

        chown -R user:group *

Add `.htaccess` to html `directory` file with:

        RewriteEngine On
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteRule ^ index.php [QSA,L]

# TODO

- Move app and document root to ./public ?