<style type="text/css">
    #frame{width:410px; margin:50px auto; background-color:#eee; border:1px solid #ccc; padding:10px;}
    h1{font-size:1.2em; color:#555; margin-left:20px;}
    .movie{margin:20px; width:93px; border:1px solid #eee; cursor:pointer;}
    .movie:hover{border:1px solid #222;}
</style>
<script type="text/javascript" language="javascript">
    var _remplaceFile;
    var ___name;
    Ext.namespace('nexus.project.lastchanges.panelchanges');
    nexus.project.lastchanges.panelchanges={
        init:function(){
            var data=$("#_idsinformation").val()+"-User";
            var tree = new Ext.tree.TreePanel({
                title:"Archivos de Usuario",
                id:"_tree_panel_user",
                border: false,
                autoScroll:true,
                dataUrl:'/index.php/repositorio/getUserDirectory/'+data,
                root: new Ext.tree.AsyncTreeNode({
                    id:'.',
                    text: 'User files'
                }),
                listeners:{
                    click:function(node){
                        var ruta=node.id.substring(2,node.id.length);
                        _remplaceFile=ruta;
                        var file_arr=node.id.split("/");
                        var file=file_arr[Number(file_arr.length-1)];
                        ___name=file;
                    }
                }
            });
            /*
             *Parametros falsos
             **/
            var system = new Ext.tree.TreePanel({
                title:"Archivos de Sistema",
                id:"_tree_panel_system",
                border: false,
                autoScroll:true,
                dataUrl:'/index.php/repositorio/getUserDirectory/'+"1-1-1-System",

                root: new Ext.tree.AsyncTreeNode({
                    id:'.',
                    text: 'User files'
                })
            });
            /*
             *Dobleclick events
             **/
            tree.on("dblclick",function(node){
                var ruta=node.id.substring(1,node.id.length);
                var file_arr=node.id.split("/");
                var file=file_arr[Number(file_arr.length-1)];
                alert(ruta+" "+file);
            });
            var _route=this;
            /*tree.on("cellclick",function(node){
               alert("si")
            });*/


            var actions=new Ext.Panel({
                buttons:[{
                        text:"Ver Cambios",
                        handler:function(){
                            var store=new Ext.data.JsonStore({
                                url:"/index.php/repositorio/getChanges/"+data,
                                root:"rows",
                                totalProperty:"totalCount",
                                fields:['id','nombre','ruta','fecha','estado','updatefecha']
                            });
                            store.load({params:{z:30,start:0,limit:20}});

                            var grid=new Ext.grid.GridPanel({
                                store:store,
                                width:600,
                                height:400,
                                autoScroll:true,
                                columns:[
                                    new Ext.grid.RowNumberer(),
                                    {header:"id",dataIndex:"id",sortable:true,width:20},
                                    {header:"Nombre",dataIndex:"nombre",width:100},
                                    {header:"Ruta",dataIndex:"ruta",width:200,renderer:_route.ruta},
                                    {header:"Fecha",dataIndex:"fecha",width:100},
                                    {header:"Ultima Actualización",dataIndex:"updatefecha",width:150,renderer:_route.link}
                                ],
                                border: false,
                                layout:"fit",
                                stripeRows: true
                            });
                            var win=new Ext.Window({
                                title:"Archivos Modificados",
                                items:[grid],
                                width:600,
                                height:300
                            });
                            win.show();
                        }},{
                        text:"Copiar Archivo",handler:function(){
                            var ids=$("#_idsinformation").val().split("-");
                            $.post("/index.php/repositorio/copyFile/",{"relative":_remplaceFile,"name":___name,"idUs":ids[0],"idEq":ids[1]},function(data){
                                if(data.success==false){
                                    var path=data.path;

                                    Ext.MessageBox.confirm('Confirm', '¿El archivo ya existe deseas reemplazarlo?',function(data){
                                        if(data=="yes"){
                                            //alert(path);
                                            Ext.MessageBox.show({
                                                msg: 'Copiando El archivo espere porfavor...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true
                                            });
                                            $.post("/index.php/repositorio/remplace/",{"relative":_remplaceFile,"name":___name,"idUs":ids[0],"idEq":ids[1],"path":path},function(data){
                                                if(data.success){
                                                    Ext.MessageBox.hide();
                                                    if(data.result=="cambios terminados"){
                                                        $("#_avatar_send").html("<div id='_avatar_users'><b>SE ENVIARA UNA NOTIFICACIÓN A LOS SIGUIENTES USUARIOS</b><br/>Se completo la actualización del repositorio, ahora se notificara a los miembros dueños de los repositorios para actualizar sus versiones<br/></div>");
                                                        var idUss="";
                                                        var idEqs="";
                                                        var nicks="";
                                                        for(var i=0;i<data.list.length;i++){
                                                            Ext.DomHelper.append('_avatar_users',{
                                                                tag:'img',
                                                                src:"/uploads/"+data.list[i].avatar,
                                                                alt:data.list[i].nick,
                                                                title:data.list[i].nick,
                                                                cls: 'movie'
                                                            });

                                                            idUss+=data.list[i].idUs;
                                                            idEqs+=data.list[i].idEq;
                                                            nicks+=data.list[i].nick;
                                                            if(i<data.list.length-1){
                                                                idUss+="-";
                                                                idEqs+="-";
                                                                nicks+="-";
                                                            }

                                                        }
                                                        var value_v
                                                        if(data.version==""){
                                                            value_v="Especifiqué una versión aqui";
                                                        }else{
                                                            value_v==data.version;
                                                        }
                                                        var notificacion=new Ext.Window({
                                                            title:"Se han terminado los Cambios",
                                                            modal:true,
                                                            width:400,
                                                            height:400,
                                                            contentEl:"_avatar_users",
                                                            items:[{
                                                                    name:'nombre',
                                                                    id:'_p_messaje',
                                                                    minLength:4,
                                                                    maxLength:250,
                                                                    allowBlank:false,
                                                                    xtype:"textarea",
                                                                    width:600,
                                                                    height:100
                                                                },{
                                                                    name:'version',
                                                                    id:"_version_s",
                                                                    allowBlank:false,
                                                                    xtype:"textfield",
                                                                    value:value_v,
                                                                    width:200
                                                                }],
                                                            buttons:[{text:"Enviar",handler:function(){
                                                                        /*
                                                                         *Esta acción envia las notificaciones a los usuarios
                                                                         *dueños de repositorios
                                                                         *además publica en la pared los cambios
                                                                         *cierra el repositorio del usuario para evitar que sea visto
                                                                         *
                                                                         **/

                                                                        Ext.MessageBox.show({
                                                                            msg: 'Creando entrada y notificaciones espere porfavor...',
                                                                            progressText: 'Saving...',
                                                                            width:300,
                                                                            wait:true
                                                                        });
                                                                        var parameters=$("#_idsinformation").val().split("-");

                                                                        $.post("/index.php/repositorio/finishUpdate/",{
                                                                            ids:idUss,
                                                                            idEqs:idEqs,
                                                                            nicks:nicks,
                                                                            id:parameters[0],
                                                                            idEq:parameters[1],
                                                                            idChanges:parameters[2],
                                                                            msn:$("#_p_messaje").val()
                                                                        },
                                                                        function(data){
                                                                            Ext.MessageBox.hide();
                                                                            notificacion.close();
                                                                        },"json");
                                                                    }}]
                                                        });
                                                        notificacion.show();
                                                    }
                                                    //Ext.example.msg('Remplazo!',"Se har emplazado el archivo con éxito");

                                                }
                                            },"json");
                                        }
                                    },"json");
                                }else{
                                    Ext.example.msg('Mensaje',"se ha copiado el archivo con éxito");

                                }
                            },"json");
                        }
                    },{
                        text:"Copiar Solo los cambios",handler:function(){
                            var data=$("#_idsinformation").val().split("-");

                            $.post("/index.php/repositorio/setAllChanges/",
                            {idUs:data[0],idEq:data[1]},function(data){
                                if(data.success){
                                    $("#_avatar_send").html("<div id='_avatar_users'><b>SE ENVIARA UNA NOTIFICACIÓN A LOS SIGUIENTES USUARIOS</b><br/>Se completo la actualización del repositorio,<br/> ahora se notificara a los miembros dueños de los repositorios para actualizar sus versiones<br/></div>");
                                    var idUss="";
                                    var idEqs="";
                                    var nicks="";
                                    for(var i=0;i<data.list.length;i++){
                                        Ext.DomHelper.append('_avatar_users',{
                                            tag:'img',
                                            src:"/uploads/"+data.list[i].avatar,
                                            alt:data.list[i].nick,
                                            title:data.list[i].nick,
                                            cls: 'movie'
                                        });

                                        idUss+=data.list[i].idUs;
                                        idEqs+=data.list[i].idEq;
                                        nicks+=data.list[i].nick;
                                        if(i<data.list.length-1){
                                            idUss+="-";
                                            idEqs+="-";
                                            nicks+="-";
                                        }

                                    }
                                    var value_v
                                    if(data.version==""){
                                        value_v="Especifiqué una versión aqui";
                                    }else{
                                        value_v=data.version;
                                    }
                                    var notificacion=new Ext.Window({
                                        title:"Se han terminado los Cambios",
                                        modal:true,

                                        contentEl:"_avatar_users",
                                        items:[{
                                                name:'nombre',
                                                id:'_p_messaje',
                                                minLength:4,
                                                maxLength:250,
                                                xtype:"textarea",
                                                width:600,
                                                height:100
                                            },{
                                                name:'version',
                                                id:"_version_s",
                                                allowBlank:false,
                                                xtype:"textfield",
                                                value:value_v,
                                                width:150
                                            }],
                                        buttons:[{text:"Enviar",handler:function(){
                                                    /*
                                                     *Esta acción envia las notificaciones a los usuarios
                                                     *dueños de repositorios
                                                     *además publica en la pared los cambios
                                                     *cierra el repositorio del usuario para evitar que sea visto
                                                     *
                                                     **/
                                                    Ext.MessageBox.show({
                                                        msg: 'Creando entrada y notificaciones espere porfavor...',
                                                        progressText: 'Saving...',
                                                        width:300,
                                                        wait:true
                                                    });
                                                    var parameters=$("#_idsinformation").val().split("-");

                                                    $.post("/index.php/repositorio/finishUpdate/",{
                                                        ids:idUss,
                                                        idEqs:idEqs,
                                                        nicks:nicks,
                                                        id:parameters[0],
                                                        idEq:parameters[1],
                                                        idChanges:parameters[2],
                                                        msn:$("#_p_messaje").val(),
                                                        version:$("#_version_s").val()

                                                    },
                                                    function(data){
                                                        Ext.MessageBox.hide();
                                                        notificacion.close();
                                                    },"json");
                                                }}]
                                    });
                                    notificacion.show();
                                }
                            },"json"
                        );
                        }
                    },{
                        text:"Reprobar Cambio",handler:function(){
                            var data=$("#_idsinformation").val().split("-");
                            var rep=new Ext.Window({
                                title:"Rechazo",
                                items:[{
                                        name:'nombre',
                                        id:'_p_messaje',
                                        minLength:4,
                                        maxLength:250,
                                        xtype:"textarea",
                                        width:400,
                                        height:100
                                    }],
                                buttons:[{text:"Enviar",handler:function(){
                                            Ext.MessageBox.show({
                                                msg: 'Creando entrada y notificaciones espere porfavor...',
                                                progressText: 'Saving...',
                                                width:300,
                                                wait:true
                                            });
                                            $.post("/index.php/repositorio/rejected/",{messaje:$("#_p_messaje").val(),id:data[0],idChanges:data[2]},function(data){
                                                Ext.MessageBox.hide();
                                                rep.close();
                                            },"json");
                                        }}]
                            });
                            rep.show();
                        }
                    }
                ]
            });
            var panel =new Ext.Panel({
                layout:"table",
                layoutConfig: {columns:2},
                defaults: {frame:true, width:250, height: 250},
                items:[
                    tree,
                    system,{
                        colspan:2,
                        width:490,
                        title:"Acciones",
                        items:[actions]
                    }
                ],
                renderTo:"__containerchanges"
            });
        },
        ruta:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            return value;
        },
        link:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            var str="<a href='javascript:onClickEvent("+record.get("id")+")' id='log_"+colIndex+"'>Ver Log</a>";

            return value+"<br/>"+str;

        }
    }
    Ext.onReady(nexus.project.lastchanges.panelchanges.init,nexus.project.lastchanges.panelchanges);

</script>
<input type="hidden" value="<?php echo $ids ?>" id="_idsinformation"/>
<div id="__containerchanges">
</div>
<div id="_grid_panel">
    prueba1
</div>
<div id="_options_panel">
    prueba2
</div>
<div id="frame">
    <div id="_avatar_send">
        hola
    </div>
</div>