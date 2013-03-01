<script type="text/javascript">

    $(document).ready(function(){
        $("#_crear").hide();
        $("#_crear").click(function(){
            var ruta;
            $.post("index.php/repositorio/createrepositorio",{
                "_rep_n":$("#_repository_name").val()
            },function(data){
                //alert(data.message);
                $("#_rep_main_content").html(data.message);
                $("#_rep_main_content2").html("Copie un Framework por defecto para comenzar a trabajar <br/><input type='button' value='Copiar Framework' id='_copy' />FrameWorks Disponibles:<select id='_combo_source'><option>codeigniter</option><option>kumbia</option></select>");
                /*
                 *Recuperamos el click
                 **/
                 ruta=data.newrep;
                 Ext.MessageBox.alert("Success","Directorio base creado con éxito");
                 //alert(ruta);
                $("#_copy").click(function(){

                    //hacemos la copia de el poderoso framework
                    var mask = new Ext.LoadMask(Ext.get('_r_container_module'), {msg:'Se esta haciendo una copia del Framework, su repositorio nuevo, el proceso puede tardar unos segundos'});
                    mask.show();
                    $.post("/index.php/repositorio/createcopy/",{"source":$("#_combo_source").val(),
                        "dest":ruta},
                    function(data){
                        mask.hide();
                        Ext.MessageBox.alert("Success", "Felicidades el proceso culmino sin problemas, usted ya tiene los archivos en su repositorio")
                        //alert(data.result+"");
                        if(data.result){
                            $("#_msn").html("Se ha copiado el Framework Correctamente!!! "+data.result+"");
                            $("#_copy").hide();
                        }
                    },"json"
                );
                });
            },"json")
        });
        $("#_repository_name").keyup(function(){
            //llamaos asincronamente
            var cad=$("#_repository_name").val();
            if(cad.length>4){
                $.post("/index.php/repositorio/checkname",{"cad":cad},
                function(data){
                    if(data.r){
                        $("#_crear").show();
                    }else{
                        $("#_crear").hide();
                    }
                    $("#ruta").html(data.result);
                },"json");
            }else{
                $("#ruta").html(cad+" debe contener minimamente 5 caracteres");
            }
        });
        $("#_copy").click(function(){
            //alert($("#_combo_source").val());
            //hacemos la copia de el poderoso framework
            var mask = new Ext.LoadMask(Ext.get('_r_container_module'), {msg:'Se esta haciendo una copia del Framework, su repositorio nuevo, el proceso puede tardar unos segundos'});
            mask.show();
            $.post("/index.php/repositorio/createcopy/",{"source":$("#_combo_source").val(),
                "dest":$("#_source_dest").val()},
            function(data){
                mask.hide();
                Ext.MessageBox.alert("Success", "Felicidades el prioceso culmino sin problemas, usted ya tiene los archivos en su repositorio")
                //alert(data.result+"");
                if(data.result){
                    $("#_msn").html("Se ha copiado el Framework Correctamente!!! "+data.result+"");
                    $("#_copy").hide();
                }
            },"json"
        );
        });
    });

</script>
<?php
            if(!$this->roles_model->checkPermit(82)){
                redirect("/error/redirect/82");
            }
        ?>
<div class="pp-bigcontainer" id="_r_container_module">
    <span class="top"><span></span></span>
    <table>
        <tr>
            <td valign="top" style="vertical-align:top; width: 100%;" width="850px">
                <div id="panels">
                    <div class="mainheadpad">
                        <h1 class="maintitle">Resumen</h1>
                    </div>
                    <div class="maincontent">
                        <?php
                        //$nombrePro="";
                        foreach($proyecto->result() as $pro) {
                            echo"Proyecto <b>".$pro->nombre."</b><br/>";
                            //$nombrePro=$pro->nombre;
                            if($pro->framework!="") {
                                echo"Basado en el FrameWork <b>".$pro->framework."</b>";
                            }
                        }

                        ?>
                    </div>
                    <div class="myline" style="margin-top:10px"></div>
                </div>

                <div id="panels">
                    <div class="mainheadpad">
                        <h1 class="maintitle">Repositorio</h1>
                    </div>
                    <div class="maincontent" id="_rep_main_content">
                        <?php
                        if($repositorio->num_rows()==0) {
                            echo"Aún no ha creado un repositorio para el Proyecto <b>".$pro->nombre."</b> <br/> El repositorio
                                 es un sitio centralizado donde se almacena y mantiene información digital, de los archivos del proyecto que a lo largo se irán manejando conforme el desarrollo del proyecto.
                                ";
                            ?>
                        <!--
                            Formulario de creación
                        -->
                        <div id="form_create">
                            <h1><b>Crear Repositorio</b></h1>
                            El nombre Especificado tiene que ser único, cuando cumpla esas condiciones se procedera a la creación<br/><br/>
                            <!--<form action="/index.php/repositorio/version/" method="post">-->
                            <b>Nombre :</b> <input id="_repository_name" name="_rep_n" type="text" size="25" /> <input type="submit" value="Crear" id="_crear" />
                            <!--
                            Errores
                            !-->

                            <!--</form>-->
                        </div>
                        <br/>
                        <div id="ruta">

                        </div>
                        <?php
                        }else {
                            ?>
                        Tu repositorio Actual es <?php foreach($repositorio->result() as $rep) echo $rep->nombre."  en la dirección <a href='".$rep->ruta."'>$rep->ruta</a> ";

                            ?>
                        Si deseas hacer una reedición puedes entrar a está dirección <a href=""> Editar Repositorio </a><br/> Esta opción es poco recomendable
                        por el riesgo que conlleva cuando el proyecto tiene avances importantes.<br/>
                        
                        <?php
                        }
                        ?>
                    </div>
                    <div class="myline" style="margin-top:10px"></div>
                </div>
                <div id="panels">
                    <div class="mainheadpad">
                        <h1 class="maintitle">Copy Default Framework</h1>
                    </div>
                    <div class="maincontent" id="_rep_main_content2">
                        <?php
                        if($repositorio->num_rows()>0) {
                            ?>
                        Copie un Framework por defecto para comenzar a trabajar
                        Usted habia Escogido <?php foreach($proyecto->result() as $pro) {echo "<b>".$pro->framework."</b>";} ?>
                        <p>
                            <b>Copiar Repositorio a: </b>
                                <?php echo $rep->rutaabsoluta ?><br/>
                            <input type="button" value="Copiar Framework" id="_copy" />
                            FrameWorks Disponibles: 
                            <select id="_combo_source">
                                <option>codeigniter</option>
                                <option>kumbia</option>
                            </select>
                            <input type="hidden" value="<?php echo $rep->rutaabsoluta ?>" id="_source_dest" />

                        </p>
                        <div id="_msn">

                        </div>
                        <?php }else { ?>

                        <?php } ?>
                    </div>
                    <div class="myline" style="margin-top:10px"></div>
                </div>
                <div class="clearfix"></div>
            </td>
        </tr>
    </table>
    <span class="bottom"><span></span></span>
</div>