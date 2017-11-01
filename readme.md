
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
- synonyms cannot be parsed trivially, since the field has diverse contents

TODO: synonym field is truncated, make field longer

Example species:
13001562 Mus musculus
10300011 Sir David ... no synonyms

Only works with species.

JSON-API way of creating the URL pattern seems to be
- Choose either singular or plural, then use that consistently. May workshop suggested singular ("taxon").
- /taxon/{id} should return the taxon with {id}
        - What to do if the id is HTTP-URI or contains slashes? /taxon/http://tun.fi/MX.123
- /taxon/filter[scientific_name]=Mus

SHOULD HAVE
- Full scientifi name in one field -> easy to do autocomplete


Copy data to temp table:


        INSERT INTO binomialTemp (MSW_ID, speciesBinomial)
        SELECT MSW_ID, CONCAT(mammal_msw.Genus, " ", mammal_msw.Species)
        FROM mammal_msw
        WHERE
        mammal_msw.TaxonLevel = "SPECIES"
        ;

Copy from temp to production table:


        UPDATE `mammal_msw` be
        JOIN `binomialTemp` fdb ON fdb.`MSW_ID` = be.`MSW_ID`
        SET be.`SpeciesBinomial` = fdb.`speciesBinomial`;
        ;




