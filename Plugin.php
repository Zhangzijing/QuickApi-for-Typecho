<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

include_once 'Utils.php';
/**
 * Quick Api 插件
 * 
 * @package quickapi 
 * @author meloduet
 * @version 0.0.1
 * @link https://v.meloduet.com
 */
class QuickApi_Plugin implements Typecho_Plugin_Interface
{

    /**
     * 激活插件方法,如果激活失败,直接抛出异常
     * 
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function activate()
    {
        Helper::addRoute("route_quickapi","/quickapi","QuickApi_Action",'action');
    }
    
    /**
     * 禁用插件方法,如果禁用失败,直接抛出异常
     * 
     * @static
     * @access public
     * @return void
     * @throws Typecho_Plugin_Exception
     */
    public static function deactivate()
    {
        Helper::removeRoute("route_quickapi");
    }
    
    /**
     * 获取插件配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form 配置面板
     * @return void
     */
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $element_apikey = new Typecho_Widget_Helper_Form_Element_Text('apikey', null, Utils::getRandChar(16), _t('ApiKey 应用程序密钥'), '用于获得基本的数据获取权限, 默认随机生成');
        $element_admkey = new Typecho_Widget_Helper_Form_Element_Text('admkey', null, Utils::getRandChar(16), _t('AdmKey 管理密钥'), '用于获得发布修改等高级权限, 默认随机生成');
        $form->addInput($element_apikey);
        $form->addInput($element_admkey);
    }
    
    /**
     * 个人用户的配置面板
     * 
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){}
    
    /**
     * 插件实现方法
     * 
     * @access public
     * @return void
     */
    public static function render()
    {
        
    }


   
}
