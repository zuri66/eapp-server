<?php

namespace App\JDMPatternEngine;

/**
 * Règle de la forme pred1 && pred2 && ... -> predconcl
 * Les variables de même noms dans des termes différents doivent être une même instance d'atome
 */
class Rule
{
    private $hypothesis  = [];
    private $conclusions = [];
    private $atoms       = [];

    private function add(&$where, Term ...$terms)
    {
        foreach ($terms as $term) {
            foreach ($term->getAtoms() as $k => $atom) {
                $ares = array_search($atom, $this->atoms);

                //Nouvel atome
                if ($ares === false) {
                    $this->atoms[] = $atom;
                }
                //Atome similaire présent
                else {
                    $term->setAtom($k, $this->atoms[$ares]);
                }
            }
        }
        $where = array_merge($where, $terms);
    }

    public function addConclusion(Term ...$terms)
    {
        return $this->add($this->conclusions, ...$terms);
    }

    public function addHypothesis(Term ...$terms)
    {
        return $this->add($this->hypothesis, ...$terms);
    }

    public function getVariables()
    {
        return array_filter($this->atoms, function($a) {
            return $a->isVariable();
        });
    }

    public function getHypotheses()
    {
        return $this->hypothesis;
    }

    public function getHypothesis(int $i = 0)
    {
        return $this->hypothesis[$i];
    }

    public function getConclusions()
    {
        return $this->conclusions;
    }

    public function getConclusion(int $i = 0)
    {
        return $this->conclusions[$i];
    }

    public function getAllTerms()
    {
        return array_merge($this->getHypotheses(), $this->getConclusions());
    }

    private function cloneOne(&$what)
    {
        $tmp  = $what;
        $what = [];

        foreach ($tmp as $term) {
            $this->add($what, clone $term);
        }
    }

    public function __clone()
    {
        $this->atoms = [];
        $this->cloneOne($this->hypothesis);
        $this->cloneOne($this->conclusions);
    }

    public function bind($vars)
    {
        foreach ($this->atoms as $atom) {
            //Normalement toujours le cas
//            if ( $atom->isVariable() )
            {
                $name = $atom->getName();

                if (isset($vars[$name])) {
                    $atom->setValue($vars[$name]);
                }
            }
        }
    }

    public function __toString()
    {
        $ret = '';
        foreach ($this->getHypotheses() as $hterm) {
            $ret .= (string) $hterm . ' ';
        }
        $ret .= '->';

        foreach ($this->getConclusions() as $cterm) {
            $ret .= ' ' . (string) $cterm;
        }
        return $ret;
    }
}