<?php
/**Sitemap (можно перенести в отдельный файл)*/
$GLOBALS['sitemap'] = array (
    '_404' => '404.php',   // Страница 404</span>
    '/en' => 'en.php',   // Английская версия
    '/' => 'main.php',   // Главная страница
    '/about' => 'about.php', //О нас
    '/news(/[A-Za-z0-9-]+)?' => 'news.php',   // Новости - страница c параметрами
    '/pages(/[A-Za-z0-9-]+)?' => 'pages.php',
    '/partners' => 'partners.php',
    '/feedback' => 'feedback.php',
    '/contacts' => 'contacts.php',
);
// Код роутера
class uSitemap {
    public $title = '';
    public $params = null;
    public $classname = '';
    public $data = null;
    public $request_uri = '';
    public $url_info = array();
    public $found = false;
    public $arr_params = false;

    function __construct() {
        $this->mapClassName();
    }
    function mapClassName() {
        $this->classname = '';
        $this->title = '';
        $this->params = null;

        $map = &$GLOBALS['sitemap'];
        $this->request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->url_info = parse_url($this->request_uri);
        $uri = urldecode($this->url_info['path']);
        $data = false;
        foreach ($map as $term => $dd) {
            $match = array();
            $i = preg_match('@^'.$term.'$@Uu', $uri, $match);
            if ($i > 0) {
                //dpr(array_filter(explode('/',$uri)));
                // Get class name and main title part
                $m = explode(',', $dd);
                //dpr($term);
                $data = array(
                    'classname' => isset($m[0])?strtolower(trim($m[0])):'',
                    'title' => isset($m[1])?trim($m[1]):'',
                    'params' => $match,
                    'arr_params' => array_values(array_filter(explode('/',$uri)))
                );
                break;
            }
        }
        if($data === false){
            // 404
            if(isset($map['_404'])){
                $dd = $map['_404'];
                $m = explode(',', $dd);
                $this->classname = strtolower(trim($m[0]));
                $this->title = @trim($m[1]);
                $this->params = array();
            }
            $this->found = false;
        }else{
            // Found!
            $this->classname = $data['classname'];
            $this->title = $data['title'];
            $this->params = $data['params'];
            $this->arr_params = $data['arr_params'];
            $this->found = true;
        }
        return $this->classname;
    }
}