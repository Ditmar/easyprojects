<?php echo$this->load->view("global/header");  ?>
<script src="/script/adapter/ext/ext-base.js" type="text/javascript"></script>
<script src="/script/ext-all.js" type="text/javascript"></script>
<script type="text/javascript" src="/script/ux/RowExpander.js"></script>
<script type="text/javascript" src="/script/shared/examples.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        
        $("#_send").click(function(){
                sendData($("#_codeForm").val());
        });
    });
    function sendData( data){
        $.post("/index.php/proyecto/checkCode", { "_codeForm":data },
        function(data){
            createPanel(data.title,data.result);
        }, "json");
    }
    function createPanel(titles,message){
        var win=new Ext.Window({
            title:titles,
            width:500,
            height:600,
            modal:true,
            html:message,
            maximizable:true,
            collapsille:true
        });
        win.show();
    }
</script>
<div class="pp-bigcontainer">
	<span class="top"><span>Usuarios</span></span>
		<div class="contenido">
                    <input id="_codeForm" type="text" />
                    <input id="_send" type="button" value="Enviar"/>
                </div>
	<span class="bottom"><span></span></span>
</div>
<?php echo$this->load->view("global/foother");  ?>