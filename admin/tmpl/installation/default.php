<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); ?>

<style>tr.error td {color:red }</style>

<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs">
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=dashboard' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_DH_DASHBOARD' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupon' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></a></li>
			<li class="nav-item"><a class="nav-link active" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=installation' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_FI_INSTALLATION_CHECK' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=about' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_AT_ABOUT' ); ?></a></li>
		</ul>

<form action="index.php" method="post" id="adminForm" name="adminForm">


	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NAME' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE'); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_FILE'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_GBL_ID' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php foreach ( $this->rows as $i => $row ) { ?>
		<tr class="row<?php echo ($i%2); ?>">
			<td class="center"><?php echo JHtml::_('grid.id', $i, $row->id); ?></td>
			<td><a href="<?php echo JRoute::_( 'index.php?option=com_plugins&view=plugins&task=plugin.edit&extension_id=' . $row->id ); ?>"><?php echo $row->name; ?></a></td>
			<td><img width="16" height="16" class="hand" border="0" style="cursor:pointer;"
					src="<?php echo com_awocoupon_ASSETS; ?>/images/<?php echo ( $row->enabled == 1 ? 'published.png' : 'unpublished.png' ); ?>"
					onclick="Joomla.listItemTask( 'cb<?php echo $i; ?>', '<?php echo ( $row->enabled == 1 ? 'Installation.unpublishplugin' : 'Installation.publishplugin' ); ?>');"
				/>
			</td>
			<td><?php echo $row->folder; ?>&nbsp;</td>
			<td><?php echo $row->element; ?>&nbsp;</td>
			<td><?php echo $row->id; ?></td>
		</tr>
		<?php } ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="view" value="installation" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

	</div>
</div>