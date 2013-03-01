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
        Personaliza tu cuenta con un avatar <br/>
        <?php echo $error;  ?>
        <?php   ?>
        <?php echo form_open_multipart("usuario/upload");?>
        <input type="file" name="userfile" size="20" />
        <br/>
        <br/>
        <input type="submit" value="Subir" />
        <?php echo form_close(); ?>
        <?php
            if(isset($url)){
              
        ?>
        <center><img src="<?php echo"/uploads/".$url ?>" alt="<?php echo $this->session->userdata["us_nick"]; ?> "/></center>
        <?php
            }else{
        ?>
        <center><img src="<?php echo"/uploads/".$user->avatar ?>" alt="<?php echo $this->session->userdata["us_nick"]; ?> "/></center>
        <?php
            }
        ?>
    </body>
</html>