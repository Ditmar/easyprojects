<script type="text/javascript" language="javascript">
    var _global_store;
    Ext.namespace("nexus.dashboard.mensajes");
    nexus.dashboard.mensajes={
        init:function(){
            var store=new Ext.data.JsonStore({
                url:"/index.php/message/getMessages/",
                root:"rows",
                totalProperty:"totalCount",
                fields:['id','subject','create_at','readed','idRem','avatar','nick','nombre','apellido','mensaje']
            });
            store.load({params:{z:30,start:0,limit:20}});
            _global_store=store;
            var pagin=new Ext.PagingToolbar({
                store: store, // <--grid and PagingToolbar using same store (required)
                displayInfo: true,
                displayMsg: '{0} - {1} of {2} Tweets',
                emptyMsg: 'no Hay Mensajes para mostrar',
                pageSize: 30,
                baseParams: {x:10,y:20}
            });
            var grid=new Ext.grid.GridPanel({
                store:store,
                width:650,
                height:400,
                autoScroll:true,
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"id",dataIndex:"id",sortable:true,width:20},
                    {header:"Enviado por",dataIndex:"idRem",width:150,renderer:this.remitente},
                    {header:"Subject",dataIndex:"subject",width:150,renderer:this.subject},
                    {header:"Enviado él  ",dataIndex:"create_at",width:150,renderer:this.send},
                    {header:"readed",dataIndex:"readed",width:100,renderer:this.readed},
                    {header:"Borrar",dataIndex:"nombre",renderer:this.action}
                ],
                bbar:pagin,
                border: false,
                layout:"fit",
                stripeRows: true,
                buttons:[{text:"Nuevo",handler:function(){
                            var form=new Ext.FormPanel({
                                url:"/index.php/message/insert",
                                id:"_messajes_dashboard",
                                defaults:{xtype:'textfield',width:200},
                                border:true,
                                bodyStyle:'padding: 10px',
                                items:[
                                    {
                                        fieldLabel:"Subject",
                                        name:'subject',
                                        minLength:4,
                                        maxLength:50,
                                        allowBlank:false
                                    },{
                                        fieldLabel:"Para:",
                                        name:'sendNick',
                                        minLength:4,
                                        maxLength:50,
                                        allowBlank:false
                                    },{
                                        xtype:"textarea",
                                        fieldLabel:"Message",
                                        name:'mensaje',
                                        minLength:4,
                                        maxLength:1000,
                                        allowBlank:false,
                                        width:300,
                                        height:100
                                    }
                                ]
                            });
                            var win=new Ext.Window(
                            {
                                title:"Enviar Información",
                                items:[form],
                                modal:true,
                                width:500,
                                buttons:[{text:"Enviar",handler:function(){
                                            if(form.getForm().isValid()){
                                                var mask = new Ext.LoadMask(Ext.get('_messajes_dashboard'), {msg:'Enviando...'});
                                                mask.show();
                                                form.getForm().submit({
                                                    method: 'post',
                                                    success: function(form,action){
                                                        mask.hide();
                                                        Ext.Msg.alert('Success',action.result.msg);
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
                                        }}]
                            }
                        );
                            win.show();
                        }},{text:"Enviados",handler:function(){
                            alert("enviados");
                        }}],
                renderTo:"_grid_view_messages"
            });
        },
        remitente:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            var str="<img src='/uploads/"+record.get("avatar")+"' title='"+record.get("nick")+"' alt='"+record.get("nick")+"'></img>";
            str+="<br/><b>Enviado por: "+record.get("nombre")+" "+record.get("apellido")+"</b>";
            return str;
        },
        subject:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            //return value;
            return "<a href='javascript:ventana("+rowIndex+")'>"+value+"</a>";
        },
        send:function(value, metaData, record, rowIndex, colIndex, store){

            return value;
        },
        readed:function(value, metaData, record, rowIndex, colIndex, store){
            //alert(value);
            if(value=='0'){
                return "<img src='/iconos/messajes/email.png' title='Sin leer' alt='Sin leer'></img>";
            }else{
                return "<img src='/iconos/messajes/email_open.png' title='Sin leer' alt='Leido'></img>";
            }
        },
        action:function(value, metaData, record, rowIndex, colIndex, store){

            return "<a href='javascript:borrar("+rowIndex+")'>Borrar</a>";
        }
    }
    Ext.onReady(nexus.dashboard.mensajes.init,nexus.dashboard.mensajes);
    function ventana(id){
        //updateReaded
        var record=_global_store.getAt(id);
        var index=record.get("id");
        var _html="<table><tr><td><img src='/uploads/"+record.get("avatar")+"'></img></td><td><b>Subject: </b> "+record.get("subject")+"</br>";
        _html+="<b>Enviado por: </b> "+record.get("nick")+"</br></td></tr>";
        _html+="<tr><td colspan='2'><b>Mesaje: </b> "+record.get("mensaje")+"</br></td></tr></table>";
        var win=new Ext.Window({
            title:"Mensaje",
            width:400,
            height:400,
            html:_html

        });

        win.show();
        $.post("/index.php/message/updateReaded",{id:index},function(data){
            if(data.success){
                _global_store.reload({params:{z:30,start:0,limit:20}});
            }
        },"json");
        /*
         *datos posibles
         *accion-> ___type
         *parametro-> parameter_messages_panel
         *acepted_btn_panel
         *refushed_btn_panel
         **/
        if($("#___type").val()=="addfriend"){
            //registramos los eventos
            $("#acepted_btn_panel").click(function(){
                $.post("/index.php/friends/setFriends",
                {"idFr":$("#parameter_messages_panel").val(),
                    "follow":true,
                    "response":"response"
                },function(data){
                    if(data.success){
                        Ext.MessageBox.alert("Success","Accion Correcta =)");
                        Ext.Ajax.request({
                            url: '/index.php/friends/getFriends',
                            params: {}, //solicitamos todos los registros
                            method: 'post', //utilizando el método GET
                            scope: this,
                            success: _friends_global.createTopTen //e indicamos la función que procesará la respuesta
                        });
                    }
                },"json");
            });
            $("#refushed_btn_panel").click(function(){

            });
        }
        //alert($("#___type").val());
    }
    function borrar(id){
        var record=_global_store.getAt(id);
        var index=record.get("id");

        Ext.MessageBox.confirm("Alerta!","Esta seguro que quiere borrar este mensaje",function(data){
            if(data=="yes"){

                $.post("/index.php/message/delete",{id:index},function(data){
                    if(data.success){
                        _global_store.reload({params:{z:30,start:0,limit:20}});
                    }
                },"json");
            }
        });
    }

</script>
<div id="_grid_view_messages">

</div>