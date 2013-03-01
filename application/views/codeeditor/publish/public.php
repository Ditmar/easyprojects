<script type="text/javascript">
    var _root;
    var _root2;
    var __clip=null;
    var _copyto=null;
    Ext.namespace('nexus.project.publish');
    nexus.project.publish={
        init:function(){
            __clip = new ZeroClipboard.Client();
            __clip.addEventListener('mouseDown', click_de_raton_aqui);

            _root=this;
            var store=new Ext.data.JsonStore({
                url:"/index.php/repositorio/getChanges/s",
                root:"rows",
                totalProperty:"totalCount",
                fields:['id','nombre','ruta','fecha','estado','updatefecha']
            });
            store.load({params:{z:30,start:0,limit:20}});

            var grid=new Ext.grid.GridPanel({
                store:store,
                width:400,
                title:"Archivos donde se han hecho cambios",
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"id",dataIndex:"id",sortable:true,width:20},
                    {header:"Nombre",dataIndex:"nombre",width:80},
                    {header:"Ruta",dataIndex:"ruta",width:200,renderer:this.ruta},
                    {header:"Fecha",dataIndex:"fecha",width:80},
                    {header:"Estado",dataIndex:"estado",width:60},
                    {header:"Ultima Actualización",dataIndex:"updatefecha",width:130,renderer:this.link}
                ],
                border: false,
                layout:"fit",
                stripeRows: true
            });
            var store_log=new Ext.data.JsonStore({
                id:"_store_log_file",
                url:"/index.php/repositorio/getLog",
                root:"rows",
                totalProperty:"totalCount",
                fields:['id','logText','fecha','hora']
            });
            _root=store_log;
            var pagin=new Ext.PagingToolbar({
                store: store_log, // <--grid and PagingToolbar using same store (required)
                displayInfo: true,
                displayMsg: '{0} - {1} of {2} Tweets',
                emptyMsg: 'no Hay Tweets para mostrar',
                pageSize: 30,
                baseParams: {x:10,y:20}
            });
            var gridLog=new Ext.grid.GridPanel({
                store:store_log,
                title:"Grid Log",
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"id",dataIndex:"id",sortable:true,width:100,renderer:this.action},
                    {header:"Code",dataIndex:"logText",width:700,renderer:this.code},
                    {header:"fecha",dataIndex:"fecha",width:100},
                    {header:"hora",dataIndex:"hora",width:100},
                ],
                bbar:pagin,
                border: false,
                layout:"fit",
                stripeRows: true
            });


            var editor= new Ext.form.HtmlEditor({
                width:1000,
                height:300
            });
            var panel=new Ext.Panel({
                layout:"table",
                id:"_panel_panel_system4",
                layoutConfig: {columns:2},
                defaults: {frame:true, width:550, height: 350},
                items:[
                    grid
                    ,gridLog,{
                        title:"Editor de Texto",
                        colspan:2,
                        width:1100,
                        items:[editor],
                        buttons:[{text:"Publicar",handler:function(){
                                    Ext.MessageBox.confirm('Confirmación', '¿Esta seguro que desea enviar los cambios efectuados?',function(data){
                                        if(data=="yes"){
                                            /*
                                             *publicación de archivos
                                             *
                                             **/
                                            var mask = new Ext.LoadMask(Ext.get("_panel_panel_system4"), {msg:'Publicando Archivo'});
                                            mask.show();
                                            if(editor.getValue()==""){
                                                Ext.MessageBox.alert("Error","No puede enviar un Mensaje vacio");
                                                mask.hide();
                                                return;
                                            }
                                            $.post("/index.php/wall/submitwallPost/yes-publicFiles",
                                            {"title":"Propuesta de Cambios",
                                                "cuerpo":editor.getValue()
                                            },
                                            function(data){
                                                mask.hide();
                                                if(data.success){
                                                    Ext.MessageBox.alert("Success","La información se Publico con éxito");

                                                    //mask.hide();
                                                    //Ext.example.msg('Guardado!',"Información Publicada Con éxito");
                                                }else{
                                                    //Ext.example.msg('Error!',"No se ha guaraddo la información Con éxito");

                                                    Ext.MessageBox.alert("Alert","Error");
                                                    //mask.hide();
                                                }
                                            },"json");
                                        }

                                    });
                                    /*$.post("",{},function(){

                                    },"json");*/
                                    //alert(editor.getValue());

                                }},{text:"Cerrar",handler:function(){
                                    Ext.getCmp("_publish_changes").close();

                                }},{text:"Preview",handler:function(){
                                    window.open(_virtuallocalpath);

                                }}]
                    }],
                renderTo:"_files_changed"

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

        },
        code:function(value, metaData, record, rowIndex, colIndex, store){
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
        }

    }
    Ext.onReady(nexus.project.publish.init,nexus.project.publish);
    function onClickEvent(id){
        _root.load({params:{id:id,start:0,limit:30}});
    }
    function click_de_raton_aqui(id){
        //Ext.example.msg('Copiado!',"Información copiada a la Papelera");
        var fl=getObj("_flashObj_"+id);
        fl.setText(_root.getAt(id).get("logText"));
    }
    function getObj(thingy) {
        // simple DOM lookup utility function
        if (typeof(thingy) == 'string') thingy = document.getElementById(thingy);
        if (!thingy.addClass) {
            // extend element with a few useful methods
            thingy.hide = function() { this.style.display = 'none'; };
            thingy.show = function() { this.style.display = ''; };
            thingy.addClass = function(name) { this.removeClass(name); this.className += ' ' + name; };
            thingy.removeClass = function(name) {
                this.className = this.className.replace( new RegExp("\\s*" + name + "\\s*"), " ").replace(/^\s+/, '').replace(/\s+$/, '');
            };
            thingy.hasClass = function(name) {
                return !!this.className.match( new RegExp("\\s*" + name + "\\s*") );
            }
        }
        return thingy;
    }
</script>
<div id="_files_changed">
</div>