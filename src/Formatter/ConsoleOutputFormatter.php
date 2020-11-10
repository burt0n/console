<?php
namespace Raketsky\Component\Console\Formatter;

use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleOutputFormatter
{
    public function setOutputStyle(OutputInterface $output): void
    {
        // default
        $style = new OutputFormatterStyle(null);
        $output->getFormatter()->setStyle('defaulttag', $style);
        $style = new OutputFormatterStyle(null);
        $output->getFormatter()->setStyle('defaulttext', $style);

        // info
        $style = new OutputFormatterStyle(null, 'cyan', ['bold']);
        $output->getFormatter()->setStyle('infotag', $style);
        $style = new OutputFormatterStyle('cyan');
        $output->getFormatter()->setStyle('infotext', $style);

        // success
        $style = new OutputFormatterStyle(null, 'green', ['bold']);
        $output->getFormatter()->setStyle('successtag', $style);
        $style = new OutputFormatterStyle('green');
        $output->getFormatter()->setStyle('successtext', $style);

        // warning
        $style = new OutputFormatterStyle(null, 'yellow', ['bold']);
        $output->getFormatter()->setStyle('warningtag', $style);
        $style = new OutputFormatterStyle('yellow');
        $output->getFormatter()->setStyle('warningtext', $style);

        // error
        $style = new OutputFormatterStyle(null, 'red', ['bold']);
        $output->getFormatter()->setStyle('errortag', $style);
        $style = new OutputFormatterStyle('red');
        $output->getFormatter()->setStyle('errortext', $style);

        // important
        $style = new OutputFormatterStyle(null, 'magenta', ['bold']);
        $output->getFormatter()->setStyle('importanttag', $style);
        $style = new OutputFormatterStyle('magenta');
        $output->getFormatter()->setStyle('importanttext', $style);
    }
}
