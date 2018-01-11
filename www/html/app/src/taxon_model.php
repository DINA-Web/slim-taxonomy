<?php

Class Taxon
{
    var $db;
    var $mylog;
    
	// ------------------------------------------------------------------------
	// Constructor	
	public function __construct($db, $mylog)
	{
        $this->db = $db;
        $this->mylog = $mylog;
    }

    public function fetchTaxon($id, $withParent = TRUE) {
        $limit = 1;

        $sql = "
            SELECT *
            FROM mammal_msw
            WHERE MSW_ID=:id
            LIMIT $limit
        ";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        ($this->mylog)("Query matched " . $statement->rowCount() . " rows, LIMIT set to $limit");
        
        // Can potentially return multiple taxa having (incorrectly) the same id
        $taxa = Array();        
        while ($row = $statement->fetch()) {
            $taxa[] = $row;            
        }

        return $this->taxonToJSONAPIArray($taxa, $withParent);
    }

    public function fetchName($name, $withParent = TRUE, $search_type) {
        $limit = 10;

        if ("partial" == $search_type) {
            // Note: have to include TaxonLevel if using other than SpeciesBinomial on WHERE clause, to return only species
            $sql = "
                SELECT *
                FROM mammal_msw
                WHERE 
                    (
                    SpeciesBinomial LIKE :name
                    OR CommonName LIKE :name            
                    OR Synonyms LIKE :name 
                    )
                    AND TaxonLevel LIKE 'SPECIES'
                LIMIT $limit
            ";
            $statement = $this->db->prepare($sql);
            $statement->bindValue(":name", ('%'.$name.'%'), PDO::PARAM_INT);
        }
        elseif ("exact" == $search_type) {
            $sql = "
                SELECT *
                FROM mammal_msw
                WHERE SpeciesBinomial=:name
                LIMIT $limit
                ";
//            exit($sql . $search_type . $name); // debug
            $statement = $this->db->prepare($sql);
            $statement->bindValue(":name", $name, PDO::PARAM_INT);
        }
        $statement->execute();

        ($this->mylog)("Query matched " . $statement->rowCount() . " rows, LIMIT set to $limit");        

        $taxa = Array();
        while ($row = $statement->fetch()) {
            $taxa[] = $row;            
        }

        return $this->taxonToJSONAPIArray($taxa, $withParent);        
    }

    public function taxonToJSONAPIArray($taxa, $withParent) {
        $taxonN = 0;
        $res['data'] = Array();
        $attributes = Array();

        foreach ($taxa as $key => $taxon) {
            if ($withParent) {
//                $taxon['Genus'] = "debug"; // debug
                
                // Parent taxon
                $sql = "
                    SELECT *
                    FROM mammal_msw
                    WHERE TaxonLevel = 'GENUS'
                    AND Genus = '" . $taxon['Genus'] . "'
                    LIMIT 1
                ";
                $statement = $this->db->prepare($sql);
                $statement->execute();

                ($this->mylog)("Parent query with taxon '" . $taxon['Genus'] . "' matched " . $statement->rowCount() . " rows, LIMIT set to 1");
                $parentData = $statement->fetch(); // Expecting only one row
                
                $attributes['parent']['id'] = $parentData['MSW_ID'];
                $attributes['parent']['scientific_name'] = $parentData['Genus'];
                $attributes['parent']['rank'] = strtolower($parentData['TaxonLevel']);
            }

            $withRubin = TRUE;
            if ($withRubin) {
                // Rubin number
//                $taxon['SpeciesBinomial'] = "debug"; // debug
                $sql = "
                    SELECT RUBINNO
                    FROM mammal_rubin
                    WHERE SPECIES LIKE '" . $taxon['SpeciesBinomial'] . "'
                    LIMIT 1
                ";
                $statement = $this->db->prepare($sql);
                $statement->execute();
                ($this->mylog)("Rubin query with name '" . $taxon['SpeciesBinomial'] . "' matched " . $statement->rowCount() . " rows, LIMIT set to 1");                
                $rubinData = $statement->fetch(); // Expecting only one row
                
                $attributes['rubin_number'] = $rubinData['RUBINNO'];
            }
                
            // Higher taxa
            $attributes['higherTaxa']['order'] = ucfirst(strtolower($taxon['Order']));
            $attributes['higherTaxa']['suborder'] = ucfirst(strtolower($taxon['Suborder']));
            $attributes['higherTaxa']['infraorder'] = ucfirst(strtolower($taxon['Infraorder']));
            $attributes['higherTaxa']['superfamily'] = $taxon['Superfamily'];
            $attributes['higherTaxa']['family'] = $taxon['Family'];
            $attributes['higherTaxa']['subfamily'] = $taxon['Subfamily'];
            $attributes['higherTaxa']['tribe'] = $taxon['Tribe'];
            $attributes['higherTaxa']['genus'] = $taxon['Genus'];
            $attributes['higherTaxa']['subgenus'] = $taxon['Subgenus'];
            $attributes['higherTaxa'] = array_filter($attributes['higherTaxa']);

            // Taxon
            $attributes['rank'] = strtolower($taxon['TaxonLevel']);
            $attributes['scientific_name'] = $taxon['Genus'] . " " . $taxon['Species'];
            $attributes['author'] = $taxon['Author'];
            $attributes['author_date'] = $taxon['AuthorDate'];
                
            if ("YES" == $taxon['ValidName']) {
                $attributes['valid_name'] = TRUE;
            }
            else {
                $attributes['valid_name'] = FALSE;
            }

            if (!empty($taxon["CommonName"])) {
                $attributes['vernacular_names']['en'][] = $taxon["CommonName"];
            }

            // Synonyms
            if (! empty($taxon['Synonyms'])) {
                $attributes['synonyms'] = $taxon['Synonyms'];
            }
            /*
            // Trying to parse synonyms to different fields
            if (! empty($taxon['Synonyms'])) {
                $synonymsArrHTML = explode(";", $taxon['Synonyms']);
                $n = 0;
                foreach($synonymsArrHTML as $key => $synonymHTML) {
                    $synonymArr = explode("</i>", $synonymHTML);
                    $attributes['synonyms'][$n]['species_ephithet'] = trim(str_replace("<i>", "", $synonymArr[0]));
                    @$attributes['synonyms'][$n]['author'] = trim($synonymArr[1]); // Suppress errors resulting from inconsistent synonym data (missing </i>)
                    $n++;
                }
            }
            */

            // Other data
            $attributes['sort_order'] = $taxon['SortOrder'];

            $res['data'][$taxonN]['type'] = "taxon";
            $res['data'][$taxonN]['id'] = $taxon['MSW_ID'];
            $res['data'][$taxonN]['attributes'] = array_filter($attributes);     

            unset($attributes);
            $taxonN++;
        }
    //    $attributes = $taxon; // debug - see full data from db

        $res['jsonapi']['version'] = "1.0";
        $res['meta']['source'] = "Mammal Species of the World";
        $res['meta']['number_of_records_returned'] = $taxonN;
    
        return $res;
    }
}