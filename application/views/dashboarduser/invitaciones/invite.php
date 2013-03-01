<html>
    <head>
        <title></title>

        <style type="text/css">
            #title_css{
                color:#000000;
                padding:10px;
            }
        </style>
        <script type="text/javascript">
            //cremaos el contenedor de información
            Ext.onReady(function(){
                var titulos="";
                $("#_all").click(function(){
                    loaderGrid('all');

                });
                $("#_new").click(function(){
                    loaderGrid('new');

                });
                $("#_refuse").click(function(){
                    loaderGrid('refuse');

                });
                function loaderGrid(data){
                    //alert("yea")
                    if(data=="all"){
                        $("#message").html("<br/>Todas las invitaciones<br/>");
                    }
                    if(data=="new"){
                        $("#message").html("<br/>Invitaciones Nuevas<br/>");
                    }
                    if(data=="refuse"){
                        $("#message").html("<br/>Invitaciones que rechazaste<br/>");
                    }
                    
                    $("#grid_view").html("");
                    var ss=new Ext.data.JsonStore({
                        id:'id'
                        ,totalProperty:'totalCount'
                        ,root:'bandeja'
                        ,remoteSort: true
                        ,fields:[
                            {name:'readed',type:'bool' }
                            ,{name:'title',type:'string'}
                            ,{name:'fecha',type:'date'}
                            ,{name:'avatar',type:"string"}
                        ],
                        proxy:new Ext.data.HttpProxy({
                            url:"/index.php/dashboarduser/bandejaInvitaciones/"+data
                        })

                    });
                    var grid=new Ext.grid.GridPanel({
                        store:ss,
                        columns:[
                            {header:'Leido',width:25,sortable:true,dataIndex:'readed',renderer:email}
                            ,{header:'Titulo',width:230,sortable:true,dataIndex:'title',renderer:titleFuncition}
                            ,{header:'Fecha',width:100,sortable:true,dataIndex:'fecha'}
                            ,{header:'Usuario',width:130,sortable:true,dataindex:'avatar',renderer:avatar}
                        ],
                        viewConfig:{
                            forceFit:true
                        },
                        height:300,
                        width:450,
                        title:"Bandeja de Entrada (Invitaciones)",
                        bbar: new Ext.PagingToolbar({
                            pageSize: 5,
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
                    grid.render("grid_view");
                    
                    ss.load({params:{start:0, limit:6}});
                }

                /*$("#_btn_acepted").click(function(){
                    alert("holas");
                });*/
                /*
                 *creamos un objeto de tipo JsonStore
                 *que se encargara de llenar la información para posteriormente
                 *sea mostrado en el grid
                 **/
                var store=new Ext.data.JsonStore({
                    id:'id'
                    ,totalProperty:'totalCount'
                    ,root:'bandeja'
                    ,remoteSort: true
                    ,fields:[
                        {name:'readed',type:'bool' }
                        ,{name:'title',type:'string'}
                        ,{name:'fecha',type:'date'}
                        ,{name:'avatar',type:"string"}
                    ],
                    proxy:new Ext.data.HttpProxy({
                        url:"/index.php/dashboarduser/bandejaInvitaciones/"
                    })

                });
                //store.setDefaultSort('lastpost', 'desc');
                /*
                 *funcion llamada por Renderer de Leido
                 **/
                function email(val){
                    if(!val){
                        return "<img src='/images/dashboard/email.png' alt='Sin leer'/>";
                    }
                    return "<img src='/images/dashboard/email_open.png' alt='Sin leer'/>";

                }
                function avatar(val){
                    return "<img src="+val+" alt='avatar'/>";
                }

                function titleFuncition(val){
                    var n=10;
                    var acepted=val.split("3mo3D");
                    var cadena=acepted[0].split(" ");
                    var cad="";
                    for(var i=1;i<=cadena.length;i++){
                        if(i>1)
                            cad+=" "+cadena[i-1];
                        if(i%6==0){
                            cad+="<br/>";
                        }

                    }
                    var respuesta;
                    if(acepted[1]=="nocontest"){
                        respuesta="No ha respondido";
                    }
                    if(acepted[1]=="refuse"){
                        respuesta="Ha Rechazado esta invitación";
                    }
                    if(acepted[1]=="acepted"){
                        respuesta="Ha Aceptado esta invitación";
                    }
                    //alert(cadena.length/2+"");
                    return "<div id='title_css'><a href='javascript:onclickLink("+cadena[0]+")'>"+cad+"<br/> "+respuesta+"</a></div>";
                }

                loaderGrid("all")
                //store.load({params:{start:0, limit:6}});

            });
            function createPanel(titles,urlLoad){
                var win=new Ext.Window({
                    title:titles,
                    width:500,
                    height:600,
                    modal:true,
                    maximizable:true,
                    collapsille:true,
                    autoLoad: {url: urlLoad, scripts: true},
                    tbar:[{
                            text:'Bandeja de Entrada'
                            ,handler:function() {
                                win.load(win.autoLoad.url);
                            }
                        }]
                });
                win.show();
            }
            function onclickLink(id){
                createPanel("Titulos","/index.php/dashboarduser/renderMessage/"+id);
            }
        </script>
    </head>
    <body>
        <input type="button" value="Listar  Todas" id="_all"/>
        <input type="button" value="Listar Invitaciones Recientes" id="_new" />
        <input type="button" value="Listar invitaciones rechazadas" id="_refuse" />
        <div id="message"></div>
        <div id="grid_view"></div>
    </body>
</html>