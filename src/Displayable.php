<?php
namespace Raketsky\Component;

use App\Exceptions\RetryAttemptsLimitReachedException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait Displayable
 *
 * @todo move somewhere?
 */
trait Displayable
{
    /**
     * @var OutputInterface
     */
    //private $output = null;

    protected function initColors(): void
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
     * @throws RetryAttemptsLimitReachedException
     */
    public function handle(): ?int
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

    public function onExecute(): ?int
    {
        return 0;
    }

    public function onTerminate()
    {
    	$this->beforeTerminate();
    	$this->afterTerminate();

        exit;
    }

    protected function beforeExecute(): bool
    {
        //$this->setOutput();

        return true;
    }

    protected function afterExecute(): void
    {
        //$this->setOutput();
    }

    protected function beforeTerminate(): void
    {
	    echo "\n";
    }

    protected function afterTerminate(): void
    {
    	$this->displayWarning("Command is terminated", "TERMINATED");
    }




    /**
     * Display on the same line
     *
     * @param string $message
     * @param string $tag
     */
    public function displaySl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false);
    }

    /**
     * Display on new line
     * @param string $message
     * @param string $tag
     */
    public function display(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true);
    }

    public function displayInfoSl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false, 'info');
    }
    public function displayInfo(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true, 'info');
    }

    public function displaySuccessSl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false, 'success');
    }
    public function displaySuccess(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true, 'success');
    }

    public function displayWarningSl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false, 'warning');
    }
    public function displayWarning(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true, 'warning');
    }

    public function displayErrorSl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false, 'error');
    }
    public function displayError(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true, 'error');
    }

    public function displayImportantSl(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, false, 'important');
    }
    public function displayImportant(string $message, string $tag=''): void
    {
        $this->displayBase($message, $tag, true, 'important');
    }

    public function displayExecTime()
    {
        $this->displayImportant('Execution time (s): '.(microtime(true) - $this->startTime), 'EOF');
    }

    private $startTime = null;
    protected function displayBase($message, $tag='', $nl=true, $style='default')
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
}
