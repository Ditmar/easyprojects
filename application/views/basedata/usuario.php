<script language="javascript" type="text/javascript">
    var _db_father;
    var _db_users;
    var _db_ids;
    Ext.namespace("nexus.db.createuser");
    nexus.db.createuser={
        init:function(){
            Ext.QuickTips.init();
            _db_father=this;
            _db_users=new Array();
            _db_ids=new Array();

            this.r=0;
            while(Ext.get("_db_nameid_"+this.r)){
                $("#_db_nameid_"+this.r).click(function(){

                    var cad=this.id;
                    var array=cad.split("_");
                    var id=array[array.length-1];
                    //alert($("#nick_form"+id).val());
                    _db_users.push($("#nick_form"+id).val());
                    _db_ids.push($("#iddata_form"+id).val());
                    //alert("ok "+$("#_db_nameid_"+id).val());
                });
                this.r++;
            }
            $("#_db_userddelete").click(function(){
                var use="";
                for(var i=0;i<_db_users.length;i++){
                    use+=_db_users[i];
                    if(i<_db_users.length-1)
                    use+=",";

                }
                var id="";
                for(i=0;i<_db_ids.length;i++){
                    id+=_db_ids[i];
                    if(i<_db_ids.length-1)
                    id+=",";
                }
                $.post("/index.php/basedata/borrarUsuario",{"users":use,"ids":id},function(data){
                    if(data.result){
                        var us="";
                        for(i=0;i<data.users.length;i++){
                            us+=data.users[i]+" ";
                        }
                        Ext.MessageBox.alert("Eliminado","se ha eliminado con éxito a los usuarios "+us);
                    }
                },"json");
            });
            this.crear=new Ext.FormPanel({
                id:"_form_db_create",
                width:300,
                url:"/index.php/basedata/createuser",
                defaults:{xtype:'textfield',width:200},
                border:false,
                items:[
                    {
                        fieldLabel:"Nombre del Usuario se le añadira el prefijo del id",
                        name:"nombre",
                        id:"_nameuser",
                        width:150,
                        allowBlank:false,
                        maxLength:50,
                        minLength:5
                    },
                    {
                        fieldLabel:"Password",
                        name:"password",
                        id:"dbpassword",
                        width:150,
                        allowBlank:false,
                        maxLength:50,
                        minLength:5,
                        inputType:"password",
                        vtype:"match",
                        matchField:"dbpasswordConfirm",
                        matchInvalidText: 'Los Passwords no Coinciden'
                    },
                    {
                        fieldLabel:"Re Password",
                        name:"password2",
                        id:"dbpasswordConfirm",
                        width:150,
                        allowBlank:false,
                        maxLength:50,
                        minLength:5,
                        inputType:"password",
                        vtype:"match",
                        matchField:"dbpassword",
                        matchInvalidText: 'Los Passwords no Coinciden'
                    }
                ],
                buttons:[{id:"_db_btngo",text:"Guardar"}],
                renderTo:"_db_crearus"
            });
            Ext.apply(Ext.form.VTypes, {
                match: function(value, field)
                { if (field.matchField)
                    {
                        var form = field.findParentByType('form');
                        var f;
                        if (form && form.getForm() && (f = form.getForm().findField(field.matchField)))
                        { if (value == f.getValue())
                                return true;
                            else if (field.matchInvalidText)
                                this.matchText = field.matchInvalidText;
                            f.markInvalid(this.matchText);
                        } return false;
                    }

                    return (true);
                },
                matchText: 'El valor no coincide'

            });

            $("#_db_btngo").click(function(){
                //alert("hola ");
                if(!_db_father.crear.getForm().isValid()){
                    Ext.MessageBox.alert("Error", "Error En la validación");
                }else{
                    _db_father.crear.getForm().submit({
                        methos:"post",
                        success:function(form,action){
                            Ext.MessageBox.alert("Response","Usuario Creado Con éxito");
                            var data=action.result.data;
                            var htmlString="";
                            htmlString="<div id='users'><b>Usuarios Creados</b> "+data.length+"<br/>";
                            for(var i=0;i<data.length;i++){
                                //_db_users.push();
                                htmlString+="<b>Nombre:</b> "+data[i].nick+" <b>Creado el </b>"+data[i].fecha+" <input type='checkbox' id='_db_nameid_"+i+"' name='id"+i+"' value='ON' /></br>";
                                htmlString+="<input type='hidden' id='iddata_form"+i+"' name='id_data_form"+i+"' value='"+data[i].id+"' />";
                                htmlString+="<input type='hidden' id='nick_form"+i+"' name='nick_form"+i+"' value='"+data[i].nick+"' />";
                            }
                            htmlString+="<input type='hidden' name='length' value="+i+" />";
                            htmlString+="<input type='submit' id='_db_userddelete' value='Borrar Usuarios'/>";
                            if(i==0) {
                                htmlString+="No existen usuarios creados, en la base de datos";
                            }
                            htmlString+="</div>";
                            Ext.DomHelper.overwrite("users",{
                                html:htmlString
                            },true);
                            for(i=0;i<data.length;i++){
                                $("#_db_nameid_"+i).click(function(){
                                    var cad=this.id;
                                    var array=cad.split("_");
                                    var id=array[array.length-1];
                                    //alert($("nick_form"+id).val());
                                    _db_users.push($("#nick_form"+id).val());
                                    _db_ids.push($("#iddata_form"+id).val());
                                    //alert("ok "+$("#_db_nameid_"+id).val());
                                });
                            }
                            Ext.get("users").fadeIn();

                        },
                        failure:function(form,action){
                            switch (action.failureType) {
                                case Ext.form.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Failure',action.result.data);
                                    break;
                                case Ext.form.Action.CONNECT_FAILURE:
                                    Ext.Msg.alert('Failure', 'Ajax communication failed');
                                    break;
                                case Ext.form.Action.SERVER_INVALID:
                                    Ext.Msg.alert('Failure', action.result.msg);
                                    break;
                                default:
                                    Ext.Msg.alert('Failure',action.result.msg);
                            }
                        }
                    });

                }
            });
        }
    }
    Ext.onReady(nexus.db.createuser.init,nexus.db.createuser);
