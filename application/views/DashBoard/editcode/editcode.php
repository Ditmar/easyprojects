<html>
    <head>
        <title>Editor</title>
        <link rel="stylesheet" type="text/css" href="../../build/menu/assets/skins/sam/menu.css">
        <link rel="stylesheet" type="text/css" href="/extjs/resources/css/ext-all.css" />
        <link href="/css/shared/examples.css" type="text/css" rel="stylesheet"/>
        <link href="/css/styles.css" type="text/css" rel="stylesheet"/>
        <link href="/css/menuBox.css" type="text/css" rel="stylesheet"/>
        <link href="/css/xtheme-slickness.css" type="text/css" rel="stylesheet"/>

        <script type="text/javascript" src="/extjs/adapter/ext/ext-base.js"></script>
        <script type="text/javascript" src="/extjs/ext-all.js"></script>
        <!-- ENDLIBS -->

        <!-- Dependency source files -->
        <script type="text/javascript" src="../../build/yahoo-dom-event/yahoo-dom-event.js"></script>
        <script type="text/javascript" src="../../build/container/container_core.js"></script>
        <!-- Menu source file -->
        <script type="text/javascript" src="../../build/menu/menu.js"></script>
        <script language="Javascript" type="text/javascript" src="/edit_area/edit_area_full.js"></script>
        <script language="Javascript" type="text/javascript" src="/js/jquery.js"></script>
        <script type="text/javascript">
            var $j=jQuery.noConflict();
            var father;
            var _path;
            var _file;
            var _arreglo=new Array();
            $j(document).ready(function(){
            });
            function flashSave(rutaAbs){
                saveFile(editAreaLoader.getValue("editor"),rutaAbs)
            }
            function flashSend(ruta,archivo,absRoute){
                var mask = new Ext.LoadMask(Ext.get("systemeditcode"), {msg:'Cargando Archivo'});
                mask.show();
                _path=ruta;
                _file=archivo
                _arreglo[_file]=_path;
                $j.post("/index.php/editCode/loadFile/", { "ruta": ruta,"archivo":archivo,"rutaAbs":absRoute},
                function(data){
                    mask.hide();
                    var extension=data.file.split(".");
                    var new_file= {id:ruta+data.file, text: data.result+"", syntax:extension[1], title:data.file};
                     editAreaLoader.openFile('editor', new_file);
                    //alert(""+data.result)
                }, "json");
            }
            function saveFile(datos,rutaAbs){
                var mask = new Ext.LoadMask(Ext.get("systemeditcode"), {msg:'Guardando los Datos espere porfavor'});

                mask.show();
                //alert("-> "+editAreaLoader.getCurrentFile("editor").id);
                $j.post("/index.php/editCode/saveFile/", { "data":datos,"file":editAreaLoader.getCurrentFile("editor").id,"rutaAbs":rutaAbs},
                function(data){
                    if(data.result){
                         mask.hide();
                        //Ext.MessageBox.alert("Guardado Con exito","Guardado Con éxito")
                        
                    }
                    
                }, "json");
            }
            function loadComplete(){
                this.parent.onLoadComplete();
            }
            editAreaLoader.init({
                id: "editor"	// id of the textarea to transform
                ,start_highlight: true
                ,allow_toggle: false
                ,language: "en"
                ,syntax: "php"
                ,allow_resize: true
                ,toolbar: "search, go_to_line, |, undo, redo, |, select_font, |, change_smooth_selection, highlight, reset_highlight, |, help"
             
                ,syntax_selection_allow: "css,html,js,php,python,vb,xml,c,cpp,sql,basic,pas,brainfuck"
                ,is_multi_files: true
                ,EA_load_callback: "editAreaLoaded"
                ,show_line_colors: true
                
            });
            function editAreaLoaded(id){
                if(id=="editor")
                {
                    loadComplete();
                   
                }
            }
            function open_file1()
            {
                var new_file= {id: "to\\ é # € to", text: "$authors= array();\n$news= array();", syntax: 'php', title: 'beautiful title'};
                editAreaLoader.openFile('editor', new_file);
            }
            function open_file2()
            {
                var new_file= {id: "Filename", text: "<a href=\"toto\">\n\tbouh\n</a>\n<!-- it's a comment -->", syntax: 'html'};
                editAreaLoader.openFile('editor', new_file);
            }
        </script>
    </head>
    <body>
        <div id="systemeditcode">
            <textarea id="editor" style=" height: 100%; width: 100%;" name="test_2" rows="1000" cols="10000">
            </textarea>
        </div>
    </body>
</html>