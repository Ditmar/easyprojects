<html>
    <title></title>
    <script type="text/javascript" language="javascript">
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        };
    </script>
    <body onload="centrar()">
        Sube un Archivo al Sistema <br/>
        <?php echo $url; ?>
        <?php echo $error;  ?>
        <?php   ?>
        <?php echo form_open_multipart("CodeEditor/Upload/1");?>
        <input type="file" name="userfile" size="20" />
        <input type="hidden" name="path" value="<?php echo $url; ?>"/>
        <br/>
        <br/>
        <input type="submit" value="Subir" />
        <?php echo form_close(); ?>
       
    </body>
</html>