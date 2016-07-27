<?php 
class Drillr
{
	/*
		[TODO before Github]
				//na suite de teste, fazer um bootstrap pra ficar suave sacas??
				//ai a cena é meter o loco mesmo saca
				//se sair documentando tudo também
				//teoricamente eu ia mudar e fazer como se fosse um singleton, MAS ANTES ISSO CONFIGURAR TDD

		 - Make it a singleton
		 - Make tests suite
		 - Make demo folder with the iterations n shit
	*/


	private $path, $block, $break, $wrapper, $filter = array();
	
	public function __construct($path = null)
	{
		if($path)
		{
			$this->path = $path;
		}
	}

	public function addToPath($str)
	{
		$this->path .= $str;
		return $this;
	}

	public function changePath($str)
	{
		$this->path = $str;
		return $this;
	}

	public function loadBlock($str)
	{
		if(file_exists($this->path.$str))
		{
			$this->block = file_get_contents($this->path.$str);
		}
		return $this;
	}

	public function addBreak($num, $dom)
	{
		$this->break = array($num => $dom);
		return $this;
	}

	public function addWrapper($num, $dom1, $dom2)
	{
		$this->wrapper = array($num => array($dom1,$dom2));
		return $this;
	}

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

	public function iterate($params)
	{
		$this->outputHandler(iterator_to_array($this->blockLevelHandler($params)));
	}

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

	private function blockLevelHandler($params)
	{
		foreach($params as $single_param)
		{
			yield $this->parameterLevelHandler($this->block, $single_param);
		}
	}
	
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
}