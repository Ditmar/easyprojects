<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>Proyectos Faciles</title>
        <link href="/css/stylo.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <center>
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
                <td>
                 <div id="header">
                    <div id="menu">
                        <ul>
                            <?php foreach($menu as $m){  ?>
                            <li><a href="<?php echo $m->link; ?>"><?php echo $m->name;  ?></a></li>
                            <?php }  ?>
                        </ul>
                    </div>
                </div>
                <td>
            </tr>
            <tr>
                <td>
                    <div id="banner">
                        <div id="icomenu">
                            <table cellspacing="2" cellpadding="2" border="0">
                                <tr>
                                    <td><img src="/imadesignier/proyectos.png" alt="Proyectos"></td><td>Proyectos</td>
                                    <td><img src="/imadesignier/registrate.png" alt="Proyectos"></td><td>Registrate</td>
                                    <td><img src="/imadesignier/buscar.png" alt="Proyectos"></td><td>Buscar</td>
                                    <td><img src="/imadesignier/audio.png" alt="Proyectos"></td><td>participa</td>
                                </tr>
                            </table>
                        </div>
                   </div>
                </td>

            </tr>
        </table>
        <table border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div id="header_body">
                        <h1><?php echo $title ?></h1>
                        <div id="mensaje">
                            <h3>Registro completado recibira un mail <br/> en su correo para confirmar la cuenta gracias </h3>
                        </div>
                    </div>
                </td>

            </tr>
            <tr>
                <td><div id="footer">

                    </div>
                </td>
            </tr>
        </table>


    </center>
</body>
</html>