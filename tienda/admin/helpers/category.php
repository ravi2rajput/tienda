<?php
/**
 * @version	1.5
 * @package	Tienda
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

JLoader::import( 'com_tienda.helpers._base', JPATH_ADMINISTRATOR.DS.'components' );
jimport('joomla.filesystem.file');

class TiendaHelperCategory extends TiendaHelperBase
{
	function getImage( $id, $by='id', $alt='', $type='thumb', $url=false )
	{
		switch($type)
		{
			case "full":
				$path = 'categories_images';
				$size = "";
			  break;
			case "thumb":
			default:
				$path = 'categories_thumbs';
				$size = "style='max-width: 48px; max-height: 48px;'";
			  break;
		}
		
		$tmpl = "";
		if (strpos($id, '.'))
		{
			// then this is a filename, return the full img tag if file exists, otherwise use a default image
			$src = (JFile::exists( Tienda::getPath( $path ).DS.$id))
				? Tienda::getUrl( $path ).$id : 'media/com_tienda/images/noimage.png';
			
			// if url is true, just return the url of the file and not the whole img tag
			$tmpl = ($url)
				? $src : "<img src='".$src."' alt='".JText::_( $alt )."' title='".JText::_( $alt )."' name='".JText::_( $alt )."' align='center' border='0' {$size} >";

		}
			else
		{
			if (!empty($id))
			{
				// load the item, get the filename, create tmpl
				JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
				$row = JTable::getInstance('Categories', 'TiendaTable');
				$row->load( (int) $id );
				$id = $row->category_full_image;

				$src = (JFile::exists( Tienda::getPath( $path ).DS.$row->category_full_image))
					? Tienda::getUrl( $path ).$id : 'media/com_tienda/images/noimage.png';

				// if url is true, just return the url of the file and not the whole img tag
				$tmpl = ($url)
					? $src : "<img src='".$src."' alt='".JText::_( $alt )."' title='".JText::_( $alt )."' name='".JText::_( $alt )."' align='center' border='0' {$size} >";
			}			
		}
		return $tmpl;
	}
	
	/**
	 * Returns a formatted path for the category
	 * @param $id
	 * @param $format
	 * @return unknown_type
	 */
	function getPathName( $id, $format='flat', $linkSelf=false )
	{
		$name = '';
		if (empty($id))
		{
			return $name;
		}
		
		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'tables' );
		$item = JTable::getInstance( 'Categories', 'TiendaTable' );
		$item->load( $id );
		if (empty($item->category_id))
		{
			return $name;
		}
		$path = $item->getPath();

		switch ($format)
		{
			case "bullet":
				foreach (@$path as $cat)
				{
					if (!$cat->isroot)
					{
						$name .= '&bull;&nbsp;&nbsp;';
						$name .= JText::_( $cat->category_name );
						$name .= "<br/>";
					}
				}
					$name .= '&bull;&nbsp;&nbsp;';
					$name .= JText::_( $item->category_name );
			  break;
            case 'links':
                $link = JRoute::_("index.php?option=com_tienda&view=products&filter_category=", false);
                $name .= " <a href='$link'>".JText::_('All Categories').'</a> ';
			    foreach (@$path as $cat) 
			    {
			        if (!$cat->isroot) {
			            $link = JRoute::_("index.php?option=com_tienda&view=products&filter_category=$cat->category_id", false);
			            $name .= " > ";
			            $name .= " <a href='$link'>".JText::_( $cat->category_name ).'</a> ';
			        }
			    }
                    $name .= " > ";
                    if ($linkSelf)
                    {
                    	$link = JRoute::_("index.php?option=com_tienda&view=products&filter_category=$id", false);
                    	$name .= " <a href='$link'>".JText::_( $item->category_name ).'</a> ';
                    	//$name .= JText::_( $item->category_name );
                    }
                        else
                    {
                        $name .= JText::_( $item->category_name );	
                    }
			        
                break;
			default:
				foreach (@$path as $cat)
				{
					if (!$cat->isroot)
					{
						$name .= " / ";
						$name .= JText::_( $cat->category_name );
					}
				}
					$name .= " / ";
					$name .= JText::_( $item->category_name );
			  break;
		}

		return $name;
	}
	
    /**
     * Finds the prev & next items in the list 
     *  
     * @param $id   product id
     * @return array( 'prev', 'next' )
     */
    function getSurrounding( $id )
    {
        $return = array();
        
        $prev = intval( JRequest::getVar( "prev" ) );
        $next = intval( JRequest::getVar( "next" ) );
        if ($prev || $next) 
        {
            $return["prev"] = $prev;
            $return["next"] = $next;
            return $return;
        }
        
        $app = JFactory::getApplication();
        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tienda'.DS.'models' );
        $model = JModel::getInstance( 'Categories', 'TiendaModel' );
        $ns = $app->getName().'::'.'com.tienda.model.'.$model->getTable()->get('_suffix');
        $state = array();
        
        $state['limit']     = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $state['limitstart'] = $app->getUserStateFromRequest($ns.'limitstart', 'limitstart', 0, 'int');
        $state['filter']    = $app->getUserStateFromRequest($ns.'.filter', 'filter', '', 'string');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
                
        $state['order']             = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.lft', 'cmd');
        $state['filter_id_from']    = $app->getUserStateFromRequest($ns.'id_from', 'filter_id_from', '', '');
        $state['filter_id_to']      = $app->getUserStateFromRequest($ns.'id_to', 'filter_id_to', '', '');
        $state['filter_name']       = $app->getUserStateFromRequest($ns.'name', 'filter_name', '', '');
        $state['filter_parentid']   = $app->getUserStateFromRequest($ns.'parentid', 'filter_parentid', '', '');
        $state['filter_enabled']    = $app->getUserStateFromRequest($ns.'enabled', 'filter_enabled', '', '');
                
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );   
        }
        $rowset = $model->getList();
            
        $found = false;
        $prev_id = '';
        $next_id = '';

        for ($i=0; $i < count($rowset) && empty($found); $i++) 
        {
            $row = $rowset[$i];     
            if ($row->category_id == $id) 
            { 
                $found = true; 
                $prev_num = $i - 1;
                $next_num = $i + 1;
                if (isset($rowset[$prev_num]->category_id)) { $prev_id = $rowset[$prev_num]->category_id; }
                if (isset($rowset[$next_num]->category_id)) { $next_id = $rowset[$next_num]->category_id; }
    
            }
        }
        
        $return["prev"] = $prev_id;
        $return["next"] = $next_id; 
        return $return;
    }
}