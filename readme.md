
# Slim Taxonomy

Mockup of a taxonomy API, serving data from Mammal Species of the Workl taxon list and Rubin taxon list.

## Setup (UNTESTED)

- `git clone https://github.com/mikkohei13/slim-taxonomy.git`
- Set up credentials to env/.env-mysql
- `docker-compose up`
- Install dependencies with composer
        - log in to backend container (`docker exec -ti` ...)
        - `cd /var/www/html/app`
        - `composer install`
        - `exit`
        - `chown -R user:group www/html/app/vendor`
- `chmod www/html/app/logs a+w`
- Access the service with following example requests:

### Example requests:

- http://localhost:90/taxon/13001562 // House mouse
- http://localhost:90/taxon/10300011 // Sir David's... without synonyms

- http://localhost:90/taxon/?filter[name]=Mus%20mus&search_type=partial
- http://localhost:90/taxon/?filter[name]=Mus%20musculus&search_type=exact
- http://localhost:90/taxon/?filter[name]=Mus%20musculus // search_type defaults to exact
- http://localhost:90/taxon/?filter[name]=Mus%20mus // no results with this exact name

- http://localhost:90/taxon/123 // nonexistent id
- http://localhost:90/taxon/?filter[name]=Foo%20bar&search_type=exact // nonexistent name

## Notes on the API

- Only works with species rank
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
- How to format the URL if taxon id contains slashes? (e.g. http-uri)
-MSW contains somewhat messy data. Now part of this is cleaned in the API
        - Uppercase order etc. names to lowercase
        - HTML in some fields (`<i>` removed)
        - Synonyms cannot be parsed trivially, since the field has diverse contents
        - Missing ranks: Marsupialia, Mammalia

Possible alternative format for the API:

        taxon
                id
                name
                rank ...
                parents
                        2
                                id: 2
                                name
                                rank ...
                children
                        3
                                id: 3
                                name
                                rank ...


## TODO

### For mockup:

- Validation with Dredd
        - Documentation for validation
- Replace docker-compose with a simple makefile docker build command

### For more stable use:

- Package as Docker Hub image, with proper directory permissions
- Clean up synonyms. Currently synonym field is truncated.

## Dev notes

Import with DataGrip
- Importing Extinct to TINYINT UNSIGNED failed, but converting the column to VARCHAR(5), importing data and then converting the column back to TINYINT UNSIGNED worked.

Import with DBeaver
- Imports only some fields, complains field truncation even if field lengths are ok

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

Database dumps:

        mysqldump -u taxonomyuser -p taxonomy > taxonomy1.sql

### Slim Setup

Go inside Docker to container /var/www/html directory and run 

        composer create-project slim/slim-skeleton app

**Note:** that if you use other name for the app than "app", you have to change that to the Dockerfile and rebuild the image.

Exit container and give rights to your user

        chown -R user:group *

Allow writing to monolog directory

        chmod a+w /var/www/html/app/logs/app.log

## Notes on the data

Data is imported from Mammal Species of the World taxon list and Rubin list with only minor modifications.

Synonym field of MSW is messy, and truncated in the database.

MSW id's don't contain duplicates.

Rubinno's contain following duplicates (id, number of occurrences):

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

