<script type="text/javascript" language="javascript">
    var _friends_global;
    var _friends_ids=new Array();
    nexus.dashboard.friends = {
        init: function(){
            _friends_global=this;
            //Get the information
            var _root=this;
            
            var panel=new Ext.Panel({
                tbar:[
                    {text:"Refrescar"},
                    {text:"Add",handler:function(){
                            var components='<div id="_quest_panel_fr"><input type="text" id="_questtext" size="40" value="Buscar " /><input type="button" id="_click_quest_fr" value="Buscar Usuario"/><div id="render_grid_quest"></div></div>';
                            $("#buscar_datos_fr").html(components);
                            var __store=new Ext.data.JsonStore({
                                url:"/index.php/friends/searchFriend/",
                                root:"rows",
                                totalProperty:"totalCount",
                                fields:['id','avatar','nombre','apellido','email']
                            });


                            var grid=new Ext.grid.GridPanel({
                                store:__store,
                                width:400,
                                height:400,
                                autoScroll:true,
                                columns:[
                                    new Ext.grid.RowNumberer(),
                                    {header:"id",dataIndex:"id",sortable:true,width:20},
                                    {header:"Avatar",dataIndex:"avatar",width:120,renderer:_root.avatar},
                                    {header:"Nombre",dataIndex:"nombre",width:200,renderer:_root.nombre},
                                    {header:"Actions",dataIndex:"email",width:60,renderer:_root.actions}
                                ],
                                border: false,
                                layout:"fit",
                                stripeRows: true
                            });
                            var quest=new Ext.Window({
                                title:"Panel de Busqueda",
                                contentEl:"_quest_panel_fr",
                                width:410,
                                height:300
                            });
                            quest.show();
                            $("#buscar_datos_fr").show("slow");
                            grid.render("render_grid_quest");
                            $("#_questtext").keyup(function(){
                                __store.load({params:{stringquest:$("#_questtext").val()}});
                            });
                            $("#_click_quest_fr").click(function(){
                                
                            });
                            $("#_questtext").focus(function(){
                                $("#_questtext").attr("value","");
                            });
                        }}
                ],
                renderTo:"_options_panel_Fr"
            });
            Ext.Ajax.request({
                url: '/index.php/friends/getFriends',
                params: {}, //solicitamos todos los registros
                method: 'post', //utilizando el método GET
                scope: this,
                success: this.createTopTen //e indicamos la función que procesará la respuesta
            });

            $("#buscar_datos_fr").hide();


        },

        createTopTen: function(response){
            //alert(response.responseText);
            //create the main list
            var info = Ext.decode(response.responseText); //decodificamos el texto que recibimos
            if(info.data.length>0){
                $("#content_friends").html("");
                Ext.each(info.data,function(movie){ //iteramos la información recibida
                    //alert(movie.avatar)
                    
                    _friends_ids.push(movie.id);
                    Ext.DomHelper.append('content_friends',{ //y creamos una imagen para cada elemento
                        tag:'img',
                        src:"/uploads/"+movie.avatar,
                        alt:movie.title,
                        title:movie.title,
                        cls: 'thumb',
                        id:"img_users"+movie.avatar
                    });
                    Ext.get("img_users"+movie.avatar).on("click",function(){
                        
                    })
                },this);
            }
        },

        showDetail: function(id){
            //create and load the form
        },
        avatar:function(value, metaData, record, rowIndex, colIndex, store){
            return"<img src='/uploads/"+value+"' title='"+record.get("nick")+"' class='thumb'/>";
        },
        nombre:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            var str="<b>Nombre</b> "+value+"<br/>"+record.get("apellido");
            return str;
        },
        actions:function(value, metaData, record, rowIndex, colIndex, store){
            if(_friends_global.buscar(record.get("id"))){
                return "<img src='/img/accept.png' title='Es Tú amigo'/> <br/>Es tu amigo";
            }
            return "<a href='javascript:addUser("+record.get("id")+")'><img src='/images/team/user_add.png' title='Agregar Usuario'/></a>";
        },
        buscar:function(id){
            for(var i=0;i<_friends_ids.length;i++){
                if(_friends_ids[i]==id){
                    return true;
                }
            }
            if(id==_id_usuario){
                return true;
            }
            return false;
        }
    }
    function addUser(id){
        Ext.MessageBox.confirm("Agregar","¿Desea agregar de amigo a este usuario?",function(data){
            if(data=="yes"){
                $.post("/index.php/friends/setFriends/",{"follow":true,"idFr":id},function(data){
                    if(data.success){
                        Ext.MessageBox.alert("Success",data.messaje);
                        Ext.Ajax.request({
                            url: '/index.php/friends/getFriends/',
                            params: {}, //solicitamos todos los registros
                            method: 'post', //utilizando el método GET
                            scope: this,
                            success: _friends_global.createTopTen //e indicamos la función que procesará la respuesta
                        });
                    }
                },"json");
            }
        });
    }
    Ext.onReady(nexus.dashboard.friends.init,nexus.dashboard.friends);

</script>
<div id="_options_panel_Fr">

</div>
<div id="content_friends">
</div>
<div id="buscar_datos_fr">

</div>

