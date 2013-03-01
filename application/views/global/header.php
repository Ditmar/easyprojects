<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

        <title>Proyectos Fáciles</title>
        <link href="/css/reset.css" rel="stylesheet" type="text/css"/>
        <link rel="stylesheet" type="text/css" href="/css/resources/css/ext-all.css" />
        <link href="/css/grid-examples.css" type="text/css" rel="stylesheet"/>
        <link href="/css/shared/examples.css" type="text/css" rel="stylesheet"/>
        
<!--xtheme-slickness -->
        <link href="/css/styles.css" type="text/css" rel="stylesheet"/>
        <link href="/css/menuBox.css" type="text/css" rel="stylesheet"/>
        <link href="/css/xtheme-slickness.css" type="text/css" rel="stylesheet"/>
        <script src="/js/jquery.tools.min.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $("#menutooltip img[title]").tooltip("#tooltip");
                /*
                 *verificamos si el form existe
                 *Si existe le damos las propiedades necesarias para 
                 *entrar al sistema
                 *como usuario logeado
                 ***/
                if($("#submit_item").val()){
                    $("#submit_item").click(function(){
                        //alert("holas "+$("#nick").val()+" "+$("#password").val());
                        $.post("/index.php/usuario/logea", { "nick": $("#nick").val(),"pass":$("#password").val() },
                        function(data){
                            if(data.type){
                                $("#logeo").html(data.form);
                                $("#formu").hide("slow");
                                $("#menutooltip img[title]").tooltip("#tooltip");
                                //alert("numero2");
                                //$("#logeo").html(data.form);
                            }else{
                                //escribimos el error
                                $("#error").html(data.error);
                            }
                        }, "json");
                    });
                }

            });
        </script>
    </head>

    <body>

        <div id="container">
            <div id="header-container">
                <div id="logo">
                    <a href="http://localhost:8181"><img src="/images/logo_2.png" alt="PP-Projects"/></a>
                </div>
                <div id="logeo">
                    <?php echo $loginControl; ?>
                </div>

                <!--
                    MENU DE LOS TABS
                -->
                <div id="tabsH">
                    <ul>
                        <?php foreach($menu as $m) {  ?>
                        <li><a href="<?php echo $m->link; ?>"><span><?php echo $m->name;  ?></span></a></li>
                        <?php }  ?>
                    </ul>
                </div>
                <!--
                    FIN DE LOS TABS
                -->
                <div class="clearfix"></div>
            </div>
            <div id="content">
                <div class="clearfix"></div>