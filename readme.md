
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



## Example requests:

- http://localhost:90/taxon/13001562 // House mouse
- http://localhost:90/taxon/10300011 // Sir David's... without synonyms

- http://localhost:90/taxon/?filter[name]=Mus%20mus&search_type=partial
- http://localhost:90/taxon/?filter[name]=Mus%20musculus&search_type=exact
- http://localhost:90/taxon/?filter[name]=Mus%20musculus // defaults to exact

- http://localhost:90/taxon/123 // nonexistent id
- http://localhost:90/taxon/?filter[name]=Foo%20bar&search_type=exact // nonexistent name

## TODO

- Blueprint & validation with Dredd

## Notes on the API

- When an attribute (e.g. Rubin number or parent taxon) is not found, should the element in the API
        - contain null
        - contain empty string
        - be removed
        - be notified about in meta section (e.g. an array of missing fields)
- If taxon requested by id is not a species (e.g. 10300002), taxon data is returned except for
        - correct parent hierarchy (always starting from subgenus)
        - parent (fetched by genus name)
        - rubin number (fetched by species binomial name)
- Taxon name search searches only by species name. Extending search to other ranks would require either multiple database queries (try species, if not found try genus, if not found try family...), or restructuring the database (id, name, rank, parent -format).


## Notes on the data

MSW id's don't contain duplicates.

Rubinno's contain following duplicates (id, number of duplicates):

        8165139	8
        5100906	7
        8160333	4
        7350903	3
        8162706	3
        8163006	3
        8163609	3
        50303	2
        3060303	2
        3065506	2
        5150903	2
        5520606	2
        5520903	2
        7250312	2
        7250318	2
        7550618	2
        7700306	2
        7700903	2
        7800303	2
        7801503	2
        8160351	2
        8160378	2
        8160903	2
        8162106	2
        8162415	2
        8162709	2
        8163909	2
        8163921	2
        8164209	2
        8165112	2
        8168703	2
        8169609	2
        8300318	2
        8650303	2

Number of species in each classification:

        SELECT COUNT(*)
        FROM mammal_msw
        WHERE TaxonLevel = "SPECIES"
        -- 5416

        SELECT COUNT(*)
        FROM mammal_rubin
        WHERE RANK = "SPECIES"
        -- 1473

        SELECT COUNT(*)
        FROM mammal_msw
        INNER JOIN mammal_rubin ON mammal_msw.SpeciesBinomial = mammal_rubin.SPECIES
        WHERE TaxonLevel = "SPECIES"
        -- 1456

