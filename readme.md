# DEPRECATED

Slim taxonomy is deprecated and replaced by a new service that is bundled with [dina collections](https://github.com/DINA-Web/dina-collections). See [API docs](https://alpha-api.dina-web.net/docs#/taxonService/getTaxaByName) or the [service implementation](https://github.com/DINA-Web/dina-collections/tree/master/packages/backend/src/services/taxonService)



# Slim Taxonomy

Mockup of a taxonomy API, serving readonly data from Mammal Species of the World taxon list and Rubin taxon list in JSON-API format.


The purpose of this API is to 

* **A)** support development of Collections module before a production-ready API is created, and 
* **B)** support design of the production API.


Alpha service: https://alpha-slimtaxonomy.dina-web.net/

### See also separate documents:

- **API Schema**: https://alpha-api-docs.dina-web.net/ This is edited on the dina-schema repository: https://github.com/DINA-Web/dina-schema
- **Developer notes**: Developer_notes.md
- **API notes and questions**: https://docs.google.com/document/d/1oGb9dfATz7Vpu85BoXP3qkZsRpk4R52sJKmRSdnPJEk/edit?usp=sharing

## Setup

- `git clone https://github.com/DINA-Web/slim-taxonomy.git`
- Set up credentials to env/.env-mysql
- `docker-compose up --build`
- Access the service example requests below

### Running on your local host

The slim-taxonomy service is tied to the 'dwproxy_default'-network. To set this up locally:

- Add `alpha-slimtaxonomy.dina-web.net` to `/etc/hosts`
- start the dw-proxy (`https://github.com/DINA-Web/proxy-docker`, branch 'compose-version3')

Other optionj is to run the service without a network, using `docker-compose.local.yml` settings:

        docker-compose -f ./docker-compose.local.yml up

## GOTCHAs

- If you change user/password env variables, they won't be taken into account on restart, unless you first emove the local data volume: "none of the variables below will have any effect if you start the container with a data directory that already contains a database: any pre-existing database will always be left untouched on container startup."
- Same with creating tha database with mysqldump: the database is only created using the dump file if it does not already exist.

## Upgrade (UNTESTED)

- `docker-compose down`, `git pull` & `docker-compose up` if db has not changed

## Example requests:

- http://localhost:90/taxon/13001562 // House mouse
- http://localhost:90/taxon/10300011 // Sir David's... without synonyms

- http://localhost:90/taxon?filter[name]=Mus%20mus&search_type=partial
- http://localhost:90/taxon?filter[name]=Mus%20musculus&search_type=exact
- http://localhost:90/taxon?filter[name]=Mus%20musculus // search_type defaults to exact
- http://localhost:90/taxon?filter[name]=Mus%20mus // no results with this exact name

- http://localhost:90/taxon/123 // nonexistent id
- http://localhost:90/taxon?filter[name]=Foo%20bar&search_type=exact // nonexistent name


## Notes of the API

- Follows JSON-API, with a *sensu lato* view of a resource
- Searches from both scientific and vernacular name.
- Fuzzy search matches any part of name
- When an attribute (e.g. Rubin number or parent taxon) is not found, this field is not shown on the API. (https://github.com/DINA-Web/slim-taxonomy/pull/14)
- Identifiers are strings (https://github.com/DINA-Web/dina-schema/pull/38)

### Limitations

The API is based on data from Mammal Species of the World with only little modifications (cleanup). This causes some limitations:

- Taxon name search searches only by species name. Extending search to other ranks would require either multiple database queries (try species, if not found try genus, if not found try family...), or restructuring the database (id, name, rank, parent -format).
- If taxon requested by id is not a species (e.g. 10300002), taxon data is returned except for
        - correct parent hierarchy (always starting from subgenus)
        - parent (fetched by genus name)
        - rubin number (fetched by species binomial name)
-MSW contains somewhat messy data. Now part of this is cleaned in the API
        - Uppercase order etc. names to lowercase
        - HTML in some fields (`<i>` removed)
        - Synonyms cannot be parsed trivially, since the field has diverse contents
        - Missing ranks: Marsupialia, Mammalia


## TODO

Could do for more stable use:

- CI
- Testing and validation
- Package as Docker Hub image, with proper directory permissions
- Clean up data, at least synonyms. Currently synonym field is truncated.
- Review of permission settings
- Data security - does Slim auto-sanitize user input?

## Examples

- https://alpha-slimtaxonomy.dina-web.net/taxon/14200208
- https://alpha-slimtaxonomy.dina-web.net/taxon?filter[name]=Alces%20alces&search_type=exact
- https://alpha-slimtaxonomy.dina-web.net/taxon?filter[name]=Alc&search_type=partial
