<?php
/*****************************************************************************************
* ??document??
*****************************************************************************************/
class wv46v_gcharts extends bv46v_base {
	private $settings = array();
	public function bar_width($type='a',$value=null)
	{
		$return = new stdclass();
		$return->type = $type;
		$return->value = $value;
		return $return;		
	}
	public function bar_chart($orientation='h',$bar_width=null,$bar_spacing=null,$group_spacing=null)
	{
		if(null===$bar_width)
		{
			$bar_width = $this->bar_width();
		}
		$this->settings['chbh']=$bar_width->type;
		$this->settings['cht']="b{$orientation}s";		
	}
	public function pie_chart($type='2d',$legend=null,$labels=null,$orientation=null)
	{
		switch($type)
		{
			case '2d':
				$this->settings['cht']='p';
				break;
			case '3d':
				$this->settings['cht']='p3';
				break;
			case 'concentric':
				$this->settings['cht']='pc';
				break;
		}
		$legend = implode ( '|', $legend );
		if($legend!==null)
		{
			$this->settings['chdl']=$legend;
		}
		if($labels!=null)
		{
			$this->settings['chl']=$labels->labels; // chart data
			$this->settings['chxt']= "x"; // char axis
		}
	}
	public function labels($labels='',$position=0)
	{
		if(is_array($labels))
		{
			$labels=implode('|',$labels);
		}
		$return = new stdclass();
		$return->labels = $labels;
		$return->position = $position;
		return $return;
	}
	public function range($start,$end)
	{
		$return = new stdclass();
		$return->start = $start;
		$return->end = $end;
		return $return;
	}
	public function scale($min,$max)
	{
		$return = new stdclass();
		$return->min = $min;
		$return->max = $max;
		return $return;
	}
	public function axis($type,$labels,$show=false,$range=null,$scale=null)
	{
		$return = new stdclass();
		$return->type = $type;
		$return->labels = $labels;
		$return->range=$range;
		$return->show=$show;
		$return->scale=$scale;
		return $return;		
	}
	private $axis = array();
	public function add_axis($type,$labels,$show=false,$range=null,$scale=null)
	{
		$this->axis[]=$this->axis($type,$labels,$show,$range,$scale);
		$chxt=array();
		$chxp=array();
		$chxs=array();
		$this->settings['chxl']='';
		foreach($this->axis as $key=>$axis)
		{
			if(null!==$axis->range)
			{
				$this->settings['chxr']="{$axis->range->start},{$axis->range->end}";
			}
			if(null!==$axis->scale)
			{
				$this->settings['chds']="{$axis->scale->min},{$axis->scale->max}";//scale
			}
			$chxt[]=$axis->type;
			$chxp[]=$axis->labels->position;
			if($axis->show===false)
			{
				$chxs[]="{$key},676767,11.5,0,_,676767";
			}
			$this->settings['chxl'].="{$key}:|{$axis->labels->labels}|";
		}
		$this->settings['chxs'] = implode('|',$chxs);
		$this->settings['chxt'] = implode(',',$chxt);
		$this->settings['chxp'] = implode(',',$chxp);
	}
	public function title($title,$color=null,$fontsize=null)
	{
		$this->settings ['chtt'] = $title;
	}
	public function data($data)
	{
		if(is_array($data))
		{
			$data = implode ( ',', $data );
		}
		$this->settings['chd'] = "t:" . $data;
	}
	public function chart_colors($colors)
	{
		if(!is_array($colors))
		{
			$colors = explode("\n", $colors);
		}
		foreach($colors as &$color)
		{
			$color = trim($color,"\t \r");
		}
		unset($color);
		$this->settings ['chco'] = implode('|',$colors);
	}
	public function size($width,$height)
	{
		$this->settings ['chs'] = "{$width}x{$height}";
	}
	public function setting($key,$value)
	{
		$this->settings[$key]=$value;
	}
	public function percent()
	{
		$this->settings['chm']="N*p0*,000000,0,,11,,e"; // not sure displays %age though
	}
	public function settings()
	{
		return $this->settings;
	}
	// fix to allow background and chat colors.
	public function background_color($color)
	{
		$this->settings['chf']="bg,s,{$color}";
	}
	public function chart_background_color($color)
	{
		$this->settings['chf']="s,s,{$color}";
	}
	public function show()
	{
		$url = 'http://chart.apis.google.com/chart';
		$args = array();
		$args['method']='POST';
		$args['body'] = $this->settings;
		$args['timeout']=30;
		$return = wp_remote_get($url,$args);
		foreach ( $return['headers'] as $key => $value ) {
			header ( $key . ': ' . $value );
		}
		echo $return['body'];
	}
}