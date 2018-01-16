## Developer notes

Notes that might be useful for developers of this service.

### Data import & export

Import data with DataGrip
- Importing Extinct to TINYINT UNSIGNED failed, but converting the column to VARCHAR(5), importing data and then converting the column back to TINYINT UNSIGNED worked.

Import with DBeaver
- Imports only some fields, complains field truncation even if field lengths are ok

Database dumps:

        mysqldump -u taxonomyuser -p taxonomy > taxonomy1.sql

### Adding binomial species names to mammal_msw table:

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

### Slim framework initial setup

Go inside Docker to container /var/www/html directory and run 

        composer create-project slim/slim-skeleton app

**Note:** that if you use other name for the app than "app", you have to change that to the Dockerfile and rebuild the image.

Exit container and give rights to your user

        chown -R user:group *

Allow writing to monolog directory

        chmod a+w /var/www/html/app/logs/app.log

### Misc

Hack to log database connection

        $loggerSettings['name'] = 'slim-app';
        $loggerSettings['path'] = isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log';
        $loggerSettings['level'] = \Monolog\Logger::DEBUG;

        $settings = $loggerSettings;
        $logger = new Monolog\Logger($settings['name']);
        $logger->pushProcessor(new Monolog\Processor\UidProcessor());
        $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));

        ...

        global $logger;
        $logger->addInfo("DB ".$connectionString);


## Notes on the data

Data is imported from Mammal Species of the World taxon list and Rubin list with only minor modifications.


### Mammal Species of the World

Synonym field of MSW is messy, and curently truncated in the database.

Not all the taxa have all ranks included in the data.

MSW id's don't contain duplicates.

### Rubin list

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

### Number of species in each classification:

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
