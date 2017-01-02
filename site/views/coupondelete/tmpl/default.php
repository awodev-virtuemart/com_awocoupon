<?php
/**
 * @component AwoCoupon Pro
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @Website : http://awodev.com
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


foreach($this->coupons as $coupon) {
?>
	<div>
		<?php if(!empty($coupon['link'])) { ?>
			<a rel="nofollow" href="<?php echo JRoute::_($coupon['link']); ?>" align="middle" title="Delete From Basket">
				<img src="<?php echo JURI::root(true).'/media/com_awocoupon/images/x-48.png'; ?>" alt="x" height="14" style="height:14px;" /></a>
		<?php } ?>
		<?php echo $coupon['text']; ?>
	</div>
<?php 
}

