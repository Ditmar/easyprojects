<script language="javascript" type="text/javascript">
    Ext.BLANK_IMAGE_URL = '/extjs/resources/images/default/s.gif';
    Ext.QuickTips.init();  
    Ext.form.Field.prototype.msgTarget = 'side';
    var form= new Ext.FormPanel({
        defaults:{xtype:'textfield',width:200},
        border:false,
        url:"/index.php/usuario/submitRegistro",
        bodyStyle:'padding: 10px', //alejamos los componentes del formulario de los bordes
        items:[
            {
                fieldLabel:'Nombres', // creamos un campo
                name:'nombres', // a partir de una
                id:"nombres",
                allowBlank:false,
                maxLength:20
            },
            {id:"apellidos",
                fieldLabel:'Apellidos', // creamos un campo
                name:'apellidos', // a partir de una
                
                allowBlank:false,
                maxLength:20
            },
            {
                xtype:"combo",
                triggerAction:'all',
                store:["Afganistán",
                    "Albania",
                    "Alemania",
                    "Andorra",
                    "Angola",
                    "Antigua y Barbuda",
                    "Antillas Holandesas",
                    "Arabia Saudí",
                    "Argelia",
                    "Argentina",
                    "Armenia",
                    "Aruba",
                    "Australia",
                    "Austria",
                    "Azerbaiyán",
                    "Bahamas",
                    "Bahrein",
                    "Bangladesh",
                    "Barbados",
                    "Bélgica",
                    "Belice",
                    "Benín",
                    "Bermudas",
                    "Bielorrusia",
                    "Bolivia",
                    "Botswana",
                    "Bosnia",
                    "Brasil",
                    "Brunei",
                    "Bulgaria",
                    "Burkina Faso",
                    "Burundi",
                    "Bután",
                    "Cabo Verde",
                    "Camboya",
                    "Camerún",
                    "Canadá",
                    "Chad",
                    "Chile",
                    "China",
                    "Chipre",
                    "Colombia",
                    "Comoras",
                    "Congo",
                    "Corea del Norte",
                    "Corea del Sur",
                    "Costa de Marfil",
                    "Costa Rica",
                    "Croacia",
                    "Cuba",
                    "Dinamarca",
                    "Dominica",
                    "Dubai",
                    "Ecuador",
                    "Egipto",
                    "El Salvador",
                    "Emiratos Árabes Unidos",
                    "Eritrea",
                    "Eslovaquia",
                    "Eslovenia",
                    "España",
                    "Estados Unidos de América",
                    "Estonia",
                    "Etiopía",
                    "Fiyi",
                    "Filipinas",
                    "Finlandia",
                    "Francia",
                    "Gabón",
                    "Gambia",
                    "Georgia",
                    "Ghana",
                    "Grecia",
                    "Guam",
                    "Guatemala",
                    "Guayana Francesa",
                    "Guinea-Bissau",
                    "Guinea Ecuatorial",
                    "Guinea",
                    "Guyana",
                    "Granada",
                    "Haití",
                    "Honduras",
                    "Hong Kong",
                    "Hungría",
                    "Holanda",
                    "India",
                    "Indonesia",
                    "Irak",
                    "Irán",
                    "Irlanda",
                    "Islandia",
                    "Islas Caimán",
                    "Islas Marshall",
                    "Islas Pitcairn",
                    "Islas Salomón",
                    "Israel",
                    "Italia",
                    "Jamaica",
                    "Japón",
                    "Jordania",
                    "Kazajstán",
                    "Kenia",
                    "Kirguistán",
                    "Kiribati",
                    "Kósovo",
                    "Kuwait",
                    "Laos",
                    "Lesotho",
                    "Letonia",
                    "Líbano",
                    "Liberia",
                    "Libia",
                    "Liechtenstein",
                    "Lituania",
                    "Luxemburgo",
                    "Macedonia",
                    "Madagascar",
                    "Malasia",
                    "Malawi",
                    "Maldivas",
                    "Malí",
                    "Malta",
                    "Marianas del Norte",
                    "Marruecos",
                    "Mauricio",
                    "Mauritania",
                    "México",
                    "Micronesia",
                    "Mónaco",
                    "Moldavia",
                    "Mongolia",
                    "Montenegro",
                    "Mozambique",
                    "Myanmar",
                    "Namibia",
                    "Nauru",
                    "Nepal",
                    "Nicaragua",
                    "Níger",
                    "Nigeria",
                    "Noruega",
                    "Nueva Zelanda",
                    "Omán",
                    "Orden de Malta",
                    "Países Bajos",
                    "Pakistán",
                    "Palestina",
                    "Palau",
                    "Panamá",
                    "Papúa Nueva Guinea",
                    "Paraguay",
                    "Perú",
                    "Polonia",
                    "Portugal",
                    "Puerto Rico",
                    "Qatar",
                    "Reino Unido",
                    "República Centroafricana",
                    "República Checa",
                    "República del Congo",
                    "República Democrática del Congo (antiguo Zaire)",
                    "República Dominicana",
                    "Ruanda",
                    "Rumania",
                    "Rusia",
                    "Sáhara Occidental",
                    "Saint Kitts-Nevis",
                    "Samoa Americana",
                    "Samoa",
                    "San Marino",
                    "Santa Lucía (país)",
                    "Santo Tomé y Príncipe",
                    "San Vicente y las Granadinas",
                    "Senegal",
                    "Serbia",
                    "Seychelles",
                    "Sierra Leona",
                    "Singapur",
                    "Siria",
                    "Somalia",
                    "Sri Lanka (antes Ceilán)",
                    "Sudáfrica",
                    "Sudán",
                    "Suecia",
                    "Suiza",
                    "Suazilandia",
                    "Tailandia",
                    "Taiwán o Formosa (República Nacionalista China)",
                    "Tanzania",
                    "Tayikistán",
                    "Tíbet (actualmente bajo soberanía China)",
                    "Timor Oriental (antiguamente ocupado por Indonesia)",
                    "Togo",
                    "Tonga",
                    "Trinidad y Tobago",
                    "Túnez",
                    "Turkmenistán",
                    "Turquía",
                    "Tuvalu",
                    "Ucrania",
                    "Uganda",
                    "Uruguay",
                    "Uzbequistán",
                    "Vanuatu",
                    "Vaticano",
                    "Venezuela",
                    "Vietnam",
                    "Wallis y Futuna",
                    "Yemen",
                    "Yibuti",
                    "Zambia",
                    "Zaire",
                    "Zimbabue"],
                fieldLabel:'Pais', // creamos un campo
                name:'pais', // a partir de una
                id:"pais",
                allowBlank:false,
                maxLength:35
            },
            {
                fieldLabel:'Email', // creamos un campo
                name:'email', // a partir de una
                id:"email",
                vtype:"email",
                allowBlank:false,
                maxLength:40
            },
            {
                fieldLabel:'Nick', // creamos un campo
                name:'nick', // a partir de una
                id:"nick",
                allowBlank:false,
                maxLenght:40
            },
            {
                inputType:"password",
                fieldLabel:'Password', // creamos un campo
                name:'password', // a partir de una
                id:"password"
            },
            {
                inputType:"password",
                fieldLabel:'Re Password', // creamos un campo
                name:'repassword', // a partir de una
                id:"repassword"
            },
            {
                fieldLabel:'icq', // creamos un campo
                name:'icq', // a partir de una
                value:'', //configuración
                id:"icq"
            },
            {
                fieldLabel:'Msn', // creamos un campo
                name:'msn', // a partir de una
                id:"msn",
                vtype:"email"
            },
            {
                fieldLabel:'Gmail', // creamos un campo
                name:'gmail', // a partir de una
                id:"gmail"
            },
            {
                fieldLabel:'Web', // creamos un campo
                name:'web', // a partir de una
                id:"web",
                vtype:"url"
            },
            {
                fieldLabel:'Facebook', // creamos un campo
                name:'facebook', // a partir de una
                id:"facebook",
                vtype:"email"
            }
        ]
    });
    //alert("entra "+form);
    var panel=new Ext.Panel({
        id:"mywin",
        title:"Información personal",
        width:350,
        bodyStyle:'padding: 10px',
        items:[form],
        buttons:[{text:"Guardar",handler:sendData,scope:this},{text:"Borrar"}],
        renderTo:"personal"
    });

    function sendData(){
        if(!form.getForm().isValid()){
           Ext.Msg.alert('Invalido','Existen Campos invalidos');
        }else{
            var mask = new Ext.LoadMask(Ext.get('mywin'), {msg:'Salvando Porfavor espera'});
            mask.show();
            form.getForm().submit({
                method: 'post',
                success: function(form,action){
                    mask.hide();
                    panel.hide();
                    Ext.Msg.alert('Success',action.result.msg);
                    var result=new Ext.Panel({
                        title:"Registro Completado",
                        html:"Se envio la información Correctamente",
                        width:300,
                        height:300,
                        renderTo:"info"
                    });
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
    }
</script>
<center>
    <div id="personal"></div>
    <div id="info"></div>
</center>