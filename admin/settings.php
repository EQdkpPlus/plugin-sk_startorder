<?php
/*	Project:	EQdkp-Plus
 *	Package:	Local Itembase Plugin
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);
define('PLUGIN', 'sk_startorder');

$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path.'common.php');


/*+----------------------------------------------------------------------------
  | SKSOrder
  +--------------------------------------------------------------------------*/
class SKSOrder extends page_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('pm', 'user', 'config', 'core', 'in', 'jquery', 'html', 'tpl');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  /**
   * Constructor
   */
  public function __construct()
  {
    // plugin installed?
    if (!$this->pm->check('sk_startorder', PLUGIN_INSTALLED))
      message_die($this->user->lang('skso_plugin_not_installed'));

    $handler = array(
    );
	
	$this->user->check_auth('a_sk_startorder_manage');  
	
    parent::__construct(null, $handler);

    $this->process();
  }
  
  private $arrData = false;


  public function update(){
  		$strCurrentLayout = $this->pdh->get_eqdkp_base_layout($this->config->get('eqdkp_layout'));
  		
  		foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
  			$arrSort = $this->in->getArray('sort_'.$mdkp_id, 'int');

  			if($strCurrentLayout == 'sk'){
  				$this->config->set('sk_startlist_'.$mdkp_id, $arrSort);
  			} else {
  				$this->config->set('sk_fix_startlist_'.$mdkp_id, $arrSort);
  			}
  		}
  		
  		$this->pdc->flush();
  		
  		$this->core->message($this->user->lang('save_suc'), $this->user->lang('success'), 'green');
  }
  
  /**
   * display
   * Display the page
   *
   * @param    array  $messages   Array of Messages to output
   */
  public function display($messages=array())
  {
    // -- Messages ------------------------------------------------------------
    if ($messages)
    {
      foreach($messages as $name)
        $this->core->message($name, $this->user->lang('sk_startorder'), 'green');
    }

    $strCurrentLayout = $this->pdh->get_eqdkp_base_layout($this->config->get('eqdkp_layout'));
    if(!in_array($strCurrentLayout, array('sk', 'sk_fixed', 'sk_bottom'))){
    	message_die($this->user->lang('skso_no_sk_layout'));
    }
    
    $arrMembers = $this->pdh->sort($this->pdh->get('member', 'id_list', array(false, false)), 'member', 'creation_date', 'asc');
    
    $blnShowTwinks = $this->config->get('show_twinks');
    
    foreach($this->pdh->get('multidkp',  'id_list', array()) as $mdkp_id){
		if($strCurrentLayout == 'sk'){
			$startList = $this->config->get('sk_startlist_'.$mdkp_id);
		} else {
			$startList = $this->config->get('sk_fix_startlist_'.$mdkp_id);
		}

    	if (!$startList){
    		shuffle($arrMembers);
    		$startList = $arrMembers;
    	}
    	
    	$member_hash = array('single' => array(), 'multi' => array());
    	
    	foreach($startList as $intMemberID){
    		if (in_array($intMemberID, $arrMembers)){
    			$member_hash['single'][] = $intMemberID;
    			$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
    			if (!in_array($intMainID, $member_hash['multi'])) $member_hash['multi'][] = $intMainID;
    		}
    	}
    	
    	//New Members at the bottom
    	foreach($arrMembers as $intMemberID){
    		if (!in_array($intMemberID, $startList)){
    			$member_hash['single'][] = $intMemberID;
    			$intMainID = $this->pdh->get('member', 'mainid', array($intMemberID));
    			if (!in_array($intMainID, $member_hash['multi'])) $member_hash['multi'][] = $intMainID;
    		}
    	}
    	
    	if($blnShowTwinks){
    		$arrOutMembers = $member_hash['single'];
    	} else {
    		$arrOutMembers = $member_hash['multi'];
    	}
    	
    	$this->tpl->assign_block_vars('tab_row', array(
    		'MDKP_NAME' => $this->pdh->get('multidkp', 'name', array($mdkp_id)),
    		'ID'		=> $mdkp_id,
    	));
    	
    	foreach ($arrOutMembers as $key => $memberID){
    		$this->tpl->assign_block_vars('tab_row.member_row', array(
    			'ID'		=> $memberID,
   				'NAME'		=> $this->pdh->geth('member', 'name', array($memberID)),
    		));
    	}
    }
   
	$this->jquery->Tab_header('mdkp_tabs');
	
	$this->tpl->add_js("
			$(\"#profilefield_table tbody\").sortable({
				cancel: '.not-sortable, input, select, th',
				cursor: 'pointer',
			});
		", "docready");
    
    // -- EQDKP ---------------------------------------------------------------
    $this->core->set_vars(array(
      'page_title'    => $this->user->lang('skso_change_order'),
      'template_path' => $this->pm->get_data('sk_startorder', 'template_path'),
      'template_file' => 'admin/settings.html',
    		'page_path'			=> [
    				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
    				['title'=>$this->user->lang('skso_change_order'), 'url'=>' '],
    		],
      'display'       => true
    ));

  }
  
}

registry::register('SKSOrder');

?>