<?php

namespace Giftcards\FixedWidth;

/**
 * @author Cody Phillips
 */
class FileInline implements \IteratorAggregate, \ArrayAccess, FileInterface
{
    protected $file;
    protected $width;
    protected $lineSeparator;

    public function __construct(
        \SplFileObject $file,
        $width,
        $lineSeparator = "\r\n"
    ) {
        $this->file = $file;
        $this->width = (int)$width;
        $this->lineSeparator = $lineSeparator;
    }

    public function __toString()
    {
        return null;
    }

    public function getLines()
    {
        throw new \BadMethodCallException('Method not supported by this class.');
    }

    public function getLine($index)
    {
        $this->file->seek($index);
        
        if (!$this->file->valid()) {

            throw new \OutOfBoundsException('The index is outside of the available indexes of lines.');
        }
        
        return new Line($this->file->current());
    }

    public function offsetExists($offset)
    {
        $this->file->seek($offset);
        return $this->file->valid();
    }

    public function offsetGet($offset)
    {
        return $this->getLine($offset);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {

            $this->addLine($value);
            return;
        }

        $this->setLine($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->removeLine($offset);
    }

    public function count()
    {
        return count($this->lines);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->file->getFilename();
    }

    public function getIterator()
    {
        return $this->file;
    }

    public function addLine($line)
    {
        $line = $this->validateLine($line);
        
        $this->file->fseek(0, SEEK_END);
        
        $this->file->fwrite((string)$line . $this->lineSeparator);
        return $this;
    }

    public function setLine($index, $line)
    {
        throw new \BadMethodCallException('Can not write anywhere except the end of the file. Use addLine().');
    }

    public function removeLine($index)
    {
        throw new \BadMethodCallException('Can not remove lines.');
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    public function getLineSeparator()
    {
        return $this->lineSeparator;
    }
    
    protected function validateLine($line)
    {
        if (!$line instanceof Line) {

            $line = new Line((string)$line);
        }

        if ($line->getLength() != $this->getWidth()) {

            throw new \InvalidArgumentException(sprintf(
                'All lines in a batch file must be %d chars wide this line is %d chars wide.',
                $this->getWidth(),
                strlen($line)
            ));
        }

        return $line;
    }
}
