<?php

/**
 * Kumbia PHP Framework
 *
 * LICENSE
 *
 * This source file is subject to the GNU/GPL that is bundled
 * with this package in the file docs/LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.kumbia.org/license.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kumbia@kumbia.org so we can send you a copy immediately.
 *
 * @category Kumbia
 * @package Filter
 * @subpackage BaseFilters
 * @copyright Copyright (c) 2007-2007 Andres Felipe Gutierrez (andresfelipe at vagoogle.net)
 * @copyright Copyright (c) 2007-2007 Emilio Rafael Silveira Tovar(emilio.rst at gmail.com)
 * @copyright Copyright (c) 2007-2007 Deivinson Tejeda Brito (deivinsontejeda at gmail.com)
 * @license http://www.kumbia.org/license.txt GNU/GPL
 */

/**
 * Filtra una cadena para que contenga solo alpha-numeic
 *
 * @param string
 * @return string
 */
class AlnumFilter implements FilterInterface {

	/**
 	 * Ejecuta el filtro
 	 *
 	 * @param string $value
 	 * @return string
 	 */
	public function execute($str){
		/**
		 * Revisa si PCRE esta compilado para soportar UNICODE
		 * de esta forma filtra tambien tildes y otros caracteres latinos
		 */
		if(@preg_match('/\pL/u', 'a')){
			$patron = '/[^\p{L}\p{N}]/';
		} else {
			$patron = '/[^a-zA-Z0-9\s]/';
		}
		return preg_replace($patron, '', (string) $str);

	}

}

?>