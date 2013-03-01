<script type="text/javascript">
    var __store;
    var __expander;
    /*Ext.namespace("nexus.project.invite");
    nexus.project.invite={
        init:function(){

        }
    }
    Ext.onReady(nexus.db.createbase.init,nexus.db.createbase);*/
    function ___nick1(value, metaData, record, rowIndex, colIndex, store){
        return "<img src='/uploads/"+value+"' title='"+record.get("nick1")+"' class='thumb'></img>";
    }
    function ___nick2(value, metaData, record, rowIndex, colIndex, store){
        return "<img src='/uploads/"+value+"' title='"+record.get("nick2")+"' class='thumb'></img>";
    }
    function ___message(value, metaData, record, rowIndex, colIndex, store){
        metaData.attr = 'style="white-space:normal"';
        return value;
    }
    Ext.onReady(function(){
        
        var store=new Ext.data.JsonStore({
            url:"/index.php/proyecto/loadInvitaciones/",
            root:"rows",
            totalProperty:"totalCount",
            fields:['id','title','message','fecha','acepted','avatar1','avatar2','nick1','nick2']
        });
        store.load({params:{z:30,start:0,limit:20}});
var ___pagin=new Ext.PagingToolbar({
                    store: store, // <--grid and PagingToolbar using same store (required)
                    displayInfo: true,
                    displayMsg: '{0} - {1} of {2} Tweets',
                    emptyMsg: 'no Hay Tweets para mostrar',
                    pageSize: 5,
                    baseParams: {x:10,y:20}
                });
        var grid=new Ext.grid.GridPanel({
            store:store,
            width:650,
            height:500,
            autoScroll:true,
            columns:[
                new Ext.grid.RowNumberer(),
                {header:"id",dataIndex:"id",sortable:true,width:20},
                {header:"Invitacion enviada a:",dataIndex:"avatar1",width:120,renderer:___nick1},
                {header:"Enviado Por",dataIndex:"avatar2",width:120,renderer:___nick2},
                {header:"Mensaje",dataIndex:"message",width:250,renderer:___message},
                {header:"Estado",dataIndex:"acepted",width:50}
            ],
            bbar:___pagin,
            border: false,
            layout:"fit",
            stripeRows: true,
            renderTo:"_render_invite_panel_"
        });
        $("#bus").hide();
        $("#form_invite").hide();
        $("#invite_grid_view").hide();
        $("#_si").click(function(){
            $("#bus").hide("fast");
        });
        $("#_no").click(function(){
            $("#bus").show("fast");
        });
        $("#buscar_txt").keydown(function (){
            if($("#buscar_txt").val()!=""){
                searchData($("#buscar_txt").val());
            }else{
                $("#grid_view").hide("slow");
            }

        });
        //Ext.QuickTips.init();
        Ext.state.Manager.setProvider(new Ext.state.CookieProvider());
        __store=new Ext.data.ArrayStore({
            fields:[
                {name:'nick'},
                {name:'nombre'},
                {name:'apellido'},
                {name:'email'},
                {name:'descripcion'}
            ]
        });

        __expander = new Ext.ux.grid.RowExpander({
            tpl : new Ext.Template(
            '<p><b>Usuario:</b> {nick}</p><br>',
            '<p><b>Resumen:</b><br/> {descripcion}</p>'
        )
        });
        //alert("hola hola");
        /*
         *Click
         **/
        $("#_send_invite").click(function(){
            $.post("/index.php/proyecto/sendinvite", {
                "_title":$("#_title").val(),
                "_message":$("#_message_data").val(),
                "_subject":$("#_subject").val(),
                "_name":$("#_name").val()
            },
            function(data){
                createPanel("Lista de Usuarios",
                "<b> Invitacion </b>"+data.title+
                    data.msn
            );
            }, "json");
        });

        var __grid=new Ext.grid.GridPanel({
            store:__store,
            id:"_in_grid",
            columns:[
                __expander,
                {id:"id",header:"nick",dataIndex:"nick"},
                {header:"Nombre",dataIndex:"nombre"},
                {header:"Apellido",dataIndex:"apellido"},
                {header:"Email",dataIndex:"email"}

            ],
            viewConfig: {
                forceFit:true
            },
            listeners:{cellclick:function(grid,rowIndex, columnIndex,e){
                    var record = grid.getStore().getAt(rowIndex);
                    //alert(columnIndex);
                    var field=grid.getColumnModel().getDataIndex(1);
                    $("#_suject").attr("value",record.get(field));
                    $("#_prueba").html("<b>Invitar a :</b></br>"+record.get(field)+"<br/>"+'<input type="button" value="Invitar" id="_invite" name="_invite"/>');
                    $("#_invite").click(function(){
                        $("#form_invite").show("fast");
                        $("#_subject").attr("value",record.get(field));

                    });

                }},
            plugins:[__expander],
            stripeRows: true,
            autoExpandColumn: 'id',
            autoHeight: true,
            width: 630,
            title: 'Array Grid',

            // config options for stateful behavior
            stateful: true,
            stateId: 'grid'
        });

        __grid.render("invite_grid_view");
        /*var win=new Ext.Window({
            title:"Grilla ",
            width:500,
            height:600,
            items:[grid],
            modal:true,
            maximizable:true,
            collapsille:true
        });
        win.show();*/
    });
    function createPanel(titles,message){
        var win=new Ext.Window({
            title:titles,
            width:500,
            height:600,
            modal:true,
            html:message,
            maximizable:true,
            collapsille:true
        });
        win.show();
    }
    function searchData( data){
        $.post("/index.php/proyecto/buscarUsuario", { "keyquest":$("#buscar_txt").val() },
        function(data){
            //console.debug(__store);
            __store.loadData(data.result);
            $("#invite_grid_view").show("fast");
            //alert("ok");
        }, "json");
    }
