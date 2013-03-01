<html>
    <head>
        <title>
            mails
        </title>
        <script type="text/javascript">
            $("#_aceptar").click(function(){
                Ext.MessageBox.confirm("Alerta","¿Desea Aceptar el proyecto?",function(data){
                    if(data=="yes"){
                        $("#result").html("cargando...");
                        $.post("/index.php/proyecto/checkcode",{
                            "id":$("#_id").val(),
                            "idPro":$("#_idPro").val()
                        },
                        function(data){
                            $("#__buttons__").hide("slow");
                            $("#result").html("---------------------<br/>"+data.result);

                        },"json");
                    }
                });

            });
            $("#_rechazar").click(function(){
                Ext.MessageBox.confirm("Alerta","¿Desea Aceptar el proyecto?",function(data){
                    if(data=="yes"){
                        $("#__buttons__").hide("slow");
                        $.post("/index.php/proyecto/rechazo",{"id":$("#_id").val()},function(data){
                            if(data.result){
                                Ext.MEssageBox.aler("Alerta",data.message);
                            }
                        },"json");
                    }
                });
                
            });
        </script>
    </head>
    <body>
        <?php
        echo $message;
        foreach($invitacion as $invite) {
            echo "<h1><b>Titulo</b></h1>";
            echo $invite->title."<br/>";
            echo "<h1><b>Mensaje</b></h1>";
            echo $invite->message;
            if($invite->acepted=="nocontest") {
                echo "<input type='hidden' value='".$invite->id."' id='_id'/>";
                echo "<input type='hidden' value='".$invite->idPro."' id='_idPro'/>";
                echo "<div id='__buttons__'><br/><input type='button' id='_aceptar' value='Aceptar'/>";
                echo "<input type='button' id='_rechazar' value='Rechazar'/></div>";

            }
        }
        ?>

        <div id="result"></div>
    </body>
</html>