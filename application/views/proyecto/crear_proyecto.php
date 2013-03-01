<script language="javascript" type="text/javascript">
    var _fatherpro;
    Ext.namespace("nexus.project.create");
    nexus.project.create={
        init:function(){
            _fatherpro=this;
            Ext.QuickTips.init();
            this.form=new Ext.FormPanel({
                id:"_form_create",
                title:"Crear Proyecto",
                width:450,
                url:"/index.php/proyecto/crearProject",
                defaults:{xtype:'textfield',width:200},
                border:true,
                bodyStyle:'padding: 10px',
                items:[
                    {
                        fieldLabel:"Nombre del Proyecto",
                        name:'nombre',
                        id:'_pp_nombre',
                        minLength:4,
                        maxLength:50,
                        allowBlank:false
                    },
                    {
                        fieldLabel:"Descripción",
                        name:'summary',
                        id:'_p_descripcion',
                        minLength:4,
                        maxLength:150,
                        allowBlank:false
                    },
                    {
                        fieldLabel:"Detalle",
                        name:'descripcion',
                        id:'_p_nombre',
                        minLength:4,
                        maxLength:250,
                        allowBlank:false,
                        xtype:"textarea",
                        width:300,
                        height:100
                    },
                    {
                        xtype:"combo",
                        fieldLabel:"Licencia",

                        triggerAction:'all',
                        store:["Apache licence 2.0",
                            "Eclipse Public Licence 1.0",
                            "GNU General Public License V2",
                            "GNU General Public License V3",
                            "GNU Lesser General Public License",
                            "MIT License",
                            "Mozilla Public License 1.0",
                            "New BSD License"
                        ],
                        name:'licencia',
                        id:'_p_licencia',
                        allowBlank:false
                    },
                    {
                        xtype:"combo",
                        fieldLabel:"FrameWork",
                        name:'framework',
                        id:'_p_framework',
                        allowBlank:false,

                        triggerAction:'all',
                        store:["codeIgniter 1.7.1","Kumbia"
                            ,"None"]
                    }
                ],
                buttons:[{id:"_btncrearproyecto",text:"Crear Proyecto"}],
                renderTo:"_p_formsend"
            });
            $("#_btncrearproyecto").click(function(){

                if(_fatherpro.form.getForm().isValid()){
                    var mask = new Ext.LoadMask(Ext.get('_p_formsend'), {msg:'Guardando la información...'});
                    mask.show();
                    _fatherpro.form.getForm().submit({

                        methos:"post",
                        success:function(form,action){
                            mask.hide();
                            Ext.get("_form_create").fadeOut();
                            var result=new Ext.Panel({
                                title:"Proyecto Creado",
                                html:"Se ha Creado el proyecto sin problemas",
                                width:300,
                                height:300,
                                renderTo:"_p_formsend"
                            });
                            result.fadeIn();
                        },
                        failure:function(form,action){
                            switch (action.failureType) {
                                case Ext.form.Action.CLIENT_INVALID:
                                    Ext.Msg.alert('Failure', 'Form fields may not be submitted with invalid values');
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
        /*
         *<option>Apache licence 2.0</option>
                            <option>Artistic Licence/GPL</option>
                            <option>Eclipse Public Licence 1.0</option>
                            <option>GNU General Public License V2</option>
                            <option>GNU General Public License V3</option>
                            <option>GNU Lesser General Public License </option>
                            <option>MIT License</option>
                            <option>Mozilla Public License 1.0</option>
                            <option>New BSD License</option>
         **/
    }
    Ext.onReady(nexus.project.create.init,nexus.project.create);
</script>
<center>
    <div id="_p_formsend">

    </div>
</center>