<script language="javascript" type="text/javascript">
    //C:/Users/Ditmar/Documents/NetBeansProjects/tesis/projects/blogoficial/tags/Selena/
    //var _absolutepath="C:/Users/Ditmar/Documents/NetBeansProjects/tesis/projects/blogoficial/tags/Selena";
    var _c_father;
    var _top_father=this;
    var _global_var_path;
    var _proyectname="nexus";
    var _copyStore="";
    var _filesmodificate;
    var _lastline=0;
    var _split_store=new Array();
    var _split_remove=new Array();
    //var $j=jQuery.noConflict();
    Ext.ns("nexus.proyecto.editor");

    nexus.proyecto.editor.codeEditor = {

        init: function(){
            Ext.QuickTips.init();
            _c_father=this;
            _filesmodificate=new Array();
            /*
             *Verificamos si eslos tabs estan expadidos de no ser así
             *lo expandimos
             **/
            this.westpanel=Ext.getCmp("west-panel");

            this.east=Ext.getCmp("_p_east");
            if(!this.westpanel.collapsed)
                this.westpanel.collapse();
            if(!this.east.collapsed)
                this.east.collapse();
            this.contextmenu = new Ext.menu.Menu({
                id: '_path_contextMenu',
                items: [
                    {
                        text: 'Crear nueva carpeta',
                        handler:this.createFolder,
                        icon:'/iconos/codeditor/folder_add.png'
                    },{
                        text: 'Crear Archivo',
                        handler:this.createFile,
                        icon:'/iconos/codeditor/page_white_php.png'
                    },{
                        text: 'Copiar',
                        handler:this.copyFile,
                        icon:'/iconos/codeditor/page_copy.png'
                    },{
                        text: 'Pegar',
                        handler:this.pasteFile,
                        icon:'/iconos/codeditor/page_paste.png'
                    },{
                        text: 'Cambiar Nombre',
                        handler:this.renameFile,
                        icon:'/iconos/codeditor/textfield_rename.png'
                    },{
                        text: 'Subir Archivo',
                        handler:this.uploadFile,
                        icon:'/iconos/codeditor/arrow_up.png'
                    },
                    {
                        text: 'Borrar',
                        handler:this.deleteFile,
                        icon:'/iconos/codeditor/page_delete.png'
                    },{
                        text:"Ver log",
                        handler:this.viewLogFile,
                        icon:"/iconos/codeditor/report_disk.png"

                    }]
            });
            //_absolutepath=_absolutepath.replace('/',"-");
            var abssplit=_absolutepath.split("/");
            var _abspath="";
            for(var i=0;i<abssplit.length;i++){
                _abspath+=abssplit[i]
                if(i<(abssplit.length-1))
                _abspath+="-";
            }

            //alert(_abspath);
            var tree = new Ext.tree.TreePanel({
                id:"_tree_panel_system",
                border: false,
                autoScroll:true,
                dataUrl:'/index.php/codeeditor/getDirectory/'+_abspath,

                root: new Ext.tree.AsyncTreeNode({
                    id:'.',
                    text: 'User files'
                }),
                listeners:{
                    click:function(node){
                        //alert(node.id);
                    }
                    ,
                    render:function(){
                        Ext.getBody().on("contextmenu", Ext.emptyFn, null, {preventDefault: true});
                    },
                    contextmenu:function(e,evento){
                        _global_var_path=e.id;
                        var xy=evento.getXY();
                        _c_father.contextmenu.showAt(xy);
                    }
                }
            });
            /*
             *verificamos si existen notificaciones
             */

            /*
             *Entrada a los archivos
             **/

            tree.on("dblclick",function(node){

                var ruta=node.id.substring(1,node.id.length);
                var file_arr=node.id.split("/");
                var file=file_arr[Number(file_arr.length-1)];
                var aux=file.split(".");
                if(aux.length==2){
                    //projects/probando/tags/Ditmar/
                    if(aux[1]=="jpg"|aux[1]=="png"|aux[1]=="gif"){
                        var win=new Ext.Window({
                            title:file,

                            html:"<img src='"+_virtuallocalpath+ruta+"' alt='img'/>",
                            width:350,
                            height:350,
                            autoScroll:true
                        });
                        win.show();
                    }else{
                        var mask = new Ext.LoadMask(Ext.get("text_code_area"), {msg:'Cargando Archivo'});
                        mask.show();
                        $.post("/index.php/editcode/loadFile/", { "ruta": ruta,"archivo":file,"rutaAbs":_absolutepath},
                        function(data){
                            mask.hide();
                            var extension=data.file.split(".");
                            var new_file= {id:ruta, text: data.result+"", syntax:extension[1], title:data.file};

                            reloadFile(ruta,data.result);

                            editAreaLoader.openFile('_code_system_editor', new_file);
                        }, "json");
                    }
                }
            });
            var toolbar = new Ext.Toolbar({
                defaults:{
                    iconAlign: 'top'
                },
                items: [
                    {text:'Guardar',iconCls:"save-icon",tooltip:"El guardado Rápido=> Guarda la informacion en el sistema de archivos sin un log \n a diferencia el guardado con log crea el log de los cambios realizados en la base de datos",split:true,
                        menu:{
                            items:[
                                {text:"Guardado Rápido",handler:function(){
                                        var mask = new Ext.LoadMask(Ext.get("text_code_area"), {msg:'Guardando los Datos espere porfavor'});
                                        mask.show();
                                        $.post("/index.php/editcode/savefile/", { "data":editAreaLoader.getValue("_code_system_editor"),"file":editAreaLoader.getCurrentFile("_code_system_editor").id,"rutaAbs":_absolutepath},
                                        function(data){
                                            if(data.result){
                                                mask.hide();
                                            }

                                        }, "json");

                                    }},
                                {text:"Guardado con Log",handler:function(){
                                        //saveFileLog
                                        change_data(null);
                                        var win_log=new Ext.Window({
                                            title:"Save Log",
                                            id:"_log_save_file",
                                            html:"<div class='log_content_system'>Se están modificando las siguientes lineas de Código <center><textarea name='' rows='15' cols='38'>"+_lastCad_m+"</textarea></center><br/></div>",
                                            width:350,
                                            height:350,
                                            autoScroll:true,
                                            buttons:[{text:"Guardar",handler:function(data){
                                                        var mask = new Ext.LoadMask(Ext.get("_log_save_file"), {msg:'Guardando los Datos y creando el Log de Cambios'});

                                                        $.post("/index.php/editcode/savefilelog/", { "data":editAreaLoader.getValue("_code_system_editor"),"file":editAreaLoader.getCurrentFile("_code_system_editor").id,"rutaAbs":_absolutepath,"log":_lastCad_m},
                                                        function(data){
                                                            if(data.result){
                                                                mask.hide();
                                                                win_log.close();
                                                                /*
                                                                 *Recargamos
                                                                 **/
                                                                reloadFile(editAreaLoader.getCurrentFile("_code_system_editor").id,editAreaLoader.getValue("_code_system_editor"));

                                                            }

                                                        }, "json");
                                                    },scope:this}]

                                        });
                                        win_log.show();

                                    }}
                            ]
                        }}, // <--- Buttons
                    {text:'Test',iconCls:"play-icon",handler:function(){
                            window.open(_virtuallocalpath);
                            //_virtuallocalpath

                        }},
                    {text:'Publicar Cambio',iconCls:"report-icon",handler:function(){
                            var changes=new Ext.Window({
                                title:"Publicar CAmbios",
                                width:600,
                                height:600,
                                
                                autoLoad: {url: '/index.php/repositorio/publishChanges', scripts: true},
                                id:"_publish_changes"
                            });

                            changes.show();
                            changes.center();
                        }},
                    {
                        text:'Bajar Source',iconCls:"download-icon",handler:function(){
                            window.open("/index.php/repositorio/downloadzip");
                            /*$.post("/index.php/repositorio/downloadZip",{url:_absolutepath+"/"},function(data){

                          },"json")*/
                        }
                    },{
                        text:'Notificaciones',
                        iconCls:"note-icon",
                        handler:function(){
                            windows();
                        }
                    },{
                        text:'Repositorio del Sistema',
                        iconCls:"resource-icon",
                        handler:function(){
                            onClickRepositorio();
                        }
                    },
                    '->',
                    {text:"Cerrar",iconCls:"close-icon",handler:function(){
                            var filesstring="";
                            var key=editAreaLoader.getCurrentFile("_code_system_editor").id;
                            var files=editAreaLoader.getAllFiles("_code_system_editor");
                            for(var f in files){
                                if(searchChanges(files[f].id)){
                                    filesstring+=" <br/><b>"+files[f].id+"</b>";
                                };
                            }
                            var message="";
                            if(filesstring!=""){
                                message="Desea Cerrar El editor, Pero se ha hecho cambios, y no se ha guardado el log de los cambios en la base de datos de estos archivos "+filesstring+" Estrictamente le pedimos que guarde los cambios con creación de log, ¿aún desea salir del editor?";
                            }else{
                                message="¿Desea Cerrar el Editor?";
                            }
                            Ext.MessageBox.show({
                                title:"Salir del Editor",
                                msg: message,
                                buttons: Ext.MessageBox.YESNOCANCEL,
                                fn:function(data){
                                    if(data=="yes"){
                                        var container=Ext.getCmp("_project_container");
                                        container.remove(Ext.getCmp("Ir a Editor"));
                                    }
                                },
                                animEl: 'mb4',
                                icon: Ext.MessageBox.QUESTION
                            });
                        }}
                ]
            });
            /*$("#_code_system_editor").keydown(function(){
                alert("->");
            });*/
            this.center =new Ext.TabPanel({
                id:"_code_container",
                region: 'center', // a center region is ALWAYS required for border layout
                deferredRender: false,
                tbar:toolbar,
                contentEl: 'text_code_area',
                activeTab: 0,
                closable:false
            });
            /*
             *defaults:{
                    iconAlign: 'top'
                },
             **/

            this.east = {
                id:"_code_east",
                region:"east",
                width:220,
                split:true,
                collapsible:true,
                title:"Repositorio",
                margins:"0 3 0 3",
                border:false,
                autoScroll:true,
                items:[tree]
            };
            var tamheigth=Math.ceil(Ext.getCmp('mainport').getSize().height*0.82);
            this.main = new Ext.Panel({
                border:false,
                renderTo: "_code_projects",
                layout:"border",
                height:tamheigth,
                items:[this.center,this.east]
            });

        },
        createFolder:function(){
            //alert("hola ->");
            /*
             *Creamos el formulario y lo mostramos en forma de ventana
             *además de ello recuperamos la ruta y otros datos importantes del la
             *variable _global_var_path
             **/
            //verificamos si _global_var_path es carpeta o archivo
            var check=_global_var_path.split("/");
            var direccion=_global_var_path.substring(1,_global_var_path.length);
            var father="";
            if(check[(check.length-1)].split(".").length==2){
                father=check[(check.length-2)];
                direccion=direccion.substring(0,(direccion.length-check[(check.length-1)].length));
            }else{
                father=check[(check.length-1)];
                direccion=direccion+"/";
            }

            this.form=new Ext.FormPanel({
                defaults:{xtype:'textfield',width:200},
                border:false,
                id:"create_folder",
                url:"/index.php/codeeditor/createDirectory",
                bodyStyle:'padding: 10px',
                modal:true,
                items:[{
                        fieldLabel:"Nombre de La Carpeta",
                        name:"name_folder",
                        allowBlank:false,
                        value:"Nueva_carpeta",
                        maxLength:150
                    },{
                        fieldLabel:"Folder Padre",
                        name:"name_father",
                        allowBlank:false,
                        value:father,
                        maxLength:150
                    },{
                        fieldLabel:"Nombre del Proyecto",
                        name:"name_project",
                        allowBlank:false,
                        value:_proyectname,
                        maxLength:150
                    },{
                        fieldLabel:"Dirección",
                        name:"name_path",
                        id:"_browser_textfield",
                        value:direccion,
                        allowBlank:false,
                        width:200
                    },{
                        fieldLabel:"Ruta",
                        id:"_browser_folder",
                        name:"name_path",
                        allowBlank:false,
                        text:"Browser",
                        width:120,

                        xtype:"button"
                    },{
                        xtype:"hidden",
                        value:_absolutepath,
                        name:"absolutepath"
                    }]
            });
            var win=new Ext.Window({
                title:"Crear Carpeta",
                modal:true,
                items:[this.form],
                width:400,
                height:250,
                buttons:[{id:"_code_folder_btn",text:"crear"}]
            });
            win.show();
            Ext.getCmp("_browser_folder").on("click",function(){
                _c_father.treeFolder("_browser_textfield");
            });
            Ext.getCmp("_code_folder_btn").on("click",function(){
                if(!Ext.getCmp("create_folder").getForm().isValid()){
                    Ext.Msg.alert('Invalido','Existen Campos invalidos');
                }else{
                    //_c_father.tree.el.mask('Creando...', 'x-mask-loading');
                    Ext.getCmp("create_folder").getForm().submit({
                        method:"post",
                        success:function(form,action){
                            win.close();
                            var url=action.result.data.substring(0,(action.result.data.length-1));
                            //alert(url);
                            Ext.getCmp("_tree_panel_system").getNodeById(url).reload();
                        },
                        failure:function(form,action){
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
            });
        },
        createFile:function(){
            var check=_global_var_path.split("/");
            var direccion=_global_var_path.substring(1,_global_var_path.length);
            var father="";
            if(check[(check.length-1)].split(".").length==2){
                father=check[(check.length-2)];
                direccion=direccion.substring(0,(direccion.length-check[(check.length-1)].length));
            }else{
                father=check[(check.length-1)];
                direccion=direccion+"/";
            }

            this.form=new Ext.FormPanel({
                defaults:{xtype:'textfield',width:200},
                border:false,
                id:"create_file",
                url:"/index.php/codeeditor/createFile",
                bodyStyle:'padding: 10px',
                modal:true,
                items:[{
                        fieldLabel:"Nombre de La Carpeta",
                        name:"name_folder",
                        allowBlank:false,
                        value:"nuevo.php",
                        maxLength:150
                    },{
                        fieldLabel:"Folder Padre",
                        name:"name_father",
                        allowBlank:false,
                        value:father,
                        maxLength:150
                    },{
                        fieldLabel:"Nombre del Proyecto",
                        name:"name_project",
                        allowBlank:false,
                        value:_proyectname,
                        maxLength:150
                    },{
                        fieldLabel:"Dirección",
                        name:"name_path",
                        id:"_browser_textfield",
                        value:direccion,
                        allowBlank:false,
                        width:200
                    },{
                        fieldLabel:"Ruta",
                        id:"_browser_folder",
                        name:"name_path",
                        allowBlank:false,
                        text:"Browser",
                        width:120,

                        xtype:"button"
                    },{
                        xtype:"hidden",
                        value:_absolutepath,
                        name:"absolutepath"
                    }]
            });
            var win=new Ext.Window({
                title:"Crear Archivo",
                modal:true,
                items:[this.form],
                width:400,
                height:250,
                buttons:[{id:"_code_folder_btn",text:"crear"}]
            });
            win.show();
            Ext.getCmp("_browser_folder").on("click",function(){
                _c_father.treeFolder("_browser_textfield");
            });
            Ext.getCmp("_code_folder_btn").on("click",function(){
                if(!Ext.getCmp("create_file").getForm().isValid()){
                    Ext.Msg.alert('Invalido','Existen Campos invalidos');
                }else{
                    //_c_father.tree.el.mask('Creando...', 'x-mask-loading');
                    Ext.getCmp("create_file").getForm().submit({
                        method:"post",
                        success:function(form,action){
                            win.close();
                            var url=action.result.data.substring(0,(action.result.data.length-1));
                            Ext.getCmp("_tree_panel_system").getNodeById(url).reload();
                        },
                        failure:function(form,action){
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
            });
        },
        copyFile:function(){
            _copyStore=_global_var_path
        },
        pasteFile:function(){
            if(_copyStore!=""){
                var name=_copyStore.split("/");
                var filename=name[name.length-1];
                var source=_c_father.getPath(_copyStore);
                var dest=_c_father.getPath(_global_var_path);
                var isfile;
                source=source.substring(1,source.length);
                dest=dest.substring(1,dest.length);
                var _url="";
                if(_c_father.isFile(_copyStore)){
                    _url="/index.php/codeeditor/copyTo";
                }else{
                    _url="/index.php/codeeditor/copyDirectory";
                }
                Ext.getCmp("_tree_panel_system").el.mask('Copiando…', 'x-mask-loading');
                Ext.Ajax.request({
                    methos:"post",
                    url:_url,
                    params:{
                        absolute:_absolutepath,
                        source:source,
                        dest:dest,
                        name:filename
                    },
                    success:function(data){

                        var data=Ext.decode(data.responseText);
                        var url=data.data.substring(0,Number(data.data.length-1));
                        Ext.getCmp("_tree_panel_system").el.unmask();
                        Ext.getCmp("_tree_panel_system").getNodeById(url).reload();
                    },
                    failure:function(data){
                        Ext.getCmp("_tree_panel_system").el.unmask();
                    }
                });
            }
        },
        getPath:function(_param){
            var path=_param.split("/");
            if(path[path.length-1].split(".").length==2){
                var result=_param.substring(0,(_param.length-path[path.length-1].length));
                return result;
            }else{
                return _param+"/";
            }
        },
        isFile:function(_param){
            var path=_param.split("/");
            if(path[path.length-1].split(".").length==2){
                return true;
            }else{
                return false;
            }
        },
        renameFile:function(){
            //_global_var_path
            //var ruta=_c_father.getPath(_global_var_path);
            var nombreaux=_global_var_path.split("/");
            var name=nombreaux[nombreaux.length-1];
            var ruta=_global_var_path.substring(1,_global_var_path.length);
            var padre;
            if(_c_father.isFile(_global_var_path)){
                padre=_c_father.getPath(_global_var_path);
                padre=padre.substring(1,padre.length);
            }else{
                padre=_global_var_path.substring(1,(_global_var_path.length-name.length))
            }
            var form=new Ext.FormPanel({
                defaults:{xtype:"textfield",width:200},
                border:false,
                url:"/index.php/codeeditor/reNameFile",
                bodyStyle:"padding: 10px",
                items:[
                    {
                        fieldLabel:'Nuevo Nombre',
                        name:'name',
                        value:name,
                        allowBlank:false,
                        maxLength:50
                    },
                    {
                        xtype:"hidden",
                        value:_absolutepath,
                        name:"absoluterute"

                    },
                    {
                        xtype:"hidden",
                        value:ruta,
                        name:"path"
                    },{
                        xtype:"hidden",
                        value:padre,
                        name:"fatherPath"
                    }
                ]
            });
            var win=new Ext.Window({
                title:"Cambiar nombre",
                modal:true,
                items:[form],
                width:370,
                height:100,
                buttons:[{id:"_code_folder_btn",text:"Cambiar de nombre"}]
            });
            win.show();
            Ext.getCmp("_code_folder_btn").on("click",function(){
                //alert(_absolutepath+" "+ruta+" "+name);
                if(!form.getForm().isValid()){
                    Ext.Msg.alert('Invalido','Existen Campos invalidos');
                }else{
                    Ext.getCmp("_tree_panel_system").el.mask('Cambiando El nombre', 'x-mask-loading');
                    form.getForm().submit({
                        method:"post",
                        success:function(form,action){
                            win.close();
                            Ext.getCmp("_tree_panel_system").el.unmask();
                            var url=action.result.data.substring(0,action.result.data.length-1);
                            Ext.getCmp("_tree_panel_system").getNodeById(url).reload()
                        },
                        failure:function(form,action){
                            Ext.getCmp("_tree_panel_system").el.unmask();
                        }
                    });
                }
            });
        },
        uploadFile:function(){
            var ruta=_c_father.getPath(_global_var_path);
            var ruta=ruta.substring(1,ruta.length);
            var fp = new Ext.FormPanel({
                fileUpload: true,
                width: 500,
                frame: true,
                title: 'File Upload Form',
                autoHeight: true,
                bodyStyle: 'padding: 10px 10px 0 10px;',
                labelWidth: 50,
                defaults: {
                    anchor: '95%',
                    allowBlank: false,
                    msgTarget: 'side'
                },
                items: [{
                        xtype: 'hidden',
                        name:"absolutepath",
                        value:_absolutepath
                    },
                    {
                        xtype: 'hidden',
                        name:"path",
                        value:ruta
                    },
                    {
                        xtype: 'fileuploadfield',
                        id: 'form-file',
                        emptyText: 'Selecciona un Archivo',
                        fieldLabel: 'Archivo',
                        name: 'userfile',
                        buttonText: '',
                        buttonCfg: {
                            iconCls: 'upload-icon'
                        }
                    }],
                buttons: [{
                        text: 'Save',
                        handler: function(){
                            if(fp.getForm().isValid()){
                                fp.getForm().submit({
                                    url: '/index.php/codeeditor/uploadFile',
                                    method:"post",
                                    waitMsg: 'Subiendo el Archivo...',
                                    success: function(fp, o){
                                        win.close();
                                        var url=o.result.data.substring(0,Number(o.result.data.length-1));
                                        Ext.getCmp("_tree_panel_system").getNodeById(url).reload();
                                        Ext.MessageBox.alert("Success",'Archivo subido con Exito "'+o.result.data+'"')
                                        //msg('Success', 'Processed file "'+o.result.data+'" on the server');
                                    },
                                    failure:function(form,action){
                                        Ext.MessageBox.alert("Error","Error Al subir el archivo")
                                    }
                                });
                            }
                        }
                    },{
                        text: 'Reset',
                        handler: function(){
                            fp.getForm().reset();
                        }
                    }]
            });
            var win=new Ext.Window({
                title:"Subir Archivo",
                modal:true,
                items:[fp],
                width:500,
                height:180
            });
            win.show();
            /*var abs=_absolutepath;
            var ruta=_c_father.getPath(_global_var_path);
            ruta=ruta.substring(1,ruta.length);
            var newparm=abs+ruta;
            var arreglo=newparm.split("/");
            var newstring="";
            for(var i=0;i<arreglo.length;i++){
                newstring+=arreglo[i];
                if(i<arreglo.length-2)
                    newstring+="-";

            }*/
            //window.open("/index.php/CodeEditor/Upload/"+newstring,"ventana1","width=300,height=300,scrollbars=NO");
        },
        deleteFile:function(){
            Ext.MessageBox.show({
                title:'Desea Eliminar el Archivo?',
                msg: ' <br />Desea eliminar el archivo?',
                buttons: Ext.MessageBox.YESNOCANCEL,
                fn:_c_father.onDeleteAcepted,
                animEl: 'mb4',
                icon: Ext.MessageBox.QUESTION
            });

        },
        viewLogFile:function(){
            /*
             *sacamos el archivo
             **/

            var nombreaux=_global_var_path.split("/");
            var name=nombreaux[nombreaux.length-1];
            var ruta=_absolutepath+_global_var_path.substring(1,(_global_var_path.length-name.length));
            //alert(name+" "+ruta);
            $.post("/index.php/repositorio/getFilesData",
            {"ruta":ruta,"nombre":name},
            function(data){
                if(data.success){
                    var store_log=new Ext.data.JsonStore({
                        id:"_store__file",
                        url:"/index.php/repositorio/getLog",
                        root:"rows",
                        totalProperty:"totalCount",
                        fields:['id','logText','fecha','hora']
                    });
                    var pagin=new Ext.PagingToolbar({
                        store: store_log, // <--grid and PagingToolbar using same store (required)
                        displayInfo: true,
                        displayMsg: '{0} - {1} of {2} Logs',
                        emptyMsg: 'no Hay Logs para mostrar',
                        pageSize: 30,
                        baseParams: {x:10,y:20}
                    });
                    var gridLog=new Ext.grid.GridPanel({
                        store:store_log,
                        title:"Grid Log",
                        columns:[
                            new Ext.grid.RowNumberer(),
                            {header:"id",dataIndex:"id",sortable:true,width:100,renderer:_c_father.action},
                            {header:"Code",dataIndex:"logText",width:300,renderer:_c_father.code},
                            {header:"fecha",dataIndex:"fecha",width:100,renderer:_c_father.fecha}
                        ],
                        bbar:pagin,
                        width:500,
                        height:400,
                        border: false,
                        stripeRows: true
                    });
                    var win=new Ext.Window({
                        title:"Log de Archivos",
                        width:500,
                        height:400,
                        items:[gridLog],
                        autoScroll:true
                    });
                    win.show();
                    store_log.load({params:{id:data.id,start:0,limit:30}});
                }else{
                    Ext.MessageBox.alert("Alerta","Este archivo no tiene log");
                }

            },"json");

        },
        fecha:function(value, metaData, record, rowIndex, colIndex, store){
            return value+"<br/><b>"+record.get("hora")+"</b>";
        }
        ,code:function(value, metaData, record, rowIndex, colIndex, store){
            //metaData.attr = 'style="white-space:normal"';
            //value=value.replace("\n","</br>")<textarea name="" rows="4" cols="20">
            var t=value.split("\n");
            var n=t.length+1;
            value="<textarea rows='"+n+"' cols='80'>"+value+"</textarea>"
            return value;
        },
        action:function(value, metaData, record, rowIndex, colIndex, store){
            //value="<a href='javascript:onFill("+rowIndex+")' id='_btn_copy_"+rowIndex+"'>copy to<br/>clipboard</a>"

            var protocol = location.href.match(/^https/i) ? 'https://' : 'http://';
            if (navigator.userAgent.match(/MSIE/)) {
                // IE gets an OBJECT tag
                var protocol = location.href.match(/^https/i) ? 'https://' : 'http://';
                value='<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="'+protocol+'download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="100" height="50" id="_flashObj_'+rowIndex+'" align="middle"><param name="allowScriptAccess" value="sameDomain" /><param name="allowFullScreen" value="false" /><param name="movie" value="/js/index.swf" /><param name="loop" value="false" /><param name="menu" value="false" /><param name="quality" value="best" /><param name="bgcolor" value="#ffffff" /><param name="flashvars" value="id='+rowIndex+'"/><param name="wmode" value="transparent"/></object>';

            }
            else {
                // all other browsers get an EMBED tag
                value = '<embed id="_flashObj_'+rowIndex+'" src="/js/index.swf" loop="false" menu="false" quality="best" bgcolor="#ffffff" width="100" height="50" name="'+rowIndex+'" align="middle" allowScriptAccess="sameDomain" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" flashvars="id='+rowIndex+'" wmode="transparent" />';
            }

            return value;
        },
        onDeleteAcepted:function(yes){
            if(yes=="yes"){
                var direccion;
                if(_c_father.isFile(_global_var_path)){
                    direccion="/index.php/codeeditor/deleteFile";
                }else{
                    direccion="/index.php/codeeditor/deleteFolder";
                }
                var path=_global_var_path.substring(1,_global_var_path.length);
                Ext.getCmp("_tree_panel_system").el.mask('Borrando...', 'x-mask-loading');

                Ext.Ajax.request({
                    url:direccion,
                    method:"post",
                    params:{
                        absoluterute:_absolutepath,
                        ruta:path
                    },
                    success:function(data){
                        var data=Ext.decode(data.responseText);
                        Ext.getCmp("_tree_panel_system").el.unmask();
                        var aux=data.data.split("/");
                        var url=data.data.substring(0,((data.data.length-1)-aux[aux.length-1].length));
                        Ext.getCmp("_tree_panel_system").getNodeById(url).reload()
                    },
                    failure:function(data){

                    }
                });
            }
        },
        sendCreateFolder:function(){

        },

        treeFolder:function(id){
            /*
             *cremos ventana y agregamos resultado en el id del campo de texto
             **/
            this.tree=new Ext.tree.TreePanel({
                border: false,
                autoScroll:true,
                dataUrl:'/index.php/codeeditor/getDirectory',
                root: new Ext.tree.AsyncTreeNode({
                    id:'.',
                    text: 'Root System'
                }),
                listeners:{
                    click:function(e,event){
                        var cadena=e.id.split("/");
                        var file=cadena[Number(cadena.length-1)];
                        var isfile=file.split('.');
                        var ruta="";
                        if(isfile.length==2){
                            for(var i=0;i<(cadena.length-1);i++){
                                ruta+=cadena[i]+"/";
                            }

                        }else{
                            ruta=e.id+"/";
                        }
                        $("#_textfield_path_search").val(ruta);
                    }
                }
            });
            var win=new Ext.Window({
                title:"Explorer",
                items:[{xtype:"textfield",
                        id:"_textfield_path_search",
                        value:"",
                        width:300
                    },this.tree],
                width:400,
                height:400,
                autoScroll:true,
                buttons:[{id:"_code_acepted_tree",text:"Aceptar"}]
            });
            win.show();
            Ext.getCmp("_code_acepted_tree").on("click",function(){
                $("#"+id).val($("#_textfield_path_search").val().substring(1,$("#_textfield_path_search").val().length));
                win.close();
            });
        }

    }

    Ext.onReady(nexus.proyecto.editor.codeEditor.init,nexus.proyecto.editor.codeEditor);
    editAreaLoader.init({
        id: "_code_system_editor"	// id of the textarea to transform
        ,start_highlight: true
        ,allow_toggle: false
        ,language: "en"
        ,syntax: "php"
        ,allow_resize: true,
        toolbar:"search,|,go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
        ,min_width:1500
        ,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck"
        ,is_multi_files: true
        ,EA_load_callback: "editAreaLoaded",
        EA_file_close_callback:"closeFile"
        ,save_callback:"save_data"
        ,change_callback:"change_data"
        ,show_line_colors: true,
        plugins: "test"

    });
    var _lastmove;
    var _lastCad_m="";
    function onKeyEditCodeDown(e,line,changes){
        var key=editAreaLoader.getCurrentFile("_code_system_editor").id;
        if(_lastmove==13){
            if(line["nb_line"]>_split_store[key].length){
                addToStore("void",Number(line["line_start"]-1),key);
            }
        }else if(e.keyCode==8){
            if(line["selectionEnd"]>line["selectionStart"]){
                var value=editAreaLoader.getSelectedText("_code_system_editor")
                var sp=value.split("\n");
                //console.debug(sp.length+" - "+Number(line["line_start"]-1));
                removegraph(Number(line["line_start"]-1),sp.length,key);
            }
            if(line["curr_pos"]==1)
                removeToStore(Number(line["line_start"]-1),key);
        }else if(e.keyCode==46){

        }else{
            _split_store[key][Number(line["line_start"]-1)]={text:line["curr_line"],type:"update"};
        }
        _lastmove=e.keyCode;
    }
    function removegraph(index,len,key){
        for(var i=index;i<len;i++){
            removeToStore(i,key);
        }
    }
    function removeToStore(index,key){
        _split_store[key].splice(index, 1);
    }
    function reloadFile(file,data){
        _copyStore=data;
        var splitData=_copyStore.split("\n");
        _split_store[file]=new Array();
        for(var i=0;i<splitData.length;i++){
            _split_store[file].push({text:splitData[i],type:"old"});
        }

    }
    function addToStore(cad,index,key){
        //alert(index+" "+cad);
        var i;
        var aux=new Array();
        for(i=index;i<_split_store[key].length;i++){
            aux.push(_split_store[key][i]);
        }
        _split_store[key][index]={text:cad,type:"new"};
        for(i=0;i<aux.length;i++){
            _split_store[key][Number(index+1+i)]=aux[i];
        }
    }
    function change_data(e){
        var key=editAreaLoader.getCurrentFile("_code_system_editor").id;

        _lastCad_m="";
        var actual=editAreaLoader.getValue("_code_system_editor");
        var line2=actual.split("\n");
        for(var i=0;i<_split_store[key].length;i++){
            if(_split_store[key][i].type=="update"){
                _lastCad_m+=Number(i+1)+"| "+line2[i]+"\n";
            }
        }
    }
    function searchTab(cad){
        for(var k=0;k<cad.length;k++){
            if(cad.charCodeAt(k)!=9){
                return false;
            }
        }

        return true;
    }
    function searchChanges(key){
        for(var i=0;i<_split_store[key].length;i++){
            if(_split_store[key][i].type=="update"){
                return true;
            }
        }
        return false;
    }
    function editAreaLoaded(id){
    }
    function closeFile(e){
        var key=editAreaLoader.getCurrentFile("_code_system_editor").id;
        if(searchChanges(key)){
            Ext.MessageBox.show({
                title:"Salir del Editor",
                msg: "¿Existen Cambios realizados en este archivo desea proseguir sin guardar los cambios?",
                buttons: Ext.MessageBox.YESNOCANCEL,
                fn:function(data){
                    //editAreaLoader.closeFile(editor_id, file_id)
                    if(data=="yes"){
                        _split_store[key].splice(0);

                        editAreaLoader.closeFile("_code_system_editor", key)
                        //Ext.getCmp("Ir a Editor").close();

                    }
                },
                animEl: 'mb4',
                icon: Ext.MessageBox.QUESTION
            });
            return false;
        }else{
            _split_store[key].splice(0);
        }
        //return false;
        //alert("Cierra "+editAreaLoader.getCurrentFile("_code_system_editor").id);
    }
    function information(value, metaData, record, rowIndex, colIndex, store){
        return value;
    }
    function message(value, metaData, record, rowIndex, colIndex, store){
        metaData.attr = 'style="white-space:normal"';
        return value;
    }
    function action(value, metaData, record, rowIndex, colIndex, store){
        return "<a href='javascript:onClickRepositorio()'>hacer cambio</a>";
    }
    function onClickRepositorio(){

        var _rutaSystem="";
        var _rutaId="";
        var system = new Ext.tree.TreePanel({
            title:"Repositorio del Sistema",
            id:"_tree_panel_system_data",
            border: false,
            layout:"fit",
            autoScroll:true,
            dataUrl:'/index.php/repositorio/getUserDirectory/'+'0-0-0-System-viewchanges',
            root: new Ext.tree.AsyncTreeNode({
                id:'.',
                text: 'System '
            }),
            listeners:{
                click:function(node){
                    _rutaId=node.id;
                    _rutaSystem=node.id.substring(7,node.id.length);


                    //alert(ruta);
                }
            }
        });
        var panel =new Ext.Panel({
            layout:"table",
            layoutConfig: {columns:1},
            defaults: {frame:true, width:250, height: 250},
            items:[
                system
            ],
            buttons:[{text:"Ver Cambios",handler:function(){
                        alert("ver cambios")
                    }},{text:"Copiar Archivo",handler:function(){
                        var file=_rutaSystem.split("/");
                        var filesname=file[(file.length-1)];
                        _rutaSystem=_rutaSystem.substr(0,_rutaSystem.indexOf(filesname));
                        $.post("/index.php/repositorio/copysystemfile/",{name:filesname,relative:_rutaSystem,userpath:_absolutepath},function(data){
                            if(!data.success){
                                Ext.MessageBox.confirm('Confirm', '¿El archivo ya existe deseas reemplazarlo?',function(data){
                                    if(data=="yes"){
                                        $.post("/index.php/repositorio/remplaceSystemFile/",
                                        {
                                            name:filesname,
                                            relative:_rutaSystem,
                                            userpath:_absolutepath
                                        },
                                        function(data){
                                            //./system/application/controllers
                                            //./index
                                            if(data.success){
                                                /*
                                                 *Publicar en la pared
                                                 *si cambiaste los archivos
                                                 **/

                                                Ext.getCmp("_tree_panel_system").getNodeById("."+_rutaSystem.substr(0,_rutaSystem.length-1)).reload();
                                            }
                                        },"json");
                                    }
                                });
                            }else{
                                /*
                                 *actualizamos carpeta del repositorio
                                 */
                                Ext.getCmp("_tree_panel_system").getNodeById("."+_rutaSystem.substr(0,_rutaSystem.length-1)).reload();
                            }
                        },"json");
                    }},{text:"copiar Todos los cambios",handler:function(){
                        /*
                         *acciones de copia
                         **/
                        Ext.MessageBox.confirm("Alerta!",
                        "Este Copiado puede ser Riesgoso ya que hace una copia directa sobre los archivos sin preguntar si usted no tiene archivos involucrados en el update, pude seguir adelante"
                        ,function(data){
                            if(data=="yes"){
                                var mask = new Ext.LoadMask(Ext.get("_winrep_system"), {msg:'Copiando repositorio'});
                                mask.show();
                                $.post("/index.php/repositorio/remplaceallfilessystem",
                                {userpath:_absolutepath
                                },
                                function(data){
                                    if(data.success){
                                        mask.hide();
                                        Ext.MessageBox.alert("Success","ya tiene los cambios en su repositorio");
                                        Ext.getCmp("_tree_panel_system").getNodeById(".").reload();
                                    }
                                },"json");
                            }
                        }
                    );

                    }}]
        });
        var win =new Ext.Window({
            title:"Repositorio Principal",
            id:"_winrep_system",
            items:[panel],
            width:400,
            height:400
        });
        win.show();
    }
    function windows(){
        var store=new Ext.data.JsonStore({
            url:"/index.php/lastchanges/getnotificaciones/",
            root:"rows",
            totalProperty:"totalCount",
            fields:['id','message','fecha','idPro','idUs','idEq','tipo','params','read']
        });

        var grid=new Ext.grid.GridPanel({
            store:store,
            width:600,
            height:400,
            autoScroll:true,
            columns:[
                new Ext.grid.RowNumberer(),
                {header:"id",dataIndex:"id",sortable:true,width:20},
                {header:"Information",dataIndex:"fecha",width:100,renderer:information},
                {header:"Message",dataIndex:"message",width:200,renderer:message},
                {header:"Action",dataIndex:"params",width:100,renderer:action}
            ],
            border: false,
            layout:"fit",
            stripeRows: true
        });
        var win=new Ext.Window({
            title:"Archivos Modificados",
            items:[grid],
            width:420,
            height:300,
            buttons:[
                {text:"Nuevos",
                    handler:function(){
                        store.load({params:{filter:false,start:0,limit:20}});
                    }
                },
                {text:"Ya Vistos",
                    handler:function(){
                        store.load({params:{filter:true,start:0,limit:20}});
                    }
                }
            ]
        });

        win.show();
        store.load({params:{z:30,start:0,limit:20}});
    }
</script>
<div id="_code_projects">
</div>
<div id="text_code_area">
    <textarea id="_code_system_editor" style=" height:100%; width: 100%;" name="test_2" rows="1000" cols="10000">
    </textarea>
</div>