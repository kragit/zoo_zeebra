<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$plugin_info = array(
						'pi_name'			=> 'Zeebra',
						'pi_version'		=> '1.1.1',
						'pi_author'			=> 'Filip Vanderstappen',
						'pi_author_url'		=> 'http://filipvanderstappen.be/',
						'pi_description'	=> 'Modified by Adam Kragt (<a href="http://twitter.com/kragit" target="_blank">http://twitter.com/kragit</a>)',
						'pi_usage'			=> Zeebra::usage()
					);


/**
 * Zeebra Class
 *
 * @package			ExpressionEngine
 * @category		Plugin
 * @author			Filip Vanderstappen
 * @copyright		Copyright (c) 2010, Filip Vanderstappen - Modifications 2013, Adam Kragt
 * @link			http://filipvanderstappen.be/ee/details/zeebra
 */

class Zeebra {

    var $returnCount = 0;
	var $return_data;
	var $tag = '{zeebra}';
	var $tagTips = '{zeebra:tips}';
	var $tagInt = '{zeebra:interval}';
	var $tagTotal = 0;
	var $tagTotalTips = 0;
	var $tagTotalInt = 0;
	var $tagCount = 1;
	var $tagCountTips = 1;
	var $tagCountInt = 1;
	var $attrTips;
	var $attrTipsClass;
	var $attrInterval;
	var $attrIntervalClass;
	
	/**
	 * Constructor
	 *
	 */
	function Zeebra($str = '')
	{
		$this->EE =& get_instance();

        // Set vars
        $tagdata = $this->EE->TMPL->tagdata;
        $this->tagTotal = count(explode($this->tag, $tagdata)) - 1;
        $this->tagTotalTips = count(explode($this->tagTips, $tagdata)) - 1;
        $this->tagTotalInt = count(explode($this->tagInt, $tagdata)) - 1;
		$this->return_data = '';
		
		// Get attributes
		$this->attrTips = $this->EE->TMPL->fetch_param('tips', 'yes');
		$this->attrTipsClass = explode("|",$this->EE->TMPL->fetch_param('tipsclass', "first|last"));
		$this->attrInterval = $this->EE->TMPL->fetch_param('interval', '2');
		$this->attrIntervalClass = $this->EE->TMPL->fetch_param('intervalclass', 'item-%');
		
		// Parse tagdata
		$tmpData = $this->parseData($tagdata);
		
		// Return data
		$this->return_data = $tmpData;
	}
	
	// Standard {zeebra} tag
	function parseData($tagdata)
	{
	    // Search for the first zeebra in the wild
	    $tmpPos = stripos($tagdata, $this->tag);
	    $tmpClass = array();
	    
	    // If no zeebras are found in the bushes, go away 
	    if($tmpPos === false){
	    	// Proceed to next tag check
	        return $this ->parseIntData($tagdata);
	    }
	    // Go catch them
	    else {
	        // If it's your first zeebra… shoot it!
	        if($this->tagCount == 1 && $this->attrTips == 'yes')
	        {
	            array_push($tmpClass, $this->attrTipsClass[0]);
	        }
	        
	        // If it's your last zeebra… finalize it.
	        if($this->tagCount === $this->tagTotal && $this->attrTips == 'yes')
	        {
	            array_push($tmpClass, $this->attrTipsClass[1]);
	        }
	        
	        // Get your zeebras in rows
	        if(is_numeric($this->attrInterval))
	        {
	            $tmpInterval = ($this->tagCount%$this->attrInterval == 0) ? $this->attrInterval : ($this->tagCount%$this->attrInterval);
	            array_push($tmpClass, str_replace("%", $tmpInterval, $this->attrIntervalClass));
	        }
	    }
	    
	    // Kill the zeebra and get it replaced with our classes
	    $tagdata = substr_replace($tagdata, implode(" ", $tmpClass), $tmpPos, strlen($this->tag));
	    
	    // Hunt for other zeebras
	    $this->tagCount++;
	    return $this->parseData($tagdata);
	}

