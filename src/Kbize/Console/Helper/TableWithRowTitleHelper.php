<?php

namespace Kbize\Console\Helper;

use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Output\OutputInterface;

class TableWithRowTitleHelper extends TableHelper
{
    const LAYOUT_DEFAULT = 0;
    const LAYOUT_BORDERLESS = 1;

    /**
     * Table headers.
     *
     * @var array
     */
    private $headers = array();

    /**
     * Table rows.
     *
     * @var array
     */
    private $rows = array();

    /**
     * Table title.
     *
     * @var string
     */
    private $title;

    // Rendering options
    private $paddingChar;
    private $horizontalBorderChar;
    private $verticalBorderChar;
    private $crossingChar;
    private $cellHeaderFormat;
    private $cellRowFormat;
    private $borderFormat;
    private $padType;

    /**
     * Column widths cache.
     *
     * @var array
     */
    private $columnWidths = array();

    /**
     * Number of columns cache.
     *
     * @var array
     */
    private $numberOfColumns;

    /**
     * @var OutputInterface
     */
    private $output;

    public function __construct()
    {
        $this->setLayout(self::LAYOUT_DEFAULT);
    }

    /**
     * Sets table layout type.
     *
     * @param int $layout self::LAYOUT_*
     *
     * @return TableHelper
     */
    public function setLayout($layout)
    {
        switch ($layout) {
            case self::LAYOUT_BORDERLESS:
                $this
                    ->setPaddingChar(' ')
                    ->setHorizontalBorderChar('=')
                    ->setVerticalBorderChar(' ')
                    ->setCrossingChar(' ')
                    ->setCellHeaderFormat('<info>%s</info>')
                    ->setCellRowFormat('<comment>%s</comment>')
                    ->setBorderFormat('%s')
                    ->setPadType(STR_PAD_RIGHT)
                ;
                break;

            case self::LAYOUT_DEFAULT:
                $this
                    ->setPaddingChar(' ')
                    ->setHorizontalBorderChar('-')
                    ->setVerticalBorderChar('|')
                    ->setCrossingChar('+')
                    ->setCellHeaderFormat('<info>%s</info>')
                    ->setCellRowFormat('<comment>%s</comment>')
                    ->setBorderFormat('%s')
                    ->setPadType(STR_PAD_RIGHT)
                ;
                break;

            default:
                throw new InvalidArgumentException(sprintf('Invalid table layout "%s".', $layout));
                break;
        };

        return $this;
    }

    public function setHeaders(array $headers)
    {
        $this->headers = array_values($headers);

        return $this;
    }

    public function setRows(array $rows)
    {
        $this->rows = array();

        return $this->addRows($rows);
    }

    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    public function addRows(array $rows)
    {
        foreach ($rows as $row) {
            $this->addRow($row);
        }

        return $this;
    }

    public function addRow(array $row)
    {
        $this->rows[] = array_values($row);

        $keys = array_keys($this->rows);
        $rowKey = array_pop($keys);

        foreach ($row as $key => $cellValue) {
            if (!strstr($cellValue, "\n")) {
                continue;
            }

            $lines = explode("\n", $cellValue);
            $this->rows[$rowKey][$key] = $lines[0];
            unset($lines[0]);

            foreach ($lines as $lineKey => $line) {
                $nextRowKey = $rowKey + $lineKey + 1;

                if (isset($this->rows[$nextRowKey])) {
                    $this->rows[$nextRowKey][$key] = $line;
                } else {
                    $this->rows[$nextRowKey] = array($key => $line);
                }
            }
        }

        return $this;
    }

    public function setRow($column, array $row)
    {
        $this->rows[$column] = $row;

        return $this;
    }

    /**
     * Sets padding character, used for cell padding.
     *
     * @param string $paddingChar
     *
     * @return TableHelper
     */
    public function setPaddingChar($paddingChar)
    {
        $this->paddingChar = $paddingChar;

        return $this;
    }

    /**
     * Sets horizontal border character.
     *
     * @param string $horizontalBorderChar
     *
     * @return TableHelper
     */
    public function setHorizontalBorderChar($horizontalBorderChar)
    {
        $this->horizontalBorderChar = $horizontalBorderChar;

        return $this;
    }

