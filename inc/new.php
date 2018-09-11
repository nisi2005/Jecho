<?php
//一下为添加代码
/**
 * 禁用 WordPress 的 JSON REST API 
 * https://www.wpdaxue.com/disable-json-rest-api-in-wordpress.html
 */
add_filter('rest_enabled', '_return_false');
add_filter('rest_jsonp_enabled', '_return_false');
remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
remove_action( 'wp_head', 'wp_oembed_add_discovery_links', 10 );
/**
 * WordPress 关闭 XML-RPC 的 pingback 端口
 */
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
	unset( $methods['pingback.ping'] );
	return $methods;
}
//去除分类标志代码
add_action( 'load-themes.php',  'no_category_base_refresh_rules');
add_action('created_category', 'no_category_base_refresh_rules');
add_action('edited_category', 'no_category_base_refresh_rules');
add_action('delete_category', 'no_category_base_refresh_rules');
function no_category_base_refresh_rules() {
    global $wp_rewrite;
    $wp_rewrite -> flush_rules();
}
add_action('init', 'no_category_base_permastruct');
function no_category_base_permastruct() {
    global $wp_rewrite, $wp_version;
    if (version_compare($wp_version, '3.4', '<')) {
        // For pre-3.4 support
        $wp_rewrite -> extra_permastructs['category'][0] = '%category%';
    } else {
        $wp_rewrite -> extra_permastructs['category']['struct'] = '%category%';
    }
}
add_filter('category_rewrite_rules', 'no_category_base_rewrite_rules');
function no_category_base_rewrite_rules($category_rewrite) {
    $category_rewrite = array();
    $categories = get_categories(array('hide_empty' => false));
    foreach ($categories as $category) {
        $category_nicename = $category -> slug;
        if ($category -> parent == $category -> cat_ID)// recursive recursion
            $category -> parent = 0;
        elseif ($category -> parent != 0)
            $category_nicename = get_category_parents($category -> parent, false, '/', true) . $category_nicename;
        $category_rewrite['(' . $category_nicename . ')/(?:feed/)?(feed|rdf|rss|rss2|atom)/?$'] = 'index.php?category_name=$matches[1]&feed=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/page/?([0-9]{1,})/?$'] = 'index.php?category_name=$matches[1]&paged=$matches[2]';
        $category_rewrite['(' . $category_nicename . ')/?$'] = 'index.php?category_name=$matches[1]';
    }
    global $wp_rewrite;
    $old_category_base = get_option('category_base') ? get_option('category_base') : 'category';
    $old_category_base = trim($old_category_base, '/');
    $category_rewrite[$old_category_base . '/(.*)$'] = 'index.php?category_redirect=$matches[1]';
    return $category_rewrite;
}
add_filter('query_vars', 'no_category_base_query_vars');
function no_category_base_query_vars($public_query_vars) {
    $public_query_vars[] = 'category_redirect';
    return $public_query_vars;
}
add_filter('request', 'no_category_base_request');
function no_category_base_request($query_vars) {
    //print_r($query_vars); // For Debugging
    if (isset($query_vars['category_redirect'])) {
        $catlink = trailingslashit(get_option('home')) . user_trailingslashit($query_vars['category_redirect'], 'category');
        status_header(301);
        header("Location: $catlink");
        exit();
    }
    return $query_vars;
}
//去WordPress DNS预加载代码
function remove_dns_prefetch( $hints, $relation_type ) {
if ( 'dns-prefetch' === $relation_type ) {
return array_diff( wp_dependencies_unique_hosts(), $hints );
}
return $hints;
}
add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );
//去除“重写规则必须被更新”
add_filter('got_rewrite', 'nginx_has_rewrites');
function nginx_has_rewrites() {
    return true;
}
// 同时删除head和feed中的WP版本号
add_filter('the_generator', 'fanly_remove_wp_version');
function fanly_remove_wp_version() { return '';}
//后台版本
add_filter('admin_footer_text', 'left_admin_footer_text');
function left_admin_footer_text($text) {
// 左边信息改成自己的站点
$text = '感谢访问雨沐晨枫博客';
return $text;
}
add_filter('update_footer', 'right_admin_footer_text', 11);
function right_admin_footer_text($text) {
// 隐藏右边版本信息
}
// 同时删除head和feed中的WP版本号
function ludou_remove_wp_version() {
  return '';
}
add_filter('the_generator', 'ludou_remove_wp_version');
// 隐藏js/css附加的WP版本号
function ludou_remove_wp_version_strings( $src ) {
  global $wp_version;
  parse_str(parse_url($src, PHP_URL_QUERY), $query);
  if ( !empty($query['ver']) && $query['ver'] === $wp_version ) {
    // 用WP版本号 + 12.8来替代js/css附加的版本号
    // 既隐藏了WordPress版本号，也不会影响缓存
    // 建议把下面的 12.8 替换成其他数字，以免被别人猜出
    $src = str_replace($wp_version, $wp_version + 2017, $src);
  }
  return $src;
}
add_filter( 'script_loader_src', 'ludou_remove_wp_version_strings' );
add_filter( 'style_loader_src', 'ludou_remove_wp_version_strings' );
//wordpress上传文件重命名
function git_upload_filter($file) {
    $time = date("YmdHis");
    $file['name'] = $time . "" . mt_rand(1, 100) . "." . pathinfo($file['name'], PATHINFO_EXTENSION);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'git_upload_filter');
