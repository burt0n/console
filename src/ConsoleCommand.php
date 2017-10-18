<?php
namespace Burt0n\Component;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleCommand extends Command
{
    /**
     * @var OutputInterface
     */
    private $output = null;
    
    protected function setOutput(OutputInterface $output)
    {
        //$style = new OutputFormatterStyle(null, 'cyan', array('bold', 'blink'));
        
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
        
        $this->output = $output;
    }

    /**
     * Executes the current command.
     *
     * This method is not abstract because you can use this class
     * as a concrete class. In this case, instead of defining the
     * execute() method, you set the code to execute by passing
     * a Closure to the setCode() method.
     *
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     *
     * @return null|int null or 0 if everything went fine, or an error code
     *
     * @throws LogicException When this abstract method is not implemented
     *
     * @see setCode()
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->beforeExecute($input, $output)) {
            return 1;
        }
        $status = $this->onExecute($input, $output);
        $this->afterExecute($input, $output);
        
        return $status;
    }
    
    public function onExecute(InputInterface $input, OutputInterface $output)
    {
        return 0;
    }
    
    protected function beforeExecute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
        
        return true;
    }
    
    protected function afterExecute(InputInterface $input, OutputInterface $output)
    {
        $this->setOutput($output);
    }
    
    
    
    protected function display($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl);
    }
    
    protected function displayInfo($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl, 'info');
    }
    
    protected function displaySuccess($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl, 'success');
    }
    
    protected function displayWarning($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl, 'warning');
    }
    
    protected function displayError($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl, 'error');
    }
    
    protected function displayImportant($message, $tag='', $nl=true)
    {
        $this->displayBase($message, $tag, $nl, 'important');
    }
    
    protected function displayBase($message, $tag='', $nl=true, $style='default')
    {
        $text = '';
        if ($tag) {
            $text .= '<'.$style.'tag> '.strtoupper($tag).' </'.$style.'tag> ';
        }
        $text .= '<'.$style.'text>'.$message.'</'.$style.'text>';
        
        if ($nl) {
            $this->output->writeln($text);
        } else {
            $this->output->write($text);
        }
    }
}
