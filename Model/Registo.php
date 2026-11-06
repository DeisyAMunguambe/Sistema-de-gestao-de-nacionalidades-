<?php
        Class RegistoNacionalidade{
            private $codigoRegisto;
            private $idContinente;
            private $idPais;
            private $idNacionalidade;
            private $idPessoa;
            private $dataRegisto;
            private $motivo;
            private $nomeContinente;
            private $nomePais;
            private $nomeNacionalidade;
            private $nomePessoa;

            public function __construct($codigoRegisto,$idContinente,$idPais,$idNacionalidade,$idPessoa,
            $dataRegisto,$motivo,$nomeContinente="",$nomePais="",$nomeNacionalidade="",$nomePessoa=""){
                $this->codigoRegisto=$codigoRegisto;
                $this->idContinente=$idContinente;
                $this->idPais=$idPais;
                $this->idNacionalidade=$idNacionalidade;
                $this->idPessoa=$idPessoa;
                $this->dataRegisto=$dataRegisto;
                $this->motivo=$motivo;
                $this->nomeContinente=$nomeContinente;
                $this->nomePais=$nomePais;
                $this->nomeNacionalidade=$nomeNacionalidade;
                $this->nomePessoa=$nomePessoa;
            }

            public function getCodigoRegisto() {return $this->codigoRegisto;}
            public function setCodigoRegisto($codigoRegisto){$this->codigoRegisto=$codigoRegisto;}
            public function getIdContinente() {return $this->idContinente;}
            public function setIdContinente($idContinente){$this->idContinente=$idContinente;}
            public function getIdPais() {return $this->idPais;}
            public function setIdPais($idPais){$this->idPais=$idPais;}
            public function getIdNacionalidade() {return $this->idNacionalidade;}
            public function setIdNacionalidade($idNacionalidade){$this->idNacionalidade=$idNacionalidade;}
            public function getIdPessoa() {return $this->idPessoa;}
            public function setIdPessoa($idPessoa){$this->idPessoa=$idPessoa;}
            public function getDataRegisto() {return $this->dataRegisto;}
            public function setDataRegisto($dataRegisto){$this->dataRegisto=$dataRegisto;}
            public function getMotivo() {return $this->motivo;}
            public function setMotivo($motivo){$this->motivo=$motivo;}
            public function getNomeContinente() {return $this->nomeContinente;}
            public function getNomePais() {return $this->nomePais;}
            public function getNomeNacionalidade() {return $this->nomeNacionalidade;}
            public function getNomePessoa() {return $this->nomePessoa;}
        }
?>