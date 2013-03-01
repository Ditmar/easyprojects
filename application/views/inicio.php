<html>
    <head>
        <title>Complex Layout</title>
        <link rel="stylesheet" type="text/css" href="/extjs/resources/css/ext-all.css" />
        <link href="/css/shared/examples.css" type="text/css" rel="stylesheet"/>
        <link href="/css/styles.css" type="text/css" rel="stylesheet"/>
        <link href="/css/menuBox.css" type="text/css" rel="stylesheet"/>
        <link href="/css/xtheme-slickness.css" type="text/css" rel="stylesheet"/>
        <link rel="stylesheet" type="text/css" href="/script/ux/css/Portal.css" />
        <link href="/css/grid-examples.css" type="text/css" rel="stylesheet"/>
        <link href="/extjs/ux/fileuploadfield/css/fileuploadfield.css" type="text/css" rel="stylesheet"/>

        <style type="text/css">

            html, body {
                font:normal 12px verdana;
                margin:0;
                padding:0;
                border:0 none;
                overflow:hidden;
                height:100%;
            }
            p {
                margin:5px;
            }
            .settings {
                background-image:url(../shared/icons/fam/folder_wrench.png);
            }
            .nav {
                background-image:url(../shared/icons/fam/folder_go.png);
            }
        </style>


        <!-- GC -->
        <!-- LIBS -->
        <script type="text/javascript" src="/extjs/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="/script/jquery.js"></script>
        <!-- ENDLIBS -->

        <script type="text/javascript" src="/extjs/ext-all.js"></script>
        <script type="text/javascript" src="/script/ux/RowExpander.js"></script>
        <script type="text/javascript" src="/script/ux/Portal.js"></script>
        <script type="text/javascript" src="/script/ux/PortalColumn.js"></script>
        <script type="text/javascript" src="/script/ux/Portlet.js"></script>
        <!-- Archivo que hace posible la subida de datos -->
        <script type="text/javascript" src="/extjs/ux/fileuploadfield/FileUploadField.js"></script>
        <script language="Javascript" type="text/javascript" src="/edit_area/edit_area_full.js"></script>
        <script language="Javascript" type="text/javascript" src="/edit_area/plugins/test/test.js"></script>
        <script language="Javascript" type="text/javascript" src="/js/ZeroClipboard.js"></script>
        <script language="Javascript" type="text/javascript" src="/js/swfobject.js"></script>



        <script type="text/javascript">
            var _id_usuario="";
            var _id_avatar="";
            Ext.onReady(function(){
                checkSession();
                

                function createLog(){
                    Ext.QuickTips.init();
                    Ext.form.Field.prototype.msgTarget = 'side';
                    
                    var loginform=new Ext.FormPanel(
                    {
                        id:"loginform",
                        defaults:{xtype:'textfield',width:140},
                        border:false,
                        height:240,
                        autoScroll:false,
                        url:"/index.php/usuario/logea",
                        bodyStyle:'padding: 10px',
                        items:[{
                                fieldLabel:'Nick', // creamos un campo
                                name:'_nick', // a partir de una
                                id:"_nick",
                                allowBlank:false,
                                maxLength:30
                            },{
                                fieldLabel:'Password', // creamos un campo
                                name:'_password', // a partir de una
                                id:"_password",
                                inputType:"password",
                                allowBlank:false,
                                maxLength:30
                            }]
                    }
                );
                    var logPanel= new Ext.Window({
                        id:"logform",
                        title: 'Loading data into a form',
                        bodyStyle: 'padding:10px;background-color:#fff;',
                        width:320,
                        height:250,
                        modal:true,
                        items:[loginform],
                        buttons: [{text:'Entrar',handler:onLogInn,scope:this},{text:'Cerrar'}]
                    });
                    logPanel.show();
                }
                function onLogInn(){
                   

                    if(Ext.getCmp("loginform").getForm().isValid()){
                         var mask = new Ext.LoadMask(Ext.get("logform"), {msg:'Consultando la Base de Datos'});
                    mask.show();
                        Ext.getCmp("loginform").getForm().submit({
                            method: 'post',
                            success: function(form,action){
                                /*
                                 *Guardamos ID 
                                 **/
                                mask.hide();
                                if(action.result.msg=="comprobar"){
                                    
                                    var win=new Ext.Window({
                                        title:"Copie el código de seguridad",
                                        width:300,
                                        height:200,
                                        modal:true,
                                        items:[{
                                                fieldLabel:"code",
                                                width:290,
                                                xtype:'textfield',
                                                id:"_code_system_autentication",
                                                allowBlank:false
                                        }],
                                        buttons:[{text:"Enviar",handler:function(){
                                                    $.post("/index.php/usuario/checkCodeAutentication/",
                                                {"code":$("#_code_system_autentication").val()},
                                                function(data){
                                                    if(data.success==true){
                                                        win.close();
                                                        Ext.MessageBox.alert("success","Su cuenta ya esta activada, proceda a logearse");
                                                    }else{
                                                            Ext.MessageBox.alert("Error",data.msg);

                                                    }
                                                },"json");
                                        }}]
                                    });
                                    win.show();
                                }else{
                                    _id_usuario=action.result.id;
                                    _id_avatar=action.result.avatar;
                                    Ext.getCmp("logform").hide();
                                    Ext.Msg.alert('Success',action.result.msg);
                                    //llamamos a los eventos
                                    checkSession();
                                }

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
                    }else{
                        Ext.Msg.alert('Invalido','Existen Campos invalidos')
                    }
                }
                //logPanel.show();
                Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
                /*
                 *Items del recuadro
                 **/
                var myPages;

                var panel=new Ext.Panel(
                {title:"panel",
                    iconCls: 'home-icon',
                    closable:true,
                    style:'padding: 10px 10px 10px 10px',
                    autoLoad: {url: '/index.php/inicio/initpage', scripts: true},
                    tbar:[{
                            text:'Reload'
                            ,handler:function() {
                                win.load(win.autoLoad.url);
                            }
                        }]
                }
            );
                var mainTab=new Ext.TabPanel({
                    id:"containerTapPanel",
                    region: 'center', // a center region is ALWAYS required for border layout
                    deferredRender: false,
                    activeTab: 0,     // first tab initially active
                    items: [panel]
                });
                var _itemslayout=[{
                        region: 'west',
                        id: 'west-panel', // see Ext.getCmp() below
                        title: 'Menu Principal',
                        split: true,
                        width: 270,
                        minSize: 175,
                        maxSize: 400,
                        collapsible: true,
                        margins: '0 0 0 5',
                        layout: {
                            type: 'accordion',
                            animate: true
                        },
                        items: [{
                                contentEl: 'west',
                                title: 'Menu Principal',
                                border: false,
                                iconCls: 'nav' // see the HEAD section for style used
                            }]
                    },
                    // create instance immediately
                    new Ext.BoxComponent(
                    {
                        region: 'north',
                        height: 60, // give north and south regions a height
                        contentEl:'north'
                    }),
                    mainTab];

                var viewport = new Ext.Viewport({
                    id:"mainport",
                    layout: 'border',
                    items:_itemslayout
                });
                // get a reference to the HTML element with id "hideit" and add a click listener to it

                /*
                 *Cargamos el menu principal 
                 **/

                loadMenu();

                function loadMenu(){
                    $.post("/index.php/inicio/getMenu/",{},function(data){
                        var menu=data.menu;
                        //west-panel

                        var html="<div id='menu2'><ul>";

                        for(var i=0;i<menu.length;i++){
                            html+="<li id='main_"+i+"'><a href='javascript:'>"+(menu[i].name)+"</a></li>";
                        }
                        html+="</ul></div>";
                        //alert(html);
                        /*
                         *Colocamos el menu
                         **/
                        $("#west").html(html);
                        /*
                         *registramos los eventos de los botones
                         **/
                        for(i=0;i<menu.length;i++){
                            $("#main_"+i).click(function(){
                                switch(this.id){
                                    //inicio
                                    case"main_0":{
                                            createTabPanel("/index.php/inicio/initpage","Pagina Inicial");
                                            break;
                                        }
                                    //Noticias
                                case"main_1":{
                                            createTabPanel("/index.php/usuario/registro","Registrar Usuarios");
                                        break;
                                    }
                                //proyectos
                            case"main_2":{

                                    break;
                                }
                            //Registrate
                        case"main_3":{
                                
                                break;
                            }
                    }
                });
            }
        },"json");
    }
    /*
     *verificamos el log
     **/
    function checkSession(){

        $.post("/index.php/usuario/checksession",{},function(data){
            if(data.result){
                //llamamos al método que nos carga el menu
                callMenu();

            }
            //alert(_id_avatar);
            _id_usuario=data.id;
            _id_avatar=data.avatar;

            //alert(data.avatar);
            $("#logPanel").html(data.html);

            $("#loginss").click(function(){
                    createLog();
                    
                });
        },"json");
    }
    function callMenu(){
        $.post("/index.php/usuario/loadmenulogin",
        {},
        function(data){
            if(data.roles.length>1){
                Ext.getCmp("west-panel").add({
                    contentEl: 'menu',
                    title: 'Menu Login',
                    border: false,
                    iconCls: 'nav' // see the HEAD section for style used
                })
            }
            
            var menu=data.roles;
            $("#menu").html(menu);
            metodos();
            Ext.getCmp("west-panel").doLayout();
        },"json");
    }
    function metodos(){

        Ext.get("_perfil").on("click",function(){
            createTabPanel("/index.php/usuario/perfil","Perfil de Usuario");
        });
        Ext.get("_dashboard").on("click",function(){
            createTabPanel("/index.php/usuario/dashboard","DashBoard-User");
        });
        Ext.get("_cproyecto").on("click",function(){
            createTabPanel("/index.php/proyecto/crear","Crear Proyectos");
        });
        /*Ext.get("_uproyecto").on("click",function(){

        });*/
        /*Ext.get("_mproyecto").on("click",function(){
            createTabPanel("/index.php/proyecto/misproyectos","Mis Proyectos");
        });*/
        /*Ext.get("_usuario").on("click",function(){

        });
        Ext.get("_contenido").on("click",function(){

        });*/
    }
    $("#_submit").click(function(){
        //agregamos dinamicamente un nuevo row en el menu
        /*Ext.getCmp("west-panel").add({
            contentEl: 'menu',
            title: 'Menu Principal',
            border: false,
            iconCls: 'nav' // see the HEAD section for style used
        })
        Ext.getCmp("west-panel").doLayout();*/
    });
    /*
     *Creación de tablas dinámicamente
     **/
    function createTabPanel(urlRequest,ti){
        var comp=Ext.getCmp(ti);
        if(!Ext.getCmp(ti)){
            var pad=new Ext.Panel({
                id:ti,
                title:ti,
                closable:true,
                style:'padding: 5px 5px 5px 5px',
                autoScroll:true,
                autoLoad: {url:urlRequest,scripts:true}
            });
            var tabs=Ext.getCmp("containerTapPanel");
            tabs.add(pad);
            tabs.activate(pad);
            pad.on("close",function(){                
            });
        }else{
            Ext.getCmp("containerTapPanel").activate(comp);
        }
    }
    /*
     **
     *data es un arreglo de obajetos que tiene
     *propiedaes de crteación
     */
});
function action(){

}
        </script>
    </head>
    <body>
        <!-- use class="x-hide-display" to prevent a brief flicker of the content -->
        <div id="west" class="x-hide-display">
            <p>Hi. I'm the west panel. </p>
        </div>
        <div id="menu" class="x-hide-display">
        </div>


        <div id="props-panel" class="x-hide-display" style="width:200px;height:200px;overflow:hidden;">
        </div>

        <div id="north" class="x-hide-display">
            <table>
                <tr>
                    <td>
                        <img src="/img/logo.png" title="Easy-Projects"/>
                        
                    </td>
                    <td valign="top">
                        <div id="logPanel"> <a href="">EBTRARAASD</a></div>
                    </td>
                </tr>
            </table>
        </div>
    </body>
</html>