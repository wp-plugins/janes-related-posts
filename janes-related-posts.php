<?php
/**
 * Plugin Name: Jane's Related Posts
 * Plugin URI: http://romantelychko.com/downloads/wordpress/plugins/janes-related-posts.latest.zip
 * Description: A related posts plugin.
 * Version: 0.1.1
 * Author: Roman Telychko
 * Author URI: http://romantelychko.com
 */

///////////////////////////////////////////////////////////////////////////////

add_filter( 'the_content', array( 'Janes_Related_Posts', 'filterPostContent' ), 99 );

///////////////////////////////////////////////////////////////////////////////

if( is_admin() )
{
    // create plugin settings menu
    add_filter( 'admin_menu', array( 'Janes_Related_Posts', 'createAdminMenu' ) );
}

///////////////////////////////////////////////////////////////////////////////

/**
 * Jane's Related Posts
 *
 */
class Janes_Related_Posts
{   
    ///////////////////////////////////////////////////////////////////////////
    
    protected static $class_name    = 'janes-related-posts';
    
    protected static $settings      = array(
        'title'                     => 'Related Posts:',
        'count'                     => 5,
        'cache_lifetime'            => 3600,
        'one_element_html'          => "<span class=\"entry-content\">\n  <a href=\"{permalink}\" title=\"{post_title}\">{post_title}</a>\n</span>",
        'post_categories_separator' => ', ',
        'post_date_format'          => 'd.m.Y',
        );

    ///////////////////////////////////////////////////////////////////////////

	/**
	 * Janes_Related_Posts::filterPostContent()
	 * Append Related Posts block in tail
	 *
	 * @param       string      $content
	 * @return      string
	 */
    public static function filterPostContent($content) 
    {
        #if( is_single() && isset($_GET['z']) )
        if( is_single() )
        {
            $content .= Janes_Related_Posts::getPosts();
        }
        
        return $content;
    }
    
    ///////////////////////////////////////////////////////////////////////////

	/**
	 * Janes_Related_Posts::createAdminMenu()
	 * Create admin menu item
	 *
	 * @return      void
	 */
    public static function createAdminMenu() 
    {
	    // create new top-level menu
	    add_menu_page( 'Jane\'s Related Posts Settings', 'Related Posts', 'administrator', __FILE__, array( __CLASS__, 'adminSettingsPage' ) );

	    // call register settings function
	    add_action( 'admin_init', array( __CLASS__, 'registerSettings' ) );
    }
    
    ///////////////////////////////////////////////////////////////////////////

	/**
	 * Janes_Related_Posts::registerSettings()
	 * Register settings
	 *
	 * @return      void
	 */
    public static function registerSettings() 
    {
        // register settings
        register_setting( self::$class_name, self::$class_name.'__title' );
        register_setting( self::$class_name, self::$class_name.'__count' );
        register_setting( self::$class_name, self::$class_name.'__cache_lifetime' );
        register_setting( self::$class_name, self::$class_name.'__one_element_html' );
        register_setting( self::$class_name, self::$class_name.'__post_categories_separator' );
        register_setting( self::$class_name, self::$class_name.'__post_date_format' );
    }
    
    ///////////////////////////////////////////////////////////////////////////

