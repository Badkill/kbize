<?php
namespace Kbize\Console;

class String
{
    public function __construct($value, $fg = null, $options = [])
    {
        $this->value = (string) $value;
        $this->fg = $fg;
        $this->options = $options;
    }

    public static function box($value)
    {
        return new self($value);
    }

    public function fixed($size)
    {
        return new self(str_pad(
            substr($this->value, 0, $size),
            $size,
            ' '
        ));
    }

    /**
     * @param string $color
     */
    public function color($color)
    {
        if ($color) {
            return new self($this->value, $color, $this->options);
        }

        return $this;
    }

    public function bold()
    {
        return new self($this->value, $this->fg, array_merge($this->options, ['bold']));
    }

    public function __toString()
    {
        if ($this->fg || $this->options) {
            $tag = "";

            if ($this->fg) {
                $tag .= "fg=$this->fg";
            }

            if ($this->options) {
                if ($tag) {
                    $tag .= ';';
                }

                $options = implode($this->options, ',');
                $tag .= "options=$options";
            }

            return "<$tag>$this->value</$tag>";
        }

        return $this->value;
    }
}
