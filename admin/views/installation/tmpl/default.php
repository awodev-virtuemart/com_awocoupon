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

<form action="index.php" method="post" id="adminForm" name="adminForm">


	<table class="adminlist" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NUM' ); ?></th>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="<?php echo version_compare( JVERSION, '1.6.0', 'ge' ) ? 'Joomla.checkAll(this)' : 'checkAll('.count( $this->rows ).')'; ?>;" /></th>
			<th class="title"><?php echo JText::_( 'COM_AWOCOUPON_GBL_NAME' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_TYPE'); ?></th>
			<th class="title"><?php echo JText::_('COM_AWOCOUPON_GBL_FILE'); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_GBL_ID' ); ?></th>
		</tr>
	</thead>

	<tbody>
		<?php
		foreach($this->rows as $i=>$row) :
		
			if ( $row->enabled == 1 ) {
				$img = com_awocoupon_ASSETS.'/images/published.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'unpublishplugin\')"';
			} else{
				$img = com_awocoupon_ASSETS.'/images/unpublished.png';
				$alt = JText::_( 'COM_AWOCOUPON_CP_UNPUBLISHED' );
				$imgjs = 'onclick="listItemTask(\'cb'.$i.'\',\'publishplugin\')"';
			}			

			$link =  version_compare( JVERSION, '1.6.0', 'ge' )
						? 'index.php?option=com_plugins&amp;view=plugins&amp;task=plugin.edit&extension_id='.$row->id
						:'index.php?option=com_plugins&amp;view=plugin&amp;client=site&amp;task=edit&amp;cid[]='.$row->id;
	
		?>
		<tr class="row<?php echo ($i%2); ?>">
			<td><?php echo $this->pageNav->getRowOffset( $i ); ?></td>
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left"><a href="<?php echo $link; ?>"><?php echo $row->name; ?></a></td>
			<td align="center"><?php echo '<img src="'.$img.'" width="16" height="16" class="hand" border="0" alt="'.$alt.'" title="'.$alt.'" '.$imgjs.' style="cursor:pointer;"/>'; ?></td>
			<td align="center"><?php echo $row->folder; ?>&nbsp;</td>
			<td align="center"><?php echo $row->element; ?>&nbsp;</td>
			<td align="center"><?php echo $row->id; ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="view" value="installation" />
	<input type="hidden" name="task" value="" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>