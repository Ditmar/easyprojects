<script type="text/javascript">
    var _father;
    var _length;
    var _idsRepo=Array()
    var _absolutepath="";
    var _virtuallocalpath="";
    var _pathcreateFolder="";
    var _pathTitleRoute="";
    Ext.namespace("nexus.projects.admin");
    nexus.projects.admin={

        init:function(){

            _father=this;
            this.datos=new Array();
            //this.datos=[$("#_p_information"),$("#_p_miembros"),$("#_p_avatar"),$("#_p_admin"),$("#_p_db"),$("#_p_equipo"),$("#_p_proyecto"),$("#_p_log")];
            this.datos["Information"]=$("#_p_information");
            this.datos["Miembros"]=$("#_p_miembros");
            this.datos["Avatar"]=$("#_p_avatar");
            this.datos["Admin"]=$("#_p_admin");
            this.datos["Base de Datos"]=$("#_p_db");
            this.datos["Proyecto"]=$("#_p_proyecto");
            this.datos["Equipos de Trabajo"]=$("#_p_equipo");
            this.datos["Mi Log"]=$("#_p_log");
            this.center =new Ext.TabPanel({
                id:"_project_container",
                region: 'center', // a center region is ALWAYS required for border layout
                deferredRender: false,
                activeTab: 0,
                closable:false
            });
            this.east = {
                id:"_p_east",
                region:"east",
                width:220,
                split:true,
                collapsible:true,
                title:"East region",
                margins:"0 3 0 3",
                border:false,
                layout:{
                    type:'accordion',
                    animate:true
                }
            };

            var tamheigth=Math.ceil(Ext.getCmp('mainport').getSize().height*0.88);
            this.main = new Ext.Panel({
                border:false,
                renderTo: "_projectPanel",
                layout:"border",
                height:tamheigth,
                items:[this.center,this.east]
            });
            $.post("/index.php/proyecto/getMenuProjects/",{},function(data){
                var father=data.menu.father;
                var sons=data.menu.sons;
                var url=data.menu.url;
                var divfather="";
                _length=father.length;
                for(var i=0;i<father.length;i++){
                    switch(father[i]){
                        case"Information":{
                                divfather="_p_information";
                                break;
                            }
                        case"Miembros":{
                                divfather="_p_miembros";
                                break;
                            }
                        case"Avatar":{
                                divfather="_p_avatar";
                                break;
                            }
                        case"Admin":{
                                divfather="_p_admin";
                                break;
                            }
                        case"Base de Datos":{
                                divfather="_p_db";
                                break;
                            }
                        case"Proyecto":{
                                //_p_equipo
                                divfather="_p_proyecto";
                                break;
                            }
                        case"Equipos de Trabajo":{
                                divfather="_p_equipo";
                                break;
                            }
                        case"Mi Log":{
                                divfather="_p_log";
                                break;
                            }
                    }
                    Ext.getCmp("_p_east").add({

                        contentEl:divfather,
                        title:father[i],
                        border: false,
                        autoScroll:true,
                        iconCls: 'nav'
                    });
                    var html;

                    if(divfather=="_p_information"){
                        html=sons[father[i]][0];
                        _father.datos[father[i]].html(html);

                    }else if(divfather=="_p_miembros"){
                        html=sons[father[i]][0];
                        _father.datos[father[i]].html(html);
                    }else if(divfather=="_p_avatar"){
                        html=sons[father[i]][0];
                        _father.datos[father[i]].html(html);
                    }else{
                        html="<div id='menu4'><ul>";
                        for(var j=0;j<sons[father[i]].length;j++){

                            html+="<li><a href='#' id='"+sons[father[i]][j]+"_"+father[i]+"_"+j+"'>"+sons[father[i]][j]+"</a></li>";
                        }
                        html+="</ul></div>";
                        _father.datos[father[i]].html(html);
                        _father.metodos(father[i],sons,url);
                    }
                    //console.debug(father.datos[i]);


                    Ext.getCmp("_p_east").doLayout();

                }
                _father.createPanel("/index.php/wall/layoutWall","Wall",false);
            },"json");

        },
        metodos:function(indice,sons,url){
            for(var i=0;i<sons[indice].length;i++){
                Ext.get(sons[indice][i]+"_"+indice+"_"+i).on("click",function(){
                    var btninfo=this.id.split("_");
                    //alert("/index.php"+url[btninfo[1]][btninfo[2]]);
                    if(btninfo[0]=="Ir a Editor"){
                        $.post("/index.php/controluser/getTeam",{},function(data){
                            _idsRepo=data.ids;
                            var form=new Ext.FormPanel({
                                id:"form_rep",
                                border:false,
                                bodyStyle:'padding: 10px',
                                items:[
                                    {
                                        id:"combo_repositorios",
                                        xtype:"combo",
                                        store:data.data
                                    }
                                ]
                            });
                            var win=new Ext.Window({
                                id:"_rep_window",
                                title:"Entrar al Repositorio",
                                items:[form],
                                width:270,
                                height:100,
                                buttons:[{text:"entrar",handler:_father.enterToRepositori,scope:this}]
                            });
                            win.show();
                            _pathcreateFolder="/index.php"+url[btninfo[1]][btninfo[2]];
                            _pathTitleRoute=btninfo[0]
                        },"json")
                    }else{
                        _father.createPanel("/index.php"+url[btninfo[1]][btninfo[2]],btninfo[0],true);
                    }
                });
            }
        },
        enterToRepositori:function(){

            //alert(_idsRepo[$("#combo_repositorios").val()]);
            $.post("/index.php/controluser/getWorkSpace",{
                "idEq":_idsRepo[$("#combo_repositorios").val()]
            },function(data){
                if(data.result){
                    _absolutepath=data.info.rutaabs.substring(0,(data.info.rutaabs.length-1));
                    _virtuallocalpath=data.info.path;
                    _father.createPanel(_pathcreateFolder,_pathTitleRoute,true);
                    //alert(data.info.rutaabs);
                }else{
                    alert("Error");
                }
                
            },"json");

        },
        createPanel:function(urlRequest,ti,close){
            if(Ext.get(ti))
                return;
            var pad=new Ext.Panel({
                title:ti,
                id:ti,
                closable:close,
                style:'padding: 5px 5px 5px 5px',
                autoScroll:true,
                autoLoad: {url:urlRequest,scripts:true},
                tbar:[{
                        text:'ReLoad '+ti
                        ,handler:function() {
                            pad.load(pad.autoLoad.url);
                        }
                    }]
            });

            var container=Ext.getCmp("_project_container");


            container.add(pad);
            container.doLayout();
            container.activate(pad);

        }
    }
    Ext.onReady(nexus.projects.admin.init,nexus.projects.admin);
    /*
    **admin user
    */
    function onCheckUserScript(id){
        var win=new Ext.Window({
                title:"Propiedades de Usuario",
       
                width:600,
                height:600,
                modal:true,
                maximizable:true,
                collapsille:true,
                autoLoad: {url:"/index.php/proyecto/loadadminuser/"+id, scripts: true}
            });
            win.show();
        //$.post("/index.php/proyecto/loadadminuser/"+id,{});
    }
</script>
<div>

</div>
<div id="_projectPanel">
</div>
<div id="_p_information"></div>
<div id="_p_miembros"></div>
<div id="_p_avatar">
    aqui viene el menu
</div>

<div id="_p_admin"></div>
<div id="_p_db"></div>
<div id="_p_proyecto"></div>
<div id="_p_equipo"></div>
<div id="_p_log"></div>