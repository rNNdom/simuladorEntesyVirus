<?php
include_once ('class.Ente.php');

class Ambiente{
    private $ancho;
    private $alto;
    private $entes;
    private $max;
    private $cantidad;
    private $sintomaticos;
    private $infectados;
    private $contadorSintomaticos=0;
    private $contadorMarcados=0;
    private $contadorMortalidad=0;
    private $mortalidad;
  


    function __construct($ancho_x,$alto_y,$density,$sintomaticos,$mortalidad){
        $this->ancho=$ancho_x;
        $this->alto=$alto_y;
        $this->entes=[];
        $this->infectados=[];
        $this->max= (($this->ancho * $this->alto)/100)/10;
        $this->cantidad=$this->max * $density/100;
        $this->sintomaticos = $sintomaticos/100;
        $this->mortalidad = $mortalidad/100;
        $this->primerosInfectados=[];
        $this->infectadosSintomaticos=[];
        $this->infectadosMuertos=[];
    }
    function generaEnte($state,$id,$ciclo){
        $delx = rand(0,10) -5;
        $dely = rand(0,10) -5;
        $cel = new Ente(rand(0,$this->ancho),rand(0,$this->alto),$delx,$dely,$state,$id,$ciclo);
        array_push($this->entes,$cel);
    }

    function generaEntesAlAzar($infected, $ciclo){
        if($infected > $this->cantidad){
            echo "<p>Simulacion infactible para parametros seleccionados, cantidad de infectados es mayor a la cantidad total de celulas</p>";
        }
        else{
            for($i=0;$i<$infected;$i++){
                $this->generaEnte(1,$i,$ciclo);
                array_push($this->primerosInfectados,$i);
                
            }
            for($i=$infected;$i<($this->cantidad);$i++){
                $this->generaEnte(0,$i,$ciclo);
            }

        }

    }
    function inmunizedCount(){
        $count=0;
        foreach($this->entes as $ente){
            if($ente->state==2){
                $count++;
            }
        }
        return $count;
    }
   
    function infectedCount(){
        foreach($this->entes as $ente){
            if($ente->state==1 && in_array($ente,$this->infectados)==false ){
                array_push($this->infectados,$ente); 
            }
        }
        return count($this->infectados);
    }
    function sanitizedCount(){
        $sterilized = 0;
        foreach($this->entes as $ente){
            if($ente->state == 0){
                $sterilized++;
            }
        }
        return $sterilized;
    }
    function getcontadorMarcados(){
        return count($this->infectadosMuertos);
    }
    function sintomaticosCount(){
        return count($this->infectadosSintomaticos);
    }
    function vistaSVG(){
        $ret= '<svg width="'.$this->ancho.'" height="'.$this->alto.'">'."\n";
        foreach ($this->entes as $ente) {
            $ret .= $ente->svg()."\n";
        }
        $ret .= '</svg>';
        return $ret;
    }

    function mueve(){
     
        if($this->infectedCount() === $this->cantidad || $this->infectedCount() == $this->inmunizedCount()
         || $this->inmunizedCount() + count($this->infectadosMuertos) == $this->infectedCount()){
            return true;
        }

        foreach ($this->entes as $ente) {
                $ente->mueve(0,0,$this->ancho,$this->alto);
                //para cada ente, si está cerca de otro ($this->entes[$i]=todos los entes que hay) se aplica la función contagio
                for ($i=0; $i < count($this->entes) ; $i++) { 
                   $ente->contagio($this->entes[$i]);
                }
                //para cada ente, si cumple la condición de que YA ESTÉ CONTAGIADO, el contadorSintomaticos(número de cantidad de infectados sintomáticos)
                //es menor a la cantidad de infectados sintomáticos esperada, la id del $ente no corresponde a los primeros infectados ingresados por el usuario
                //y además es asíntomatico, entonces se vuelve sintomático.
                if($this->contadorSintomaticos<round(count($this->infectados)*$this->sintomaticos) && !in_array($ente->id,$this->primerosInfectados)
                 && $ente->state==1 && $ente->sintomas==false){
                       $ente->sintomas=true;
                       array_push($this->infectadosSintomaticos,$ente);
                       $this->contadorSintomaticos++;
                }
                //Obtener tasa de mortalidad en relación a la cantidad total de infectados sintomáticos
                if($this->contadorMarcados<round(count($this->infectadosSintomaticos)*$this->mortalidad) && $ente->sintomas==true && $ente->state==1
                 && $ente->marked==false){
                    $ente->marked=true;
                    $this->contadorMarcados++;
                     
                }
                
            $ente->ciclo();
            if($ente->state==3 && !in_array($ente,$this->infectadosMuertos)){
                array_push($this->infectadosMuertos,$ente);
            }
            
        }
        
    }
}
?>