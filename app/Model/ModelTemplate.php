<?php

/*
 * This file is part of the PHP Indonesia package.
 *
 * (c) PHP Indonesia 2013
 */

namespace app\Model;

use app\Parameter;
use \Twig_SimpleFilter;
use \Twig_Loader_Filesystem;
use \Twig_Environment;
use dflydev\markdown\MarkdownExtraParser;

/**
 * ModelTemplate
 *
 * @author PHP Indonesia Dev
 */
class ModelTemplate extends ModelBase 
{
    protected $defaultData = array(
        'title' => 'Unknown Error',
        'content' => 'Ada yang salah. Harap hubungi administrator.',
        'menu_top' => array(
            array('title' => 'Home', 'link' => '/'),
            array('title' => 'Masuk', 'link' => '/auth/login'),
            array('title' => 'Daftar', 'link' => '/auth/register'),
        ),
        'menu_bottom' => array(),
    );

    /**
     * Render data ke template via Twig
     *
     * @param string $template eg:layout.tpl
     * @param array $data 
     *
     * @return string HTML representation
     */
    public static function render($template, $data = array()) {
        // Inisialisasi Twig. Load template yang berkaitan dan assign data.
        $loader = new Twig_Loader_Filesystem(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'Templates');
        $templateEngine = new Twig_Environment($loader);

        // Filter declaration
        $filters = array(
            new Twig_SimpleFilter('isContainArticle', array(__CLASS__, 'isContainArticle')),
            new Twig_SimpleFilter('toUserName', array(__CLASS__, 'getUserNameFromId')),
            new Twig_SimpleFilter('toUserFullName', array(__CLASS__, 'getUserFullnameFromId')),
            new Twig_SimpleFilter('toUserAvatar', array(__CLASS__, 'getUserAvatarFromId')),
            new Twig_SimpleFilter('toUserUniversalProfile', array(__CLASS__, 'getUserProfileFromCollection')),
            new Twig_SimpleFilter('toDate', array(__CLASS__, 'getDateFromUnix')),
            new Twig_SimpleFilter('displayEditor', array(__CLASS__, 'parseEditor')),
            new Twig_SimpleFilter('displayArticleBody', array(__CLASS__, 'parseDocument')),
            new Twig_SimpleFilter('displayPostBody', array(__CLASS__, 'parseDocument')),
            new Twig_SimpleFilter('displayMarkdown', array(__CLASS__, 'parseMd')),
            new Twig_SimpleFilter('displayLinkNewArticle', array(__CLASS__, 'parseLinkNewArticle')),
            new Twig_SimpleFilter('displayLinkNewPost', array(__CLASS__, 'parseLinkNewPost')),
        );

        // Register filter
        foreach ($filters as $filter) $templateEngine->addFilter($filter);
        
        return $templateEngine->render($template, $data);
    }

    /**
     * Helper untuk parsing text 
     *
     * @param string $text
     * @param int $maxLength
     * @param bool $stripped
     * @return string $text Formatted text
     */
    public static function formatText($text = '', $maxLength = 0, $stripped = TRUE) {
        // Perlu escape?
        if ($stripped) {
            $text = strip_tags($text);
        }

        // Format
        if ($maxLength > 0) {
            if (strlen($text) > $maxLength) {
                $text = substr($text, 0, ($maxLength-3)).'...';
            }
        }
        
        return $text;
    }

