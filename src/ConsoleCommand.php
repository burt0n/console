<?php
namespace Egosun\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConsoleCommand extends Command
{
    /**
     * @var OutputInterface
     */
    protected $output = null;

    /**
     * @var InputInterface
     */
    protected $input = null;

    protected function setInput(InputInterface $input): void
    {
        $this->input = $input;
    }

    /**
     * @param OutputInterface $output
     */
    protected function setOutput(OutputInterface $output): void
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
    protected function execute(InputInterface $input, OutputInterface $output): ?int
    {
        if (function_exists('pcntl_signal')) {
    	    declare(ticks=1);
            pcntl_signal(SIGTERM, [&$this, 'onTerminate']);
            pcntl_signal(SIGINT, [&$this, 'onTerminate']);
            //pcntl_signal(SIGHUP, [&$this, 'onRestart']);
        }
        if (!$this->beforeExecute($input, $output)) {
            return 1;
        }
        $status = $this->onExecute($input, $output);
        $this->afterExecute($input, $output);

        return $status;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int|null
     */
    public function onExecute(InputInterface $input, OutputInterface $output): ?int
    {
        return 0;
    }

    public function onTerminate()
    {
    	$this->beforeTerminate();
    	$this->afterTerminate();

        exit;
    }

    protected function beforeExecute(InputInterface $input, OutputInterface $output): bool
    {
        $this->setInput($input);
        $this->setOutput($output);

        return true;
    }

    protected function afterExecute(InputInterface $input, OutputInterface $output): void
    {
    }

    protected function beforeTerminate(): void
    {
	    echo "\n";
    }

    protected function afterTerminate(): void
    {
    	$this->displayWarning('Command is terminated', 'TERMINATED');
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
    protected function displayBase(string $message, string $tag='', bool $nl=true, string $style='default'): void
    {
        if (null !== $this->startTime) {
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
