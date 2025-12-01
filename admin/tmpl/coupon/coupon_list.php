<?php
/**
 * @component AwoCoupon for Virtuemart
 * @copyright Copyright (C) Seyi Awofadeju - All rights reserved.
 * @license : GNU/GPL
 * @Website : http://awodev.com
 **/
 
// Disallow direct access to this file
defined('_JEXEC') or die('Restricted access'); 
use AwoDev\Component\AwoCoupon\Administrator\Helper\AwocouponHelper;

?>

<div class="row">
	<div class="col-12">
		<ul class="nav nav-tabs">
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=dashboard' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_DH_DASHBOARD' ); ?></a></li>
			<li class="nav-item"><a class="nav-link active" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupon' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPONS' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=installation' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_FI_INSTALLATION_CHECK' ); ?></a></li>
			<li class="nav-item"><a class="nav-link" href="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=about' ); ?>" ><?php echo JText::_( 'COM_AWOCOUPON_AT_ABOUT' ); ?></a></li>
		</ul>

<form action="<?php echo JRoute::_( 'index.php?option=com_awocoupon&view=coupon&layout=coupons' ); ?>" method="post" name="adminForm" id="adminForm">

	<div class="searchtools">
		<?php
		// Search tools bar
		echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
		?>
	</div>

	<table class="table" cellspacing="1">
	<thead>
		<tr>
			<th width="5"><input type="checkbox" name="toggle" value="" onClick="Joomla.checkAll(this);" /></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_COUPON_CODE', 'c.coupon_code', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_FUNCTION_TYPE', 'c.function_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_TYPE', 'c.coupon_value_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE', 'c.coupon_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_NUMBER_USES', 'c.num_of_uses', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title" width="1%"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_VALUE_MIN', 'c.min_value', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DISCOUNT_TYPE', 'c.discount_type', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_CUSTOMERS' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_ASSET' ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_DATE_START', 'c.startdate', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th class="title"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_CP_EXPIRATION', 'c.expiration', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JText::_( 'COM_AWOCOUPON_CP_PUBLISHED' ); ?></th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_AWOCOUPON_GBL_ID', 'c.id', $this->lists['order_Dir'], $this->lists['order'] ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr><td colspan="17"><?php echo $this->pagination->getListFooter(); ?></td></tr>
	</tfoot>

	<tbody>
		<?php foreach ( $this->rows as $i => $row ) { ?>
		<tr class="row<?php echo ($i%2); ?>">
			<td width="7"><?php echo JHTML::_('grid.id', $i,$row->id ); ?></td>
			<td align="left">
				<span class="editlinktip hasTip" title="<?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>::<?php echo $row->coupon_code; ?>">
					<a href="<?php echo JRoute::_( 'index.php?option=' . AWOCOUPON_OPTION . '&view=coupon&layout=coupon&id=' . $row->id ); ?>"><?php echo $row->coupon_code; ?></a>
				</span>
			</td>
			<td><?php echo JText::_( 'COM_AWOCOUPON_CP_COUPON' ); ?>&nbsp;</td>
			<td><?php echo $this->def_lists['coupon_value_type'][ $row->coupon_value_type ]; ?>&nbsp;</td>
			<td><?php echo ( ! empty( $row->coupon_value ) ? number_format( $row->coupon_value, 2 ) : $row->coupon_value_def ); ?>&nbsp;</td>
			<td><?php echo ( empty( $row->num_of_uses ) ? JText::_( 'COM_AWOCOUPON_GBL_UNLIMITED' ) : $row->num_of_uses . ' ' . $this->def_lists['num_of_uses_type'][ $row->num_of_uses_type ] ); ?>&nbsp;</td>
			<td><?php echo $row->min_value; ?>&nbsp;</td>
			<td><?php echo ( $this->def_lists['discount_type'][ $row->discount_type ] ); ?>&nbsp;</td>
			<td nowrap>
				<?php if ( ! empty($row->usercount ) ) { ?>
				<button type="button" class="btn btn-link p-0" onclick="load_link('<?php echo JRoute::_( 'index.php?option=' . AWOCOUPON_OPTION . '&view=coupon&layout=users&id=' . $row->id ); ?>');">
					<?php echo JText::_('COM_AWOCOUPON_CP_CUSTOMERS'); ?> (<?php echo $row->usercount; ?>)
				</button>
				<?php } else echo JText::_( 'COM_AWOCOUPON_GBL_ALL' ); ?>&nbsp;
			</td>
			<td nowrap>
				<?php if(!empty($row->assetcount)) { ?>
				<a class="modal" href="index.php?option=<?php echo AWOCOUPON_OPTION; ?>&amp;view=assets&id=<?php echo $row->id; ?>" rel="{handler: 'iframe', size: {x: 580, y: 550}}">
					<span><?php echo $this->def_lists['function_type2'][$row->function_type2];; ?> (<?php echo $row->assetcount; ?>)</span>
				</a>
				<button type="button" class="btn btn-link p-0" onclick="load_link('<?php echo JRoute::_( 'index.php?option=' . AWOCOUPON_OPTION . '&view=coupon&layout=assets&id=' . $row->id ); ?>');">
					<?php echo $this->def_lists['function_type2'][$row->function_type2];; ?> (<?php echo $row->assetcount; ?>)
				</button>
				<?php } else echo JText::_( 'COM_AWOCOUPON_GBL_ALL' ); ?>&nbsp;
			</td>
			<td><?php echo empty( $row->startdate ) ? '' : str_replace( ' ', '<br />', AwocouponHelper::instance()->getDate( $row->startdate, 'Y-m-d' ) ); ?>&nbsp;</td>
			<td><?php echo empty( $row->expiration ) ? '' : str_replace( ' ', '<br />', AwocouponHelper::instance()->getDate( $row->expiration, 'Y-m-d' ) ); ?>&nbsp;</td>
			<td><img
					src="<?php echo com_awocoupon_ASSETS; ?>/images/<?php echo ( $row->published == 1 ? 'published.png' : 'unpublished.png' ); ?>"
					width="16" height="16" class="hand" border="0" style="cursor:pointer;"
					onclick="Joomla.listItemTask('cb<?php echo $i; ?>','coupon.<?php echo ( $row->published == 1 ? 'unpublish' : 'publish' ); ?>')"
				/></td>
			<td><?php echo $row->id; ?></td>
		</tr>
		<?php } ?>
	</tbody>

	</table>

	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="option" value="<?php echo AWOCOUPON_OPTION; ?>" />
	<input type="hidden" name="view" value="coupon" />
	<input type="hidden" name="layout" value="coupons" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

	</div>
