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

if (!defined('EQDKP_INC'))
{
  header('HTTP/1.0 404 Not Found');
  exit;
}


/*+----------------------------------------------------------------------------
  | localitembase
  +--------------------------------------------------------------------------*/
class sk_startorder extends plugin_generic
{
  /**
   * __dependencies
   * Get module dependencies
   */
  public static function __shortcuts()
  {
    $shortcuts = array('user', 'config', 'pdc', 'pfh', 'pdh', 'routing');
    return array_merge(parent::$shortcuts, $shortcuts);
  }

  public $version    = '1.0.1';
  public $build      = '';
  public $copyright  = 'GodMod';
  public $vstatus    = 'Alpha';
  
  protected static $apiLevel = 23;

  /**
    * Constructor
    * Initialize all informations for installing/uninstalling plugin
    */
  public function __construct()
  {
    parent::__construct();

    $this->add_data(array (
      'name'              => 'SK Startorder',
      'code'              => 'sk_startorder',
      'path'              => 'sk_startorder',
      'template_path'     => 'plugins/sk_startorder/templates/',
      'icon'              => 'fa fa-list',
      'version'           => $this->version,
      'author'            => $this->copyright,
      'description'       => $this->user->lang('sk_startorder_short_desc'),
      'long_description'  => $this->user->lang('sk_startorder_long_desc'),
      'homepage'          => EQDKP_PROJECT_URL,
      'manuallink'        => false,
      'plus_version'      => '2.1',
      'build'             => $this->build,
    ));

    $this->add_dependency(array(
      'plus_version'      => '2.1'
    ));

	// -- Menu --------------------------------------------
    $this->add_menu('admin', $this->gen_admin_menu());
    
    $this->add_permission('a', 'manage',	'N', $this->user->lang('manage'),	array(2,3));
}

  /**
    * pre_install
    * Define Installation
    */
  public function pre_install()
  {
  
  }
  
  /**
   * post_install
   * Add Default Settings
   */
  public function post_install(){
  }

  /**
    * pre_uninstall
    * Define uninstallation
    */
  public function pre_uninstall()
  {

  }


  /**
    * gen_admin_menu
    * Generate the Admin Menu
    */
  private function gen_admin_menu()
  {

    $admin_menu = array (array(
        'name' => $this->user->lang('sk_startorder'),
        'icon' => 'fa fa-list',
        1 => array (
          'link'  => 'plugins/sk_startorder/admin/settings.php'.$this->SID,
          'text'  => $this->user->lang('skso_change_order'),
          'check' => 'a_config_man',
          'icon'  => 'fa-list'
        ),
    ));


    return $admin_menu;
  }

}
?>
