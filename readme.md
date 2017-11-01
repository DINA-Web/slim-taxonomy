
# Slim Setup

Go inside Docker to container /var/www/html directory and run 

        composer create-project slim/slim-skeleton app

**Note:** that if you use other name for the app than "app", you have to change that to the Dockerfile and rebuild the image.

Exit container and give rights to your user

        chown -R user:group *

Allow writing to monolog directory

        chmod a+w /var/www/html/app/logs/app.log


# Notes

- Todo: ? Move app and document root one level down to /var/www/app/public ??

Database dumps:

        mysqldump -u taxonomyuser -p taxonomy > taxonomy1.sql

Import with DataGrip
- Importing Extinct to TINYINT UNSIGNED failed, but converting the column to VARCHAR(5), importing data and then converting the column back to TINYINT UNSIGNED worked.

Import with DBeaver
- Imports only some fields, complains field truncation even if field lengths are ok

LU_RUBIN

Missing detailed info, including rank:
1748	4500000		Människoapor	Hominidae
1749	4550000			Hominidae

Missing rank:
Marsupialia
Mammalia

Duplicate rubinno:
7801503
7550618
etc...

MSW contains somewhat messy data. Now this is cleaned in the API
- Uppercase order etc. names
- html in some fields (<i>)


