<?php

namespace MyApp\common;

/**
 * Html.class.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/10/26
 */
class Html
{

	/**
	 * 要素名
	 * @var string
	 */
	private $element = null;

	/**
	 * 属性
	 * @var array
	 */
	private $attributes = [];

	/**
	 * クラス
	 * @var type
	 */
	private $class = [];

	/**
	 * ID
	 * @var type
	 */
	private $id = null;

	/**
	 * 内部HTML
	 * @var string
	 */
	private $innerHtml = null;
	private $noendtags = [
		'br', 'img', 'hr', 'meta', 'input'
		, 'embed', 'area', 'base', 'col', 'keygen'
		, 'link', 'param', 'source'
	];

	public function __construct($element)
	{
		$this->element($element);
		return $this;
	}

	public function __toString()
	{
		return $this->render();
	}

	private function element($element)
	{
		$this->element = strtolower($element);
		return $this;
	}

	public function attr($name, $val)
	{
		$this->attributes[] = [$name => $val];
		return $this;
	}

	public function addClass($className)
	{
		$this->class[] = $className;
		return $this;
	}

	public function id($id)
	{
		$this->id = $id;
		return $this;
	}

	public function html($html)
	{
		$this->innerHtml = $html;
		return $this;
	}

	public function render()
	{
		$html = '<';
		$html .= $this->element;
		if (!is_null($this->id)) {
			$html .= sprintf(' id="%s"', $this->id);
		}
		if (0 < count($this->class)) {
			$html .= sprintf(' class="%s"', implode(' ', $this->class));
		}
		if (0 < count($this->attributes)) {
			foreach ($this->attributes as $key => $val) {
				$html .= sprintf(' %s="%s"', $key, $val);
			}
		}
		$html .= '>';
		$html .= $this->innerHtml;

		if (!in_array($this->element, $this->noendtags)) {
			$html .= '</';
			$html .= $this->element;
			$html .= '>';
		}

		return $html;
	}

}
