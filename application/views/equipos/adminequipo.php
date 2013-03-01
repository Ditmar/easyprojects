<script type="text/javascript">
    var _extfather;
    Ext.onReady(function (){
        /*
         *Crear Grid
         *basandose en el JsonStore
         */
        var store=new Ext.data.JsonStore({
            root:"users",
            TotalProperty:"total",
            id:"id",
            fields:[
                {name:"id",type:"string"},
                {name:"nick",type:"string"},
                {name:"infpersonal",type:"string"},
                {name:"email",type:"string"},
                {name:"contacto",type:"string"},
                {name:"fecha",type:"date"},
                {name:"avatar",type:"string"}
            ],
            proxy:new Ext.data.HttpProxy({
                url:"/index.php/equipo/getUsuarios/"
            })
        });
        function renderAvatar(value){
            return "<img src='"+value+"' alt='Avatar'/>";
        }
        var usuarioGrid=new Ext.grid.GridPanel({
            store:store,
            columns:[
                {header:"Id",width:50,dataIndex:"id"},
                {header:"Nick",width:80,sortable:true,dataIndex:"nick"},
                {header:"Inf. Personal ",width:150,sortable:false,dataIndex:"infpersonal"},
                {header:"Email",width:70,dataIndex:"email"},
                {header:"Contacto",width:120,sortable:false,dataIndex:"contacto"},
                {header:"Fecha de Ingreso",width:70,sortable:true,dataIndex:"fecha"},
                {header:"Avatar",width:100,dataIndex:"avatar",renderer:renderAvatar}
            ],
            listeners:{cellclick:function(grid,rowIndex, columnIndex,e){
                    var record = grid.getStore().getAt(rowIndex);
                    var field=grid.getColumnModel().getDataIndex(1);
                    var clave=grid.getColumnModel().getDataIndex(0);

                    $("#_idUs").attr("value",record.get(clave));
                    $("#_us_dg").attr("value",record.get(field));

                }
            },
            viewConfig:{
                forceFit:true
            },
            autoHeight:true,
            width:650,
            title:"Usuarios Miembros del Proyecto",
            bbar: new Ext.PagingToolbar({
                pageSize: 20,
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying topics {0} - {1} of {2}',
                emptyMsg: "No topics to display",
                items:[
                    '-', {
                        pressed: true,
                        enableToggle:true,
                        text: 'Show Preview',
                        cls: 'x-btn-text-icon details',
                        toggleHandler: function(btn, pressed){
                            var view = grid.getView();
                            view.showPreview = pressed;
                            view.refresh();
                        }
                    }]
            })
        });
        $("#equipo_btn").click(function(){

            //llamos al a funcion Json
            //alert("hola ");
            $.post("/index.php/equipo/createTeam/",
            {"nombre":$("#equipo_txt").val()},
            function(data){
                alert(data.result);
                store_team.load({params:{start:0, limit:20}});
            },"json"
        );
        });

        /*
         **Grid para equipos de trabajo
         */
        var store_team=new Ext.data.JsonStore({
            root:'teams',
            totalProperty:'totalcount',
            fields:[
                {name:'id',type:"string"},
                {name:'nombre',type:"string"},
                {name:'create_at',type:"date"},
                {name:'users', type:'string'},
                {name:'actions',type:"string"}
            ],
            proxy:new Ext.data.HttpProxy({
                url:"/index.php/equipo/getEquipos/"
            })
        });
        _extfather=store_team;
        function viewUsers(value){
            var users=value.split("|token|");
            var htmlList="<table border='0'>";
            for(var i=0;i<users.length-1;i++){
                var parse=users[i].split('-');
                htmlList+="<tr><td>"+parse[1]+"</td><td><a href='javascript:onAdminRoles("+parse[0]+")'>Administrar Roles <img src='/images/team/wrench_orange.png'/></a><br/><a href='javascript:onRemoveUser("+parse[0]+")'>Quitar Usuario <img src='/images/team/user_delete.png'/></a></td></tr>";
            }
            htmlList+="</table>";
            return htmlList;
        }
        function actions(value){
            var data=value.split(",");
            var html="";
            var icon;
            for(var i=0;i<data.length;i++){
                icon=data[i].split("/");
                switch(icon[icon.length-1]){
                    case"user_add.png":{
                            html+="<a href='javascript:onClickUser("+i+")'><img src='"+data[i]+"'/> Agregar Usuario</a><br/>";
                            break;
                        }
                    case"note_delete.png":{
                            html+="<a href='javascript:onClickDelete("+i+")'><img src='"+data[i]+"'/> Borrar Equipo Usuario</a> </br>";
                            break;
                        }
                    case"time_add.png":{
                            //html+="<a href='javascript:onClickTime()'><img src='"+data[i]+"'/> Crear Cronograma</a>  </br>";
                            break;
                        }
                    case"package_add.png":{
                            html+="<a href='javascript:onClickRepositorio()'><img src='"+data[i]+"'/> Crear Repositorios</a>  </br>";
                            break;
                        }
                    }
                }
                return html;
            }
            function viewName(value){
                return "<b>"+value+"</b>";
            }
            var teamGrid=new Ext.grid.GridPanel({
                store:store_team,
                id:"_team_grid_system",
                columns:[
                    {header:"Id",dataIndex:"id",width:50},
                    {header:"Nombre", dataIndex:"nombre", renderer:viewName},
                    {header:"Usuarios",dataIndex:"users", renderer:viewUsers, width:150},
                    {header:"Fecha", dataIndex:"create_at"},
                    {header:"Actions",dataIndex:"actions",renderer:actions, width:100},
                ],
                listeners:{cellclick:function(grid,rowIndex, columnIndex,e){
                        var record = grid.getStore().getAt(rowIndex);
                        var field=grid.getColumnModel().getDataIndex(1);
                        var id=grid.getColumnModel().getDataIndex(0);

                        $("#_idTeam").attr("value",record.get(id));
                        $("#_team_dg").attr("value",record.get(field));

                    }
                },
                viewConfig:{
                    forceFit:true
                },
                height:400,
                width:650
            });
            teamGrid.render("equipo_dg");
            store_team.load({params:{start:0, limit:20}});
            usuarioGrid.render("usuario_dg");
            store.load({params:{start:0, limit:20}});

        });
        function onClickUser(){
            //alert("Usuario "+$("#_idTeam").val()+" "+$("#_idUs").val());
            $.post("/index.php/equipo/addUser",{"idUs":$("#_idUs").val(),"idTeam":$("#_idTeam").val()},
            function(data){
                _extfather.load({params:{start:0, limit:20}});
                Ext.MessageBox.alert('Correcto', data.result);
            },"json"
        );
        }
        function onClickDelete(i){
            /*
             *vamos a crear el deleted Extremo!!!
             **/
            Ext.MessageBox.confirm("Alerta","¿Deseas borrar el equipo?",function(data){
                if(data=="yes"){

                    $.post("/index.php/equipo/removeTeam",{idEq:$("#_idTeam").val()},function(data){
                        if(data.success){
                            Ext.MessageBox.alert("Success","Borrado Con éxito");
                            _extfather.load({params:{start:0, limit:20}});
                        }else{
                            Ext.MessageBox.alert("Error","No se puede borrar el equipo mientras hayan usurios en el");
                        }
                    },"json");
                }
            });

        }
        function onClickTime(){
            /*
             *cargamos el cronograma
             **/
        }
        function onClickRepositorio(){
            createPanel("Crear Repositorio de Trabajo","/index.php/repositorio/workspace/"+$("#_idTeam").val());

            //alert("-> "+$("#_idTeam").val())
            //createPanel("Administración de Roles","/index.php/proyecto/viewRoles/"+obj);
        }
        function onCreate(obj){
            alert("hola "+obj);
        }
        function onAdminRoles(obj){
            createPanel("Administración de Roles","/index.php/proyecto/viewRoles/"+obj+"-"+$("#_idTeam").val());

        }
        function onRemoveUser(obj){
            Ext.MessageBox.confirm("¿Esta Seguro?","Esta Seguro que desea quitar del equipo a este usuario?",function(data){
                if(data=="yes"){
                    /*
                     *consultamos si tiene repositorio
                     *$("#_idTeam")
                     **/
                    var mask = new Ext.LoadMask(Ext.get("_team_grid_system"), {msg:'Borrando espere porfavor...'});
                    mask.show();
                    $.post("/index.php/equipo/getWorkSpace",{idUs:obj,idEq:$("#_idTeam").val()},function(data){
                        if(data.success){
                            mask.hide();
                            Ext.MessageBox.confirm("Alerta!","El Usuario Tiene un Repositorio Asignado, ¿desea continuar con la operación?, si hace click en aceptar, se borraran los archivos físicos y aquellos archivos que no tenian relación con los logs principales del sistema",function(data){
                                if(data=="yes"){
                                    mask.show();
                                    if($("#_idTeam").val()!=""){
                                        $.post("/index.php/equipo/deleteWorkSpace",{idUs:obj,idEq:$("#_idTeam").val()},function(data){
                                            if(data.success){
                                                mask.hide();
                                                Ext.MessageBox.alert("Success","El borrado fue exitoso!");
                                                _extfather.load({params:{start:0, limit:20}});
                                            }
                                        },"json");
                                    }
                                }
                            },"json");
                        }else{
                            mask.hide();
                            Ext.MessageBox.alert("Success","El borrado fue exitoso!");
                            _extfather.load({params:{start:0, limit:20}});

                        }
                    },"json");
                }
            });
        }
        function createPanel(titles,urlLoad){
            var win=new Ext.Window({
                title:titles,
                width:600,
                height:600,
                modal:true,
                maximizable:true,
                collapsille:true,
                autoLoad: {url: urlLoad, scripts: true},
                tbar:[{
                        text:'Repositorios'
                        ,handler:function() {
                            win.load(win.autoLoad.url);
                        }
                    }]
            });
            win.show();
        }

