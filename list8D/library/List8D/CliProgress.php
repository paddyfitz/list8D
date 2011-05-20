<?php
 
class List8D_CliProgress extends List8D_console 
{ 
     
    private $escapeSequence = "\033[%sm"; 
     
    private $text = ''; 
    private $steps = 0; 
    private $delim = ''; 
    public $step = 0; 
    private $maxchars = 70; 
     
    public function __construct($steps=100,$text='',$delim='#',$maxchars=70,$intialDraw = true) 
    { 
        $this->steps = abs($steps); 
        $this->step  = 0; 
        $this->text  = $text; 
        $this->delim = $delim; 
        $this->maxchars = $maxchars; 
        if (isset($initialDraw) && $initialDraw)
	        $this->draw(); 
    } 
     
    public function update() 
    { 
        $this->step++; 
        $this->redraw(); 
    } 
     
    public function draw() 
    { 
        print $this->text.' ['; 
         
        $proc = round(($this->step/$this->steps)*100,0); 
        $complete = $proc.'% complete'; 
        //$complete = sprintf($this->afterText, 
         
        $isuse = strlen($complete) + 4 + strlen($this->text); 
         
        $max = $this->maxchars - $isuse; 
         
        $dash = round($max*($proc/100)+1); 
        $free = $max - $dash; 
         
        //print 'max:'.$max.' dash:'.$dash.' free:'.$free; 
        if($dash>0) print str_repeat($this->delim,$dash); 
        if($free>0) print str_repeat('-',$free); 
        print '] '.$complete; 
    } 
    private function redraw() 
    { 
        $this->toPos(); 
        $this->draw(); 
    } 
    public function toPos( $column = 1 )  
    { 
        echo "\033[{$column}G"; 
    } 
} 