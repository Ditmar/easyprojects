<script type="text/javascript" language="javascript">
    var ___root=null;
    Ext.namespace('nexus.project.lastchanges');
    nexus.project.lastchanges={
        init:function(){
          
            var store_log=new Ext.data.JsonStore({
                id:"_store_log_file",
                url:"/index.php/lastchanges/getLastChanges",
                root:"rows",
                totalProperty:"totalCount",
                fields:['avatar','nick','id','idEq','idUs','idPro','fecha','estado']
            });
            ___root=store_log;
           
            var pagin=new Ext.PagingToolbar({
                store: store_log, // <--grid and PagingToolbar using same store (required)
                displayInfo: true,
                displayMsg: '{0} - {1} of {2} Tweets',
                emptyMsg: 'no Hay Tweets para mostrar',
                pageSize: 30,
                baseParams: {x:10,y:20}
            });
            var lastchanges=new Ext.grid.GridPanel({
                store:store_log,
                title:"Ultimos Cambios",
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"Avatar",dataIndex:"avatar",sortable:true,width:150,renderer:this.avatar},
                    {header:"Fecha",dataIndex:"fecha",width:150},
                    {header:"Estado",dataIndex:"estado",width:120,renderer:this.viewChanges}
                ],
                bbar:pagin,
                border: false,
                layout:"fit",
                stripeRows: true,
                height:500,
                renderTo:"__cambios"
            });
            store_log.load({params:{z:30,start:0,limit:20}});
        },
        avatar:function(value, metaData, record, rowIndex, colIndex, store){
            var str="";
            str="<img src='http://localhost:8181/uploads/"+value+"' alt='"+record.get("nick")+"'/><br/>"+record.get("nick");
            return str;
        },
        viewChanges:function(value, metaData, record, rowIndex, colIndex, store){
            var str="";
            if(value=="review"){
                str="<a href='javascript:openwindowsSystem("+rowIndex+")' id='__checkstatus"+rowIndex+"'><img src='/iconos/project/eye.png' title='En revisión' alt='En revisión' /></a> <a href='javascript:openwindowsSystem("+rowIndex+")' id='__checkstatus"+rowIndex+"'>En revision click <br/> para entrar<a/>";
            }
            if(value=="approved"){
                str="<img src='/iconos/project/accept.png' title='Aprobado' alt='Aprobado' />";
            }
            if(value=="rejected"){
                   str="<img src='/iconos/project/cross.png' title='Rechazado' alt='Rechazado' />";

            }
            return str;
        }
    }
    Ext.onReady(nexus.project.lastchanges.init,nexus.project.lastchanges);
    function openwindowsSystem(index){
        var params;
        var idEq=___root.getAt(index).get("idEq");
        var idUs=___root.getAt(index).get("idUs");
         var id=___root.getAt(index).get("id");
        //alert(idUs+"-"+idEq+" ")
        var win=new Ext.Window({
            title:"Archivos",
            width:500,
            height:500,
            autoLoad: {url: '/index.php/lastchanges/checkchanges/'+idUs+"-"+idEq+"-"+id, scripts: true}
        });
        win.show();
    }
</script>
<div id="__cambios">

</div>