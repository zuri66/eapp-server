<?php

namespace App\JDMPatternEngine;

use App\JDMPatternEngine\Term;

/**
 * Base de données de Termes locale au programme
 */
class Database
{
    private $predicates = [];

    public function addTerm(Term ...$preds)
    {
        $this->predicates = array_merge($this->predicates, $preds);
    }

    /**
     * Retourne les termes similaires à $pattern
     * Les atomes à un même endroit entre $pattern et les matchs doivent être
     * soit les même constantes, soit une variable dans $pattern
     * 
     * @param Term $pattern
     * @return boolean
     */
    public function matchingTerms(Term $pattern)
    {
        $x = $pattern->getAtom(0);
        $y = $pattern->getAtom(1);

        if ($x->isVariable() && $y->isVariable()) {
            $pred = $pattern->getPredicate();

            $f = function($a) use ($pred) {
                return $pred === $a->getPredicate();
            };
        }
        elseif ($x->isVariable()) {
            $pred = $pattern->getPredicate();
            $y    = $pattern->getAtom(1)->getValue();

            $f = function($a) use ($pred, $y) {
                return $pred === $a->getPredicate() && $y === $a->getAtom(1)->getValue();
            };
        }
        elseif ($y->isVariable()) {
            $pred = $pattern->getPredicate();
            $x    = $pattern->getAtom(0)->getValue();

            $f = function($a) use ($pred, $x) {
                return $pred === $a->getPredicate() && $x === $a->getAtom(0)->getValue();
            };
        }
        else {
            $pred = $pattern->getPredicate();
            $x    = $pattern->getAtom(0)->getValue();
            $y    = $pattern->getAtom(1)->getValue();

            $f = function($a) use ($pred, $x, $y) {
                return $pred === $a->getPredicate() && $x === $a->getAtom(0)->getValue() && $y === $a->getAtom(1)->getValue();
            };
        }
        $res = array_filter($this->predicates, $f);

        if (empty($res))
            return false;

        return array_values($res);
    }
}