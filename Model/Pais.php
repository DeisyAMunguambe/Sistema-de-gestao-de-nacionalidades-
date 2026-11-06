<?php

        Class Pais{
            private $codigoPais;
            private $nomePais;
            private $nomeContinente;   

            public function __construct($codigoPais,$nomePais,$nomeContinente){
                $this->codigoPais=$codigoPais;
                $this->nomePais=$nomePais;
                $this->nomeContinente = $nomeContinente;
            }

            public function getCodigoPais() {return $this-> codigoPais;}
            public function setCodigoPais($codigoPais){$this -> codigoPais=$codigoPais;}
            public function getNomePais() {return $this-> nomePais;}
            public function setNomePais($nomePais){$this -> nomePais=$nomePais;}
            public function getNomeContinente() {return $this-> nomeContinente;}
            public function setNomeContinente($nomeContinente){$this -> nomeContinente=$nomeContinente;}
        }
?>
