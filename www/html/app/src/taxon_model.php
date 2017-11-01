<?php

Class Taxon
{
    var $db;

	// ------------------------------------------------------------------------
	// Constructor	
	public function __construct($db)
	{
        $this->db = $db;
    }

    public function fetchTaxon($id, $withParent = TRUE) {
        $sql = "
            SELECT *
            FROM mammal_msw
            WHERE MSW_ID=:id
        ";
        $statement = $this->db->prepare($sql);
        $statement->bindValue(":id", $id, PDO::PARAM_INT);
        $statement->execute();

        $data = $statement->fetch(); // Expecting only one row

        if ("SPECIES" == $data['TaxonLevel']) {

            if ($withParent) {

                // Parent taxon
                $sql = "
                    SELECT *
                    FROM mammal_msw
                    WHERE TaxonLevel = 'GENUS'
                    AND Genus = '" . $data['Genus'] . "'
                ";
                $statement = $this->db->prepare($sql);
                $statement->execute();
                $parentData = $statement->fetch(); // Expecting only one row
                
                $attributes['parent']['id'] = $parentData['MSW_ID'];
                $attributes['parent']['scientific_name'] = $parentData['Genus'];
                $attributes['parent']['rank'] = strtolower($parentData['TaxonLevel']);
            }
        
            // Higher taxa
            $attributes['higherTaxa']['order'] = ucfirst(strtolower($data['Order']));
            $attributes['higherTaxa']['suborder'] = ucfirst(strtolower($data['Suborder']));
            $attributes['higherTaxa']['infraorder'] = ucfirst(strtolower($data['Infraorder']));
            $attributes['higherTaxa']['superfamily'] = $data['Superfamily'];
            $attributes['higherTaxa']['family'] = $data['Family'];
            $attributes['higherTaxa']['subfamily'] = $data['Subfamily'];
            $attributes['higherTaxa']['tribe'] = $data['Tribe'];
            $attributes['higherTaxa']['genus'] = $data['Genus'];
            $attributes['higherTaxa']['subgenus'] = $data['Subgenus'];

            // Taxon
            $attributes['rank'] = strtolower($data['TaxonLevel']);
            $attributes['scientific_name'] = $data['Genus'] . " " . $data['Species'];
            $attributes['author'] = $data['Author'];
            $attributes['author_date'] = $data['AuthorDate'];
                
            if ("YES" == $data['ValidName']) {
                $attributes['valid_name'] = TRUE;
            }
            else {
                $attributes['valid_name'] = FALSE;
            }

            if (!empty($data["CommonName"])) {
                $attributes['verncular_names']['en'][] = $data["CommonName"];
            }

            // Synonyms
            if (! empty($data['Synonyms'])) {
                $synonymsArrHTML = explode(";", $data['Synonyms']);
                $n = 0;
                foreach($synonymsArrHTML as $key => $synonymHTML) {
                    $synonymArr = explode("</i>", $synonymHTML);
                    $attributes['synonyms'][$n]['species_ephithet'] = trim(str_replace("<i>", "", $synonymArr[0]));
                    @$attributes['synonyms'][$n]['author'] = trim($synonymArr[1]); // Suppress errors resulting from inconsistent synonym data (missing </i>)
                    $n++;
                }
            }

            // Other data
            $attributes['sort_order'] = $data['SortOrder'];
            
        }
    //    $attributes = $data; // debug - see full data from db

        $res['jsonapi']['version'] = "1.0";
        $res['meta']['Source'] = "Mammal Species of the World";
        $res['data']['type'] = "taxon";
        $res['data']['id'] = $id;
        $res['data']['attributes'] = $attributes;

        return $res;
    }

}