<?php

        Class Nacionalidade{
            private $codigoNacionalidade;
            private $nomeNacionalidade;
            private $nomePais;
            private $nomeContinente;

            public function __construct($codigoNacionalidade,$nomeNacionalidade,$nomePais,$nomeContinente){
                $this->codigoNacionalidade=$codigoNacionalidade;
                $this->nomeNacionalidade=$nomeNacionalidade;
                $this->nomePais=$nomePais;
                $this->nomeContinente=$nomeContinente;
            }

            public function getCodigoNacionalidade() {return $this->codigoNacionalidade;}
            public function setCodigoNacionalidade($codigoNacionalidade){$this->codigoNacionalidade=$codigoNacionalidade;}
            public function getNomeNacionalidade() {return $this->nomeNacionalidade;}
            public function setNomeNacionalidade($nomeNacionalidade){$this->nomeNacionalidade=$nomeNacionalidade;}
            public function getNomePais() {return $this->nomePais;}
            public function setNomePais($nomePais){$this->nomePais=$nomePais;}
            public function getNomeContinente() {return $this->nomeContinente;}
            public function setNomeContinente($nomeContinente){$this->nomeContinente=$nomeContinente;}
        }
?>