	/**
	 * Janes_Related_Posts::adminSettingsPage()
	 * Build admin settings page
	 *
	 * @return      void
	 */
    public static function adminSettingsPage() 
    {
        ///////////////////////////////////////////////////////////////////////
    
        $temp_title = trim( strip_tags( get_option(self::$class_name.'__title', self::$settings['title']) ) );
        self::$settings['title'] = strlen($temp_title)>0 ? $temp_title : self::$settings['title'];
        unset($temp_title);

        $temp_count = intval( preg_replace( '#[^0-9]#', '', get_option(self::$class_name.'__count', self::$settings['count']) ) );
        self::$settings['count'] = $temp_count>0 ? $temp_count : self::$settings['count'];    
        unset($temp_count);

        $temp_cache_lifetime = intval( preg_replace( '#[^0-9]#', '', get_option(self::$class_name.'__cache_lifetime', self::$settings['cache_lifetime']) ) );
        self::$settings['cache_lifetime'] = $temp_cache_lifetime>0 ? $temp_cache_lifetime : self::$settings['cache_lifetime'];
        unset($temp_cache_lifetime);
        
        $temp_one_element_html = trim( get_option(self::$class_name.'__one_element_html', self::$settings['one_element_html']) );
        self::$settings['one_element_html'] = strlen($temp_one_element_html)>0 ? $temp_one_element_html : self::$settings['one_element_html'];
        unset($temp_one_element_html);
        
        $temp_post_categories_separator = strip_tags( get_option(self::$class_name.'__post_categories_separator', self::$settings['post_categories_separator']) );
        self::$settings['post_categories_separator'] = strlen($temp_post_categories_separator)>0 ? $temp_post_categories_separator : self::$settings['post_categories_separator'];
        unset($temp_post_categories_separator);
        
        $temp_post_date_format = trim( strip_tags( get_option(self::$class_name.'__post_date_format', self::$settings['post_date_format']) ) );
        self::$settings['post_date_format'] = strlen($temp_post_date_format)>0 ? $temp_post_date_format : self::$settings['post_date_format'];
        unset($temp_post_date_format);
        
        ///////////////////////////////////////////////////////////////////////
    
        echo(
            '<div class="wrap">'.
                '<h2>Jane\'s Related Posts Settings</h2>'.
                    '<form method="post" action="options.php">'
            );
            
        settings_fields( self::$class_name );
        
        echo(
            '<table class="form-table">'.
                '<tr valign="top">'.
                    '<th scope="row">Widget title:</th>'.
                    '<td><input type="text" class="widefat" name="'.self::$class_name.'__title" value="'.self::$settings['title'].'" /></td>'.
                '</tr>'.
                '<tr valign="top">'.
                    '<th scope="row">Display count:</th>'.
                    '<td><input type="text" class="widefat" name="'.self::$class_name.'__count" value="'.self::$settings['count'].'" /></td>'.
                '</tr>'.
                '<tr valign="top">'.
                    '<th scope="row">Cache lifetime (in seconds):</th>'.
                    '<td><input type="text" class="widefat" name="'.self::$class_name.'__cache_lifetime" value="'.self::$settings['cache_lifetime'].'" /></td>'.
                '</tr>'.
                '<tr valign="top">'.
                    '<th scope="row">One element HTML (inside <code>&lt;li&gt;</code>):</th>'.
                    '<td>'.
                        '<textarea class="widefat" cols="20" rows="5" name="'.self::$class_name.'__one_element_html">'.self::$settings['one_element_html'].'</textarea>'.
                        'You can use this placeholders:'.
                        '<ul>'.
		                    '<li><code>{post_id}</code> - Post ID</li>'.
		                    '<li><code>{post_title}</code> - Post title</li>'.
		                    '<li><code>{post_excerpt_N}</code> - Post excerpt, where <code>N</code> - is words count. For example: <code>{post_excerpt_10}</code> or <code>{post_excerpt_255}</code></li>'.
		                    '<li><code>{post_author}</code> - Post author name</li>'.
		                    '<li><code>{post_author_link}</code> - Post author link</li>'.
		                    '<li><code>{permalink}</code> - Post link</li>'.
		                    '<li><code>{post_date}</code> - Post date</li>'.
		                    '<li><code>{thumbnail-[medium|...|64x64]}</code> - Post thumbnail with size. For example: <code>{thumbnail-large}</code> or <code>{thumbnail-320x240}</code>'.
		                    '<li><code>{post_categories}</code> - Links to post categories with <code>'.self::$settings['post_categories_separator'].'</code> as separator</li>'.
		                    #'<li><code>{post_hits}</code> - Post hits, counted by plugin <a href="http://wordpress.org/extend/plugins/ajax-hits-counter/" title="AJAX Hits Counter" target="_BLANK">AJAX Hits Counter</a></li>'.
		                    '<li><code>{post_comments_count}</code> - Post comments count</li>'.
	                    '</ul>'.
                    '</td>'.
                '</tr>'.
                '<tr valign="top">'.
                    '<th scope="row">Categories separator:</th>'.
                    '<td><input type="text" class="widefat" name="'.self::$class_name.'__post_categories_separator" value="'.self::$settings['post_categories_separator'].'" /></td>'.
                '</tr>'.
                '<tr valign="top">'.
                    '<th scope="row">Date format<br />(for more info see <a href="http://php.net/manual/en/function.date.php" target="_BLANK">date() manual</a>):</th>'.
                    '<td><input type="text" class="widefat" name="'.self::$class_name.'__post_date_format" value="'.self::$settings['post_date_format'].'" /></td>'.
                '</tr>'.
                #'<tr valign="top">'.
                #    '<td>&nbsp;</td>'.
                #    '<td>I forgot something? <a href="http://wordpress.org/support/plugin/'.self::$class_name.'" target="_BLANK">You can write to me!</a></td>'.
                #'</tr>'.
            '</table>'
            );
            
        submit_button();

        echo(
                '</form>'.
            '</div>'
            );

        ///////////////////////////////////////////////////////////////////////
        
	    // drop cache
	    // TODO
	    self::clearCache();
            
        ///////////////////////////////////////////////////////////////////////
    }
    
