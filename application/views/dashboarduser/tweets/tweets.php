<a href="#" id="_hide">Ocultar Formulario</a>
<a href="#" id="_show">Mostrar Formulario</a>
<center>
    <div id="_form">
        <textarea name="_follow" id="_follow" rows="4" cols="35"></textarea><br/>
        <input type="button" id="_send" value="Enviar" />
    </div>
    <script type="text/javascript">
        Ext.namespace('nexus.dashboard.tweets');

        nexus.dashboard.tweets={
            init:function(){
                var father=this;
                Ext.get("_show").fadeOut();
                /*
                 *Control del form
                 **/
                Ext.get("_hide").on("click",function(){
                    
                    Ext.get("_form").fadeOut();
                    Ext.get("_show").fadeIn();
                    Ext.get("_hide").fadeOut();
                });
                Ext.get("_show").on("click",function(){
                    Ext.get("_form").fadeIn();
                    Ext.get("_hide").fadeIn();
                    Ext.get("_show").fadeOut();
                });
                Ext.get("_send").on("click",function(){
                    //peticion Jquery
                    var mask = new Ext.LoadMask(Ext.get("_form"), {msg:'Enviando la Infiormacion espere porfavor'});
            mask.show();
                    $.post("/index.php/dashboarduser/insertTweet",
                    {"message":$("#_follow").attr("value")},function(data){
                        if(data.result){
                            //alert(data.result);
                            mask.hide();
                            father.store.load({params:{z:30,start:0,limit:5}});
                        }
                    },"json")
                });
                /*
                 *creamos el store
                 **/
                this.store=new Ext.data.JsonStore({
                    url:"/index.php/dashboarduser/bandejaMyTweets",
                    root:"rows",
                    totalProperty:"totalCount",
                    fields:['idUs','avatar','id','message']
                });

                /*
                 *creación del dataGrid
                 **/


                /*
                 *Creación del PaggingToolbar
                 **/
                this.pagin=new Ext.PagingToolbar({
                    store: this.store, // <--grid and PagingToolbar using same store (required)
                    displayInfo: true,
                    displayMsg: '{0} - {1} of {2} Tweets',
                    emptyMsg: 'no Hay Tweets para mostrar',
                    pageSize: 5,
                    baseParams: {x:10,y:20}
                });
                this.store.load({params:{z:30,start:5,limit:5}});
                
                this.grid=new Ext.grid.GridPanel({
                    store:this.store,
                    columns:[
                        new Ext.grid.RowNumberer(),
                        {header:"Avatar",dataIndex:"avatar",sortable:true,renderer:this.avatar},
                        {header:"Mensaje",dataIndex:"message",sortable:true,renderer:this.message,width:300}
                    ],
                    bbar:this.pagin,
                    border: false,
                    height:400,
                    stripeRows: true
                });

                this.grid.render("_renderGrid");
                

            },
            avatar:function(value, metaData, record, rowIndex, colIndex, store){
                return "<img src='/uploads/"+value+"' alt='"+record.get('nick')+"'/>";
            },
            message:function(value, metaData, record, rowIndex, colIndex, store){
                metaData.attr = 'style="white-space:normal"';
                return value;
            }
        }
        Ext.onReady(nexus.dashboard.tweets.init,nexus.dashboard.tweets);

    </script>
    <div id="_renderGrid"></div>
</center>