<?php
class Utils{

  public static function getRandChar($length){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];
   }

   return $str;
  }
  	public static function GetPosts()
	{
		$options = Typecho_Widget::widget('Widget_Options');
		
		/**
		 * 获取数据库实例化对象
		 * 用静态变量存储实例化的数据库对象,可以保证数据连接仅进行一次
		 */
		$db = Typecho_Db::get();
		
		$select = $db->select('cid', 'title', 'slug', 'created', 'allowComment', 'commentsNum')
					->from('table.contents')
					->where('status = ?', 'publish')
					->where('type = ?', 'post');
		$rawposts = $db->fetchAll($select);
		$posts = array();
		// Loop through each post and sort it into a structured array
		foreach( $rawposts as $post ) {
			/** 取出所有分类 */
            $categories = $db->fetchAll($db
				->select('slug')->from('table.metas')
				->join('table.relationships', 'table.metas.mid = table.relationships.mid')
				->where('table.relationships.cid = ?', $post['cid'])
				->where('table.metas.type = ?', 'category')
				->order('table.metas.order', Typecho_Db::SORT_ASC));
            /** 取出第一个分类作为slug条件 */
            $post['category'] = current(Typecho_Common::arrayFlatten($categories, 'slug'));
		
			$date = new Typecho_Date($post['created']);
			$post['year'] = $date->year;
			$post['month'] = $date->month;
			$post['day'] = $date->day;
			
			$type = 'post';//$p['type'];
            $routeExists = (NULL != Typecho_Router::get($type));
            $permalink = $routeExists ? Typecho_Router::url($type, $post, $options->index) : '#';
			$post['permalink'] = $permalink;
			
			$posts[ $post['year'] . '.' . $post['month'] ][] = $post;
		}
		$rawposts = null; // More memory cleanup
		return $posts;
	}
 }

