<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Console\Helper;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Helper;

/**
 * The Progress class providers helpers to display progress output.
 *
 * @author Chris Jones <leeked@gmail.com>
 */
class ProgressHelper extends Helper
{
    const FORMAT_QUIET         = ' %percent%%';
    const FORMAT_NORMAL        = ' %current%/%max% [%bar%] %percent%%';
    const FORMAT_VERBOSE
        = ' %current%/%max% [%bar%] %percent%% Elapsed: %elapsed%';
    const FORMAT_QUIET_NOMAX   = ' %current%';
    const FORMAT_NORMAL_NOMAX  = ' %current% [%bar%]';
    const FORMAT_VERBOSE_NOMAX = ' %current% [%bar%] Elapsed: %elapsed%';

    /**
     * @var array
     */
    protected $options = array(
        'barWidth'     => null,
        'barChar'      => null,
        'emptyBarChar' => null,
        'progressChar' => null,
        'format'       => null,
        'redrawFreq'   => null,
    );

    /**
     * @var (string|int)[]
     */
    private $defaultOptions = array(
        'barWidth'     => 28,
        'barChar'      => '=',
        'emptyBarChar' => '-',
        'progressChar' => '>',
        'format'       => self::FORMAT_NORMAL_NOMAX,
        'redrawFreq'   => 1,
    );

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Current step
     *
     * @var integer
     */
    private $current;

    /**
     * Maximum number of steps
     *
     * @var integer
     */
    private $max;

    /**
     * Start time of the progress bar
     *
     * @var integer
     */
    private $startTime;

    /**
     * List of formatting variables
     *
     * @var array
     */
    private $defaultFormatVars = array(
        'current',
        'max',
        'bar',
        'percent',
        'elapsed',
    );

    /**
     * Available formatting variables
     *
     * @var array
     */
    private $formatVars;

    /**
     * Stored format part widths (used for padding)
     *
     * @var array
     */
    private $widths = array(
        'current'   => 4,
        'max'       => 4,
        'percent'   => 3,
        'elapsed'   => 6,
    );

    /**
     * Various time formats
     *
     * @var array
     */
    private $timeFormats = array(
        array(0, '???'),
        array(2, '1 sec'),
        array(59, 'secs', 1),
        array(60, '1 min'),
        array(3600, 'mins', 60),
        array(5400, '1 hr'),
        array(86400, 'hrs', 3600),
        array(129600, '1 day'),
        array(604800, 'days', 86400),
    );

    /**
     * Starts the progress output.
     *
     * @param OutputInterface $output  An Output instance
     * @param integer         $max     Maximum steps
     * @param array           $options Options for progress helper
     *
     * @return void
     */
    public function start(
        OutputInterface $output,
        $max = null,
        array $options = array()
    ) {
        $this->startTime = time();
        $this->current   = 0;
        $this->max       = (int) $max;
        $this->output    = $output;

        switch ($output->getVerbosity()) {
            case OutputInterface::VERBOSITY_QUIET:
                $this->options['format'] = self::FORMAT_QUIET_NOMAX;
                if ($this->max > 0) {
                    $this->options['format'] = self::FORMAT_QUIET;
                }
                break;
            case OutputInterface::VERBOSITY_VERBOSE:
                $this->options['format'] = self::FORMAT_VERBOSE_NOMAX;
                if ($this->max > 0) {
                    $this->options['format'] = self::FORMAT_VERBOSE;
                }
                break;
            default:
                if ($this->max > 0) {
                    $this->options['format'] = self::FORMAT_NORMAL;
                }
                break;
        }

        $this->options = array_merge($this->defaultOptions, $options);
        $this->initialize();
    }

    /**
     * Advances the progress output X steps.
     *
     * @param integer $step   Number of steps to advance
     * @param Boolean $redraw Whether to redraw or not
     *
     * @return void
     */
    public function advance($step = 1, $redraw = false)
    {
        if ($this->current === 0) {
            $redraw = true;
        }
        $this->current += $step;
        if ($redraw || $this->current % $this->options['redrawFreq'] === 0) {
            $this->display();
        }
    }

    /**
     * Outputs the current progress string.
     *
     * @param Boolean $finish Forces the end result
     *
     * @return void
     */
    public function display($finish = false)
    {
        $message = $this->options['format'];
        foreach ($this->generate($finish) as $name => $value) {
            $message = str_replace("%{$name}%", $value, $message);
        }
        $this->overwrite($this->output, $message);
    }

