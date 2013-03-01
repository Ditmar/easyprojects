<?php echo$this->load->view("global/header");  ?>
<table border="0">
    <tr>
        <td valign="top" width="850px">
            <div id="contentForm">
                <div id="headerForm">
                    <b>Crear Usuarios de la Base de Datos</b>
                </div>
                <div id="containerForm">
                    <?php
                    if($SUCCES=="") {
                        echo"<div id='Error_msn'>".$ERROR."</div>";
                    }else {
                        echo"<div id='success'>".$SUCCES."</div>";
                    }
                    ?>
                </div>
                <div class="myline" style="margin-top:0px;"></div>


            </div>
        </td>
        <td valign="top">
            <?php echo $menuProyects; ?>
        </td>
    </tr>
</table>
<?php echo$this->load->view("global/foother");  ?>