</script>
<div class="pp-bigcontainer">
    <span class="top"><span></span></span>
    <div class="contenido">
        <table border="0">
            <tr>
                <td valign="top" style="vertical-align:top; width: 100%;" width="850px">
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle">Invitaciones Enviadas</h1>
                        </div>
                        <div class="maincontent">
                            <div id="_render_invite_panel_">

                            </div>
                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle">Invitar a un Usuario</h1>
                        </div>
                        <div class="maincontent">
                            Lista de Usuarios Registrados, Podra buscar los usuarios, dentro del sistema, y podrá invitarlos a ser parte del proyecto
                            <br/>
                            <h1>Buscar por</h1>
                            <input id="buscar_txt" name="buscar_txt" type="text" size="30"/><div id="_message">Buscando...</div>

                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle">Resultados de la Busqueda</h1>
                        </div>
                        <div class="maincontent">
                            <div id="invite_grid_view"></div>
                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                    <div id="panels">
                        <div class="mainheadpad">
                            <h1 class="maintitle">Escribir Mensaje</h1>
                        </div>
                        <div class="maincontent">
                            <div id="_prueba"></div>
                            <div id="form_invite">
                                <ul>
                                    <li><h1>Adjuntar Mensaje</h1></li>
                                    <li>Nick:</li>
                                    <li><input id="_nick" type="text" disabled="true" value="<?php echo $this->session->userdata("us_nick") ?>" /></li>
                                    <li>Subject:</li>
                                    <li><input id="_subject" type="text" disabled="true"/></li>
                                    <li>Title:</li>
                                    <li><input id="_title" type="text" size="60" value="Invitación a ser parte del equipo de trabajo del proyecto <?php echo $nombrePro; ?>" /> </li>
                                    <li>Message:</li>
                                    <li><textarea id="_message_data" rows="10" cols="40"></textarea>  </li>
                                    <li><input id="_send_invite" value="Enviar" type="Button"/></li>
                                    <li><input type="hidden" value="<?php echo $nombrePro?> " id="_name"/></li>
                                </ul>
                            </div>
                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>
                </td>

            </tr>
        </table>
    </div>
    <span class="bottom"><span></span></span>
</div>