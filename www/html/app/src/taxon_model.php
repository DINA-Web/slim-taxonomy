<?php

Class Taxon
{
    var $db;
    var $logger;
    
	// ------------------------------------------------------------------------
	// Constructor	
	public function __construct($db, $logger)
	{
        $this->db = $db;
        $this->logger = $logger;
    }

    public function fetchTaxon($id, $withParent = TRUE) {
        $sql = "
            SELECT *
            FROM mammal_msw
            WHERE MSW_ID=:id
            LIMIT 1
        ";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $taxon = $statement->fetch(); // Expecting only one row

        return $this->taxonToJSONAPIArray($taxon, $withParent);
    }

    public function fetchName($name, $withParent = TRUE, $search_type) {

        $limit = 10;
        if ("partial" == $search_type) {
            $sql = "
                SELECT *
                FROM mammal_msw
                WHERE SpeciesBinomial LIKE :name
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
        $this->logger->info("Query matched " . $statement->rowCount() . " rows, LIMITing to $limit");
        $taxon = $statement->fetch(); // Expecting only one row, so taking only the first

//        exit(print_r($taxon)); // debug

        return $this->taxonToJSONAPIArray($taxon, $withParent);        
    }

    public function taxonToJSONAPIArray($taxon, $withParent) {
        if ("SPECIES" == $taxon['TaxonLevel']) {
            $limit = 1;

            if ($withParent) {

                // Parent taxon
                $sql = "
                    SELECT *
                    FROM mammal_msw
                    WHERE TaxonLevel = 'GENUS'
                    AND Genus = '" . $taxon['Genus'] . "'
                    LIMIT $limit
                ";
                $statement = $this->db->prepare($sql);
                $statement->execute();
                $this->logger->info("Query matched " . $statement->rowCount() . " rows, LIMITing to $limit");
                $parentData = $statement->fetch(); // Expecting only one row
                
                $attributes['parent']['id'] = $parentData['MSW_ID'];
                $attributes['parent']['scientific_name'] = $parentData['Genus'];
                $attributes['parent']['rank'] = strtolower($parentData['TaxonLevel']);
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
                $attributes['verncular_names']['en'][] = $taxon["CommonName"];
            }

            // Synonyms
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

            // Other data
            $attributes['sort_order'] = $taxon['SortOrder'];
            
        }
    //    $attributes = $taxon; // debug - see full data from db

        $res['jsonapi']['version'] = "1.0";
        $res['meta']['Source'] = "Mammal Species of the World";
        $res['data']['type'] = "taxon";
        $res['data']['id'] = $taxon['MSW_ID'];
        $res['data']['attributes'] = $attributes; 

        return $res;
    }
}