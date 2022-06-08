<html>
    <head>
        <title>
            ICC343 Clases 02
        </title>
    </head>
    <body>
        <?php
         include_once "class.Ambiente.php";
         $infected = $_GET['infected'];
         $density =$_GET['density'];
         $cicle = $_GET['cicle'];
         $sintomas = $_GET['sintomas'];
         $mortalidad = $_GET['mortalidad'];

         if ($density > 100){
             echo "Density must be less than 100";
         }else{
            $amb = new Ambiente(1000,500,$density,$sintomas,$mortalidad);
            $amb -> generaEntesAlAzar($infected,$cicle);
            for($k=0;$k<500;$k++){
                echo "\n";
                echo '<div id="amb_'.$k.'" style="display:none">';
                echo $amb->vistaSVG();
                echo "\n</div>";
                echo "\n";
                echo '<div id="amb2_'.$k.'" style="display:none">'; 
                echo '<p>Sanos: '.$amb->sanitizedCount().'</p>';
                echo '<p>Infectados historicos: '.$amb->infectedCount().'</p>';
                echo '<p>Inmunizados: '.$amb->inmunizedCount().'</p>';
                echo '<p>Sintomaticos: '.$amb->sintomaticosCount().'</p>';
                echo '<p>Muertos: '.$amb->getcontadorMarcados().'</p>';
                echo "\n</div>";
                if($amb->mueve()){
                    break;
                }
            }
                echo '<p id="lastIteration" style="display:none">La simulacion termina en la generacion '.$k.'</p>';
            }  
        
        ?>
        <script>
            var actual =0;
            function muestraSiguiente(){
                var nuevo =(actual+1);
                if(document.getElementById('amb_'+nuevo)==null){
                    document.getElementById('amb_'+actual).style.display = '';
                    document.getElementById('lastIteration').style.display = '';
                }else{
                document.getElementById('amb_'+actual).style.display='none';
                document.getElementById('amb_'+nuevo).style.display='';
                document.getElementById('amb2_'+actual).style.display='none';
                document.getElementById('amb2_'+nuevo).style.display='';
                actual=nuevo;
                }
                
                setTimeout(muestraSiguiente,30);
            }
            muestraSiguiente();
            
        </script>
    </body>
</html>