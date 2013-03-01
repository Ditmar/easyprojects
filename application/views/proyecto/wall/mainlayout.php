<script type="text/javascript">
    var father;
    var _top="post0";
    var n=0;
    var _coment="";
    var _rc=0;
    var _thelastcoment=new Array();
    var _stado=false;
    var _arraycomentids=new Array();
    var _globalId="";
    var __k;
    var __i;
    Ext.namespace("nexus.project.wall");
    nexus.project.wall={
        init:function(){
            _stado=false;
            father=this;
            _top="post0";
            n=0;
            _coment=""
            _rc=0;
            _thelastcoment=new Array();
            _arraycomentids=new Array();
            this.form=new Ext.FormPanel({
                defaults:{xtype:'textarea',width:200},
                border:false,
                url:"/index.php/wall/submitwall",
                bodyStyle:'padding: 10px',
                items:[{
                        fieldLabel:'Titulo',
                        name:"title",
                        id:"_titles",
                        xtype:"textfield",
                        allowBlanck:false
                    },{
                        fieldLabel:'Mensage', // creamos un campo
                        name:'cuerpo', // a partir de una
                        id:"w_message",
                        width:350,
                        height:100,
                        allowBlank:false,
                        maxLength:5000
                    },
                    {                     
                        xtype:'hidden',//<-- campo oculto (hidden)  
                        name:'nick', //el nombre con que se envia al servidor
                        value:'nick'
                    }
                ]
            });
            this.panel=new Ext.Panel({
                id:"we_form",
                title:"Escribir En la pared",
                width:500,
                collapsible:true,
                collapsed:true,
                bodyStyle:'padding: 10px',
                items:[this.form],
                buttons:[{text:"Enviar",handler:this.sendData,scope:this}],
                renderTo:"w_form"
            });
            Ext.Ajax.request({
                url:"/index.php/proyecto/getallwall",
                method:"post",
                scope:this,
                success:this.setInfoWall
            });
        },
        sendcoment:function(form){
            var id=form.id.substring(3,form.id.length);
            var ids=form.id.substring((form.id.length-1),form.id.length);

            var idname="comentform"+form.id.substring(3,form.id.length);
            var formdata=Ext.getCmp(idname);
            if(!formdata.getForm().isValid()){
                Ext.Msg.alert('Invalido','Existen Campos invalidos');
            }else{
                var mask = new Ext.LoadMask(Ext.get(id), {msg:'Salvando Porfavor espera'});
                mask.show();
                formdata.getForm().submit({
                    method:"post",
                    success:function(form,action){
                        var data=action.result.data;
                        //console.debug(ids);
                        if(idname.substring(0,(idname.length-1))!="comentformnewcoment_panel")
                        {
                            ids=Number(Number(ids)+Number(n));
                        }
                        else
                        {
                            ids=Number((Number(n)-1)-Number(ids));
                        }
                        mask.hide();
                        if(!Ext.get(_thelastcoment[ids])){
                            this.divvar="coments_"+ids;
                            if(_stado){
                                this.divvar=_thelastcoment[0];

                            }

                            this.coments= Ext.DomHelper.append(this.divvar,{
                                id:"new_comentarios"+_rc+ids,cls:"coment clearfix",tag:"div",children:[
                                    {
                                        tag:"div",
                                        cls:"info",
                                        id:"new_coment_"+_rc,
                                        children:[{tag:"div",id:"new_c"+_rc },{tag:"div",cls:"smalltext",html:"<b>Fecha:</b> "+data["fecha"]+" <b>Hora:</b> "+data["hora"]+"<br/>"+data["comentario"]}]
                                    },
                                    {tag:"div",cls:"tns",
                                        html:"<div><img src='/uploads/"+data["avatar"]+"' alt='chao'/></div>"
                                    }
                                ]
                            },true);
                            var _array_data=["update","delete"];
                            for(var i=0;i<_array_data.length;i++){
                                this.coments=Ext.DomHelper.append("new_c"+_rc,{
                                    tag:"a", id:"new_lini_"+i, href:"#", html:_array_data[i]+"|"
                                },true);
                            }
                        }else{
                            //console.debug(_thelastcoment[ids]);
                            Ext.DomHelper.insertAfter(_thelastcoment[ids],
                            {id:"new_comentarios"+_rc+ids,cls:"coment clearfix",tag:"div",children:[
                                    {
                                        tag:"div",
                                        cls:"info",
                                        id:"new_coment_"+_rc,
                                        children:[{tag:"div",id:"new_c"+_rc },{tag:"div",id:"_newbody_com"+_rc,cls:"smalltext",html:"<b>"+data["nick"]+"</b> <b>Fecha:</b> "+data["fecha"]+" <b>Hora:</b> "+data["hora"]+"<br/>"+data["comentario"]}]
                                    },
                                    {tag:"div",cls:"tns",
                                        html:"<div><img src='/uploads/"+data["avatar"]+"' title='"+data["nick"]+"' alt='chao' class='thumb'/></div>"
                                    }
                                ]
                            },true);
                            var _array_data=["update","delete"];
                            for(var i=0;i<_array_data.length;i++){
                                this.coments=Ext.DomHelper.append("new_c"+_rc,{
                                    tag:"a", id:"new_lini_"+i+"_"+_rc, href:"#", html:_array_data[i]+"|"
                                },true);
                            }
                        }
                        Ext.getCmp(id).destroy();
                        _thelastcoment[ids]="new_comentarios"+_rc+ids;
                        _arraycomentids[_rc]=data["id"];
                        //update

                        var mm=Ext.get("new_c"+_rc);
                        mm.fadeOut({duration:0.1});
                        $("#new_comentarios"+_rc+ids).hover(function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-2)];
                            //alert(""+id)
                            Ext.get("new_c"+id).fadeIn({duration:0.2})
                        },function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-2)];
                            Ext.get("new_c"+id).fadeOut({duration:0.2})
                        });

                        $("#new_lini_"+0+"_"+_rc).click(function(){
                            ids=this.id.split("_");
                            _globalId="_newbody_com";
                            __k=ids[(ids.length-1)];
                            __i="";
                            father.createUpdateForm(_arraycomentids[ids[(ids.length-1)]]);
                        });
                        //delete
                        $("#new_lini_"+1+"_"+_rc).click(function(){
                            //new_comentarios
                            ids=this.id.split("_");
                            _globalId=_thelastcoment[ids[(ids.length-1)]];
                            __k="";
                            __i="";
                            father.deleteComent(_arraycomentids[ids[(ids.length-1)]]);
                            //alert(_arraycomentids[ids[(ids.length-1)]]);
                        });
                        //for(i=0;i<_array_data.length;i++){


                        //}
                        _rc++;

                    },
                    failure:function(form,action){

                    }
                })
            }
        },
        deleteComent:function(id){
            Ext.MessageBox.confirm("Alerta!","¿Desea borrar el comentario seleccionado?",function(data){
                if(data=="yes"){
                    $.post("/index.php/wall/deleteComent",{id:id},function(data){
                        if(data.success){
                            //alert("in "+_globalId+__k+__i);
                            //var el=Ext.get(_globalId+__k+__i);
                            //el.ghost("t");
                            $("#"+_globalId+__k+__i).hide("slow");
                        }
                    },"json");
                }
            });
        },
        sendData:function(){
            if(!father.form.getForm().isValid()){
                Ext.Msg.alert('Invalido','Existen Campos invalidos');
            }else{
                var mask = new Ext.LoadMask(Ext.get('we_form'), {msg:'Salvando Porfavor espera'});
                mask.show();
                father.form.getForm().submit({
                    method: 'post',
                    success: function(form,action){
                        var data=action.result.data;
                        if(!Ext.get(_top)){
                            Ext.DomHelper.append("content",{
                                id:"post0",tag:"div",cls:"post"
                            },true);
                            _top="post0";
                        }
                        Ext.DomHelper.insertBefore(_top,{
                            id:"new"+n,tag:"div",cls:"post",
                            children:[
                                {id:"_avatar",tag:"div",children:[
                                        {tag:"img",src:"/uploads/"+data["avatar"]}
                                    ]
                                },
                                {
                                    cls:"title", tag:"h1",
                                    children:[
                                        {tag:"a",href:"#",html:data["nick"]}
                                    ]
                                },
                                {
                                    id:"title_d_system"+n,cls:"byline", tag:"p", html:"<b>Fecha</b> "+data["fecha"]+"<b>Hora:</b> "+data["hora"]+" "+data["titulo"]
                                },
                                {
                                    cls:"entry", tag:"div",
                                    children:[
                                        {
                                            id:"body_d_system"+n,tag:"p",html:data["cuerpo"]
                                        },
                                        {cls:"menu2",id:"newmenu_"+n,tag:"ul",
                                            children:[
                                                {tag:"li",children:[{tag:"a",id:"_newbtn_c"+n,href:"#comentario",html:"<b>comentar</b>"}]},
                                                {tag:"li",children:[{tag:"a",id:"_likeme_c"+n,href:"#megusta",html:"<b>Me Gusta</b>"}]},
                                                {tag:"li",children:[{tag:"a",id:"_update_c"+n,href:"#borrar",html:"<b>Update</b>"}]},
                                                {tag:"li",children:[{tag:"a",id:"_eraser_c"+n,href:"#borrar",html:"<b>Borrar</b>"}]}
                                            ]
                                        },
                                        {id:"newcoments_"+n,tag:"div"}

                                    ]
                                }
                            ]});
                        mask.hide();
                        Ext.get("new"+n).fadeIn();
                        var el=Ext.get("newmenu_"+n);
                        el.fadeOut({duration:0.1});
                        $("#new"+n).hover(function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-1)];
                            Ext.get("newmenu_"+id).fadeIn({duration:0.2});
                        },function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-1)];
                            Ext.get("newmenu_"+id).fadeOut({duration:0.2});
                        });
                        $("#_update_c"+n).click(function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-1)];
                            father.updateNews("title_d_system"+id,"body_d_system"+id,data["id"]);
                        });
                        $("#_eraser_c"+n).click(function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-1)];
                            father.deletenew("new"+id,data["id"]);
                        });
                        $("#_likeme_c"+n).click(function(){
                        });
                        $("#_newbtn_c"+n).click(function(){
                            var cad=this.id.split("");
                            var id=cad[(cad.length-1)];
                            father.createPanelComent("newcoments_"+id,"newcoment_panel"+id,data["id"]);
                        });
                        _stado=true;
                        _top="new"+n;

                        var aux=new Array();
                        aux.push("newcoments_"+n);
                        for(var p=0;p<_thelastcoment.length;p++){
                            aux.push(_thelastcoment[p]);
                        }

                        _thelastcoment=aux;
                        n++;
                        //mostramos la nueva informacion inmediatamente
                    },
                    failure: function(form,action){
                        mask.hide();
                        switch (action.failureType) {
                            case Ext.form.Action.CLIENT_INVALID:
                                Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
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
        },
        createPanelComent:function(render,name,id){
            if(Ext.getCmp(name)){
                Ext.getCmp(name).destroy();
            }
            if(!Ext.getCmp(name)){
                this.formsend=new Ext.FormPanel({
                    defaults:{xtype:'textarea',width:200},
                    border:false,
                    id:"comentform"+name,
                    url:"/index.php/wall/submitcoment",
                    bodyStyle:'padding: 10px',
                    items:[{
                            fieldLabel:'Mensaje', // creamos un campo
                            name:'coment', // a partir de una
                            id:"form"+name,
                            width:350,
                            height:100,
                            allowBlank:false,
                            maxLength:2000
                        },{
                            xtype:'hidden',//<-- campo oculto (hidden)
                            name:'idNoti', //el nombre con que se envia al servidor
                            value:id
                        }]
                });
                this.coment=new Ext.Panel(
                {
                    id:name,
                    width:500,
                    title:"Comentar!",
                    collapsible:true,
                    collapsed:false,
                    items:[this.formsend],
                    renderTo:render,
                    autoDestroy:true,
                    buttons:[{id:"btn"+name,text:"Enviar",handler:this.sendcoment,scope:this}]
                }

            );
                //comentsPanel.push(this.coment);
            }
        },
        createUpdateForm:function(id){
            var form = new Ext.form.FormPanel({
                url: '/index.php/wall/fillform', //la URL de donde vamos a llenar el formulario
                id:"_updateForm",
                border:false,
                labelWidth: 80,
                defaults: {
                    xtype:'textfield',
                    width: 150
                },
                items:[
                    {xtype:'textarea',fieldLabel:'Comment',name:'comentario',width:200,height:250},
                    {xtype:'hidden',name:'id',value:id}
                ]
            });
            var win = new Ext.Window({
                title: 'Información a actualizar',
                bodyStyle: 'padding:10px;background-color:#fff;',
                id:"_floatwindow",
                width:330,
                modal:true,
                height:270,
                items:[form],
                buttons: [{text:'Actualizar',handler:this.updateForm},{text:'Cancelar'}]
            });
            win.show();
            form.getForm().load({params:{id:id,action:"fillform"}});
        },
        megusta:function(data1,data2,id){
            $.post("/index.php/wall/submitcoment",{idNoti:id,coment:"Le a gustado el comentario"},
            function(data){

            },"json");
        },
        deletenew:function(content,id){
            Ext.MessageBox.confirm("Alerta!","Desea borrar la noticia selecionada ? ",function(data){
                if(data=="yes"){
                    $.post("/index.php/wall/deleteNew",{id:id},function(data){
                        if(data.success){
                            //var el=Ext.get(content);
                            //el.ghost("t");
                            $("#"+content).hide("slow");
                        }else{
                            Ext.MessageBox.alert("Alerta",data.message);
                        }
                    },"json");
                }
            });

        },
        updateNews:function(title,bodysystem,id){
            var form = new Ext.form.FormPanel({
                url: '/index.php/wall/fillnew', //la URL de donde vamos a llenar el formulario
                id:"_updateNewForm",
                border:false,
                labelWidth: 80,
                defaults: {
                    xtype:'textfield',
                    width: 150
                },
                items:[
                    {fieldLabel:'Titulo',name:"titulo",width:200},
                    {xtype:'textarea',fieldLabel:'Mensaje',name:'cuerpo',width:200,height:250},
                    {xtype:'hidden',name:'id',value:id}
                ]
            });
            var win = new Ext.Window({
                title: 'Actualizar Noticia',
                bodyStyle: 'padding:10px;background-color:#fff;',
                id:"_floatwindow",
                width:430,
                modal:true,
                height:3,
                items:[form],
                buttons: [{text:'Actualizar',handler:function(){
                            var mask = new Ext.LoadMask(Ext.get('_floatwindow'), {msg:'Actualizando la Información'});
                            mask.show();
                            form.getForm().submit({
                                method: 'post',
                                success:function(form,action){
                                    var data=action.result.data;
                                    //alert(data);
                                    mask.hide();
                                    Ext.getCmp("_floatwindow").close();
                                    //id:"_title_system"+i,cls:"byline", tag:"p", html:"<b>Fecha</b> "+info.data[i]["noticia"]["fecha"]+"<b>Hora:</b> "+info.data[i]["noticia"]["hora"]+" "+info.data[i]["noticia"]["titulo"]
                                    //id:"_body_system"+i,tag:"p",html:info.data[i]["noticia"]["cuerpo"]
                                    //alert("_body_system"+i);
                                    //label
                                    Ext.DomHelper.overwrite(title,{
                                        children:[{cls:"byline", tag:"p", html:"<b>Fecha</b> "+data["updatedate"]+"<b>Hora:</b> "+data["updatetime"]+" "+data["titulo"]}]
                                    },true);

                                    Ext.DomHelper.overwrite(bodysystem,{
                                        tag:"p",html:data["cuerpo"]
                                    },true);
                                },
                                failure: function(form,action){

                                }
                            });
                        }},{text:'Cancelar'}]
            });
            win.show();
            form.getForm().load({params:{id:id,action:"fillnew"}});
        },
        updateForm:function(id){
            var form=Ext.getCmp("_updateForm");
            var mask = new Ext.LoadMask(Ext.get('_floatwindow'), {msg:'Actualizando la Información'});
            mask.show();
            form.getForm().submit({
                method: 'post',
                success:function(form,action){
                    var data=action.result.data;
                    //alert(data);
                    mask.hide();
                    Ext.getCmp("_floatwindow").close();

                    Ext.DomHelper.overwrite(_globalId+__k+__i,{
                        children:[{tag:"div",id:"coment_"+__k+__i },{tag:"div",cls:"smalltext",html:"<b>Fecha:</b> "+data["fecha"]+" <b>Hora:</b> "+data["hora"]+" <b>Update:</b> "+data["updatedate"]+"<b>Horas:</b>"+data["updatedate"]+"<br/>"+data["comentario"]}]
                    },true);
                },
                failure: function(form,action){

                }
            });
        }
        ,
        setInfoWall:function(response){
            var info = Ext.decode(response.responseText);
            for(var i=0;i<info.data.length;i++){
                this.html=Ext.DomHelper.append("content",{
                    id:"post"+i,tag:"div",cls:"post",
                    children:[
                        {id:"_avatar",tag:"div",children:[
                                {tag:"img",src:info.data[i]["noticia"]["avatar"]}
                            ]
                        },
                        {
                            cls:"title", tag:"h1",
                            children:[
                                {tag:"a",href:"#",html:"<h1>"+info.data[i]["noticia"]["titulo"]+"</h1> "}
                            ]
                        },
                        {
                            id:"_title_system"+i,cls:"byline", tag:"p", html:info.data[i]["noticia"]["nick"]+" <b>Fecha</b> "+info.data[i]["noticia"]["fecha"]+"<b>Hora:</b> "+info.data[i]["noticia"]["hora"]
                        },
                        {
                            cls:"entry", tag:"div",
                            children:[
                                {
                                    id:"_body_system"+i,tag:"p",html:info.data[i]["noticia"]["cuerpo"]
                                },
                                {cls:"menu2",id:"menu_"+i,tag:"ul"},
                                {cls:"coment",id:"coments_"+i,tag:"div"}
                            ]
                        }
                    ]},true
            );
                for(var j=0;j<info.data[i]["acciones"].length;j++){
                    var btn=info.data[i]["acciones"];
                    //console.debug(btn);
                    this.html= Ext.DomHelper.append("menu_"+i,{
                        tag:"li",children:[{tag:"a",id:"_btn_c"+i+j,href:"#comentario",html:"<b>"+btn[j]+"</b>"}]
                    },true);

                }
                _thelastcoment[i]="comentarios"+(info.data[i]["comentario"].length-1)+i;
                for(var k=0;k<info.data[i]["comentario"].length;k++){
                    var comentarios=info.data[i]["comentario"];

                    this.coments= Ext.DomHelper.append("coments_"+i,
                    {id:"comentarios"+k+i,cls:"coment clearfix",tag:"div",
                        children:[
                            {
                                tag:"div",
                                cls:"info",
                                id:"_coment_"+k+i,
                                children:[{tag:"div",id:"coment_"+k+i },{tag:"div",id:"_body_com"+k+i,cls:"smalltext",html:"<b>"+comentarios[k]["com"]["nick"]+"</b> <b>Fecha:</b>"+" "+comentarios[k]["com"]["fecha"]+" <b>Hora:</b> "+comentarios[k]["com"]["hora"]+"<br/>"+comentarios[k]["com"]["comentario"]}]
                            },
                            {tag:"div",cls:"tns",
                                html:"<div><img src='/uploads/"+comentarios[k]["com"]["avatar"]+"' alt='chao' title='"+comentarios[k]["com"]["nick"]+"' class='thumb'/></div>"
                            }
                        ]
                    }
                    ,true);
                    for(var l=0;l<comentarios[k]["actions"].length;l++){
                        this.coments=Ext.DomHelper.append("coment_"+k+i,{
                            tag:"a", id:"_lini_"+i+k+l, href:"#", html:comentarios[k]["actions"][l]+"|"
                        });
                    }
                    /*
                     *update
                     **/
                    $("#_lini_"+i+k+'0').click(function(){
                        var ii=this.id.substring(6,7);
                        var cc=this.id.substring(7,8);
                        var comId=info.data[ii]["comentario"][cc]["com"]["id"];
                        _globalId="_body_com";
                        __k=cc;
                        __i=ii;
                        father.createUpdateForm(comId);

                        //alert("->"+comId);
                    });
                    /*
                     *delete
                     **/
                    $("#_lini_"+i+k+1).click(function(){
                        var ii=this.id.substring(6,7);
                        var cc=this.id.substring(7,8);
                        var comId=info.data[ii]["comentario"][cc]["com"]["id"];
                        _globalId="comentarios";
                        __k=cc;
                        __i=ii;
                        father.deleteComent(comId);
                    });

                    var mm=Ext.get("coment_"+k+i);
                    mm.fadeOut({duration:0.1});
                    $("#_coment_"+k+i).hover(function(){
                        var cad=this.id.split("");
                        var id=cad[(cad.length-2)]+""+cad[(cad.length-1)];
                        //alert(""+id)
                        Ext.get("coment_"+id).fadeIn({duration:0.2})
                    },function(){
                        var cad=this.id.split("");
                        var id=cad[(cad.length-2)]+""+cad[(cad.length-1)];
                        Ext.get("coment_"+id).fadeOut({duration:0.2})
                    });

                }
                /*
                 *controles
                 **/
                var el=Ext.get("menu_"+i);
                el.fadeOut({duration:0.1});
                $("#post"+i).hover(function(){
                    var cad=this.id.split("");
                    var id=cad[(cad.length-1)]
                    Ext.get("menu_"+id).fadeIn({duration:0.2})
                },function(){

                    var cad=this.id.split("");
                    var id=cad[(cad.length-1)]
                    Ext.get("menu_"+id).fadeOut({duration:0.2})
                    //el.fadeOut({duration:0.2});
                });
                /*
                 *Comentar
                 **/
                $("#_btn_c"+i+0).click(function(){
                    var cad=this.id.split("");
                    var id=cad[(cad.length-2)];
                    father.createPanelComent("coments_"+id,"coment_panel"+id,info.data[id]["noticia"]["id"]);
                });
                $("#_btn_c"+i+1).click(function(){
                    var cad=this.id.split("");
                    var id=cad[(cad.length-2)];
                    father.megusta("coments_"+id,"coment_panel"+id,info.data[id]["noticia"]["id"]);
                });
                $("#_btn_c"+i+2).click(function(){
                    var cad=this.id.split("");
                    var id=cad[(cad.length-2)];
                    father.updateNews("_title_system"+i,"_body_system"+i,info.data[id]["noticia"]["id"]);
                    //alert("Actualizar");
                    /*var cad=this.id.split("");
                    var id=cad[(cad.length-2)];
                    father.createPanelComent("coments_"+id,"coment_panel"+id,info.data[id]["noticia"]["id"]);*/
                });
                $("#_btn_c"+i+3).click(function(){
                    //alert("Borrar");
                    var cad=this.id.split("");
                    var id=cad[(cad.length-2)];
                    father.deletenew("post"+id,info.data[id]["noticia"]["id"]);
                    //father.createPanelComent("coments_"+id,"coment_panel"+id,info.data[id]["noticia"]["id"]);*/
                });

            }

        }
    }
    Ext.onReady(nexus.project.wall.init,nexus.project.wall);
</script>
<div id="w_form">
</div>
<div id="content">
</div>