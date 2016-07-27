<?php 
/*
 * This file is part of the Drillr package, a project by Igor Soares.
 *
 * (c) 2016 Igor Soares
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Drillr, see usage specifications at README.md
 *
 * @author      Igor Soares <igor.larcs@gmail.com>
 */
class Drillr
{
    /**
     * @var string The instance of the class.
     */
	private static $instance = null;

    /**
     * @var string Main path of the templating/views folder.
     */
	private $path; 

    /**
     * @var string Main path of the html iterable block.
     */
	private $block; 

    /**
     * @var string break dom container.
     * Suppose you want to add a <br/> for ever 2 iterated elements, thats a "breaker"	
     */
	private $break; 

    /**
     * @var string wrapper dom container.
     * Suppose you want to add a <div class='xyz'> </div> for EVERY iterated element, thats a "wrapper"
     * 	you can add as much as you want, just as breakers.	
     */
	private $wrapper; 

    /**
     * @var array filter functions container.
     */
	private $filter = array();
	
    /**
	 * Returns the Drillr Object
     *
     * @return single Drillr object instance
     */
	public static function getInstance()
	{
		if(!isset(self::$instance))
		{
			self::$instance = new Drillr();
		}	
		return self::$instance;
	}

    /**
	 * Add string to path
     *
     * @param string $str
     * @return this		
     */
	public function addToPath($str)
	{
		$this->path .= $str;
		return $this;
	}

    /**
	 * Changes path to the string specified
     *
     * @param string $str
     * @return this		
     */
	public function changePath($str)
	{
		$this->path = $str;
		return $this;
	}

    /**
	 * Add html block to the block variable by its filename
     *
     * @param string $str 
     * @return $this		
     */
	public function loadBlock($str)
	{
		if(file_exists($this->path.$str))
		{
			$this->block = file_get_contents($this->path.$str);
		}
		return $this;
	}

    /**
	 * Adds the break dom element as long as its "ocurrence value"
     * (occurs every 2, 3 elements, etc)
     * @param int $num
     * @param string $dom
     * @return this		
     */
	public function addBreak($num, $dom)
	{
		$this->break = array($num => $dom);
		return $this;
	}

    /**
	 * Adds the wrapper dom element as long as its "ocurrence value"
     * (wrap every 2, 3 element, etc)
     * @param int $num
     * @param string $dom
     * @param string $dom2
     * @return this		
     */
	public function addWrapper($num, $dom1, $dom2)
	{
		$this->wrapper = array($num => array($dom1,$dom2));
		return $this;
	}

    /**
	 * Adds a single filter function to the filter array 
	 *  $drillr->addFilter("functionName", array('param1','param2'),'target_value_at_dom_element')
     * @param string $func
     * @param array $params
     * @param string $elem
     * @return this		
     */
	public function addFilter($func,$params,$elem)
	{
		if(!$this->filter)
		{
			$this->filter = array($elem => array('function'=>$func, 'params'=>$params));
		}
		else
		{
			$this->filter = array_merge($this->filter, array($elem => array('function'=>$func, 'params'=>$params)));
		}
		return $this;
	}

    /**
	 * Iterates the collection  
     * @param array $collection
     * @return true		
     */
	public function drill($collection)
	{
		$this->outputHandler(iterator_to_array($this->blockLevelHandler($collection)));
		$this->filter   = array();
		$this->wrapper  = null;
		$this->break    = null;
		return true;
	}

    /**
	 * Handles the output (duh)  
     * @param array $blocks (serialized by the drill function)
     */
	private function outputHandler($blocks)
	{
		if(!$this->wrapper)
		{
			$i = 0;
			foreach($blocks as $single_block)
			{
				$i++;
				echo $single_block;
				$this->breakHandler($i);
			}
		}	
		else
		{
			$chunks  = array_chunk($blocks, key($this->wrapper));
			$wrapper = array_shift($this->wrapper);
			$dom_1 = $wrapper[0];
			$dom_2 = $wrapper[1];
			foreach ($chunks as $single_chunk) 
			{
				$i = 0;
				echo $dom_1;
				foreach ($single_chunk as $single_block) 
				{
					$i++;
					echo $single_block;
					$this->breakHandler($i);
				}
				echo $dom_2;
			}
		}
	}

    /**
	 * Ouputs the break  
     * @param int $i 
     */
	private function breakHandler($i)
	{
		if($this->break)
		{
			if($i == key($this->break) || $i%key($this->break) == 0)
			{
				echo array_shift($this->break); 
			}
		}

	}

    /**
	 * Handles the iteration at block-level
     * @param array $collection 
     */
	private function blockLevelHandler($collection)
	{
		foreach($collection as $single_param)
		{
			yield $this->parameterLevelHandler($this->block, $single_param);
		}
	}
    /**
	 * Handles the iteration at parameter level, also applies the filters
     * @param string $block 
     * @param array $single_param 
     */
	private function parameterLevelHandler($block, $single_param)
	{
		foreach($single_param as $key => $val)
		{
			if(array_key_exists($key, $this->filter))
			{
				$function_container = $this->filter[$key];  						
				$function_container['params'] = array_replace($function_container['params'], array_fill_keys(array_keys($function_container['params'], $key), $val));
				$val   = call_user_func_array($function_container['function'], $function_container['params']);
			}
			$block = str_replace("{{".$key."}}", $val, $block);	
		}
		return $block;
	}	

    /**
	 * User mostly for debugging purposes
     * @param string $var 
     * @return $this->$var 
     */
	public function _expose($var)
	{
		return $this->$var;
	}

}