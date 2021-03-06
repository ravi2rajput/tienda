<?php
defined('_JEXEC') or die('Restricted access');
JHTML::_('stylesheet', 'menu.css', 'media/com_tienda/css/');
JHTML::_('script', 'tienda.js', 'media/com_tienda/js/');
JHTML::_('script', 'joomla.javascript.js', 'includes/js/');
Tienda::load( 'TiendaGrid', 'library.grid' );
Tienda::load( 'TiendaHelperOrder', 'helpers.order' );
Tienda::load( 'TiendaHelperCurrency', 'helpers.currency' );
$state = @$this->state;
$order = @$this->order;
$items = @$this->orderitems;
$coupons = @$this->coupons;
$display_credits = Tienda::getInstance()->get( 'display_credits', '0' );
$currency_helper = new TiendaHelperCurrency();
$default_currency = Tienda::getInstance()->get('default_currencyid', '1');
?>
<div class="cartitems">
	<div class="adminlist">
		<div id="cartitems_header" class="floatbox">
			<span class="left50"><?php echo JText::_('COM_TIENDA_PRODUCT'); ?></span>
			<span class="left20 center"><?php echo JText::_('COM_TIENDA_QUANTITY'); ?></span>
			<span class="left30 right"><?php echo JText::_('COM_TIENDA_TOTAL'); ?></span>
		</div>
            <?php $i=0; $k=0; ?> 
            <?php foreach ($items as $item) : ?>
                <div class="row<?php echo $k; ?> floatbox cart_item_list">
                    <div class="left50">
                    	<div class="inner">
	                        <a href="<?php echo JRoute::_("index.php?option=com_tienda&controller=products&view=products&task=view&id=".$item->product_id); ?>">
	                            <?php echo $item->orderitem_name; ?>
	                        </a>
	                        <br/>
	                        
	                        <?php if (!empty($item->orderitem_attribute_names)) : ?>
	                            <?php echo $item->orderitem_attribute_names; ?>
	                            <br/>
	                        <?php endif; ?>
	                        
	                        <?php if (!empty($item->orderitem_sku)) : ?>
	                            <b><?php echo JText::_('COM_TIENDA_SKU'); ?>:</b>
	                            <?php echo $item->orderitem_sku; ?>
	                            <br/>
	                        <?php endif; ?>
	
	                        <?php if ($item->orderitem_recurs) : ?>
	                            <?php $recurring_subtotal = $item->recurring_price; ?>
	                            <?php echo JText::_('COM_TIENDA_RECURRING_PRICE'); ?>: <?php echo $currency_helper->format($item->recurring_price, $default_currency ); ?>
	                            (<?php echo $item->recurring_payments . " " . JText::_('COM_TIENDA_PAYMENTS'); ?>, <?php echo $item->recurring_period_interval." ". JText::_('COM_TIENDA_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_TIENDA_PERIODS'); ?>) 
											            <?php if( $item->subscription_prorated ) : ?>
	                                <br/>
			                                <?php echo JText::_('COM_TIENDA_INITIAL_PERIOD_PRICE'); ?>: <?php echo $currency_helper->format( $item->recurring_trial_price, $default_currency ); ?>
			                                (<?php echo "1 " . JText::_('COM_TIENDA_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_TIENDA_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_TIENDA_PERIOD'); ?>)
											            <?php else : ?>
				                            <?php if ($item->recurring_trial) : ?>
			                                <br/>
			                                <?php echo JText::_('COM_TIENDA_TRIAL_PERIOD_PRICE'); ?>: <?php echo $currency_helper->format($item->recurring_trial_price, $default_currency ); ?>
			                                (<?php echo "1 " . JText::_('COM_TIENDA_PAYMENT'); ?>, <?php echo $item->recurring_trial_period_interval." ". JText::_('COM_TIENDA_PERIOD_UNIT_'.$item->recurring_period_unit)." ".JText::_('COM_TIENDA_PERIOD'); ?>)
											            <?php endif;?>
	                            <?php endif; ?>    
	                        <?php else : ?>
	                            <?php echo JText::_('COM_TIENDA_PRICE'); ?>:
	                            <?php echo $currency_helper->format( $item->price, $default_currency ); ?>
	                        <?php endif; ?> 
	                        
						    <?php if (!empty($this->onDisplayOrderItem) && (!empty($this->onDisplayOrderItem[$i]))) : ?>
						        <div class='onDisplayOrderItem_wrapper_<?php echo $i?>'>
						        <?php echo $this->onDisplayOrderItem[$i]; ?>
						        </div>
						    <?php endif; ?>  
	
	                        <?php if( in_array($item->product_id, $coupons) ){ ?>
	                        	<span style="float: right;"><?php echo JText::_('COM_TIENDA_COUPON_DISCOUNT_APPLIED'); ?></span>
	                        <?php } ?>
                    	</div>                      
                    </div>
                    <div class="left20 center">
                        <?php echo $item->orderitem_quantity;?>  
                    </div>
                    <div class="left30 right">
                    	<div class="inner">
                    		<?php echo $currency_helper->format($item->orderitem_final_price, $default_currency ); ?>
                    	</div>
                    </div>
                </div>
              	<div class="marginbot"></div>
            <?php ++$i; $k = (1 - $k); ?>
            <?php endforeach; ?>
            <div class="marginbot"></div>
                <div class="floatbox">
                    <span class="left50 header">
                    	<span class="inner">
                    		<?php echo JText::_('COM_TIENDA_SUBTOTAL'); ?>
                    	</span>
                    </span>
                    <span class="right">
                    	<span class="inner">
                    		<?php echo $currency_helper->format($order->order_subtotal,$default_currency); ?>
                    	</span>
                    </span>
                </div>
                
                <?php if (!empty($order->_coupons['order_price'])) : ?>
                <div class="floatbox">
                    <span class="left50 header">
                    	<span class="inner">
                    		<?php echo JText::_('COM_TIENDA_DISCOUNT'); ?>
                    	</span>
                    </span>
                    <span class="left50 right">
                    	<span class="inner">
                    		<?php echo $currency_helper->format( $order->order_discount, $default_currency ); ?>
                    	</span>
                    </span>
                </div>
                <?php endif; ?>
        </div>
        <div class="floatbox">
					<?php echo $this->displayTaxes(); ?>
        </div>
        
        <?php if( $display_credits ): ?>
        <div class="marginbot"></div>
        <div class="floatbox">
        	<span class="left50 header">
        		<span class="inner">
        			 <?php echo JText::_('COM_TIENDA_STORE_CREDIT'); ?>
        		</span>
            </span>
            <span class="left50 right">
            	<span class="inner">
            		- <?php echo $currency_helper->format( $order->order_credit, $default_currency ); ?>
            	</span>
            </span>
        </div>        
        <?php endif; ?>
        
        <div class="marginbot"></div>
        <div class="floatbox">
        	<span class="left50 header">
        		<span class="inner">
        			<?php echo JText::_('COM_TIENDA_TOTAL'); ?>
        		</span>
            </span>
            <span class="left50 right">
            	<span class="inner">
            		<?php echo $currency_helper->format( $order->order_total, $default_currency ); ?>
            	</span>
            </span>
        </div>
</div>