    /**
     * Sets vertical border character.
     *
     * @param string $verticalBorderChar
     *
     * @return TableHelper
     */
    public function setVerticalBorderChar($verticalBorderChar)
    {
        $this->verticalBorderChar = $verticalBorderChar;

        return $this;
    }

    /**
     * Sets crossing character.
     *
     * @param string $crossingChar
     *
     * @return TableHelper
     */
    public function setCrossingChar($crossingChar)
    {
        $this->crossingChar = $crossingChar;

        return $this;
    }

    /**
     * Sets header cell format.
     *
     * @param string $cellHeaderFormat
     *
     * @return TableHelper
     */
    public function setCellHeaderFormat($cellHeaderFormat)
    {
        $this->cellHeaderFormat = $cellHeaderFormat;

        return $this;
    }

    /**
     * Sets row cell format.
     *
     * @param string $cellRowFormat
     *
     * @return TableHelper
     */
    public function setCellRowFormat($cellRowFormat)
    {
        $this->cellRowFormat = $cellRowFormat;

        return $this;
    }

    /**
     * Sets table border format.
     *
     * @param string $borderFormat
     *
     * @return TableHelper
     */
    public function setBorderFormat($borderFormat)
    {
        $this->borderFormat = $borderFormat;

        return $this;
    }

    /**
     * Sets cell padding type.
     *
     * @param integer $padType STR_PAD_*
     *
     * @return TableHelper
     */
    public function setPadType($padType)
    {
        $this->padType = $padType;

        return $this;
    }

    /**
     * Renders table to output.
     *
     * Example:
     * +---------------+-----------------------+------------------+
     * | ISBN          | Title                 | Author           |
     * +---------------+-----------------------+------------------+
     * | 99921-58-10-7 | Divine Comedy         | Dante Alighieri  |
     * | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     * | 960-425-059-0 | The Lord of the Rings | J. R. R. Tolkien |
     * +---------------+-----------------------+------------------+
     *
     * @param OutputInterface $output
     */
    public function render(OutputInterface $output)
    {
        $this->output = $output;

        if ($this->title) {
            $this->renderTitle();
        }

        $this->renderRowSeparator();
        $this->renderRow($this->headers, $this->cellHeaderFormat);
        if (!empty($this->headers)) {
            $this->renderRowSeparator();
        }
        foreach ($this->rows as $row) {
            $this->renderRow($row, $this->cellRowFormat);
        }
        if (!empty($this->rows)) {
            $this->renderRowSeparator();
        }

        $this->cleanup();
    }

    private function renderTitle()
    {
        if (0 === $count = $this->getNumberOfColumns()) {
            return;
        }

        $markup = $this->crossingChar;
        $markup .= str_repeat($this->horizontalBorderChar, $this->getTableWidth()) . $this->crossingChar;

        $this->output->writeln(sprintf($this->borderFormat, $markup));

        $this->renderColumnSeparator();
        $this->output->write(sprintf(
            $this->cellHeaderFormat,
            str_pad(
                $this->paddingChar.$this->title.$this->paddingChar,
                $this->getTableWidth(),
                $this->paddingChar,
                STR_PAD_BOTH
            )
        ));
        $this->renderColumnSeparator();
        $this->output->writeln('');
    }

    /**
     * Renders horizontal header separator.
     *
     * Example: +-----+-----------+-------+
     */
    private function renderRowSeparator()
    {
        if (0 === $count = $this->getNumberOfColumns()) {
            return;
        }

        $markup = $this->crossingChar;
        for ($column = 0; $column < $count; $column++) {
            $markup .= str_repeat($this->horizontalBorderChar, $this->getColumnWidth($column))
                    .$this->crossingChar
            ;
        }

        $this->output->writeln(sprintf($this->borderFormat, $markup));
    }

    /**
     * Renders vertical column separator.
     */
    private function renderColumnSeparator()
    {
        $this->output->write(sprintf($this->borderFormat, $this->verticalBorderChar));
    }

