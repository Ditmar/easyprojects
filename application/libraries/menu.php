<?php if (!defined('BASEPATH')) exit('No permitir el acceso directo al
script');
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Menu
 *
 * @author Ditmar
 */
class menu {
    var $menu_names;
    var $menu_links;
    function getMenu($admin) {
        if(!$admin) {
            $this->menu_names=array("Inicio","Registrate");
            $this->menu_links=array("/index.php/inicio","/index.php/Usuario/Registro");
        }else {
            $this->menu_names=array("Inicio");
            $this->menu_links=array("/index.php/inicio","/index.php/noticias","/index.php/proyectos");
        }
        $menu=array();
        for($i=0;$i<count($this->menu_names);$i++) {
            $men=new Links();
            $men->link=$this->menu_links[$i];
            $men->name=$this->menu_names[$i];
            $menu[]=$men;
        }
        return $menu;
    }
/*
 * pequeño control de logeo
 * tiene q estar activo en todas las páginas
 */
    function getControlLogin($token,$roles,$personal) {
        if(!$token) {
            $html=$this->getLogform();
        }else {
        //está registrado
            $html=$this->getMainMenu($roles,$personal);
        }
        return $html;
    }
    /*
     * Get form
     */
    function getLogForm() {
        $html='<div id="formu">
                <ul>
                        <li><b>Usuario:</b></li>
                        <li><input id="nick" type="text"/></li>
                        <li><b>Password:</b></li>
                        <li><input id="password" type="password"/>
                        <input type="submit" id="submit_item" value="logear"/></li>
                </ul>
                <div id="error"></div>
                </div>';
        return $html;
    }

    function getMainMenu($roles,$personalData) {
        /*$html1='<div id="menutooltip">';
        $html1.="<div>Nombre:<b>".$personalData->nombre."</b><br/>Nick:<b>".$personalData->nick."</b></div>";
        $html1.='<br/><a href="/index.php/usuario/logOut" >Log-Out</a><br/>';
        $html="";*/
        $html="<div id='menu2'><ul>";
        foreach($roles as $rol) {
            switch($rol["actividad"]) {
                case'perfil': {
                        $html.='<li><a href="javascript:action()" id="_perfil">Perfil</a></li>';
                        $html.='<li><a id="_dashboard" href="javascript:action()">DashBoard</a></li>';
                        break;
                    }
                case'crear_proyecto': {
                        $html.='<li><a id="_cproyecto" href="javascript:action()">Crear Proyecto</a></li>';
                        break;
                    }
                case'unirse_proyecto': {
                        $html.='<li><a id="_uproyecto" href="javascript:action()">Unirse a un Proyecto</a></li>';
                        break;
                    }
                case'mis_proyectos': {
                        $html.='<li><a id="_mproyecto" href="javascript:action()">Mis Proyectos</a></li>';
                        break;
                    }
                case'usuarios': {
                        $html.='<li><a id="_usuario" href="javascript:action()">usuarios</a></li>';
                        break;
                    }
                case'contenido': {
                        $html.='<li><a id="_contenido" href="javascript:action()">Contenido</a></li>';
                        break;
                    }
            }
        }
        $html.="</ul></div>";
        return $html;
    }
    function getSuperMenu($roles) {
        $html_code="<center>
<table>
    <tr>
        <td>
            <table border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td>
                        <div id='menu_cc_left'></div>
                    </td>
                    <td>
                        <div id='menu_cc_body' >
                            <table border='0' cellspacing='2'>
                                <tr>";
        $html_user_img="";
        $html_user_names="";
        $html_admin_img="";
        $html_admin_names="";
        foreach($roles as $fila) {
            switch($fila["actividad"]) {
                case'perfil': {
                    //usuario
                        $html_user_img.="<td><img src='/imadesignier/menus/perfil.png' alt='Perfil'/></td>";
                        $html_user_names.="<td><a href='/index.php/Usuario/Perfil'>Perfil |</a></td>";
                        break;
                    }
                case'crear_proyecto': {
                    //usuario
                        $html_user_img.="<td><img src='/imadesignier/menus/crear_proyecto.png' alt='Crear Proyecto'/></td>";
                        $html_user_names.="<td><a href='/index.php/Proyecto/crear'>Crear Proyecto |</a></td>";
                        break;
                    }
                case'unirse_proyecto': {
                    //usuario
                        $html_user_img.="<td><img src='/imadesignier/menus/unirseproyecto.png' alt='Unirse a un poryecto'/></td>";
                        $html_user_names.="<td><a href='/index.php/Proyecto/unirse'>Unirse a un Proyecto |</a></td>";
                        break;
                    }
                case'mis_proyectos': {
                    //usuario
                        $html_user_img.="<td><img src='/imadesignier/menus/misproyectos.png' alt='Mis Proyectos'/></td>";
                        $html_user_names.="<td><a href='/index.php/Proyecto/misproyectos'>Mis Proyectos</a></td>";
                        break;
                    }
                case'usuarios': {
                    //admin
                        $html_admin_img.="<td><img src='/imadesignier/menus/usuarios.png' alt='Usuarios'/></td>";
                        $html_admin_names="<td><a href='/index.php/Admin/usuarios'> Usuarios |</a></td>";
                        break;
                    }
                case'contenido': {
                    //admin
                        $html_admin_img.="<td><img src='/imadesignier/menus/contenido.png' alt='Contenido'/></td>";
                        $html_admin_names.="<td><a href='/index.php/Admin/Contenido'> Contenido  </a></td>";
                        break;
                    }
            }
        }
        $html_code.=$html_user_img."</tr><tr>".$html_user_names."</tr>
                            </table>
                        </div>
                    </td>
                    <td>
                        <div id='menu_cc_rigth'></div>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table border='0' cellspacing='0' cellpadding='0'>
                <tr>
                    <td>
                        <div id='menu_ad_left'></div>
                    </td>
                    <td>
                        <div id='menu_ad_body' >
                            <table border='0' border='0' cellspacing='0' cellpadding='0'>
                                <tr>".$html_admin_img."</tr><tr>".$html_admin_names."</tr>
                            </table>
                        </div>
                    </td>
                    <td>
                        <div id='menu_ad_rigth'></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</center>";
        //$html_user.=
        return $html_code;
    }
    /*
     * Menu que muestra el resumen del proyecto
     */

    //me llega el menu de tipo arreglo de objetos,
    //me llefa el parametro avatar de tipo string con la ruta
    function getLeftMenuResumenPro($data) {
        $html="<table cellpadding='0' cellspacing='0'>
    	<tr>
    	<td>
        	<div  id='headerMenuL'>
                </div>
        	</td>
        </tr>
        <tr>
        	<td>
            	<div  id='bodyMenuL'>
                	<div id='titulol'><b>Proyecto</b></div>";


        return $html;

    }
    function getMenuProjects($menu,$avatar,$information,$miembros) {
        $father=array();
        $hijos=array();
        $url=array();
        $admin=0;
        $base=0;
        $proyecto=0;
        $equipo=0;
        $archivos=0;
        $father[]="Information";
        $hijos["Information"][]=$information;
        $father[]="Miembros";
        $hijos["Miembros"][]=$miembros;
        $father[]="Avatar";
        $hijos["Avatar"][]="<img src='/uploads/".$avatar."' title='Tu Avatar'></img>";
        foreach($menu as $m) {
            if($m->tipo!=3) {
                switch($m->padre) {
                    
                    case"Admin": {
                            if($admin==0) {
                                $father[]="Admin";
                                $admin++;
                            }
                            $hijos["Admin"][]=$m->nombre;
                            $url["Admin"][]=$m->url;
                            break;
                        }
                    case"Base de Datos": {
                            if($base==0) {
                                $father[]="Base de Datos";
                                $base++;
                            }
                            $hijos["Base de Datos"][]=$m->nombre;
                            $url["Base de Datos"][]=$m->url;
                            break;
                        }

                    case"Proyecto": {
                            if($proyecto==0) {
                                $father[]="Proyecto";
                                $proyecto++;
                            }

                            $hijos["Proyecto"][]=$m->nombre;
                            $url["Proyecto"][]=$m->url;
                            break;
                        }
                    case"Equipos de Trabajo": {
                            if($equipo==0) {
                                $father[]="Equipos de Trabajo";
                                $equipo++;
                            }
                            $hijos["Equipos de Trabajo"][]=$m->nombre;
                            $url["Equipos de Trabajo"][]=$m->url;
                            break;
                        }
                    /*case"Mi Log": {
                            if($archivos==0) {
                                $father[]="Mi Log";
                                $archivos++;
                            }
                            $hijos["Mi Log"][]=$m->nombre;
                            $url["Mi Log"][]=$m->url;
                            break;
                        }*/
                }
            }
        }
        $info=array("father"=>$father,"sons"=>$hijos,"url"=>$url);
        return $info;
    }
    /*function getMenuProyects($menu,$avatar,$title) {
        $html="<table cellpadding='0' cellspacing='0'>
                <tr>
                <td>
                <div  id='headerMenu'>
                </div>
        	</td>
        </tr>
        <tr>
        	<td>
            	<div  id='bodyMenu'>
                <div id='titulo'><b>".$title."</b></div><br/>
<div id='#avatarMenu'><center><img src='/uploads/".$avatar."' alt='ditmar'/></center></div><br/> ";
        //reconocemos los tags para armar el menu en base al envio de MENU
        //variables para los titulos
        $admin=0;
        $tagadmin="";
        $base=0;
        $tagbase="";
        $proyecto=0;
        $tagproyecto="";
        $equipo=0;
        $tagequipo="";
        $archivos=0;
        $taglog="";
        foreach($menu as $m) {
            switch($m->padre) {
                case"Admin": {
                        if($admin==0) {
                            $admin++;
                            $tagadmin="<div id='titulo'><b>".$m->padre."</b></div><ul class='ul'>";
                        }
                        $tagadmin.="<li><a href='".$m->url."'>".$m->nombre."</a></li>";
                        break;
                    }
                case"Base de Datos": {
                        if($base==0) {
                            $base++;
                            $tagbase="<div id='titulo'><b>".$m->padre."</b></div><ul class='ul'>";
                        }
                        $tagbase.="<li><a href='/index.php".$m->url."'>".$m->nombre."</a></li>";
                        break;
                    }
                case"Proyecto": {
                        if($proyecto==0) {
                            $proyecto++;
                            $tagproyecto="<div id='titulo'><b>".$m->padre."</b></div><ul class='ul'>";
                        }
                        $tagproyecto.="<li><a href='/index.php".$m->url."'>".$m->nombre."</a></li>";
                        break;
                    }
                case"Equipos de Trabajo":{
                       if($equipo==0) {
                            $equipo++;
                            $tagequipo="<div id='titulo'><b>".$m->padre."</b></div><ul class='ul'>";
                        }
                        $tagequipo.="<li><a href='/index.php".$m->url."'>".$m->nombre."</a></li>";
                        break;
                }
                case"Mi Log": {
                        if($archivos==0) {
                            $archivos++;
                            $taglog="<div id='titulo'><b>".$m->padre."</b></div><ul class='ul'>";
                        }
                        $taglog.="<li><a href='".$m->url."'>".$m->nombre."</a></li>";
                        break;
                    }
            }
        }
        //cerramos los UL
        if($admin==1) {
            $tagadmin.="</ul>";
        }
        if($base==1) {
            $tagbase.="</ul>";
        }
        if($proyecto==1) {
            $tagproyecto.="</ul>";
        }
        if($archivos==1) {
            $taglog.="</ul>";
        }
        $html.=$tagadmin.$tagbase.$tagproyecto.$tagequipo.$taglog."</div>
            </td>
        </tr>
        <tr>
        	<td>
            	<div  id='footherMenu'>

                </div>
            </td>
        </tr>
	</table>";
        return $html;
    }*/
}
class Links {
    var $name;
    var $link;
}
?>
