<?php

namespace App\JDMPatternEngine;

use App\JDMPatternEngine\Atom;

/**
 * Terme JSM de la forme predicate(atom_x,atom_y, ...)
 */
class Term
{
    private $predicate;
    private $atoms = [];
    private $weight;

    public function __construct($p, int $w = 0, Atom ...$atoms)
    {
        $this->predicate = $p;
        $this->addAtom(...$atoms);
        $this->setWeight($w);
    }

    public function __clone()
    {
        foreach ($this->atoms as &$atom) {
            $atom = clone $atom;
        }
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function setWeight(int $w)
    {
        $this->weight = $w;
    }

    public function getPredicate()
    {
        return $this->predicate;
    }

    public function setPredicate($p)
    {
        $this->predicate = $p;
    }

    public function addAtom(Atom ... $atoms)
    {
        $this->atoms = array_merge($this->atoms, $atoms);
    }

    public function getAtoms()
    {
        return $this->atoms;
    }

    public function getAtom(int $i = 0)
    {
        return $this->atoms[$i];
    }

    public function setAtom(int $i = 0, Atom $atom)
    {
        return $this->atoms[$i] = $atom;
    }

    /**
     * Cherche les position d'un atome $a dans le terme
     * @param Atom $a
     * @param bool $strict
     * @return type
     */
    public function getAtomPos(Atom $a, bool $strict = true)
    {
        $ret = [];

        foreach ($this->atoms as $k => $atom) {
            if (($strict && $atom === $a) || (!$strict && $atom == $a)) {
                $ret[] = $k;
            }
        }
        return $ret;
    }

    public function variableMatch(Term $b)
    {
        return self::variableMatch_($this, $b);
    }

    /**
     * Vérifie si $a peut matcher $b en terme de variables
     * @param \App\JDMPatternEngine\Term $a
     * @param \App\JDMPatternEngine\Term $b
     */
    static public function variableMatch_(Term $a, Term $b)
    {
        $aas = $a->getAtoms();
        $bas = $b->getAtoms();

        if (($c = count($aas)) !== count($bas))
            return false;

        for ($i = 0; $i < $c; $i++) {
            $aa = $aas[$i];
            $ba = $bas[$i];

            if ($aa->isVariable())
                ;
            elseif ($ba->isConstant()) {
                if ($ba->getValue() !== $aa->getValue())
                    return false;
            }
            else
                return false;
        }
        return true;
    }

    /**
     * Comparaison sans le poids (weight)
     * @param \App\JDMPatternEngine\Term $a
     * @param \App\JDMPatternEngine\Term $b
     * @return type
     */
    static public function sameValue(Term $a, Term $b)
    {
        $c = count($a->getAtoms());

        if ($c != count($b->getAtoms()))
            return false;

        for ($i = 0; $i < $c; $i++) {

            if ($a->getAtom($i)->getValue() !== $b->getAtom($i)->getValue())
                return false;
        }
        return true;
    }

    public function getVariables()
    {
        $ret = [];
        foreach ($this->atoms as $i => $atom) {
            if ($atom->isVariable())
                $ret[$i] = $atom;
        }
        return $ret;
    }

    public function getVariable($varName)
    {
        foreach ($this->atoms as $i => $atom) {
            if ($atom->isVariable() && $atom->getName() == $varName)
                return $atom;
        }
        return null;
    }

    public function hasVariable($varName)
    {
        foreach ($this->atoms as $i => $atom) {
            if ($atom->isVariable() && $atom->getName() == $varName)
                return true;
        }
        return false;
    }

    public function getConstants()
    {
        $ret = [];
        foreach ($this->atoms as $i => $atom) {
            if ($atom->isConstant())
                $ret[$i] = $atom;
        }
        return $ret;
    }

    public function __toString()
    {
        $w   = $this->weight != 0 ? "[$this->weight]" : null;
        $ret = "$this->predicate$w(";
        $tmp = [];

        foreach ($this->atoms as $atom) {
            $tmp[] = (string) $atom;
        }
        $ret .= implode(',', $tmp);
        $ret .= ")";
        return $ret;
    }
}