	// {zeebra:interval} tags
	function parseIntData($tagdata)
	{
	    // Search for the first zeebra:interval in the wild
	    $tmpPosInt = stripos($tagdata, $this->tagInt);
	    $tmpClassInt = array();

	    // If no zeebra:invervals are found in the bushes, go away
	    if($tmpPosInt === false){
	    	// Proceed to next tag check
	    	return $this ->parseTipsData($tagdata);
	    }
	    // Go catch them
	    else {
	    	// Get your zeebras in rows
	    	if(is_numeric($this->attrInterval))
	    	{
	    		$tmpInterval = ($this->tagCountInt%$this->attrInterval == 0) ? $this->attrInterval : ($this->tagCountInt%$this->attrInterval);
	            array_push($tmpClassInt, str_replace("%", $tmpInterval, $this->attrIntervalClass));
	    	}

	    }

	    // Kill the zeebra:interval and get it replaced with our classes
	    $tagdata = substr_replace($tagdata, implode(" ", $tmpClassInt), $tmpPosInt, strlen($this->tagInt));

	    // Hunt for other zeebra:intervals
	    $this->tagCountInt++;
	    return $this->parseIntData($tagdata);
	}

	// {zeebra:tips} tags
	function parseTipsData($tagdata)
	{
	    // Search for the first zeebra:tips in the wild
	    $tmpPosTips = stripos($tagdata, $this->tagTips);
	    $tmpClassTips = array();

	    // If no other zeebra:tips are found in the bushes, go away
	    if($tmpPosTips === false){
	    	return $tagdata;
	    }
	    //Go catch them
	    else {
	    	// If it's your first zeebra:tips... shoot it!
	        if($this->tagCountTips == 1 && $this->attrTips == 'yes')
	        {
	            array_push($tmpClassTips, $this->attrTipsClass[0]);
	        }
	        
	        // If it's your last zeebra:tips... finalize it.
	        if($this->tagCountTips === $this->tagTotalTips && $this->attrTips == 'yes')
	        {
	            array_push($tmpClassTips, $this->attrTipsClass[1]);
	        }
	    }

	    //Kill the zeebra:tips and get it replaced with our classes
	    $tagdata = substr_replace($tagdata, implode(" ", $tmpClassTips), $tmpPosTips, strlen($this->tagTips));

	    // Hunt for other zeebra:tips
	    $this->tagCountTips++;
	    return $this->parseIntData($tagdata);
	}
	
	/**
	 * Usage
	 *
	 * Plugin Usage
	 *
	 * @access	public
	 * @return	string
	 */
	function usage()
	{
		ob_start(); 
		?>
        
        Zeebra is a very simple plugin that adds first and last classes to your lists/entries. It also add's classes for certain intervals (default 2, for odd and even rows).

        All 'inner' tags ({zeebra}, {zeebra:tips}, {zeebra:interval}) should only be present once inside the {exp:zeebra} tag pair, otherwise counts/output will not be as expected.
        
        Attributes
        -----------------------
        
        tips (default: “yes”) (other values: “no”)
        tipsclass (default: “first|last”)
        interval (default: “2”) (other values: numeric, or “no”)
        intervalclass (default: “item-%”) (Can be any value, % will be replaced by the interval)
        
        
        Standard usage
        ---------------------------------
        
        {exp:zeebra}
         <ul>
         {exp:channel:entries}
         <li class="{zeebra}">{title}</li>
         {/exp:channel:entries}
         </ul>
        {/exp:zeebra}
        
        
        Interval attributes
        ---------------------------------
        
        {exp:zeebra tips="no" interval="5" intervalclass="nth-%"}
         <ul>
         {exp:channel:entries}
         <li class="{zeebra}">{title}</li>
         {/exp:channel:entries}
         </ul>
        {/exp:zeebra}
        
        
        Tips attributes
        ---------------------------------
        
        {exp:zeebra tipsclass="uno|duo" interval="no"}
         <ul>
         {exp:channel:entries}
         <li class="{zeebra}">{title}</li>
         {/exp:channel:entries}
         </ul>
        {/exp:zeebra}


        Extra Tags
        ---------------------------------

        {zeebra:tips} will output *only* the tips value.
        {zeebra:interval} will output *only* the interval value.


		<?php
		$buffer = ob_get_contents();
	
		ob_end_clean(); 

		return $buffer;
	}

	// --------------------------------------------------------------------

}
// END CLASS

/* End of file pi.classee_entries.php */
/* Location: ./system/expressionengine/third_party/classee_entries/pi.classee_entries.php */