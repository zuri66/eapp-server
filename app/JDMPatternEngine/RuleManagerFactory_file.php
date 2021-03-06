<?php

namespace App\JDMPatternEngine;

use App\JDMPatternEngine\RuleManager;
use App\JDMPatternEngine\Atom;
use App\RelationType;
use \Exception;

/**
 * Charge les règles depuis des données textes
 */
class RuleManagerFactory_file
{
    private $data;
    private $relations;

    /**
     * 
     * @param string|array $data Les données à charger
     * @param RelationType $relations
     */
    public function __construct($data, RelationType $relations = null)
    {
        if (is_string($data))
            $data = explode("\n", $data);

        $this->data      = $data;
        $this->relations = $relations;
    }

    public function new_()
    {
        $ruleManager = new RuleManager();

        foreach ($this->data as $line) {
//            $id   = 0; //Id pour noms générés
            $line = trim($line);

            if (empty($line) || $line[0] == '#')
                continue;

            $pattern = "/([\w_]+)\((.+?)\)/";

            preg_match_all($pattern, $line, $matches);
            $res   = array_map(null, $matches[1], $matches[2]);
            $terms = [];

//            if (count($res) !== 3) {
//                throw new Exception( "Seule les règles de la forme A B -> C sont acceptées" );
//            }

            foreach ($res as $cur) {
                $p         = $cur[0];
                $tmpvars   = preg_split('/[\W]/', $cur[1]);
                $termAtoms = [];

                foreach ($tmpvars as $cvar) {
                    $atom        = new Atom($cvar);
                    $termAtoms[] = $atom;

                    // On bind la relation avec son id
                    if ($this->relations) {
                        $pp = $this->relations->getRelation($p);

                        if ($pp === null)
                            throw new Exception("Relation $p n'existe pas !");

                        $p = $pp->_id;
                    }
                }
                $term    = new Term($p);
                $term->addAtom(...$termAtoms);
                $terms[] = $term;
            }
            $conclusion = array_pop($terms);
            $rule       = new Rule( );
            $rule->addConclusion($conclusion);
            $rule->addHypothesis(...$terms);
            $ruleManager->addRule($rule);
        }
        return $ruleManager;
    }
}