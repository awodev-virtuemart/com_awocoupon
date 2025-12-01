<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/

namespace AwoDev\Component\AwoCoupon\Administrator\Helper\Src;

\defined('JPATH_PLATFORM') or die;

class MVCFactory extends \Joomla\CMS\MVC\Factory\MVCFactory {


    /**
     * Returns a standard classname, if the class doesn't exist null is returned.
     *
     * @param   string  $suffix  The suffix
     * @param   string  $prefix  The prefix
     *
     * @return  string|null  The class name
     *
     * @since   3.10.0
     */
	protected function getClassName( string $suffix, string $prefix) {

		if ( ! $prefix ) {
			$prefix = \Joomla\CMS\Factory::getApplication()->getName();
		}

		$suffix = $this->getClassString( $suffix );
		$namespace = '\\AwoDev\\Component\\AwoCoupon';
		$className = trim($namespace, '\\') . '\\' . ucfirst($prefix) . '\\' . $suffix;

		if ( ! class_exists( $className ) ) {
			return null;
		}

		return $className;
    }

	private function getClassString( $text ) {
		$suffix_parts = explode( '\\', $text );
		if ( count( $suffix_parts ) != 2 ) {
			return $text;
		}

		$type = strtolower( $suffix_parts[0] );
		if ( ! in_array( $type, [ 'controller', 'model' ] ) ) {
			return $text;
		}

		if ( $type == 'controller' && strtolower( $suffix_parts[1] ) == 'displaycontroller' ) {
			return $text;
		}

		$name_parts = explode( ' ', trim( preg_replace( "([A-Z])", " $0", $suffix_parts[1] ) ) );
		if ( count( $name_parts ) == 2 ) {
			return $suffix_parts[0] . '\\' . $name_parts[0] . '\\' . $name_parts[0] . $name_parts[1];
		}
		elseif ( count( $name_parts ) == 3 ) {
			return $suffix_parts[0] . '\\' . $name_parts[0] . '\\' . $name_parts[1] . $name_parts[2];
		}

		return $text;
	}

}