	///////////////////////////////////////////////////////////////////////////
	
	/**
	 * Janes_Related_Posts::clearCache()
	 * Clear transient widget cache
	 *
	 * @return      bool
	 */
	public static function clearCache()
	{
	    global $wpdb;
	
	    $q = '
	        SELECT
		        option_name     as name
	        FROM
		        '.$wpdb->options.'
	        WHERE	
	            option_name LIKE \'_transient_'.self::$class_name.'_%\'';

	    $transients = $wpdb->get_results($q);
	    
	    if( !empty($transients) )
	    {
	        foreach( $transients as $transient )
	        {
	            delete_transient( str_replace( '_transient_', '', $transient->name ) );
	        }
	    }
	    
	    return true;
	}

    ///////////////////////////////////////////////////////////////////////////

	/**
	 * Janes_Related_Posts::getPosts()
	 * Get related posts
	 *
	 * @return      string
	 */
    public static function getPosts()
    {   
        #$start_time = microtime(true);
        
	    // fix bug in Wordpress :-)
        global $post;
        $tmp_post = $post;
    
        // cache key
        $cache_key = self::$class_name.'__'.$post->ID;
    
        // try to get cached data from transient cache
        $cache = get_transient( $cache_key );

        if( !empty($cache) )
        #if(false)
        {    
            // cache exists, return cached data
            $output = $cache;
        }
        else
        {
            global $wpdb;
        
            $output = '';

            // posts display count
            $posts_count = get_option(self::$class_name.'__count', self::$settings['count']);

            // cache lifetime
            $cache_lifetime = get_option(self::$class_name.'__cache_lifetime', self::$settings['cache_lifetime']);

            // one element html
            $one_element_html = get_option(self::$class_name.'__one_element_html', self::$settings['one_element_html']);
            
            // posts categories separator
            $post_categories_separator = get_option(self::$class_name.'__post_categories_separator', self::$settings['post_categories_separator']);
            
            // posts date format
            $posts_date_format = get_option(self::$class_name.'__post_date_format', self::$settings['post_date_format']);
            
	        $related_posts      = false;
	        $related_posts_html = '';
		
	        $tags = wp_get_post_tags($post->ID);

	        $tag_ids = array();
	
            if( !empty($tags) ) 
            {
		        foreach ( $tags as $t ) 
		        {
			        $tag_ids[] = $t->term_id;
		        }

	            $q = '
	                SELECT
		                p.ID,
		                p.post_title,
		                p.post_excerpt,
		                p.post_content,
		                p.post_author,
		                p.post_date,
		                p.comment_count         as post_comments_count,
		                count(t_r.object_id)    as matches
	                FROM
		                '.$wpdb->term_taxonomy.' t_t,
		                '.$wpdb->term_relationships.' t_r,
		                '.$wpdb->posts.' p
	                WHERE
		                t_t.taxonomy =\'post_tag\' 
		                AND
		                t_t.term_taxonomy_id = t_r.term_taxonomy_id 
		                AND
		                t_r.object_id  = p.ID 
		                AND
		                (t_t.term_id IN ('.join( ',', $tag_ids ).')) 
		                AND
		                p.ID != '.$post->ID.' 
		                AND
		                p.post_date_gmt <= \''.date( 'Y-m-d H:i:s' ).'\'
		                AND
		                p.post_status = \'publish\' 
		                AND
		                p.post_type = \'post\'
	                GROUP BY
		                t_r.object_id
	                ORDER BY
		                matches DESC,
		                ( p.comment_count + 0 ) DESC,
		                p.post_date_gmt DESC
	                LIMIT
	                    '.$posts_count;

	            $related_posts = $wpdb->get_results($q);
            }
            
            if( count($related_posts)<$posts_count )
            {
                $posts_ids = array(
                    $post->ID,
                    );
                
                if( !empty($related_posts) )
                {
                    foreach( $related_posts as $p )
                    {
                        $posts_ids[] = $p->ID;
                    }
                }
                            
                $q = '
                    SELECT
		                DISTINCT p.ID,
		                p.post_title,
		                p.post_excerpt,
		                p.post_content,
		                p.post_author,
		                p.post_date,
		                p.comment_count     as post_comments_count
                    FROM
	                    '.$wpdb->posts.' p
                    WHERE
                        p.ID NOT IN ('.join( ',', $posts_ids ).') 
                        AND
		                p.post_date_gmt <= \''.date( 'Y-m-d H:i:s' ).'\'
		                AND
	                    p.post_status = \'publish\' 
	                    AND
	                    p.post_type = \'post\'
                    ORDER BY
                        ( p.comment_count + 0 ) DESC,
	                    p.post_date_gmt DESC
                    LIMIT
                        '.( $posts_count - count($related_posts) );

                if( empty($related_posts) )
                {
                    $related_posts = $wpdb->get_results($q);
                }
                else
                {
                    $related_posts = array_merge( $related_posts, $wpdb->get_results($q) );
                }
            }
                    
            if( !empty($related_posts) )
            {            
                $i = 1;
                $i_max = count($related_posts);
                
	            foreach( $related_posts as $post )
	            {	    
	                $post_author_obj = get_userdata( $post->post_author );
	                
	                $post_author_name = $post_author_obj->display_name;
	                $post_author_link = get_author_posts_url( $post_author_obj->ID, $post_author_obj->user_nicename );
	                
	                setup_postdata($post);

	                $temp_html = 
                        str_ireplace(
                            array(
	                            '{post_id}',
	                            '{post_title}',
	                            '{post_author}',
	                            '{post_author_link}',
	                            '{permalink}',
	                            '{post_date}',	             
	                            '{post_comments_count}',
	                            ),
                            array(
                                $post->ID,
                                //$post->post_title,
                                get_the_title(),
                                $post_author_name,
                                $post_author_link,
                                get_permalink($post->ID),
                                date( $posts_date_format, strtotime($post->post_date) ),
                                $post->post_comments_count,
                                ),
                            $one_element_html
                            );
                            
                    if( preg_match_all( '#(\{thumbnail\-([^\}]+)\})#sim', $temp_html, $matches ) )
                    {
                        if( isset($matches['2']) && !empty($matches['2']) )
                        {
                            foreach( $matches['2'] as $m )
                            {
                                $size = $m;
                            
                                if( preg_match( '#([0-9]+)x([0-9]+)#i', $m, $sizes ) )
                                {
                                    if( isset($sizes['1']) && isset($sizes['2']) )
                                    {
                                        $size = array( $sizes['1'], $sizes['2'] );
                                    }
                                }
                                
                                $temp_html = str_ireplace( '{thumbnail-'.$m.'}', get_the_post_thumbnail( $post->ID, $size ), $temp_html );
                            }
                        }
                    }
                    
                    if( stripos( $one_element_html, '{post_categories}' )!==false )
                    {
                        $categories = get_the_category( $post->ID );
                        
                        if( !empty($categories) )
                        {
                            $temp = array();
                        
                            foreach( $categories as $category )
                            {
                                $temp[] = '<a href="'.get_category_link( $category->term_id ).'" title="'.esc_attr( $category->cat_name ).'">'.$category->cat_name.'</a>';
                            }
                            
	                        $temp_html = str_ireplace( '{post_categories}', join( $post_categories_separator, $temp ), $temp_html );
                        }
                    }

                    if( preg_match( '#(\{post\_excerpt\_([0-9]+)\})#sim', $temp_html, $matches ) )
                    {
                        if( isset($matches['2']) && !empty($matches['2']) )
                        {
                            $excerpt_length = intval($matches['2']);

                            if( $excerpt_length > 0 )
                            {              
                                if( $excerpt_length_isset===false )
                                {
                                    add_filter( 'excerpt_length', create_function( '', 'return '.$excerpt_length.';' ), 1024 );
                                    
                                    $excerpt_length_isset = true;
                                }
                            }
                            
                            $temp_html = str_ireplace( $matches['1'], get_the_excerpt(), $temp_html );
                        }            
                    }

	                $related_posts_html .= '<li';

	                if( $i==1 )
	                {
	                    $related_posts_html .= ' class="first"';	                
	                }
	                elseif( $i>=$i_max )
	                {
	                    $related_posts_html .= ' class="last"';
	                }
	                
	                $related_posts_html .= '>'.$temp_html.'</li>';
	                
	                $i++;
	            }
            
                $output .=  
                    '<div id="'.self::$class_name.'">'.
                        '<h3 class="title">'.get_option(self::$class_name.'__title', self::$settings['title']).'</h3>'.  
                        '<div class="items">'.
                            '<ul>'.
                                $related_posts_html.
                            '</ul>'.
                            '<div class="clear cls"></div>'.
                        '</div>'.
                    '</div>';
            }
            
            // store to cache
            set_transient( $cache_key, $output, $cache_lifetime );
        }
        
	    // restore $post (Wordpress bug fixing)
	    #wp_reset_postdata();
	    $post = $tmp_post;
	    
	    #echo( '] time: '.( microtime(true)-$start_time ) * 1000 . ' ms' );
	    
	    return $output;
    }
    
    ///////////////////////////////////////////////////////////////////////////
}

///////////////////////////////////////////////////////////////////////////////
