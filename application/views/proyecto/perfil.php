<script type="text/javascript" language="javascript">
    Ext.namespace("nexus.project.perfil");
    nexus.project.perfil={
        init:function(){
            $("#_information_editing_").click(function(){
                Ext.QuickTips.init();
                var form=new Ext.FormPanel({
                    id:"_form_perfilPro_updating_",
                    width:450,
                    url:"/index.php/proyecto/updatePerfil",
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
                        {//summary
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
                            store:["codeIgniter 1.7.1"
                                ,"None"]
                        },
                        {
                            xtype:"button",
                            id:"_btn_img_logo",
                            text:"Actualizar Imagen",
                            handler:function(){
                                var fp = new Ext.FormPanel({
                                    fileUpload: true,
                                    width: 500,
                                    frame: true,
                                    autoHeight: true,
                                    bodyStyle: 'padding: 10px 10px 0 10px;',
                                    labelWidth: 50,
                                    defaults: {
                                        anchor: '95%',
                                        allowBlank: false,
                                        msgTarget: 'side'
                                    },
                                    items: [
                                        {
                                            xtype: 'fileuploadfield',
                                            id: 'form-file',
                                            emptyText: 'Selecciona un Archivo',
                                            fieldLabel: 'Archivo',
                                            name: 'userfile',
                                            buttonText: '',
                                            buttonCfg: {
                                                iconCls: 'upload-icon'
                                            }
                                        }],
                                    buttons: [{
                                            text: 'Save',
                                            handler: function(){
                                                if(fp.getForm().isValid()){
                                                    fp.getForm().submit({
                                                        url: '/index.php/proyecto/updateLogo',
                                                        method:"post",
                                                        //waitMsg: 'Subiendo el Archivo...',
                                                        success: function(fp, o){
                                                            win.close();
                                                            
                                                            Ext.MessageBox.alert("Error",'Archivo subido con Exito "'+o.result.data+'"')
                                                            //msg('Success', 'Processed file "'+o.result.data+'" on the server');
                                                        },
                                                        failure:function(form,action){
                                                            Ext.MessageBox.alert("Error","Error Al subir el archivo")
                                                        }
                                                    });
                                                }
                                            }
                                        },{
                                            text: 'Reset',
                                            handler: function(){
                                                fp.getForm().reset();
                                            }
                                        }]
                                });
                                var win=new Ext.Window({
                                    title:"Subir Archivo",
                                    modal:true,
                                    items:[fp],
                                    width:500,
                                    height:180
                                });
                                win.show();
                            }
                        }

                    ],
                    buttons:[
                        {
                            text:"Actualizar",handler:function(){
                                if(!form.getForm().isValid()){
                                    Ext.Msg.alert('Invalido','Existen Campos invalidos');
                                }else{
                                    var mask = new Ext.LoadMask(Ext.get('_form_perfilPro_updating_'), {msg:'Actualizando Porfavor espera'});
                                    mask.show();
                                    form.getForm().submit({
                                        method: 'post',
                                        success: function(form,action){
                                            mask.hide();
                                            Ext.Msg.alert('Success',action.result.msg);
                                        },
                                        failure: function(form,action){
                                            mask.hide();
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
                            }}]
                });
                form.getForm().load({url:"/index.php/proyecto/loadPerfil"});
                var win=new Ext.Window(
                {
                    title:"Editamos la información",
                    items:[form]

                }
            );
                win.show();
            });
        }
    }
    Ext.onReady(nexus.project.perfil.init,nexus.project.perfil);
</script>
<div id="panels">
    <div class="mainheadpad">
        <h1 class="maintitle">Perfil Del Proyecto</h1>
    </div>
    <div class="maincontent">
        <h1>
            Proyectos
        </h1>
        Se detalla la información referente al proyecto que se está desarrollando<br/><br/>
        <?php

        foreach($data_proyecto as $row) {
            echo "<b>Logo: </b><img src='".$row->logo."' title='".$row->nombre."' alt='".$row->nombre."'></img><br/>";
            echo"<b>Nombre:</b> ".$row->nombre."<br/>";
            
            echo"<b>Descripción</b> <br/>".$row->descripcion."<br/>";
            echo "<b>Sumary</b> ".$row->summary."<br/>";
            echo "<b>Licencia</b>".$row->licencia;
        }
        $this->load->model("roles_model");
        if($this->roles_model->checkPermit(89)) {
            echo "<br/><a href='#' id='_information_editing_' >Editar Información</a>";
        }
        ?>


    </div>
    <div class="myline" style="margin-top:10px"></div>
</div>