//腾讯CDN自动清理缓存{
$secretKey = '75jEZiAZnxyxnS4Zu42qVyGTYSAgkkYB';
$secretId  = 'AKIDvgIqGU8UDnIckkjL0Ws32rV1NZZRPf1B';
add_action('publish_post', 'Clean_By_Publish', 0);
add_action('comment_post', 'Clean_By_Comments',0);
add_action('comment_unapproved_to_approved', 'Clean_By_Approved',0);
function Clean_By_Publish($post_ID){
    global $secretKey,$secretId;
    $url = get_permalink($post_ID);
    $action='RefreshCdnUrl';
    $PRIVATE_PARAMS = array(
                    'urls.0' => home_url(),
                    'urls.1' => $url ,
                    );
    $HttpUrl="cdn.api.qcloud.com";
    $HttpMethod="POST";
    $isHttps =true;
    $COMMON_PARAMS = array(
                    'Nonce' => rand(),
                    'Timestamp' =>time(NULL),
                    'Action' =>$action,
                    'SecretId' => $secretId,
                    );
    CreateRequest($HttpUrl,$HttpMethod,$COMMON_PARAMS,$secretKey, $PRIVATE_PARAMS, $isHttps);
}
function Clean_By_Comments($comment_id) 
{
    global $secretKey,$secretId;
    $comment = get_comment($comment_id);
    $url = get_permalink($comment->comment_post_ID);
    $action='RefreshCdnUrl';
    $PRIVATE_PARAMS = array(
                    'urls.0' => $url,
                    );
    $HttpUrl="cdn.api.qcloud.com";
    $HttpMethod="POST";
    $isHttps =true;
    $COMMON_PARAMS = array(
                    'Nonce' => rand(),
                    'Timestamp' =>time(NULL),
                    'Action' =>$action,
                    'SecretId' => $secretId,
                    );
    CreateRequest($HttpUrl,$HttpMethod,$COMMON_PARAMS,$secretKey, $PRIVATE_PARAMS, $isHttps);
}
function Clean_By_Approved($comment)
{
    global $secretKey,$secretId;
    $url = get_permalink($comment->comment_post_ID);
    $action='RefreshCdnUrl';
    $PRIVATE_PARAMS = array(
                    'urls.0' => $url,
                    );
    $HttpUrl="cdn.api.qcloud.com";
    $HttpMethod="POST";
    $isHttps =true;
    $COMMON_PARAMS = array(
                    'Nonce' => rand(),
                    'Timestamp' =>time(NULL),
                    'Action' =>$action,
                    'SecretId' => $secretId,
                    );
    CreateRequest($HttpUrl,$HttpMethod,$COMMON_PARAMS,$secretKey, $PRIVATE_PARAMS, $isHttps);
}
function CreateRequest($HttpUrl,$HttpMethod,$COMMON_PARAMS,$secretKey, $PRIVATE_PARAMS, $isHttps)
{
        $FullHttpUrl = $HttpUrl."/v2/index.php";
        $ReqParaArray = array_merge($COMMON_PARAMS, $PRIVATE_PARAMS);
        ksort($ReqParaArray);
        $SigTxt = $HttpMethod.$FullHttpUrl."?";
        $isFirst = true;
        foreach ($ReqParaArray as $key => $value)
        {
                if (!$isFirst) 
                {
                        $SigTxt = $SigTxt."&";
                }
                $isFirst= false;
                if(strpos($key, '_'))
                {
                        $key = str_replace('_', '.', $key);
                }
                $SigTxt=$SigTxt.$key."=".$value;
        }
        $Signature = base64_encode(hash_hmac('sha1', $SigTxt, $secretKey, true));
        $Req = "Signature=".urlencode($Signature);
        foreach ($ReqParaArray as $key => $value)
        {
                $Req=$Req."&".$key."=".urlencode($value);
        }
        if($HttpMethod === 'GET')
        {
                if($isHttps === true)
                {
                        $Req="https://".$FullHttpUrl."?".$Req;
                }
                else
                {
                        $Req="http://".$FullHttpUrl."?".$Req;
                }
                $Rsp = file_get_contents($Req);
        }
        else
        {
                if($isHttps === true)
                {
                        $Rsp= SendPost("https://".$FullHttpUrl,$Req,$isHttps);
                }
                else
                {
                        $Rsp= SendPost("http://".$FullHttpUrl,$Req,$isHttps);
                }
        }
        //var_export(json_decode($Rsp,true));
        return json_decode($Rsp,true);
}
function SendPost($FullHttpUrl, $Req, $isHttps)
{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $Req);
        curl_setopt($ch, CURLOPT_URL, $FullHttpUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1 );
        if ($isHttps === true) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
        }
        $result = curl_exec($ch);
        return $result;
}
//}
//WordPress文章关键词自动内链
function tag_sort($a, $b){
	if ( $a->name == $b->name ) return 0;
	return ( strlen($a->name) > strlen($b->name) ) ? -1 : 1;
}
function tag_link($content){
	$match_num_from = 1;	//一个标签少于几次不链接
	$match_num_to = 4;	//一个标签最多链接几次
	$posttags = get_the_tags();
	if ($posttags) {
		usort($posttags, "tag_sort");
		foreach($posttags as $tag) {
			$link = get_tag_link($tag->term_id);
			$keyword = $tag->name;
			//链接代码
			$cleankeyword = stripslashes($keyword);
			$url = "<a href=\"$link\" title=\"".str_replace('%s',addcslashes($cleankeyword, '$'),__('更多关于 %s 的文章'))."\"";
			$url .= ' target="_blank"';
			$url .= ">".addcslashes($cleankeyword, '$')."</a>";
			$limit = rand($match_num_from,$match_num_to);
			//不链接代码
			$content = preg_replace( '|(<a[^>]+>)(.*)<pre.*?>('.$ex_word.')(.*)<\/pre>(</a[^>]*>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
			$content = preg_replace( '|(<img)(.*?)('.$ex_word.')(.*?)(>)|U'.$case, '$1$2%&&&&&%$4$5', $content);
			$cleankeyword = preg_quote($cleankeyword,'\'');
			$regEx = '\'(?!((<.*?)|(<a.*?)))('. $cleankeyword . ')(?!(([^<>]*?)>)|([^>]*?</a>))\'s' . $case;
			$content = preg_replace($regEx,$url,$content,$limit);
			$content = str_replace( '%&&&&&%', stripslashes($ex_word), $content);
		}
	}
	return $content;
}
add_filter('the_content','tag_link',1);
//屏蔽WP产生的rel=shortlink头部信息
remove_action('template_redirect','wp_shortlink_header',11,0);
//屏蔽WP自带API产生的头部信息
remove_action( 'template_redirect','rest_output_link_header', 11, 0 );
//给外部链接加上跳转
add_filter('the_content','the_content_nofollow',999);
function the_content_nofollow($content)
{
	preg_match_all('/<a(.*?)href="(.*?)"(.*?)>/',$content,$matches);
	if($matches){
		foreach($matches[2] as $val){
			if(strpos($val,'://')!==false && strpos($val,home_url())===false && !preg_match('/\.(jpg|jepg|png|ico|bmp|gif|tiff)/i',$val)){
			    $content=str_replace("href=\"$val\"", "href=\"".home_url()."/go/?url=$val\" ",$content);
			}
		}
	}
	return $content;
}
/*****************************************************
 函数名称：wp_login_notify v1.0 by DH.huahua. 
 函数作用：有登录wp后台就会email通知博主
******************************************************/
function wp_login_notify()
{
    date_default_timezone_set('PRC');
    $admin_email = get_bloginfo ('admin_email');
    $to = $admin_email;
	$subject = '你的博客空间登录提醒';
	$message = '<p>你好！你的博客空间(' . get_option("blogname") . ')有登录！</p>' . 
	'<p>请确定是您自己的登录，以防别人攻击！登录信息如下：</p>' . 
	'<p>登录时间：' . date("Y-m-d H:i:s") .  '<p>' .
	'<p>登录IP：' . $_SERVER['HTTP_X_FORWARDED_FOR'] . '<p>';	
	$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
	$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
	wp_mail( $to, $subject, $message, $headers );
}
add_action('wp_login', 'wp_login_notify');
/*****************************************************
 函数名称：wp_login_failed_notify v1.0 by DH.huahua. 
 函数作用：有错误登录wp后台就会email通知博主
******************************************************/
function wp_login_failed_notify()
{
    date_default_timezone_set('PRC');
    $admin_email = get_bloginfo ('admin_email');
    $to = $admin_email;
	$subject = '你的博客空间登录错误警告';
	$message = '<p>你好！你的博客空间(' . get_option("blogname") . ')有登录错误！</p>' . 
	'<p>请确定是您自己的登录失误，以防别人攻击！登录信息如下：</p>' . 
	'<p>登录名：' . $_POST['log'] . '<p>' .
	'<p>登录密码：' . $_POST['pwd'] .  '<p>' .
	'<p>登录时间：' . date("Y-m-d H:i:s") .  '<p>' .
	'<p>登录IP：' . $_SERVER['HTTP_X_FORWARDED_FOR'] . '<p>';	
	$wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));
	$from = "From: \"" . get_option('blogname') . "\" <$wp_email>";
	$headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";
	wp_mail( $to, $subject, $message, $headers );
}
add_action('wp_login_failed', 'wp_login_failed_notify');
//禁用文章自动保存
add_action('wp_print_scripts','disable_autosave');
function disable_autosave(){
wp_deregister_script('autosave');
}
 
