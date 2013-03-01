<html>
    <head>
        <title>hola </title>
        <script type="text/javascript">
            Ext.onReady(function(){
                $("#_form").hide();
                //panel.hide("slow");
                //$()
                $("#_create").hide();
                var store_users=new Ext.data.JsonStore({
                    root:'users',
                    totalProperty:'totalcount',
                    id:'id',
                    fields:[
                        {name:'id',type:'int'},
                        {name:'nombre',type:"string"},
                        {name:'nick',type:'string'},
                        {name:'avatar',type:'string'}
                    ],
                    proxy:new Ext.data.HttpProxy({
                        url:"/index.php/equipo/getUs/"+$("#_id_e").val()
                    })
                });
                var grid_user=new Ext.grid.GridPanel({
                    store:store_users,
                    columns:[
                        {header:"Id",width:10,dataIndex:"id"},
                        {header:"Nombre",width:100,dataIndex:"nombre"},
                        {header:"Nick",width:100,dataIndex:"nick"},
                        {header:"Avatar",width:100,dataIndex:"avatar",renderer:onRenderAvatar}
                    ],
                    viewConfig:{
                        forceFit:true
                    },
                    listeners:{cellclick:function(grid,rowIndex,columnIndex,e){
                            var record = grid.getStore().getAt(rowIndex);
                            var field=grid.getColumnModel().getDataIndex(2);
                            var clave=grid.getColumnModel().getDataIndex(0);
                            $("#_nick").attr("value","Crear Repositorio para "+record.get(field));
                            $("#_onick").attr("value",record.get(field));
                            $("#_idUs").attr("value",record.get(clave));
                            //panel.show("slow");
                            callbackWorkSpace();
                            
                            

                            //$("#_rol_nameUs").attr("value",record.get(grid.getColumnModel().getDataIndex(1)));
                            //$("#_idUs").attr("value",record.get(clave));
                            //$("#_us_dg").attr("value",record.get(field))
                        }},
                    height:500,
                    width:330,
                    title:"Usuarios Miembros del Equipo"
                });
                function onRenderAvatar(value){
                    return "<img src='"+value+"' alt='Avatar'/>";
                }
                function callbackWorkSpace(){
                    $.post("/index.php/repositorio/checkworkspace/",
                    {"idUs":$("#_idUs").val(),"idEq":$("#_id_e").val()},
                    function(server){
                        //alert("-> "+server.result);
                        if(server.result>0){
                            $("#_create").hide();
                            $("#_checkrol").html("Repositorio Creado el "+server.data[0]["fecha"] );
                            //alert("-> "+server.data[0]["fecha"]);
                        }else{
                            $("#_create").show();
                            $("#_create").attr("value",$("#_nick").val());
                            //$("#_checkrol").html("Repositorio Creado el "+server.result[0]["fecha"] );
                        }
                    },"json"
                     );
                }
                store_users.load({params:{start:0, limit:20}});
                grid_user.render("_users");
                $("#_create").click(function(){
                    var mask = new Ext.LoadMask(Ext.get('contentPanel_id_workspace'), {msg:'Creando Espacio de trabajo, "Se están copiando los archivos del repositorio actual el proceso podria tardar unos minutos"'});
            mask.show();
                    $.post("/index.php/repositorio/createWorkSpace/",
                {"idUs":$("#_idUs").val(),"idEq":$("#_id_e").val(),"nick":$("#_onick").val()},
                   function(data){
                       mask.hide();
                       //alert(data.result["idEq"]+"");
                       if(data.result){
                           callbackWorkSpace();
                       }else{
                           Ext.MessageBox.show({
                            title: 'Alerta!',
                            msg: 'No se pudo generar el espacio de trabajo',
                            buttons: Ext.MessageBox.OK,
                            animEl: 'mb9',
                            fn: showResult
                        });
                       }
                   },"json" );
                });
            });
        </script>
    </head>
    <body>
        <div id="contentPanel_id_workspace">
            En este Apartado usted podrá crear repositorios para los usuarios
            que pertenescan al tipo programadores, los repositorios creados en esta aréa siempre serán copias del repositorio general del sistema.
            <b>Lista De usuarios del equipo</b>
            <table>
                <tr>
                    <td>
                        <div id="_users">
                        </div>
                    </td>
                    <td>
                        <div id="_checkrol">
                            
                        </div>
                        <div id="_form">
                            Crear Repositorio<br/>
                            <input id="_create" type="button" value="Crear Repositorio " />
                        </div>
                    </td>
                </tr>
            </table>

        </div>
        <input type="hidden" value="" id="_nick" />
        <input type="hidden" value="" id="_onick" />
        <input type="hidden" value="<?php echo $id ?>" id="_id_e" />
        <input type="hidden" value="" id="_idUs" />
    </body>
</html>