    /**
     * Provider untuk template Home
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getHomeData($otherData = array()) {
        $data = array(
            'title' => 'Home',
            'content' => NULL,
        );

        return $this->prepareData($data, $otherData);
    }

     /**
     * Provider untuk template Setting
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getSettingData($otherData = array()) {
        $data = array(
            'title' => 'Setelan',
            'content' => NULL,
            'menus' => array(
                new Parameter(array(
                    'liClass' => 'nav-header',
                    'text' => 'Setelan',
                )),

                new Parameter(array('liClass' => 'divider')),

                new Parameter(array(
                    'liClass' => 'nav-header',
                    'text' => 'Profil',
                )),
                new Parameter(array(
                    'liClass' => '',
                    'text' => 'Informasi',
                    'link' => '/setting/info',
                    'icon' => 'icon-info-sign',
                )),

                new Parameter(array('liClass' => 'divider')),

                new Parameter(array(
                    'liClass' => 'nav-header',
                    'text' => 'Akun',
                )),
                new Parameter(array(
                    'liClass' => '',
                    'text' => 'Email',
                    'link' => '/setting/mail',
                    'icon' => 'icon-envelope',
                )),
                 new Parameter(array(
                    'liClass' => '',
                    'text' => 'Password',
                    'link' => '/setting/password',
                    'icon' => 'icon-key',
                )),
            ),
        );

        return $this->prepareData($data, $otherData);
    }

    /**
     * Provider untuk template User
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getUserData($otherData = array()) {
        $data = array(
            'title' => 'Pengguna',
            'content' => NULL,
        );

        return $this->prepareData($data, $otherData);
    }

     /**
     * Provider untuk template CommunityIndex
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getComIndexData($otherData = array()) {
        $data = array(
            'title' => 'Komunitas',
            'content' => NULL,
        );

        return $this->prepareData($data, $otherData);
    }

    /**
     * Provider untuk template CommunityArticle
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getComArticleData($otherData = array()) {
        $data = array(
            'title' => 'Tulisan',
            'content' => NULL,
        );

        return $this->prepareData($data, $otherData);
    }

     /**
     * Provider untuk template CommunityPost
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getComPostData($otherData = array()) {
        $data = array(
            'title' => 'Forum',
            'content' => NULL,
        );

        return $this->prepareData($data, $otherData);
    }

    /**
     * Provider untuk template Auth
     *
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     * @see ModelTemplate::finalData
     */
    public function getAuthData($otherData = array()) {
        $data = array();

        return $this->prepareData($data, $otherData);
    }

    /**
     * Mendapat default data
     *
     * @return array Default data
     */
    public function getDefaultData() {
        return $this->defaultData;
    }

     /**
     * Custom Twig filter untuk mendapat nama lengkap user
     *
     * @param int ID
     * @return mixed
     * @codeCoverageIgnore
     */
    public static function getUserFullnameFromId($id) {
        return ModelBase::factory('Template')->getUserNameFromId($id, 200);
    }

    /**
     * Custom Twig filter untuk mendapat nama user
     *
     * @param int ID
     * @param int Limit text
     * @return mixed
     * @codeCoverageIgnore
     */
    public static function getUserNameFromId($id, $limitLen = 10) {
        $userData = ModelBase::factory('User')->getUser($id);

        if (empty($userData)) {
            $name = 'Tak diketahui';
        } else {
            $name = ModelTemplate::formatText($userData->get('Name'), $limitLen);
        }

        return $name;
    }

    /**
     * Custom Twig filter untuk mendapat avatar
     *
     * @param int ID
     * @return mixed
     * @codeCoverageIgnore
     */
    public static function getUserAvatarFromId($id) {
        $userData = ModelBase::factory('User')->getUser($id);

        if (empty($userData)) {
            $avatar = 'https://secure.gravatar.com/avatar/'.md5('Tak diketahui');
        } else {
            $avatar = $userData->get('Avatar');
        }

        return $avatar;
    }

     /**
     * Custom Twig filter untuk mendapat link user profile (atau FB)
     *
     * @param mixed (array or Parameter)
     * @return string Anchor profile
     * @codeCoverageIgnore
     */
    public static function getUserProfileFromCollection($collection) {
        $collection =  (is_array($collection)) ? new Parameter($collection) : $collection;
        $uid = $collection->get('Uid');
        $fid = $collection->get('Fid');
        $name = $collection->get('Name');

        $target = '';
        $profileLink = '#!';

        // External account (FB)
        if (!empty($fid)) {
            $target = '_blank';
            $profileLink = '//www.facebook.com/'.$fid;
            $user = ModelBase::factory('User')->getQuery()->findOneByFid($fid);

            if ( ! empty($user)) {
                $uid = $user->getUid();
            }
        }

        // Found UID?
        if (!empty($uid)) {
            $target = '';
            $profileLink = '/user/profile/'.$uid;
        }

       

        // Check for uid
        return '<a href="'.$profileLink.'" target="'.$target.'">'.$name.'</a>';
    }

