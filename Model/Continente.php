<?php

class Continente {
    private $codigoContinente;
    private $nomeContinente;

    public function __construct($codigoContinente, $nomeContinente){
        $this->codigoContinente = $codigoContinente;
        $this->nomeContinente = $nomeContinente;
    }

    public function getCodigoContinente(){ return $this->codigoContinente; }
    public function setCodigoContinente($codigoContinente){ $this->codigoContinente = $codigoContinente; }

    public function getNomeContinente(){ return $this->nomeContinente; }
    public function setNomeContinente($nomeContinente){ $this->nomeContinente = $nomeContinente; }
}

?>