<script type="text/javascript" language="javascript">
    Ext.namespace('nexus.project.adminus');
    nexus.project.adminus={
        init:function(){
            var panel=new Ext.Panel({

                tbar:[{
                        text:'Administrar Roles'
                        ,handler:function() {
                            var win=new Ext.Window({
                                title:"Roles",
                                width:580,
                                height:480,
                                modal:true,
                                maximizable:true,
                                collapsille:true,
                                autoLoad: {url:"/index.php/proyecto/viewRoles/"+$("#__idUs__").val()+"-"+$("#_btn_radio").val(), scripts: true}
                               
                            });
                            win.show();
                        }
                    },{
                        text:'Quitar del proyecto',
                        handler:function(){
                            alert($("#_btn_radio").val());
                        }
                    }],
                renderTo:"_panel_options_adminuser"
            });
            //createPanel("Administración de Roles","/index.php/proyecto/viewRoles/"+obj+"-"+$("#_idTeam").val());
        }
    }
    Ext.onReady(nexus.project.adminus.init,nexus.project.adminus);
</script>
<div id="_panel_options_adminuser">
</div>
<ul class="columns">
    <li class="col1">
        <h3>Información</h3>
        <p>
            <?php
            if(isset($user)) {
                echo "<img src='/uploads/".$user->avatar."' class='thumb' title='".$user->nombre." ".$user->apellido."'></img><br/>";
                echo "--Datos Personales--<br/>";
                echo "<b>Nombre:</b> ".$user->nombre." ".$user->apellido."<br/>";
                echo "<b>Pais</b> ".$user->pais."<br/>";
                echo "<b>Email</b> ".$user->email."  ".$user->id."<br/>";
                echo"--Datos Sociales--<br/>";
                echo "<b>Nick:</b> ".$user->nick."<br/>";
                echo "<b>Msn</b> ".$user->msn."<br/>";
                echo "<b>FaceBook</b> ".$user->facebook."<br/>";
                echo "<b>Web o Blog</b> ".$user->web."<br/>";
                echo "<b>FaceBook</b> ".$user->gmail."<br/>";
                echo '<input type="hidden" name="__idUs__" id="__idUs__" value="'.$user->id.'" />';
            }
            ?>
        </p>
    </li>
    <li class="col2">
        <h3>Equipos de Trabajo</h3>
        <p>
            <b>Alerta</b></br>
            Si desea hacer un cambio en los roles tiene que escoger un equipo de trabajo en el form
            
            <?php
            if(isset($teams)) {
                foreach($teams->result() as $t) {
                    echo "<b>".$t->nombre."</b> Creado el ".$t->create_at." ";
                    echo '<input type="radio" name="_btn_radio" id="_btn_radio" value="'.$t->id.'" />';
                }
                
            }
            ?>
        </p>

    </li>
    <li class="col3">
        <h3>Espacios De trabajo <b>WorkSpace</b></h3>
        <p>
            <?php
            if(isset($workspaces)) {
                for($i=0;$i<count($workspaces);$i++) {
                    foreach($workspaces[$i]->result() as $w) {
                        echo "W.S. ".$w->fecha." permiso ".$w->permit."<br/>";
                    }
                }
            }
            ?>
        </p>
    </li>
</ul>