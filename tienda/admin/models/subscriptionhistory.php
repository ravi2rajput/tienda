<?php
/**
 * @package	Tienda
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

Tienda::load( 'TiendaModelBase', 'models._base' );

class TiendaModelSubscriptionHistory extends TiendaModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
       	$filter_subscriptionid     = $this->getState('filter_subscriptionid');
       	$filter_notified    = $this->getState('filter_notified');
       	$filter_type    = $this->getState('filter_type');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.subscriptionhistory_id) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
        if ($filter_subscriptionid)
        {
            $query->where('tbl.subscription_id = '.$this->_db->Quote($filter_subscriptionid));
        }
        
        if ($filter_notified)
        {
            $query->where('tbl.notify_customer = '.$this->_db->Quote($filter_notified));
        }

        if (strlen($filter_type))
        {
            $query->where('tbl.subscriptionhistory_type = '.$this->_db->Quote($filter_type));
        }        
    }
    
    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__tienda_subscriptions AS subscriptions ON subscriptions.subscription_id = tbl.subscription_id');   
    }
    
    protected function _buildQueryFields(&$query)
    {
        $field = array();

        $field[] = " tbl.* ";
        $field[] = " subscriptions.* ";

        $query->select( $field );
    }
}