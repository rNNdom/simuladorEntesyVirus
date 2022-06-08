<?php 
class Ente{
    private $x;
    private $y;
    private $deltax;
    private $deltay;
    public $state;
    private $color;
    private $radio;
    public $id;
    private $ciclo=0;
    private $cicloContador=0;
    



    function __construct($pos_x, $pos_y, $velocidad_x, $velocidad_y,$state,$id,$ciclo){
        $this->x=$pos_x;
        $this->y=$pos_y;
        $this->deltax=$velocidad_x;
        $this->deltay=$velocidad_y;
        $this->radio=10;
        $this->color = "#33AA44";
        $this->state = $state;
        $this->id = $id;
        $this->ciclo = $ciclo;
        $this->sintomas=false;
        $this->marked=false;
        
        
    }
    function ciclo(){
        //Por cada ente, si su estado es 1, se aumenta un valor al contador
        if($this->state==1){
            $this->cicloContador++;
        }
        //Si el ente está marcado como paciente terminal, y su contador llegó a la mitad del ciclo de inmunización
        //Su estado cambia a 3, lo que significa que la célula ha muerto
        if($this->cicloContador == ($this->ciclo)/2 && $this->marked==true){
            $this->state=3;
            $this->cicloContador=0;
        }
        //Si el contador del ente es igual al ciclo de inmunidad ingresado en la página, este se vuelve inmune.
        if($this->cicloContador==$this->ciclo && $this->state != 3){
            $this->state=2;
            $this->cicloContador=0;
        }
    }
    //get distance between objects 
    function getDistanceTo($other){
        $x1 = $this->x;
        $y1 = $this->y;
        $x2 = $other->x;
        $y2 = $other->y;
        $distance = sqrt(pow($x2-$x1,2)+pow($y2-$y1,2));
        return $distance;
    }


    function contagio($other){
        if($this->id == $other->id) return;
        $distance = $this->getDistanceTo($other);
        //Si el contagiado se inmuniza, se vuelve asintomático
        if($this->state==2){
            $this->sintomas=false;
        }
        //Si el contagiado está muerto, se queda inmóvil (sintomático pero sin contagiar al resto)
        if($this->state==3){
            $this->sintomas=true;
        }
         if($distance<($this->radio)*2 && $other->state==1 && $this->state != 2 && $this->state != 1 && $this->state!=3){
            $this->state = 1;
        }
    }
   
    function svg(){
        $ret = '<circle stroke ="black" stroke-width="1.5" cx="&1" cy="&2" r="&3" fill="&4"/>';
        $ret = str_replace('&1', $this->x,$ret);
        $ret = str_replace('&2', $this->y,$ret);
        $ret = str_replace('&3', $this->radio,$ret);
        if($this->state==1){
            $this->color = "#AA3344";
        }
        if($this->state==2){
            $this->color = "#870083";
        }
        //Si el ente es sintomático y su estado es contagiado(1), su color cambia a marrón
        if($this->sintomas==true && $this->state==1){
            $this->color="#490000";
        }
        //Si el ente es sintomático, está muerta y marcada, su color cambia a negro.
        if($this->sintomas==true && $this->state==3 && $this->marked==true){
            $this->color="#000000";
        }
        $ret = str_replace('&4', $this->color,$ret);
        return $ret;

    }
    function mueve($minx,$miny,$maxx,$maxy){
        //Si es sintomático, no se mueve
        if($this->sintomas==true) return;
        $nuevo_x = $this->x + $this->deltax;
        $nuevo_y = $this->y + $this->deltay;

        if($nuevo_x>$minx && $nuevo_x < $maxx){
            $this->x = $nuevo_x;
        }
        else{
            $this->deltax *= -1;
            $this->x += $this->deltax;
        }
        if($nuevo_y>$miny && $nuevo_y < $maxy){
            $this->y = $nuevo_y;
        }
        else{
            $this->deltay *= -1;
            $this->y += $this->deltay;
        }
    }
}


?>