//禁用文章修订版本
add_filter( 'wp_revisions_to_keep', 'specs_wp_revisions_to_keep', 10, 2 );
function specs_wp_revisions_to_keep( $num, $post ) {
return 0;
}
//禁止加载默认jq库
if ( !is_admin() ) { // 后台不禁止
function my_init_method() {
wp_deregister_script( 'jquery' ); // 取消原有的 jquery 定义
}
add_action('init', 'my_init_method');
}
wp_deregister_script( 'l10n' );
//More users' info
function get_client_ip(){
    if(getenv("HTTP_CLIENT_IP")&&strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown")) $ip = getenv("HTTP_CLIENT_IP");
    elseif(getenv("HTTP_X_FORWARDED_FOR")&&strcasecmp(getenv("HTTP_X_FORWARDED_FOR"),"unknown")) $ip = getenv("HTTP_X_FORWARDED_FOR");
    elseif(getenv("REMOTE_ADDR")&&strcasecmp(getenv("REMOTE_ADDR"),"unknown")) $ip = getenv("REMOTE_ADDR");
    elseif(isset($_SERVER['REMOTE_ADDR'])&&$_SERVER['REMOTE_ADDR']&&strcasecmp($_SERVER['REMOTE_ADDR'],"unknown")) $ip = $_SERVER['REMOTE_ADDR'];
    else $ip = "unknown";
    return ($ip);
}
add_action('wp_login','insert_last_login');
function insert_last_login($login){
    global $user_id;
    $user = get_userdatabylogin($login);
    update_user_meta($user->ID,'last_login',current_time('mysql'));
    $last_login_ip = get_client_ip();
    update_user_meta($user->ID,'last_login_ip',$last_login_ip);
}
add_filter('manage_users_columns','add_user_additional_column');
function add_user_additional_column($columns){
    $columns['user_nickname'] = '昵称';
    $columns['user_url'] = '网站';
    $columns['reg_time'] = '注册时间';
    $columns['last_login'] = '上次登录';
    $columns['last_login_ip'] = '登录IP';
    unset($columns['name']);
    return $columns;
}
add_action('manage_users_custom_column','show_user_additional_column_content',10,3);
function show_user_additional_column_content($value,$column_name,$user_id){
    $user = get_userdata($user_id);
    if('user_nickname'==$column_name) return $user->nickname;
    if('user_url'==$column_name) return '<a href="'.$user->user_url.'" target="_blank">'.$user->user_url.'</a>';
    if('reg_time'==$column_name) return get_date_from_gmt($user->user_registered);
    if('last_login'==$column_name&&$user->last_login) return get_user_meta($user->ID,'last_login',ture);
    if('last_login_ip'==$column_name) return get_user_meta($user->ID,'last_login_ip',ture);
    return $value;
}
add_filter("manage_users_sortable_columns",'cmhello_users_sortable_columns');
function cmhello_users_sortable_columns($sortable_columns){
    $sortable_columns['reg_time'] = 'reg_time';
    return $sortable_columns;
}
add_action( 'pre_user_query','cmhello_users_search_order');
function cmhello_users_search_order($obj){
    if(!isset($_REQUEST['orderby'])||$_REQUEST['orderby']=='reg_time'){
        if(!in_array($_REQUEST['order'],array('asc','desc'))) $_REQUEST['order'] = 'desc';
        $obj->query_orderby = "ORDER BY user_registered ".$_REQUEST['order']."";
    }
}
/**
 * 登陆验证码
 *
 * 登陆页面显示数字算术验证码
 */
//后台登陆数学验证码
function myplugin_add_login_fields() {
//获取两个随机数, 范围0~9
$num1=rand(-10,30);
$num2=rand(2,70);
//最终网页中的具体内容
    echo "<p><label for='math' class='small'>证明你不是机器人，告诉我</label> $num1 + $num2 = ?<input type='text' name='sum' class='input' value='' size='25' tabindex='4'>"
."<input type='hidden' name='num1' value='$num1'>"
."<input type='hidden' name='num2' value='$num2'></p>";
}
add_action('login_form','myplugin_add_login_fields');
function login_val() {
$sum=$_POST['sum'];//用户提交的计算结果
switch($sum){
//得到正确的计算结果则直接跳出
case $_POST['num1']+$_POST['num2']:break;
//未填写结果时的错误讯息
case null:wp_die('错误: 请输入验证码.');break;
//计算错误时的错误讯息
default:wp_die('错误: 验证码错误,请重试.');
}
}
add_action('login_form_login','login_val');
//登录页面的LOGO链接为首页链接
add_filter('login_headerurl', create_function(false,"return get_bloginfo('url');"));
//修改后台显示更新的代码 留笔记博客 2016-09-22
add_filter('pre_site_transient_update_core',    create_function('$a', "return null;")); // 关闭核心提示
add_filter('pre_site_transient_update_plugins', create_function('$a', "return null;")); // 关闭插件提示
add_filter('pre_site_transient_update_themes',  create_function('$a', "return null;")); // 关闭主题提示
remove_action('admin_init', '_maybe_update_plugins'); // 禁止 WordPress 更新插件
remove_action('admin_init', '_maybe_update_core');    // 禁止 WordPress 检查更新
remove_action('admin_init', '_maybe_update_themes');  // 禁止 WordPress 更新主题

/*禁用Google字体 提升后台速度*/
function coolwp_remove_open_sans_from_wp_core() {
    wp_deregister_style( 'open-sans' );
    wp_register_style( 'open-sans', false );
    wp_enqueue_style('open-sans','');
}
add_action( 'init', 'coolwp_remove_open_sans_from_wp_core' );
//阻止WordPress的PingBack
function no_self_ping( &$links ) {
 $home = get_option( 'home' );
 foreach ( $links as $l => $link )
 if ( 0 === strpos( $link, $home ) )
 unset($links[$l]);
}
add_action( 'pre_ping', 'no_self_ping' );
//在 WordPress 编辑器添加“下一页”按钮
function add_next_page_button($mce_buttons) {
    $pos = array_search('wp_more', $mce_buttons, true);
    if ($pos !== false) {
        $tmp_buttons = array_slice($mce_buttons, 0, $pos + 1);
        $tmp_buttons[] = 'wp_page';
        $mce_buttons = array_merge($tmp_buttons, array_slice($mce_buttons, $pos + 1));
    }
    return $mce_buttons;
}
add_filter('mce_buttons', 'add_next_page_button');
//取消静态资源版本的查询
function _remove_script_version( $src ){
    $parts = explode( '?', $src );
    return $parts[0];
}
add_filter( 'script_loader_src', '_remove_script_version', 15, 1 );
add_filter( 'style_loader_src', '_remove_script_version', 15, 1 );
/* 给分类目录和单页链接末尾加上斜杠 */
$permalink_structure = get_option('permalink_structure');
if (!$permalink_structure || '/' === substr($permalink_structure, -1))
    return;
add_filter('user_trailingslashit', 'ppm_fixe_trailingslash', 10, 2);
function ppm_fixe_trailingslash($url, $type)
{
   if ('single' === $type)
     return $url;
     return trailingslashit($url);
}
/***圆角背景色标签***/
function colorCloud($text) {  
$text = preg_replace_callback('|<a (.+?)>|i', 'colorCloudCallback', $text);  
return $text;  
}  
function colorCloudCallback($matches) {  
$text = $matches[1];  
$colors = array('F99','C9C','F96','6CC','6C9','37A7FF','B0D686','E6CC6E');  
$color=$colors[dechex(rand(0,7))]; 
$pattern = '/style=(\'|\")(.*)(\'|\")/i';  
$text = preg_replace($pattern, "style=\"display: inline-block; *display: inline; *zoom: 1; color: #fff; padding: 1px 5px; margin: 0 5px 5px 0; background-color: #{$color}; border-radius: 3px; -webkit-transition: background-color .4s linear; -moz-transition: background-color .4s linear; transition: background-color .4s linear;\"", $text);  
$pattern = '/style=(\'|\")(.*)(\'|\")/i';  
return "<a $text>";  
}  
add_filter('wp_tag_cloud', 'colorCloud', 1);