<script type="text/javascript" language="javascript">
    Ext.namespace('nexus.teamworks.team');
    nexus.teamworks.team={
        init:function(){
            var __store=new Ext.data.JsonStore({
                url:"/index.php/equipo/getEquipos/true",
                root:"teams",
                totalProperty:"totalCount",
                fields:['id','nombre','create_at','users']
            });
            __store.load({params:{z:30,start:0,limit:20}});

            var grid=new Ext.grid.GridPanel({
                store:__store,
                width:650,
                height:500,
                autoScroll:true,
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"id",dataIndex:"id",sortable:true,width:20},
                    {header:"Nombre",dataIndex:"nombre",width:150,renderer:this.nombre},
                    {header:"Creado el:",dataIndex:"create_at",width:150},
                    {header:"Miembros",dataIndex:"users",width:250,renderer:this.miembros}
                ],
                border: false,
                layout:"fit",
                stripeRows: true,
                renderTo:"_equipo_grid_users__"
            });
        },
        nombre:function(value, metaData, record, rowIndex, colIndex, store){
            return "<b>"+value+"</b>";
        },
        miembros:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            return value;
        }
    }
    Ext.onReady(nexus.teamworks.team.init,nexus.teamworks.team);
</script>
<div id="_equipo_grid_users__">

</div>