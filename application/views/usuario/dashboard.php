<script language="javascript" type="text/javascript">
    
    Ext.namespace('nexus.dashboard');
    nexus.dashboard.Panel={
        init:function(){
            var Invitaciones =  new Ext.Panel({
                id:"invite",
                title: 'Invitaciones',
                collapsible: true,
                collapsed: false,
                autoWidth: true,
                autoHeight: true,
                deferredRender: false,
                draggable: true,
                style:'padding: 10px 0 10px 0',
                autoLoad: {url: '/index.php/dashboarduser/renderInvite', scripts: true}
            });
            var Messages =  new Ext.Panel({
                title: 'Mensajes',
                collapsible: true,
                collapsed: false,
                autoWidth: true,
                autoHeight: true,
                deferredRender: false,
                draggable: true,
                style:'padding: 10px 0 10px 0',
                autoLoad: {url: '/index.php/dashboarduser/renderMessages', scripts: true}
            });
            var Friends =  new Ext.Panel({
                title: 'Mis Amigos',
                collapsible: true,
                collapsed: false,
                autoWidth: true,
                autoHeight: true,
                deferredRender: false,
                draggable: true,
                style:'padding: 10px 0 10px 0',
                autoLoad: {url: '/index.php/dashboarduser/myfriends', scripts: true},
                tbar:[{text:"refres",handler:function(){
                            Friends.load("/index.php/dashboarduser/myfriends");
                }}]
            });
            var Twitter =  new Ext.Panel({
                title: '¿Que idea tienes en mente ahora?',
                collapsible: true,
                collapsed: false,
                autoWidth: true,
                autoHeight: true,
                deferredRender: false,
                draggable: true,
                style:'padding: 10px 0 10px 0',
                autoLoad: {url: '/index.php/dashboarduser/mytwitter', scripts: true}
            });
            var Projects =  new Ext.Panel({
                title: 'Mis Proyectos',
                collapsible: true,
                collapsed: false,
                autoWidth: true,
                autoHeight: true,
                deferredRender: false,
                draggable: true,
                style:'padding: 10px 0 10px 0',
                autoLoad: {url: '/index.php/dashboarduser/myprojects', scripts: true},
                tbar:[{text:"refres",handler:function(){
                            Projects.load("/index.php/dashboarduser/myprojects");
                }}]
            });
            
            var tabViewport = new Ext.Panel({
                renderTo:"archuleta",
                items:[{
                        xtype: 'portal',
                        region:'center',
                        items:[
                            {
                                columnWidth:0.49,
                                style:'padding:10px 0 10px 10px',
                                items:[Invitaciones,Messages,Projects]
                            },
                            {
                                columnWidth:0.49,
                                style:'padding:10px 0 10px 10px',
                                items:[Twitter,Friends]
                            }
                        ]
                    }]
            });
        }

    }
    Ext.onReady(nexus.dashboard.Panel.init,nexus.dashboard.Panel);


</script>
<div id="archuleta">
</div>