    /**
     * Custom Twig filter untuk mendapat tanggal
     *
     * @param int ID (UNIX Timestamp)
     * @return string
     * @codeCoverageIgnore
     */
    public static function getDateFromUnix($ts) {
        return date('d M, Y',$ts);
    }

    /**
     * Custom Twig filter untuk melihat apakah resource adalah artikel
     *
     * @param string Current URL
     * @return bool 
     * @codeCoverageIgnore
     */
    public static function isContainArticle($url) {
        return preg_match('/community\/article\/[0-9]/', $url);
    }

    /**
     * Custom Twig filter untuk mendisplay editor
     *
     * @param object Editor object bundled with Parameter
     * @return string Parsed editor
     * @codeCoverageIgnore
     */
    public static function parseEditor(Parameter $param) {
        return '<textarea class="markdown-editor-standalone" data-action="'.$param->get('action').'" data-prompt="'.$param->get('prompt').'" data-redirect="'.$param->get('redirect').'"></textarea>';
    }

    /**
     * Custom Twig filter untuk mendisplay body value
     *
     * @param object Document object bundled with Parameter
     * @return string Parsed body
     * @codeCoverageIgnore
     */
    public static function parseDocument(Parameter $param) {
        $type = $param->get('bodyFormat');
        // Validate type
        $type = strpos($param->get('body'), '<p') === false ? 'markdown' : 'full_html';

        if ($type == 'full_html') {
            // Take care code tag
            $bodyText = $param->get('body');
            $bodyText = preg_replace_callback('/<code>([\s\S]*)<\/code>/msU', function($match){
                return '<code>'.trim(htmlentities($match[1])).'</code>';
                }, $bodyText);
        } else {
            $bodyText = self::parseMd($param->get('body'));
        }

        return $bodyText;
    }

    /**
     * Custom Twig filter untuk mendisplay Markdown
     *
     * @param string Markdown string
     * @return string Parsed HTML body
     * @codeCoverageIgnore
     */
    public static function parseMd($markdown = '') {
        $markdownParser = new MarkdownExtraParser();

        return $markdownParser->transformMarkdown($markdown);
    }

    /**
     * Custom Twig filter untuk mendisplay link artikel baru
     *
     * @param bool Acl status
     * @return string Parsed anchor link
     * @codeCoverageIgnore
     */
    public static function parseLinkNewArticle($allowWriteArticle = false) {
        return $allowWriteArticle ? '<hr/><a href="/community/article?new=true" class="btn btn-primary btn-large">Tulis Artikel</a>' : '';
    }

    /**
     * Custom Twig filter untuk mendisplay link post baru
     *
     * @param bool Acl status
     * @return string Parsed anchor link
     * @codeCoverageIgnore
     */
    public static function parseLinkNewPost($allowWritePost = false) {
        return $allowWritePost ? '<hr/><a href="/community/post?new=true" class="btn btn-primary btn-large">Tulis Post Baru</a>' : '';
    }

    /**
     * PrepareData
     *
     * @param array $data Data default tiap section
     * @param array $otherData Data dari model lain
     *
     * @return array $finalData
     */
    protected function prepareData($data = array(), $otherData = array()) {
        $finalData = $this->defaultData;

        // Hanya merge jika terdapat data
        if ( ! empty ($data)) {
            $finalData = array_merge($finalData,$data);
        }

        if ( ! empty ($otherData)) {
            $finalData = array_merge($finalData, $otherData);
        }

        return $finalData;
    }
}