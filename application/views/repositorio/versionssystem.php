
    <script type="text/javascript" language="javascript">
        Ext.namespace('nexus.project.versions');
        nexus.project.versions={
            init:function(){
                var store=new Ext.data.JsonStore({
                    url:"/index.php/repositorio/getBranchesList/",
                    root:"rows",
                    totalProperty:"totalCount",
                    fields:['id','url','idPro','fecha','version','avatar1','avatar2','nick1','nick2']
                });
                store.load({params:{z:30,start:0,limit:20}});

                var grid=new Ext.grid.GridPanel({
                    store:store,
                    width:650,
                    height:800,
                    autoScroll:true,
                    columns:[
                        new Ext.grid.RowNumberer(),
                        {header:"id",dataIndex:"id",sortable:true,width:20},
                        {header:"Cambio propuesto por",dataIndex:"avatar1",width:150,renderer:this.avatar1},
                        {header:"Aprobado por ",dataIndex:"avatar2",width:150,renderer:this.avatar2},
                        {header:"Fecha",dataIndex:"fecha",width:100},
                        {header:"version",dataIndex:"version",width:100,renderer:this.version},
                        {header:"Download",dataIndex:"idPro",width:100,renderer:this.download}
                    ],
                    border: false,
                    layout:"fit",
                    stripeRows: true,
                    renderTo:"_render_version_panel_"
                });
            },
            avatar1:function(value, metaData, record, rowIndex, colIndex, store){
                var str="<img src='/uploads/"+value+"' title='"+record.get("nick1")+"' alt='"+record.get("nick1")+"'></img><br/><b>"+record.get("nick1")+"</b>";
                return str;
            },
            avatar2:function(value, metaData, record, rowIndex, colIndex, store){
                var str="<img src='/uploads/"+value+"' title='"+record.get("nick2")+"' alt='"+record.get("nick2")+"'></img><br/><b>"+record.get("nick2")+"</b>";
                //alert(str);
                return str;
            },
            version:function(value, metaData, record, rowIndex, colIndex, store){
                var str="<b>"+value+"</b>";
                return str;
            },
            download:function(value, metaData, record, rowIndex, colIndex, store){
                var str="<a href='"+record.get("url")+"'><img src='/iconos/project/package.png' title='"+record.get("version")+"' alt='"+record.get("version")+"'></img></a>";
                return str;
            }

        }
        Ext.onReady(nexus.project.versions.init,nexus.project.versions);

    </script>
<div id="_render_version_panel_">
    
</div>
