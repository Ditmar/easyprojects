<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$dato["undato"]="Hola";
		$this->load->view('welcome_message',$dato);
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */