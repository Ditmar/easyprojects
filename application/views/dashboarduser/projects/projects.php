
<script type="text/javascript">
    var _globalData="";
    var _globalName="";
    Ext.namespace("nexus.dashboard.projects");
    nexus.dashboard.projects={

        init:function(){
            var father=this;
            this.store=new Ext.data.JsonStore({
                url:"/index.php/proyecto/getMyProjects/",
                root:"rows",
                fields:['id','nombre','descripcion','create','licencia','summary','logo','framework']
            });
            this.store.load({params:{z:30,start:5,limit:5}});
            this.grid=new Ext.grid.GridPanel({
                store:this.store,
                columns:[
                    new Ext.grid.RowNumberer(),
                    {header:"logo",dataIndex:"logo",sortable:true,renderer:this.logo},
                    {header:"Información",dataIndex:"descripcion",sortable:true,renderer:this.descripcion,width:'95%'},
                    {header:"Conectar",dataIndex:"id",sortable:true,renderer:this.conectar}
                ],
                border: false,
                height:400,
                stripeRows: true
            });
            /*Ext.get("_goproject").on("click",function(){
                alert("da");
            });*/

            this.grid.render("_proyects");
        },
        logo:function(value, metaData, record, rowIndex, colIndex, store){
            //alert(value);
            if(value==null){
                return "<img src='/logo/logopp.jpg' alt='"+record.get('nombre')+"'/>";
            }
            return "<img src='"+value+"' alt='"+record.get('nombre')+"'/>";
        },
        descripcion:function(value, metaData, record, rowIndex, colIndex, store){
            metaData.attr = 'style="white-space:normal"';
            var html="<h1>"+record.get("nombre")+"</h1>"+"<b>Fecha:</b><br/>"+record.get("create")+" <b>Licencia</b>"+record.get("licencia")+"<br/>"+"<b>Framework</b> "+record.get("framework")+"<br/>"+value+"<br/> "+record.get("summary");
            return html;
        },
        conectar:function(value,metaData,record){
            
            return "<input type='button' id='_goproject' onclick='goproyect("+value+")' value='Conectar "+value+"' />";

        },
        goproyect:function(){
            alert("hola");
        }
    }
    Ext.onReady(nexus.dashboard.projects.init,nexus.dashboard.projects);
    function goproyect(id){
        _globalData=id;
        Ext.MessageBox.show({
            title:'Te conectaras al proyecto ',
            msg: ' <br />Desea proceder en su acción?',
            buttons: Ext.MessageBox.YESNOCANCEL,
            fn:onDeleteAcepted,
            animEl: 'mb4',
            icon: Ext.MessageBox.QUESTION
        });
    }
    function onDeleteAcepted(yes){
        if(yes=="yes"){
            createTabPanel("/index.php/proyecto/admin/"+_globalData,"Proyecto");
            //console.debug(Ext.getCmp("containerTapPanel"));
        }

    }
    function createTabPanel(urlRequest,ti){
        var comp=Ext.getCmp(ti);
        if(!Ext.getCmp(ti)){
            var pad=new Ext.Panel(
            {
                id:ti,
                title:ti,
                closable:true,
                style:'padding: 5px 5px 5px 5px',
                autoScroll: true,
                autoLoad: {url:urlRequest, scripts: true}
            }
        );
            var tabs=Ext.getCmp("containerTapPanel");
            tabs.add(pad);
            tabs.activate(pad);
            pad.on("close",function(){

            });
        }else{
            Ext.getCmp("containerTapPanel").activate(comp);
        }
    }
</script>
<div id="_proyects">
</div>