</script>
<?php
if(!$this->roles_model->checkPermit(75)){
                redirect("/error/redirect/75");
            }
?>
<div class="pp-bigcontainer">
    <span class="top"><span></span></span>
    <div class="contenido">
        <table border="0">
            <tr>
                <td valign="top" width="850px" style="vertical-align:top; width: 100%;">
                    <div id="panels">

                        <div class="mainheadpad">
                            <h1 class="maintitle"> Crear Usuarios de la Base de Datos</h1>
                        </div>
                        <div class="maincontent">
                            Creación de bases de datos <br/>

                            <div id="_db_crearus">
                            </div>
                            <?php
                            $cont=0;
                            echo"<div id='users'><b>Usuarios Creados</b> ".count($users)."<br/>";
                            foreach($users as $us) {
                                echo"<b>Nombre:</b> ".$us->nick." <b>Creado el </b>".$us->fecha." <input type='checkbox' id='_db_nameid_".$cont."' name='id".$cont."' value='ON' /></br>";
                                echo"<input type='hidden' id='iddata_form".$cont."' name='id_data_form".$cont."' value='".$us->id."' />";
                                echo"<input type='hidden' id='nick_form".$cont."' name='nick_form".$cont."' value='".$us->nick."' />";
                                $cont++;
                            }
                            echo"<input type='hidden' name='length' value=".$cont." />";
                            echo"<input type='submit' id='_db_userddelete' value='Borrar Usuarios'/>";
                            if($cont==0) {
                                echo"No existen usuarios creados, en la base de datos";
                            }
                            echo"</div>";
                            ?>
                        </div>
                        <div class="myline" style="margin-top:10px"></div>
                    </div>

                </td>

            </tr>
        </table>
    </div>
    <span class="bottom"><span></span></span>
</div>