</script>
<div class="pp-bigcontainer">
    <span class="top"><span></span></span>
    <table>
        <tr>
            <td valign="top" style="vertical-align:top; width: 100%;" width="850px">
                <div id="panels">
                    <div class="mainheadpad">
                        <h1 class="maintitle">Administrar Equipos</h1>
                    </div>
                    <div class="maincontent">
                        <h1>Usuarios del Proyecto</h1>
                        <div id="usuario_dg"></div>
                        <br/>

                        <h1>Equipos del Proyecto</h1>
                        <div id="panels">
                            <div class="mainheadpad">
                                <h1 class="maintitle">Acciones Entre Equipo Y Usuario</h1>
                            </div>
                            <div class="maincontent">
                                <b>Nombre Del Equipo:</b> <input type="text" id="equipo_txt" size="25" />
                                <input type="button" value="Crear Equipo" id="equipo_btn" />
                                <br/>Relacionar, el Usuario <b>
                                    <input type="text" value="" disabled="true" id="_us_dg" />
                                    <input type="hidden" value="" id="_idUs"/>
                                </b> con el Equipo <b>
                                    <input type="text" value="" disabled="true" id="_team_dg" />
                                    <input type="hidden" value="" id="_idTeam"/>
                                </b>
                            </div>
                            <div class="myline" style="margin-top:10px"></div>
                        </div>


                        <div id="equipo_dg"></div>
                    </div>
                    <div class="myline" style="margin-top:10px"></div>
                </div>
                <div class="clearfix"></div>
            </td>
            <td valign="top">
                
            </td>
        </tr>
    </table>
    <span class="bottom"><span></span></span>
</div>
