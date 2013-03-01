<?php echo $this->load->view("global/header"); ?>
<center>
    <div id="msn_data">
        <h1><?php echo $title ?></h1>
        <?php echo form_open("Usuario/Logea");   ?>
        <table>
            <tr><td>Nombre de Usuario</td><td><input type="text" value="" name="nick" id="nick"/></td>
                <td><?php echo $this->validation->nick_error;  ?> </td>
            </tr>
            <tr><td>Password</td><td><input type="password" value="" name="pass"/></td>
                <td><?php echo $this->validation->pass_error;  ?></td>
            </tr>
            <tr><td colspan="2"><center><input type="submit" value="Entrar" name=""/></center></td>
            </tr>
            <tr>
                <td colspan="2">
                    <?php echo $MSN;  ?>
                    
                </td>
            </tr>
        </table>
        <?php echo form_close(); ?>
    </div>
</center>
<?php echo $this->load->view("global/foother"); ?>