<?php
include_once 'Utils.php';
class QuickApi_Action extends Widget_Abstract_Contents implements Widget_Interface_Do 
{

    public function execute() 
    {
    }

    public function action()
    {
    	$result = array();

        if (!isset($_GET['apikey'])) {
        	array_push($result, array(
        		'status' => -100,
        		'message' => 'Apikey not set.' ));
        	echo json_encode($result);
        	return;
        }
        //获取系统配置
        $options = Helper::options();
        $apikey = $options->plugin('QuickApi')->apikey;
		$admkey = $options->plugin('QuickApi')->admkey;
        if ($_GET['apikey']!=$apikey) {
        	array_push($result, array(
        		'status' => -101,
        		'message' => 'Apikey invalid.' ));
        	echo json_encode($result);
        	return;
        }        

        array_push($result, array(
        		'status' => 200,
        		'message' => 'Succeeded.' ));
        

        if (!isset($_GET['action'])) 
        {
        	echo json_encode($result);
			return;
        }

        switch ($_GET['action']) {
        	case 'article':
        		$posts = Utils::GetPosts();
        		array_push($result,$posts);
        		break;
        	case 'post':
        		if ((!isset($_GET['admkey']))||($admkey!=$_GET['admkey'])) 
        		{
        			$result[0]['status']=-103;
                	$result[0]['message']="无权限";

                	break;
        		}
        		
        		if ((!isset($_GET['title']))
        			||(!isset($_GET['text']))
        			||(!isset($_GET['user']))
        			||(!isset($_GET['password'])))
        		{
        			$result[0]['status']=-104;
                	$result[0]['message']="文章参数错误";
                	break;
        		}

        		$title=$_GET['title'];
        		$text=$_GET['text'];
				$user=$_GET['user'];
        		$password=$_GET['password'];

        		$this->post_article($user,$password,$result,$title,$text);        	break;	
        	default:
        		# code...
        		break;
        }

        echo json_encode($result);

    }
    private function post_article($user,$password,$result,$title,$text)
    {
        if (!$this->user->hasLogin()) {
            if (!$this->user->login($user, $password, true)) { //使用特定的账号登陆
                $result[0]['status']=-102;
                $result[0]['message']="登录失败";
                return;
            }
        }
        
        $request = Typecho_Request::getInstance();

        //填充文章的相关字段信息。
        $request->setParams(
            array(
                'title'=>$title,
                'text'=>$text,
                'fieldNames'=>array(),
                'fieldTypes'=>array(),
                'fieldValues'=>array(),
                'cid'=>'',
                'do'=>'publish',
                'markdown'=>'1',
                'date'=>'',
                'category'=>array(),
                'tags'=>'',
                'visibility'=>'publish',
                'password'=>'',
                'allowComment'=>'1',
                'allowPing'=>'1',
                'allowFeed'=>'1',
                'trackback'=>'',
            )
        );

        //设置token，绕过安全限制
        $security = $this->widget('Widget_Security');
        $request->setParam('_', $security->getToken($this->request->getReferer()));
        //设置时区，否则文章的发布时间会查8H
        date_default_timezone_set('PRC');

        //执行添加文章操作
        $widgetName = 'Widget_Contents_Post_Edit';
        $reflectionWidget = new ReflectionClass($widgetName);
        if ($reflectionWidget->implementsInterface('Widget_Interface_Do')) {$result[0]['status']=201;
            $result[0]['message']="发布成功";
            echo json_encode($result);  
            $this->widget($widgetName)->writePost();

            return;
        }
    }
}