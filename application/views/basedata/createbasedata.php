<div class="pp-bigcontainer">
    <span class="top"><span></span></span>
    <div class="contenido">

        <script type="text/javascript">
            var _principal;
            Ext.namespace("nexus.db.createbase");
            nexus.db.createbase={

                init:function(){
                    Ext.QuickTips.init();
                    _principal=this;
                    this.crear=new Ext.FormPanel({
                        id:"_form_db_create",
                        width:300,
                        url:"/index.php/basedata/createdb",
                        defaults:{xtype:'textfield',width:200},
                        border:false,
                        items:[
                            {
                                fieldLabel:"Nombre de la base de Datos",
                                name:"namedb",
                                id:"_namedb",
                                width:150,
                                allowBlank:false,
                                maxLength:50,
                                minLength:5
                            }
                        ],
                        buttons:[{id:"_btncrear",text:"Guardar"}],
                        renderTo:"_db_crear"
                    });
                    $("#_btncrear").click(function(event){
                        if(!_principal.crear.getForm().isValid()){
                            Ext.Msg.alert('Success',"Error en la validación");
                        }else{
                            _principal.crear.getForm().submit({
                                methos:"post",
                                success:function(form,action){
                                    Ext.Msg.alert('Success',action.result.msg);
                                    Ext.DomHelper.append("databaseList",{
                                        tag:"option",html:action.result.data
                                    },true)
                                },
                                failure:function(form,action){
                                    switch (action.failureType) {
                                        case Ext.form.Action.CLIENT_INVALID:
                                            Ext.Msg.alert('Failure', 'No se han insertado su petición porque hay campos invalidos');
                                            break;
                                        case Ext.form.Action.CONNECT_FAILURE:
                                            Ext.Msg.alert('Failure', 'Ajax communication failed');
                                            break;
                                        case Ext.form.Action.SERVER_INVALID:
                                            Ext.Msg.alert('Failure', action.result.msg);
                                            break;
                                        default:
                                            Ext.Msg.alert('Failure',action.result.msg);
                                    }
                                }
                            });
                        }

                    });
                    this.asiganar=new Ext.Button({
                        text  :"Asigar usuario a la base de datos",
                        id:"_btnasignar",
                        renderTo:"_db_asignar"
                    });
                    $("#_btnasignar").click(function(){
                        $.post("/index.php/basedata/asignarDb",{
                            "databaseList":$("#databaseList").val(),
                            "userbaseList":$("#userbaseList").val()
                        },function(data){
                            if(data.result){

                                var data=data.info;
                                var htmlString="";

                                for(var i=0;i<data.length;i++){
                                    htmlString+="<div id='box_"+i+"' style='height:auto;'>";
                                    htmlString+=" <div id='box'>";
                                    htmlString+="<div id='header_box'></div>";
                                    htmlString+="<div id='body_box'>";
                                    htmlString+="<table border='0'>";
                                    htmlString+="<tr>";
                                    htmlString+="<td><b>Base de datos</b></td><td><b>Usuario</b></td><td><b>Opciones</b></td<td><b>Go to</b></td></tr>";
                                    htmlString+="<input type='hidden' id='idBase_"+i+"' value='"+data[i].idBase+"' />";
                                    htmlString+="<input type='hidden' id='idUs_"+i+"' value='"+data[i].idUser+"' />";
                                    htmlString+="<input type='hidden' id='dbName_"+i+"' value='"+data[i].nombre+"' />";
                                    htmlString+="<input type='hidden' id='dbUser_"+i+"' value='"+data[i].nick+"' />";
                                    htmlString+="<tr><td>"+data[i].nombre+" Creado el "+data[i].fecha+"</td><td>"+data[i].nick+" <br/> creado el "+data[i].fecha+"</td><td><input type='submit' id='quitar_"+i+"' value='Quitar usuario' size='5' /><br/><br/><input type='submit' value='Eliminar Base de Datos' id='delete_"+i+"'/></td><td>Phpmyadmin</td>";
                                    htmlString+="</tr>";
                                    htmlString+="</table>";
                                    htmlString+="</div>";
                                    htmlString+="<div id='bottom_box'></div>";
                                    htmlString+="<div class='clearfix'></div>";
                                    htmlString+="</div>";
                                    htmlString+="</div>";
                                }
                                Ext.DomHelper.overwrite("_bd_asignation",{
                                    html:htmlString
                                },true);
                                for(i=0;i<data.length;i++){
                                    $("#quitar_"+i).click(function(){
                                        var dbName=$("#dbName_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();

                                        var dbUser=$("#dbUser_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                        //alert("hola "+dbUser);
                                        var answer=confirm("¿esta seguro? Desea quitar al usuario "+dbUser+" de la base de datos ");
                                        if(answer){
                                            var idBase=$("#idBase_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                            var idUser=$("#idUs_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                            var box=$("#box_"+$(this).attr("id").substr(7, $(this).attr("id").length));
                                            $.post("/index.php/BaseData/revokeUser", { "idBase":idBase,"idUser":idUser,"dbName":dbName,"dbUser":dbUser},
                                            function(data){
                                                box.hide("slow");
                                            }, "json");

                                        }
                                    })
                                    $("#delete_"+i).click(function(){
                                        var dbName=$("#dbName_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                        var dbUser=$("#dbUser_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                        var answer=confirm("¿esta seguro? Desea borrar la base de datos "+dbName);
                                        if(answer){
                                            var idBase=$("#idBase_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                            var idUser=$("#idUs_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                            var box=$("#box_"+$(this).attr("id").substr(7, $(this).attr("id").length));
                                            var html="";
                                            $.post("/index.php/baseData/eliminarDb", { "idBase":idBase,"idUser":idUser,"dbName":dbName,"dbUser":dbUser},
                                            function(data){
                                                //alert(data.data);
                                                if(data.data!=""){
                                                    for(var i=0;i<data.data.length;i++){
                                                        //alert("f"+data.data.length);
                                                        html+="<option>"+data.data[i]+"</option>";
                                                    }
                                                }
                                                //alert(html);
                                                $("#databaseList").html(html);
                                                box.hide("slow");
                                            }, "json");
                                        }
                                    })
                                }
                            }
                        }, "json")
                    });

                    for(var i=0;i<$("#length").val();i++){
                        $("#quitar_"+i).click(function(){
                            var dbName=$("#dbName_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();

                            var dbUser=$("#dbUser_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                            //alert("hola "+dbUser);
                            var answer=confirm("¿esta seguro? Desea quitar al usuario "+dbUser+" de la base de datos ");
                            if(answer){
                                var idBase=$("#idBase_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                var idUser=$("#idUs_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                var box=$("#box_"+$(this).attr("id").substr(7, $(this).attr("id").length));
                                $.post("/index.php/BaseData/revokeUser", { "idBase":idBase,"idUser":idUser,"dbName":dbName,"dbUser":dbUser},
                                function(data){
                                    box.hide("slow");
                                }, "json");

                            }
                        })
                    }
                    for(var i=0;i<$("#length").val();i++){
                        $("#delete_"+i).click(function(){
                            alert("fa");
                            var dbName=$("#dbName_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                            var dbUser=$("#dbUser_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                            var answer=confirm("¿esta seguro? Desea borrar la base de datos "+dbName);
                            if(answer){
                                var idBase=$("#idBase_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                var idUser=$("#idUs_"+$(this).attr("id").substr(7, $(this).attr("id").length)).val();
                                var box=$("#box_"+$(this).attr("id").substr(7, $(this).attr("id").length));
                                var html="";
                                $.post("/index.php/baseData/eliminarDb", { "idBase":idBase,"idUser":idUser,"dbName":dbName,"dbUser":dbUser},
                                function(data){
                                    //alert(data.data);
                                    if(data.data!=""){
                                        for(var i=0;i<data.data.length;i++){
                                            //alert("f"+data.data.length);
                                            html+="<option>"+data.data[i]+"</option>";
                                        }
                                    }
                                    //alert(html);
                                    $("#databaseList").html(html);
                                    box.hide("slow");
                                }, "json");
                            }
                        })
                    }
                }
            }
            Ext.onReady(nexus.db.createbase.init,nexus.db.createbase);
        </script>
<?php
if(!$this->roles_model->checkPermit(76)){
                redirect("/error/redirect/76");
            }
?>
        <table border="0">
            <tr>
                <td valign="top" style="vertical-align:top; width: 100%;" width="850px">
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle"> Crear Bases de Datos</h1>
                        </div>
                        <div class="maincontent">
                            Creación de la base de datos<br/>
                            -
                            <?php //echo form_open(base_url()."index.php/BaseData/db")  ?>
                            <ul>
                                <li>
                                    <div id="_db_crear"></div>
                                </li>
                            </ul>
                            <?php //echo form_close();  ?>


                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle"> Asignar usuarios a la base de datos</h1>
                        </div>
                        <div class="maincontent">
                            Asignación de usuarios<br/>
                            se asocia la base de datos con el usuario maestro creado <br/>
                            -
                            <?php echo form_open(base_url()."index.php/BaseData/asignarDb") ?>
                            <table>
                                <tr>
                                    <td>
                                        Bases de datos:
                                    </td>
                                    <td>
                                        <?php
                                        echo"<select name='databaseList' id='databaseList'>";
                                        foreach($basedata as $b) {
                                            echo"<option>".$b->nombre."</option>";
                                        }
                                        echo"</select>"
                                        ?>

                                    </td>
                                    <td>
                                        Usuarios:
                                    </td>
                                    <td>
                                        <?php
                                        echo"<select name='userbaseList' id='userbaseList'>";
                                        foreach($users as $us) {
                                            echo"<option>".$us->nick."</option>";
                                        }
                                        echo"</select>"
                                        ?>
                                    </td>
                                    <td>
                                        <div id="_db_asignar"></div>
                                    </td>
                                </tr>

                            </table>
                            <?php echo form_close(); ?>
                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle"> Crear Bases de Datos</h1>
                        </div>
                        <div id="_bd_asignation" class="maincontent">
                            -Lista de las bases de datos creadas <br/>
                            -
                            <?php
                            $i=0;
                            foreach($databases as $base) {
                                echo"<div id='box_".$i."' style='height:auto;'>";
                                echo" <div id='box'>";
                                echo"<div id='header_box'></div>";
                                echo"<div id='body_box'>";
                                echo"<table border='0'>";
                                echo"<tr>";
                                echo"<td><b>Base de datos</b></td><td><b>Usuario</b></td><td><b>Opciones</b></td<td><b>Go to</b></td></tr>";
                                echo"<input type='hidden' id='idBase_".$i."' value='".$base->idBase."' />";
                                echo"<input type='hidden' id='idUs_".$i."' value='".$base->idUser."' />";
                                echo"<input type='hidden' id='dbName_".$i."' value='".$base->nombre."' />";
                                echo"<input type='hidden' id='dbUser_".$i."' value='".$base->nick."' />";
                                echo"<tr><td>".$base->nombre." Creado el ".$base->fecha."</td><td>".$base->nick." <br/> creado el ".$base->fecha."</td><td><input type='submit' id='quitar_".$i."' value='Quitar usuario' size='5' /><br/><br/><input type='submit' value='Eliminar Base de Datos' id='delete_".$i."'/></td><td>Phpmyadmin</td>";
                                echo"</tr>";
                                echo"</table>";
                                echo "</div>";
                                echo "<div id='bottom_box'></div>";
                                echo "<div class='clearfix'></div>";
                                echo"</div>";
                                echo"</div>";
                                $i++;
                            }
                            echo"<input type='hidden' id='length' name='length' value='".$i."' />";
                            ?>

                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>

                </td>
            </tr>
        </table>
    </div>
    <span class="bottom"><span></span></span>
</div>