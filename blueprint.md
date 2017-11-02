FORMAT: 1A
HOST: http://localhost:90/

# Taxonomy

API that displays taxonomy data.

## Taxon [/taxon/{taxon_id}]

+ Parameters
    - taxon_id (number) - ID of the taxon (integer)

### Show a taxon [GET]

+ Response 200 (application/json)

    + Body

        {
        "data": [
            {
            "type": "taxon",
            "id": "13001562",
            "attributes": {
                "parent": {
                "id": "13001537",
                "scientific_name": "Mus",
                "rank": "genus"
                },
                "rubin_number": "9472966",
                "higherTaxa": {
                "order": "Rodentia",
                "suborder": "Myomorpha",
                "infraorder": "",
                "superfamily": "Muroidea",
                "family": "Muridae",
                "subfamily": "Murinae",
                "tribe": "",
                "genus": "Mus",
                "subgenus": "Mus"
                },
                "rank": "species",
                "scientific_name": "Mus musculus",
                "author": "Linnaeus",
                "author_date": "1758",
                "valid_name": true,
                "verncular_names": {
                "en": [
                    "House Mouse"
                ]
                },
                "sort_order": "30-01562"
            }
            }
        ],
        "jsonapi": {
            "version": "1.0"
        },
        "meta": {
            "source": "Mammal Species of the World",
            "number_of_records_returned": 1
        }
        }

    + Schema

        {
            "type": "object",
            "additionalProperties": false,
            "properties": {
                "data": {
                    "type": "array",
                    "items": {
                        "type": "object",
                        "additionalProperties": false,
                        "properties": {
                            "type": {
                                "type": "string"
                            },
                            "id": {
                                "type": "string"
                            },
                            "attributes": {
                                "type": "object"
                                "additionalProperties": false,
                                "properties": {
                                    "parent": {
                                        "type": "object",
                                        "additionalProperties": false,
                                        "properties": {
                                            "id": {
                                                "type": "string"
                                            },
                                            "scientific_name": {
                                                "type": "string"
                                            },
                                            "rank": {
                                                "type": "string"
                                            }
                                        }
                                    },
                                    "higherTaxa": {
                                        "type": "object",
                                        "additionalProperties": false,
                                        "properties": {
                                            "order": {
                                                "type": "string"
                                            },
                                            "suborder": {
                                                "type": "string"
                                            },
                                            "infraorder": {
                                                "type": "string"
                                            },
                                            "superfamily": {
                                                "type": "string"
                                            },
                                            "family": {
                                                "type": "string"
                                            },
                                            "subfamily": {
                                                "type": "string"
                                            },
                                            "tribe": {
                                                "type": "string"
                                            },
                                            "genus": {
                                                "type": "string"
                                            },
                                            "subgenus": {
                                                "type": "string"
                                            },
                                        }
                                    },
                                    "rubin_number": {
                                        "type": "number"
                                    },
                                    "rank": {
                                        "type": "string"
                                    },
                                    "scientific_name": {
                                        "type": "string"
                                    },
                                    "author": {
                                        "type": "string"
                                    },
                                    "author_date": {
                                        "type": "string"
                                    },
                                    "valid_name": {
                                        "type": "boolean"
                                    },
                                    "sort_order": {
                                        "type": "string"
                                    },
                                    "vernacular_names": {
                                        "type": "object",
                                        "additionalProperties": false,
                                        "properties": {
                                            "en": {
                                                "type": "array",
                                                "items": {
                                                    "type": "string"
                                                }
                                            }
                                        }
                                    },
                                }
                            },
                        }
                    }
                },
                "jsonapi": {
                    "type": "object",
                    "additionalProperties": false,
                    "properties": {
                        "version": {
                            "type:" "string",
                        }
                    }
                },
                "meta": {
                    "type": "object",
                    "additionalProperties": false,
                    "properties": {
                        "source": {
                            "type:" "string",
                        },
                        "number_of_records": {
                            "type:" "number",
                        }
                    }
                }
            }
        }

