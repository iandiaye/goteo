<?php

namespace Goteo\Core {
    
    class Redirection extends Error {
        
        private $url;
        
        public function __construct ($url, $code = 301) {
            
            $this->url = $url;
            parent::__construct($code);
            
        }
        
        public function getURL () {
            return $this->url;
        }
        
    }
    
    
}