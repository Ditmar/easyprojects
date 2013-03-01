<html>
    <head>
        <title>hola</title>
        <script type="text/javascript">
            //alert("holssa");
            Ext.onReady(function(){
                $("#_v_table2").hide();
                /*
                 *Agregar Rol
                 **/
                $("#_add_rol").click(function(){
                    if($("#_rol_id").val()!=""){
                        Ext.MessageBox.show({
                            title:'Deseas agregar el rol '+$("#_rol_name").val()+' al usuario '+$("#_id_nick").val()+' ?',
                            msg: 'Si hace click en aceptar los cambios se guardarán correctamente. <br />Desea proceder en su acción?',
                            buttons: Ext.MessageBox.YESNOCANCEL,
                            fn:onAcepted,
                            animEl: 'mb4',
                            icon: Ext.MessageBox.QUESTION
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Alerta!',
                            msg: 'Tiene que escoger un Rol para ser Añadido',
                            buttons: Ext.MessageBox.OK,
                            animEl: 'mb9',
                            fn: showResult
                        });
                    }
                });
                $("#_del_rol").click(function(){
                    if($("#_rol_idUs").val()!=""){
                        Ext.MessageBox.show({
                            title:'Deseas quitar el rol '+$("#_rol_nameUs").val()+' al usuario '+$("#_id_nick").val()+' ?',
                            msg: 'Si hace click en aceptar los cambios se guardarán correctamente. <br />Desea proceder en su acción?',
                            buttons: Ext.MessageBox.YESNOCANCEL,
                            fn:onDeleteAcepted,
                            animEl: 'mb4',
                            icon: Ext.MessageBox.QUESTION
                        });

                    }else{
                        Ext.MessageBox.show({
                            title: 'Alerta!',
                            msg: 'Tiene que escoger un Rol de Usuario para ser Eliminado',
                            buttons: Ext.MessageBox.OK,
                            animEl: 'mb9',
                            fn: showResult
                        });

                    }
                });
                //creamos el store
                //alert("hola");
                var store_rules=new Ext.data.JsonStore({
                    root:'rules',
                    totalProperty:'totalcount',
                    id:'id',
                    fields:[
                        {name:'id',type:'int'},
                        {name:'nombre',type:"string"},
                        {name:'tipo',type:'string'}
                    ],
                    proxy:new Ext.data.HttpProxy({
                        url:"/index.php/proyecto/getPolitica/"+$("#_id").val()
                    })
                });
                var store_user=new Ext.data.JsonStore({
                    root:'rules',
                    totalProperty:'totalcount',
                    id:'id',
                    fields:[
                        {name:'id',type:'int'},
                        {name:'nombre',type:"string"},
                        {name:'tipo',type:'string'}
                    ],
                    proxy:new Ext.data.HttpProxy({
                        url:"/index.php/proyecto/getUserPolitica/"+$("#_id").val()
                    })
                });
                var politica_grid=new Ext.grid.GridPanel({
                    store:store_rules,
                    columns:[
                        {header:"Id",width:50,dataIndex:"id"},
                        {header:"Nombre",width:180,sortable:true,dataIndex:"nombre"},
                        {header:"Tipo",width:50,sortable:true,dataIndex:"tipo"}
                    ],
                    listeners:{cellclick:function(grid,rowIndex,columnIndex,e){
                            var record = grid.getStore().getAt(rowIndex);
                            var field=grid.getColumnModel().getDataIndex(1);
                            var clave=grid.getColumnModel().getDataIndex(0);
                            $("#_rol_id").attr("value",record.get(clave));
                            $("#_rol_name").attr("value",record.get(grid.getColumnModel().getDataIndex(1)));
                            // alert($("#_rol_name").val()+" <-");
                            //"$("#_us_dg").attr("value",record.get(field))
                        }},
                    viewConfig:{
                        forceFit:true
                    },
                    height:250,
                    width:250,
                    title:"Roles del Proyecto"
                });
                function onDeleteAcepted(btn){

                    if(btn=="yes"){

                        $.post("/index.php/proyecto/deleteRol/",
                        {"idRol":$("#_rol_idUs").val(),"idUs":$("#_id").val()},
                        function(data){
                            if(data.result){
                                store_rules.load({params:{start:0, limit:20}});
                                store_user.load({params:{start:0, limit:20}});
                            }
                        },"json"
                    );
                    }
                }
                function onAcepted(btn){
                    var mask = new Ext.LoadMask(Ext.get('contentPanel_rol'), {msg:'Agregando rol, espere porfavor'});
                    mask.show();
                    if(btn=="yes"){

                        if($("#_rol_id").val()==80){
                            //comprobamos repositorio
                            //alert("-> "+$("#_idEq").val());
                            $.post("/index.php/repositorio/checkworkspace",
                            {"idUs":$("#_id").val(),"idEq":$("#_idEq").val()},function(data){
                                if(data.result>0){
                                    $.post("/index.php/proyecto/setRol/",
                                    {"idRol":$("#_rol_id").val(),"idUs":$("#_id").val()},
                                    function(data){

                                        //alert("-> <-"+data.result);
                                        if(data.result){
                                            mask.hide();
                                            // alert(data.result);
                                            store_rules.load({params:{start:0, limit:20}});
                                            store_user.load({params:{start:0, limit:20}});
                                        }
                                    },"json"
                                );
                                }else{
                                    mask.hide();
                                    $("#_v_table").hide("slow");
                                    $("#_v_table2").show("slow");
                                    $("#_click_return").click(function(){
                                        $("#_v_table").show("slow");
                                        $("#_v_table2").hide("slow");
                                    });
                                    //$("#contentPanel_rol").html("<a></a>");
                                }
                            },"json");
                        }else{
                            //contentPanel_rol
                            mask.hide();
                            $.post("/index.php/proyecto/setRol/",
                            {"idRol":$("#_rol_id").val(),"idUs":$("#_id").val()},
                            function(data){
                                //alert("-> <-"+data.result);
                                if(data.result){
                                    // alert(data.result);
                                    store_rules.load({params:{start:0, limit:20}});
                                    store_user.load({params:{start:0, limit:20}});
                                }
                            },"json"
                        );
                        }
                    }
                }
                function showResult(btn){
                    Ext.example.msg('Button Click', 'You clicked the {0} button', btn);
                };
                var user_grid=new Ext.grid.GridPanel({
                    store:store_user,
                    columns:[
                        {header:"Id",width:50,dataIndex:"id"},
                        {header:"Nombre",width:180,sortable:true,dataIndex:"nombre"},
                        {header:"Tipo",width:50,sortable:true,dataIndex:"tipo"}
                    ],
                    listeners:{cellclick:function(grid,rowIndex,columnIndex,e){
                            var record = grid.getStore().getAt(rowIndex);
                            var field=grid.getColumnModel().getDataIndex(1);
                            var clave=grid.getColumnModel().getDataIndex(0);
                            $("#_rol_idUs").attr("value",record.get(clave));
                            $("#_rol_nameUs").attr("value",record.get(grid.getColumnModel().getDataIndex(1)));

                            //$("#_idUs").attr("value",record.get(clave));
                            //$("#_us_dg").attr("value",record.get(field))
                        }},
                    viewConfig:{
                        forceFit:true
                    },
                    height:250,
                    width:250,
                    title:"Roles del Actuales del Usuario"
                });
                store_rules.load({params:{start:0, limit:20}});
                store_user.load({params:{start:0, limit:20}});
                politica_grid.render("grid_rules");
                user_grid.render("grid_user");
            });
        </script>
    </head>
    <body>
        <!-- COPIAR ESTE CODIGO EN TODOS LOS CASOS de control -->
        <?php
            $this->load->model("roles_model");
            if(!$this->roles_model->checkPermit(88)){
                redirect("/error/redirect/88");
            }
        ?>
        <div id="contentPanel_rol">
            <h1>Roles De Usuario</h1>
            <div class="panel_container">
                <?php echo "<b>Nick</b>  ".$user[0]["nick"]."<br/><b>Nombre:</b> ".$user[0]["nombre"]." ".$user[0]["apellido"] ?><br/>
                <img src="<?php echo "/uploads/".$user[0]["avatar"]?>" alt="<?php echo $user[0]["nick"] ?>"/>
            </div>
            <div class="panel_container">
                <h1>Roles</h1>
                <table border="0" width="100%" id="_v_table">
                    <tr>
                        <td>
                            <div id="grid_rules">

                            </div>
                        </td>
                        <td>
                            <input type="button" id="_add_rol" value="Agregar ->" />
                            <br/>
                            <input type="button" id="_del_rol" value="<- Quitar Rol" />
                        </td>
                        <td>
                            <div id="grid_user">

                            </div>
                        </td>
                    </tr>


                </table>
                <table border="0" width="100%" id="_v_table2">
                    <tr>
                        <td>

                            <p>
                            <center>
                                <b>Este usuario no tiene repositorio asignado, necesita crear uno primero para proceder en la acción</b>
                                <a href="#" id="_click_return">Volver</a>
                            </center>

                            </p>
                        </td>
                    </tr>
                </table>
            </div>
            <input type="hidden" id="_rol_name" value=""/>
            <input type="hidden" id="_rol_id" value=""/>
            <input type="hidden" id="_id" value="<?php echo $user[0]["id"] ?>"/>
            <input type="hidden" id="_idEq" value="<?php echo $idEq ?>"
                   <input type="hidden" id="_id_nick" value="<?php echo $user[0]["nick"] ?>"/>
            <!--
                Ids del otro Grid de roles de usuario
            -->
            <input type="hidden" id="_rol_nameUs" value=""/>
            <input type="hidden" id="_rol_idUs" value=""/>
        </div>
    </body>
</html>