    /**
     * Renders table row.
     *
     * Example: | 9971-5-0210-0 | A Tale of Two Cities  | Charles Dickens  |
     *
     * @param array  $row
     * @param string $cellFormat
     */
    private function renderRow(array $row, $cellFormat)
    {
        if (empty($row)) {
            return;
        }

        if (isset($row[0]) && $row[0] === '__LANETITLE__') {

            $this->renderRowSeparator();

            $this->renderColumnSeparator();
            $this->output->write(sprintf(
                $this->cellHeaderFormat,

                '<fg=black;bg=yellow;options=bold>' .
                str_pad(
                    $this->paddingChar.$row[1].$this->paddingChar,
                    $this->getTableWidth(),
                    $this->paddingChar,
                    STR_PAD_BOTH
                ) .
                '</fg=black;bg=yellow;options=bold>'
            ));
            $this->renderColumnSeparator();
            $this->output->writeln('');

            $this->renderRowSeparator();

            return;
        }

        $this->renderColumnSeparator();
        for ($column = 0, $count = $this->getNumberOfColumns(); $column < $count; $column++) {
            $this->renderCell($row, $column, $cellFormat);
            $this->renderColumnSeparator();
        }
        $this->output->writeln('');
    }

    /**
     * Renders table cell with padding.
     *
     * @param array   $row
     * @param integer $column
     * @param string  $cellFormat
     */
    private function renderCell(array $row, $column, $cellFormat)
    {
        $cell = isset($row[$column]) ? $row[$column] : '';
        $width = $this->getColumnWidth($column);

        // str_pad won't work properly with multi-byte strings, we need to fix the padding
        if (function_exists('mb_strlen') && false !== $encoding = mb_detect_encoding($cell)) {
            $width += strlen($cell) - mb_strlen($cell, $encoding);
        }

        $width += $this->strlen($cell) - $this->computeLengthWithoutDecoration($cell);

        $this->output->write(sprintf(
            $cellFormat,
            str_pad(
                $this->paddingChar.$cell.$this->paddingChar,
                $width,
                $this->paddingChar,
                $this->padType
            )
        ));
    }

    /**
     * Gets number of columns for this table.
     *
     * @return int
     */
    private function getNumberOfColumns()
    {
        if (null !== $this->numberOfColumns) {
            return $this->numberOfColumns;
        }

        $columns = array(0);
        $columns[] = count($this->headers);
        foreach ($this->rows as $row) {
            $columns[] = count($row);
        }

        return $this->numberOfColumns = max($columns);
    }

    /**
     * Gets column width.
     *
     * @param integer $column
     *
     * @return int
     */
    private function getColumnWidth($column)
    {
        if (isset($this->columnWidths[$column])) {
            return $this->columnWidths[$column];
        }

        $lengths = array(0);
        $lengths[] = $this->getCellWidth($this->headers, $column);
        foreach ($this->rows as $row) {
            $lengths[] = $this->getCellWidth($row, $column);
        }

        return $this->columnWidths[$column] = max($lengths) + 2;
    }

    private function getTableWidth()
    {
        $lenght = 0;

        if (0 === $count = $this->getNumberOfColumns()) {
            return $lenght;
        }

        for ($column = 0; $column < $count; $column++) {
            $lenght += $this->getColumnWidth($column) + 1;
        }

        return $lenght -1;
    }

    /**
     * Gets cell width.
     *
     * @param array   $row
     * @param integer $column
     *
     * @return int
     */
    private function getCellWidth(array $row, $column)
    {
        return isset($row[$column]) ? $this->computeLengthWithoutDecoration($row[$column]) : 0;
    }

    /**
     * Called after rendering to cleanup cache data.
     */
    private function cleanup()
    {
        $this->columnWidths = array();
        $this->numberOfColumns = null;
    }

    private function computeLengthWithoutDecoration($string)
    {
        $formatter = $this->output->getFormatter();
        $isDecorated = $formatter->isDecorated();
        $formatter->setDecorated(false);

        $string = $formatter->format($string);
        $formatter->setDecorated($isDecorated);

        return $this->strlen($string);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'row-title-table';
    }
}
