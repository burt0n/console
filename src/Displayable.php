<?php
namespace Raketsky\Component\Console;

use App\Exceptions\RetryAttemptsLimitReachedException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Raketsky\Component\Console\Display\Display;
use Raketsky\Component\Console\Formatter\ConsoleOutputFormatter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Trait Displayable
 *
 * @todo move somewhere?
 * @property OutputInterface $output
 */
trait Displayable
{
    /**
     * @var Display
     */
    protected $display = null;

    protected function initColors(): void
    {
        $outputFormatter = new ConsoleOutputFormatter();
        $outputFormatter->setOutputStyle($this->output);

        $this->display = new Display($this->output);
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
        return true;
    }

    protected function afterExecute(): void
    {
    }

    protected function beforeTerminate(): void
    {
	    echo PHP_EOL;
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
        $this->display->displayBase($message, $tag, false);
    }

    /**
     * Display on new line
     * @param string $message
     * @param string $tag
     */
    public function display(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true);
    }

    public function displayInfoSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'info');
    }
    public function displayInfo(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'info');
    }

    public function displaySuccessSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'success');
    }
    public function displaySuccess(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'success');
    }

    public function displayWarningSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'warning');
    }
    public function displayWarning(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'warning');
    }

    public function displayErrorSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'error');
    }
    public function displayError(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'error');
    }

    public function displayImportantSl(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, false, 'important');
    }
    public function displayImportant(string $message, string $tag=''): void
    {
        $this->display->displayBase($message, $tag, true, 'important');
    }

    public function displayExecTime()
    {
        $this->displayImportant(sprintf('Execution time (s): %f', $this->display->getExecTime()), 'EOF');
    }
}