</div>

<div class="modal fade modal-iframe modal-bs" tabindex="-1" id="modal-iframe-2830283923" aria-labelledby="exampleModalLabel" aria-hidden="true" style="max-height:90%;">
	<div class="modal-dialog modal-dialog-centered modal-xl">
		<div class="modal-content" style="height:800px;">
			<div class="modal-header">&nbsp;<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
			<div class="modal-body"><iframe style="width:100%;height:100%;"></iframe></div>
		</div>
	</div>
</div>


<script>
jQuery( document ).ready( function() {
	jQuery( '.modal-iframe' ).on( 'shown.bs.modal', function( e ) {
		if ( jQuery( e.relatedTarget ).length ) {
			jQuery( this ).find( 'iframe' ).attr( 'src', jQuery( e.relatedTarget ).data( 'url' ) );
		}
		else {
			link = jQuery( e.target ).data( 'url' );
			if ( link ) {
				jQuery( this ).find( 'iframe' ).attr( 'src', link );
			}
		}
	} );
	jQuery( '.modal-iframe' ).on('hidden.bs.modal', function (e) {
		jQuery( this ).find( 'iframe' ).attr( 'src', 'about:blank' );
	})
} );

function load_link( link ) {
	link += '&tmpl=component';
	myModal = new bootstrap.Modal( '#modal-iframe-2830283923' );
	jQuery( myModal._element ).data( 'url', link );
	myModal.show();
}
</script>