    /**
     * Finish the progress output
     *
     * @return void
     */
    public function finish()
    {
        if ($this->startTime !== null) {
            if (!$this->max) {
                $this->options['barChar'] = $this->options['barCharOriginal'];
                $this->display(true);
            }
            $this->startTime = null;
            $this->output->writeln('');
            $this->output = null;
        }
    }

    /**
     * Initialize the progress helper.
     *
     * @return void
     */
    protected function initialize()
    {
        $this->formatVars = array();
        foreach ($this->defaultFormatVars as $var) {
            if (strpos($this->options['format'], "%{$var}%") !== false) {
                $this->formatVars[$var] = true;
            }
        }

        if ($this->max > 0) {
            $this->widths['max']     = strlen($this->max);
            $this->widths['current'] = $this->widths['max'];
        } else {
            $this->options['barCharOriginal'] = $this->options['barChar'];
            $this->options['barChar']         = $this->options['emptyBarChar'];
        }
    }

    /**
     * Generates the array map of format variables to values.
     *
     * @param boolean $finish Forces the end result
     *
     * @return string[] Array of format vars and values
     */
    protected function generate($finish = false)
    {
        $vars    = array();
        $percent = 0;
        if ($this->max > 0) {
            $percent = (double) round($this->current / $this->max, 1);
        }

        if (isset($this->formatVars['bar'])) {
            if ($this->max > 0) {
                $completeBars = floor($percent * $this->options['barWidth']);
            } else {
                if (!$finish) {
                    $completeBars = floor(
                        $this->current % $this->options['barWidth']
                    );
                } else {
                    $completeBars = $this->options['barWidth'];
                }
            }

            $emptyBars = $this->options['barWidth'] - $completeBars
                - strlen($this->options['progressChar']);
            $bar = str_repeat($this->options['barChar'], $completeBars);
            if ($completeBars < $this->options['barWidth']) {
                $bar .= $this->options['progressChar'];
                $bar .= str_repeat($this->options['emptyBarChar'], $emptyBars);
            }

            $vars['bar'] = $bar;
        }

        if (isset($this->formatVars['elapsed'])) {
            $elapsed = time() - $this->startTime;
            $vars['elapsed'] = str_pad($this->humaneTime($elapsed), $this->widths['elapsed'], ' ', STR_PAD_LEFT);
        }

        if (isset($this->formatVars['current'])) {
            $vars['current'] = str_pad($this->current, $this->widths['current'], ' ', STR_PAD_LEFT);
        }

        if (isset($this->formatVars['max'])) {
            $vars['max'] = $this->max;
        }

        if (isset($this->formatVars['percent'])) {
            $vars['percent'] = str_pad($percent * 100, $this->widths['percent'], ' ', STR_PAD_LEFT);
        }

        return $vars;
    }

    /**
     * Converts seconds into human-readable format.
     *
     * @param integer $secs Number of seconds
     *
     * @return string Time in readable format
     */
    private function humaneTime($secs)
    {
        $text = '';
        foreach ($this->timeFormats as $format) {
            if ($secs < $format[0]) {
                if (count($format) == 2) {
                    $text = $format[1];
                    break;
                } else {
                    $text = ceil($secs / $format[2]) . ' ' . $format[1];
                    break;
                }
            }
        }

        return $text;
    }

    /**
     * Overwrites a previous message to the output.
     *
     * @param OutputInterface $output   An Output instance
     * @param string|array    $messages The message as an array of lines or a
     *     single string
     * @param Boolean         $newline  Whether to add a newline or not
     * @param integer         $size     The size of line
     *
     * @return void
     */
    private function overwrite(
        OutputInterface $output,
        $messages,
        $newline = true,
        $size = 80
    ) {
        for ($place = $size; $place > 0; $place--) {
            $output->write("\x08", false);
        }

        $output->write($messages, false);

        for ($place = ($size - strlen($messages)); $place > 0; $place--) {
            $output->write(' ', false);
        }

        // clean up the end line
        for ($place = ($size - strlen($messages)); $place > 0; $place--) {
            $output->write("\x08", false);
        }

        if ($newline) {
            $output->write('');
        }
    }

    /**
     * Returns the canonical name of this helper.
     *
     * @return string The canonical name
     */
    public function getName()
    {
        return 'progress';
    }
}
