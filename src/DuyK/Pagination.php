<?php

namespace DuyK;

use Symfony\Component\HttpFoundation\Request;

Class Pagination{

    /**
    * Configuration with default values
    *
    * @access protected
    * @var array
    */
    protected $config = array(
            
            'base_url' => 'http://example.com/', // Your base URL
            'uri_path' => '', // URI path (IE: controller/action/id), leave none for auto detection
            'num_links' => 3, // Number of links to show
            
            'total_items' => 0, // Total items
            'items_per_page' => 20, // Number of items per page
            
            'get_query' => FALSE, // Or query string, Default is false (Use URI segment)
            'uri_segment_position' => 0, // Count from 0, Exp: controller/action/id/{page} => 3
            
            // Page prefix and suffix (Exp: ?page=prefix{page}suffix)
            'prefix' => '',
            'suffix' => '',

            // Link text
            'first_link_text' => 'First',
            'last_link_text' => 'Last',

            'previous_link_text' => 'Previous',
            'next_link_text' => 'Next'
        );

    /**
    * URI splitted segments
    *
    * @access protected
    * @var array
    */
    protected $uri_segments = array();

    /**
    * Constructor
    * Import configuration
    * @param array Config
    */
    public function __construct($config = array())
    {
        // Load config
        if(is_array($config) and count($config))
        {
            $this->config = array_merge($this->config, $config);
        }

        // Split segments
        $segments = explode('/', $this->get_uri_path());
        $segments = array_filter($segments);
        $segments = array_values($segments);

        $this->uri_segments = $segments;
    }

    /**
    * Get URI path string
    *
    * @access protected
    * @return string
    */
    protected function get_uri_path()
    { 
        return ($uri = $this->config['uri_path']) ? $uri : Request::createFromGlobals()->getBasePath();
    }

    /**
    * Generator
    *
    * @param void
    * @return array
    */
    public function generate()
    {
        // Calculate number of pages
        if($this->config['total_items'] <= $this->config['items_per_page'])
        {
            return array();
        }

        $division = $this->config['total_items'] / $this->config['items_per_page'];
        $total_pages = round($division);

        if(round($division, 1) > $total_pages)
        {
            $total_pages++;
        }

        //-------------------------------------------------------------------------//

        // Current page
        $current_page = 1;

        // If use GET query
        if($this->config['get_query'])
        {
            // If query page exists
            if(isset($_GET[$this->config['get_query']]) and $_GET[$this->config['get_query']])
            {
                // Set current page
                $current_page = $_GET[$this->config['get_query']];
            }
        }
        // If use URI segment
        else
        {
            $segments = $this->uri_segments;
        
            // Check page segment
            if(isset($segments[$this->config['uri_segment_position']]) and $segments[$this->config['uri_segment_position']])
            {
                // Set current page
                $current_page = $segments[$this->config['uri_segment_position']];
            }
        }

        // Remove prefix and suffix
        $this->config['prefix'] = preg_quote($this->config['prefix'], '/');
        $this->config['suffix'] = preg_quote($this->config['suffix'], '/');

        $current_page = intval(preg_replace('/('.$this->config['prefix'].'|'.$this->config['suffix'].')+/i', '', $current_page));
        $current_page = ($current_page < 1) ? 1 : $current_page;

        
        //-------------------------------------------------------------------------//

        // Returning data
        $return = array();
    
        // Add first and previous links to returning data if current page is not the first page
        if($current_page > 1)
        {
            $return = array(

                    // First link
                    array(
                        'url' => $this->create_url(1),
                        'text' => $this->config['first_link_text'],
                        'type' => 'first',
                        'current' => FALSE
                        ),

                    // Previous link
                    array(
                        'url' => $this->create_url(($current_page-1)),
                        'text' => $this->config['previous_link_text'],
                        'type' => 'previous',
                        'current' => FALSE
                        ),
                );
        }

        // Number links
        $start = $current_page - $this->config['num_links'];
        $start = ($start < 1) ? 1 : $start;

        $end = $current_page + $this->config['num_links'];
        $end = ($end > $total_pages) ? $total_pages : $end;

        for($i = $start; $i <= $end; $i++)
        {
            // Check is current
            $current = FALSE;
            if($i == $current_page)
            {
                $current = TRUE;
            }

            $return[] = array(
                    'url' => $this->create_url($i),
                    'text' => $i,
                    'type' => 'number',
                    'current' => $current
                );
        }

        // Add last and next links to returning data if current page is not the last page
        if($current_page < $total_pages)
        {
            $return[] = array(
                    'url' => $this->create_url($current_page+1),
                    'text' => $this->config['next_link_text'],
                    'type' => 'next',
                    'current' => FALSE
                );

            $return[] = array(
                    'url' => $this->create_url($total_pages),
                    'text' => $this->config['last_link_text'],
                    'type' => 'last',
                    'current' => FALSE
                );
        }

        return $return;
    }

    /**
    * URL creator
    *
    * @access protected
    * @param integer Page number
    * @return string
    */
    protected function create_url($page)
    {
        $page = $this->config['prefix'].$page.$this->config['suffix'];

        // If use page query
        if($this->config['get_query'])
        {
            $_GET[$this->config['get_query']] = $page;
            $url = $this->url($this->get_uri_path().'?'.http_build_query($_GET));
        }
        // Use uri segment
        else
        {
            $segments = $this->uri_segments;
            $segments[$this->config['uri_segment_position']] = $page;
            $url = $this->url(implode('/', $segments));

            // If isset GET query
            if(count($_GET))
            {
                $url .= '?'.http_build_query($_GET);
            }
        }

        return $url;
    }

    /**
    * Base URL
    *
    * @access protected
    * @param string
    * @return string
    */
    protected function url($path)
    {
        $url = $this->config['base_url'];
        
        $slash = preg_match('/\/$/', $url) ? '' : '/';
        
        return $url.$slash.$path;
    }
}
