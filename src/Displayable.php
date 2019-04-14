<?php
namespace Raketsky\Component;

use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait Displayable
 *
 * @todo move somewhere?
 * @package App\Console
 */
trait Displayable
{
    /**
     * @var OutputInterface
     */
    //private $output = null;

    protected function initColors()
    {
        //$style = new OutputFormatterStyle(null, 'cyan', array('bold', 'blink'));

        // default
        $style = new OutputFormatterStyle(null);
        $this->output->getFormatter()->setStyle('defaulttag', $style);
        $style = new OutputFormatterStyle(null);
        $this->output->getFormatter()->setStyle('defaulttext', $style);

        // info
        $style = new OutputFormatterStyle(null, 'cyan', ['bold']);
        $this->output->getFormatter()->setStyle('infotag', $style);
        $style = new OutputFormatterStyle('cyan');
        $this->output->getFormatter()->setStyle('infotext', $style);

        // success
        $style = new OutputFormatterStyle(null, 'green', ['bold']);
        $this->output->getFormatter()->setStyle('successtag', $style);
        $style = new OutputFormatterStyle('green');
        $this->output->getFormatter()->setStyle('successtext', $style);

        // warning
        $style = new OutputFormatterStyle(null, 'yellow', ['bold']);
        $this->output->getFormatter()->setStyle('warningtag', $style);
        $style = new OutputFormatterStyle('yellow');
        $this->output->getFormatter()->setStyle('warningtext', $style);

        // error
        $style = new OutputFormatterStyle(null, 'red', ['bold']);
        $this->output->getFormatter()->setStyle('errortag', $style);
        $style = new OutputFormatterStyle('red');
        $this->output->getFormatter()->setStyle('errortext', $style);

        // important
        $style = new OutputFormatterStyle(null, 'magenta', ['bold']);
        $this->output->getFormatter()->setStyle('importanttag', $style);
        $style = new OutputFormatterStyle('magenta');
        $this->output->getFormatter()->setStyle('importanttext', $style);

        //$this->output = $output;
    }

    /**
     * Compiles products from source data
     *
     * @return mixed
     * @throws BindingResolutionException
     * @throws \App\Exceptions\RetryAttemptsLimitReachedException
     */
    public function handle()
    {
        if (function_exists('pcntl_signal') && function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGTERM, [&$this, 'onTerminate']);
            pcntl_signal(SIGINT, [&$this, 'onTerminate']);
            //pcntl_signal(SIGHUP, [&$this, 'onRestart']);
        }
        $this->initColors();

        if (!$this->beforeExecute()) {
            return 1;
        }
        $status = $this->onExecute();
        $this->afterExecute();

        return $status;
    }

    public function onExecute()
    {
        return 0;
    }

    public function onTerminate()
    {
    	$this->beforeTerminate();
    	$this->afterTerminate();

        exit;
    }

    protected function beforeExecute()
    {
        //$this->setOutput();

        return true;
    }

    protected function afterExecute()
    {
        //$this->setOutput();
    }

    protected function beforeTerminate()
    {
	    echo "\n";
    }

    protected function afterTerminate()
    {
    	$this->displayWarning("Command is terminated", "TERMINATED");
    }




    protected function displayAlert($message)
    {
        $this->display("\n");
        $this->alert($message);
    }

    protected function displayInfo($message, $tag='', $nl=true)
    {
        $this->display($message, $tag, $nl, 'info');
    }

    protected function displaySuccess($message, $tag='', $nl=true)
    {
        $this->display($message, $tag, $nl, 'success');
    }

    protected function displayWarning($message, $tag='', $nl=true)
    {
        $this->display($message, $tag, $nl, 'warning');
    }

    protected function displayError($message, $tag='', $nl=true)
    {
        $this->display($message, $tag, $nl, 'error');
    }

    protected function displayImportant($message, $tag='', $nl=true)
    {
        $this->display($message, $tag, $nl, 'important');
    }

    private $startTime = null;
    protected function display($message, $tag='', $nl=true, $style='default')
    {
        if ($this->startTime === null) {
            $this->startTime = microtime(true);
        }

        $text = '';
        if ($tag) {
            $text .= '<'.$style.'tag> '.mb_strtoupper($tag).' </'.$style.'tag> ';
        }
        $text .= '<'.$style.'text>'.$message.'</'.$style.'text>';

        if ($nl) {
            $this->output->writeln($text);
        } else {
            $this->output->write($text);
        }
    }

    protected function displayExecTime()
    {
        $this->displayImportant('Execution time (s): '.(microtime(true) - $this->startTime), 'EOF');
    }
}
