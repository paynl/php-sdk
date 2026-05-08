<?php

declare(strict_types=1);

namespace PayNL\Sdk\Common;

/**
 * Trait DebugTrait
 *
 * Contains the necessary methods which are declared in the corresponding interface
 * @see DebugAwareInterface
 *
 * @package PayNL\Sdk
 */
trait DebugAwareTrait
{
    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @inheritDoc
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isDebug(): bool
    {
        return true === $this->debug;
    }

    /**
     * Dumps the given arguments
     *
     * @param mixed ...$arguments
     *
     * @return void
     * @noinspection ForgottenDebugOutputInspection
     */
    public function dumpDebugInfo(...$arguments): void
    {
        if (false === $this->isDebug()) {
            return;
        }

        ini_set('xdebug.overload_var_dump', 'off');
        if (true === function_exists('dump') && 0 !== strpos(get_class($this), 'Mock_')) {
            /** @noinspection ForgottenDebugOutputInspection */
            dump(...$arguments);
            return;
        }

        echo '<pre>';
        var_dump(...$arguments);
        echo '</pre>' . PHP_EOL;
    }

    /**
     * @param string $arguments
     * @param string $title
     * @param int $height
     * @return void
     */
    public function dumpPreString(string $arguments, string $title = '', int $height = 40): void
    {
        if (false === $this->isDebug()) {
            return;
        }
        echo '<h2 style="font-size: 15px;font-family: monospace;margin-bottom: -4px;">' . $title . '</h2>';
        echo '<div style="white-space:pre-wrap;word-break: break-all;margin-top: 4px;border: 0px solid #343434;display: block;background-color: #989898;font-family: monospace;padding: 10px;color: black;max-height: ' . $height . 'px;overflow-y: auto;">';
        echo $arguments;
        echo '</div>' . PHP_EOL;
    }


    /**
     * @param string $arguments
     * @return void
     */
    public function dumpPreStringAdvanced(string $arguments, string $title = '', int $height = 40): void
    {
        if (false === $this->isDebug()) {
            return;
        }
        echo '<h2 style="font-size: 15px;font-family: monospace;margin-bottom: -4px;">' . $title .
          ' (<span style="cursor:pointer;" onclick="document.getElementById(\'data-json\').style.display = \'none\'; document.getElementById(\'data-arr\').style.display = \'\';    ">array</span>|' .
          '<span style="cursor:pointer;" onclick="document.getElementById(\'data-json\').style.display = \'\'; document.getElementById(\'data-arr\').style.display = \'none\';    ">json</span>)</h2> ';

        echo '<div style="white-space:pre-wrap;word-break: break-all;margin-top: 4px;border: 0px solid #343434;display: block;background-color: #989898;font-family: monospace;padding: 10px;color: black;max-height: ' . $height . 'px;overflow-y: auto;">';

        echo '<div id="data-arr">';
        echo print_r(json_decode($arguments), true);
        echo '</div>';

        echo '<div id="data-json">'.$arguments.'</div>';

        echo '</div>' . PHP_EOL;
    }

}
